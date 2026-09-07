# مستندات سیستم پرداخت و کارت‌به‌کارت

> این سند وضعیت فعلی branch `payment-flow` را ثبت می‌کند و علاوه بر معماری، خطاها و درس‌های مهم پیاده‌سازی را نگه می‌دارد تا در ادامه دوباره همان مسیرهای اشتباه را تکرار نکنیم.

## 1. معماری نهایی

```text
سبد خرید
   ↓
اطلاعات گیرنده
   ↓
انتخاب روش پرداخت
   ├── آنلاین
   │    ↓
   │  بانک ملت
   │    ↓
   │  callback → verify → settle
   │    ↓
   │  Payment = paid
   │    ↓
   │  Order = paid
   │    ↓
   │  اطلاع فروشنده در Bale
   │
   └── کارت‌به‌کارت
        ↓
      نمایش حساب فروشگاه
        ↓
      آپلود رسید
        ↓
      Payment = waiting_confirmation
        ↓
      Order = 2
        ↓
      ارسال رسید + مشخصات سفارش به Bale
        ↓
      فروشنده: تأیید / رد
        ├── رد → Payment = rejected
        └── تأیید
             ↓
           Payment = paid
           Order = paid
             ↓
           ساخت لینک رهگیری مشتری
             ↓
           دکمه «ارسال تأیید به مشتری»
             ↓
           باز شدن SMS گوشی فروشنده
             ↓
           پیام تأیید + لینک سفارش
```

---

## 2. اجزای اصلی

| فایل | مسئولیت |
|---|---|
| `app/Http/Controllers/front/PaymentController.php` | Checkout، ساخت Payment، کارت‌به‌کارت و Callback ملت |
| `app/Models/Payment.php` | وضعیت Payment و Dispatch رویداد کارت‌به‌کارت |
| `app/Models/Order.php` | سفارش و رابطه محصولات/مشتری/فروشگاه |
| `app/Models/Product.php` | اطلاعات محصول؛ نام محصول در فیلد `title` است |
| `app/Events/CardToCardPaymentSubmitted.php` | رویداد ثبت رسید کارت‌به‌کارت |
| `app/Listeners/SendCardToCardPaymentToBale.php` | ارسال رسید و اطلاعات کارت‌به‌کارت به Bale |
| `app/Events/PaymentWasSuccessful.php` | رویداد پرداخت موفق |
| `app/Listeners/SendPaymentSuccessToBale.php` | اطلاع پرداخت آنلاین موفق به Bale |
| `app/Services/BaleService.php` | Wrapper ارتباط با Bale API |
| `app/Http/Controllers/customer/BaleWebhookController.php` | اتصال فروشگاه به Bale و تأیید/رد پرداخت |
| `app/Services/CustomerOrderLinkService.php` | ساخت لینک رهگیری و `sms:` URL |
| `app/Http/Controllers/front/OrderTrackingController.php` | اعتبارسنجی و نمایش سفارش مشتری |
| `resources/views/Frontend/Shop/Orders/track.blade.php` | صفحه رهگیری سفارش مشتری |
| `routes/web.php` | Routeهای Checkout، پرداخت و رهگیری |
| `config/services.php` | تنظیمات Bale و مسیر `public_html` |

---

# 3. Checkout و Guest

`PaymentController@checkout` اطلاعات گیرنده را Validate می‌کند و برای Buyer یا Guest سفارش فعال Checkout را پیدا/ایجاد می‌کند.

برای Guest، اگر `buyer_login_required` فعال باشد، Login اجباری است؛ در غیر این صورت Order با `buyer_id = null` ساخته می‌شود و شناسه آن در Session با نام `checkout_order_id` نگه داشته می‌شود.

Cart نیز با کلید `cart` در Session نگه‌داری می‌شود.

بعد از پرداخت موفق آنلاین یا ثبت رسید کارت‌به‌کارت، Cart و در حالت Guest، `checkout_order_id` پاک می‌شوند.

---

# 4. پرداخت آنلاین ملت

روند کلی:

```text
Payment = pending
   ↓
bpPayRequest
   ↓
Payment = redirected
   ↓
Bank Callback
   ↓
ResCode
   ↓
bpVerifyRequest
   ↓
bpSettleRequest
   ↓
Payment = paid
Order = paid
```

بعد از موفقیت، `PaymentWasSuccessful` اجرا می‌شود و `SendPaymentSuccessToBale` اطلاعات سفارش را برای فروشنده می‌فرستد.

نکته مهم: موفقیت واقعی پرداخت فقط بعد از Verify و Settle مشخص می‌شود؛ صرفاً برگشت کاربر از درگاه به معنی پرداخت موفق نیست.

---

# 5. پرداخت کارت‌به‌کارت

## 5.1 ثبت رسید

`PaymentController@cardToCard`:

1. Order فعال را پیدا می‌کند.
2. مبلغ را از Order محاسبه می‌کند.
3. Payment کارت‌به‌کارت را پیدا یا ایجاد می‌کند.
4. رسید را ذخیره می‌کند.
5. `PaymentReceipt` را ایجاد/به‌روزرسانی می‌کند.
6. Payment را به `waiting_confirmation` می‌برد.
7. Order را به وضعیت `2` می‌برد تا دیگر Cart فعال محسوب نشود.
8. برای Guest، Sessionهای Cart و Checkout پاک می‌شوند.

وقتی Payment به `waiting_confirmation` تغییر می‌کند، مدل Payment رویداد `CardToCardPaymentSubmitted` را Dispatch می‌کند.

## 5.2 ارسال به Bale

Listener `SendCardToCardPaymentToBale` اطلاعات زیر را برای فروشنده ارسال می‌کند:

- شماره سفارش
- مبلغ
- نام مشتری
- موبایل
- کد پیگیری
- تصویر واقعی رسید، در صورت وجود
- دکمه تأیید پرداخت
- دکمه رد پرداخت

رسید به‌صورت Photo به Bale ارسال می‌شود، نه صرفاً لینک تصویر.

## 5.3 تأیید یا رد در Bale

Webhook در `BaleWebhookController` Callback دکمه‌ها را دریافت می‌کند.

برای امنیت، قبل از تغییر Payment بررسی می‌شود که Chat مربوط به Bale به همان Shop متصل و فعال باشد.

همچنین فقط Paymentهای:

```text
method = card_to_card
status = waiting_confirmation
```

قابل بررسی هستند.

تغییر وضعیت داخل Transaction و با `lockForUpdate()` انجام می‌شود تا کلیک تکراری یا هم‌زمان باعث دوباره‌کاری نشود.

### تأیید

```text
Payment → paid
Order → paid
paid_at → now()
```

### رد

```text
Payment → rejected
```

---

# 6. تأیید مشتری بدون هزینه SMS

برای تأیید مشتری از سرویس SMS پولی استفاده نکردیم.

بعد از تأیید فروشنده در Bale:

1. `CustomerOrderLinkService` یک لینک موقت رهگیری می‌سازد.
2. لینک بر اساس `shop_id + order_id + expires_at` و `app.key` با HMAC امضا می‌شود.
3. لینک به شکل زیر است:

```text
/order/{order}/track/{expires}/{token}
```

4. Bale دکمه `📱 ارسال تأیید به مشتری` را نمایش می‌دهد.
5. دکمه یک URL از نوع `sms:` باز می‌کند.
6. گوشی فروشنده برنامه SMS را با شماره مشتری و متن آماده باز می‌کند.
7. فروشنده فقط ارسال پیام را تأیید می‌کند.

این روش هزینه ارسال SMS را حذف می‌کند؛ اما ارسال نهایی پیام همچنان توسط اپراتور/گوشی فروشنده انجام می‌شود.

---

# 7. لینک رهگیری سفارش

Controller:

```text
app/Http/Controllers/front/OrderTrackingController.php
```

قبل از نمایش سفارش این موارد بررسی می‌شوند:

- `order` عدد معتبر باشد.
- `expires` معتبر و هنوز منقضی نشده باشد.
- Shop فعلی وجود داشته باشد.
- Order متعلق به همان Shop باشد.
- HMAC Token معتبر باشد.

لینک عمداً تاریخ انقضا دارد و Token آن قابل حدس ساده نیست.

---

# 8. یک نکته مهم درباره نام محصول

در مدل `Product` فیلد نام محصول `title` است، نه `name`.

بنابراین در View رهگیری باید از این استفاده شود:

```blade
{{ $product->title }}
```

و نه:

```blade
{{ $product->name }}
```

این مورد یکی از باگ‌های نهایی بود: تعداد و قیمت درست نمایش داده می‌شدند ولی نام محصول خالی بود.

---

# 9. یک نکته مهم درباره View و متغیر `$order`

در پروژه یک View Composer متغیری با نام `$order` را برای بعضی Viewها Inject می‌کند. در Guest Checkout این متغیر می‌تواند Array مربوط به Cart باشد.

در نتیجه استفاده از `$order` در View رهگیری باعث Collision شد و خطای زیر ایجاد شد:

```text
Attempt to read property "id" on array
```

راه‌حل این بود که Order واقعی رهگیری‌شده با نام مشخص `trackedOrder` به View داده شود:

```php
return view('Frontend.Shop.Orders.track', [
    'trackedOrder' => $orderModel,
    'shop' => $shop,
]);
```

و تمام موارد View به `$trackedOrder` تغییر کنند.

### قانون برای ادامه پروژه

در Viewهای جدید، مخصوصاً Viewهایی که Composer مشترک دارند، از نام‌های عمومی و متداخل مثل `$order`، `$user` و `$cart` بدون بررسی Composerها استفاده نکنیم. نام‌های دقیق مثل `$trackedOrder`، `$checkoutOrder` و ... امن‌ترند.

---

# 10. مشکل مسیر رسید روی Shared Hosting

ساختار هاست پروژه به این شکل است که Laravel خارج از Web Root قرار دارد و `public_html` کنار پوشه Laravel است.

در ابتدا فرض شد رسید در `public_path()` ذخیره و همان مسیر مستقیماً برای Bale قابل استفاده است؛ در Shared Hosting این فرض همیشه درست نبود.

برای حل آن، Listener مسیر رسید را هم در `public_html` و هم در `Laravel public` بررسی می‌کند و در صورت نیاز فایل را به مسیر قابل دسترسی منتقل/کپی می‌کند.

تنظیم مربوط به مسیر در `config/services.php`:

```env
PUBLIC_HTML_PATH=/home/USERNAME/public_html
```

و مقدار پیش‌فرض نیز `base_path('../public_html')` است.

### نکته عملی

بعد از تغییر `.env`، Config Cache ممکن است مقدار قدیمی را نگه دارد. در صورت دسترسی به Artisan:

```bash
php artisan config:clear
```

---

# 11. خطاها و اشتباهات مهمی که در این پیاده‌سازی داشتیم

## 11.1 ارسال URL رسید به جای خود تصویر

**مشکل:** ابتدا منطق ارسال رسید طوری بود که انتظار داشتیم Bale با URL تصویر کار کند، اما روی هاست مسیر فایل برای Bale قابل دسترسی/پیدا کردن نبود.

**اصلاح:** ارسال مستقیم فایل با `multipart` و متد `sendPhoto` در `BaleService`.

**درس:** وقتی مقصد API امکان Upload فایل دارد، برای رسید خصوصی/محلی بهتر است خود فایل ارسال شود و وابستگی به Public URL حذف شود.

---

## 11.2 اشتباه در مسیر `public` و `public_html`

**مشکل:** مسیر فیزیکی فایل Laravel با مسیر Web Root یکی فرض شد.

**اصلاح:** اضافه شدن `PUBLIC_HTML_PATH` و Resolver در Listener.

**درس:** در Deploymentهای Shared Hosting همیشه مسیر فیزیکی پروژه و Web Root را جداگانه در نظر بگیریم.

---

## 11.3 ارسال Notification به Bale بدون معماری Event/Listener مشخص

**مشکل:** اگر ارسال Bale مستقیماً وسط منطق پرداخت پخش شود، نگهداری و Debug سخت می‌شود.

**اصلاح:**

```text
Payment status change
        ↓
Event
        ↓
Listener
        ↓
BaleService
```

برای کارت‌به‌کارت از `CardToCardPaymentSubmitted` و برای پرداخت موفق از `PaymentWasSuccessful` استفاده می‌شود.

**درس:** Notification خارجی را تا حد امکان از منطق اصلی پرداخت جدا کنیم.

---

## 11.4 اشتباه در نوع داده Event پرداخت موفق

**مشکل:** Event `PaymentWasSuccessful` یک جا با Payment و جای دیگر با Order استفاده می‌شد و نوع داده با Constructor همخوان نبود.

**اصلاح:** Event یک `Order` دریافت می‌کند و Payment مرتبط را از Order می‌گیرد.

**درس:** Contract بین Event و Listener باید واضح و ثابت باشد؛ مخصوصاً در Eventهایی که از چند Controller فراخوانی می‌شوند.

---

## 11.5 پاک نکردن Cart بعد از کارت‌به‌کارت

**مشکل:** بعد از ثبت رسید، سفارش هنوز می‌توانست از نگاه منطق Cart به‌عنوان سفارش فعال دیده شود.

**اصلاح:** Order به وضعیت `2` رفت و برای Guest، Cart و `checkout_order_id` نیز پاک شدند.

**درس:** «ثبت رسید» پایان Checkout است، حتی اگر پرداخت هنوز توسط فروشنده تأیید نشده باشد. باید Cart از Order در انتظار تأیید جدا شود.

---

## 11.6 تأیید پرداخت بدون کنترل وضعیت قبلی

**مشکل بالقوه:** اگر چند بار روی دکمه تأیید/رد کلیک شود، ممکن است وضعیت Payment دوباره پردازش شود.

**اصلاح:** قبل از عملیات فقط `waiting_confirmation` پذیرفته می‌شود و داخل Transaction نیز `lockForUpdate()` استفاده شده است.

**درس:** Callbackهای خارجی و دکمه‌های مدیریتی باید Idempotent یا حداقل State-Guard داشته باشند.

---

## 11.7 Collision متغیر `$order` در View

**مشکل:** Controller Order مدل را با `$order` به View می‌داد، اما View Composer هم `$order` را Inject می‌کرد. برای Guest مقدار Composer یک Array بود.

**خطا:**

```text
Attempt to read property "id" on array
```

**اصلاح:** تغییر نام به `trackedOrder`.

**درس:** در Laravel، نام متغیرهای View فقط به Controller وابسته نیست؛ Composerها، `with()`ها و Layoutهای مشترک هم می‌توانند داده تزریق کنند.

---

## 11.8 استفاده از `$product->name` به جای `$product->title`

**مشکل:** View رهگیری از `name` استفاده می‌کرد، در حالی که مدل Product فیلد `title` دارد.

**نتیجه:** تعداد و قیمت درست بود ولی نام محصول نمایش داده نمی‌شد.

**اصلاح:**

```blade
{{ $product->title }}
```

**درس:** قبل از نوشتن View، مدل واقعی و نام ستون‌های واقعی DB را بررسی کنیم؛ حدس زدن نام فیلد یکی از ساده‌ترین راه‌های ایجاد باگ بی‌سروصداست.

---

## 11.9 پاک نشدن Cache/Viewهای کامپایل‌شده روی هاست

**مشکل:** بعد از اصلاح Blade ممکن است هاست همچنان View کامپایل‌شده قدیمی را نمایش دهد.

**اصلاح:** در صورت مشاهده خطای قدیمی بعد از Deployment، محتویات زیر بررسی/پاک شوند:

```text
laravel/storage/framework/views/
```

خود پوشه حذف نشود؛ فقط فایل‌های کامپایل‌شده قدیمی پاک شوند.

---

## 11.10 تغییرات عجولانه در `routes/web.php`

**مشکل:** در طول توسعه یک بار Routeهای موجود به‌صورت ناخواسته تحت تأثیر تغییر قرار گرفتند و مجبور شدیم Routeهای قبلی را برگردانیم.

**درس:** قبل از هر تغییر در فایل بزرگ Route، ابتدا نسخه فعلی همان branch را بخوانیم و فقط بخش لازم را تغییر دهیم. هیچ‌وقت کل فایل را با یک نسخه ناقص جایگزین نکنیم.

---

# 12. قوانین توسعه برای ادامه Payment Flow

1. **اول مدل، بعد View:** نام فیلدها را از Model/DB بررسی کنیم.
2. **Order واقعی را با نام دقیق منتقل کنیم:** مثلاً `trackedOrder` به جای `$order` در Viewهای خاص.
3. **Payment و Order را قاطی نکنیم:** Payment تراکنش است؛ Order سفارش.
4. **Stateها را قبل از تغییر بررسی کنیم.**
5. **Callback و Webhook را Idempotent طراحی کنیم.**
6. **ارسال Bale را از منطق اصلی پرداخت جدا نگه داریم.**
7. **مسیر فیزیکی فایل و URL عمومی فایل را جدا در نظر بگیریم.**
8. **برای Guest، Session و Order را هم‌زمان در نظر بگیریم.**
9. **بعد از تغییر Blade روی Shared Hosting، Cache کامپایل‌شده را فراموش نکنیم.**
10. **قبل از تغییر فایل‌های حساس مثل `routes/web.php` نسخه فعلی branch را بخوانیم.**
11. **تغییرات کوچک و قابل تست انجام دهیم و بعد سراغ مرحله بعد برویم.**

---

# 13. چک‌لیست تست نهایی

### Guest

- [ ] `buyer_login_required = false`
- [ ] خرید بدون Login
- [ ] ثبت اطلاعات گیرنده
- [ ] پرداخت آنلاین
- [ ] کارت‌به‌کارت
- [ ] پاک شدن Cart
- [ ] ساخته شدن Order واقعی

### کارت‌به‌کارت

- [ ] آپلود JPG/PNG/WebP
- [ ] ثبت کد پیگیری
- [ ] نمایش موفقیت ثبت رسید
- [ ] دریافت رسید واقعی در Bale
- [ ] نمایش اطلاعات مشتری
- [ ] تأیید از Bale
- [ ] رد از Bale
- [ ] جلوگیری از تأیید دوباره Payment

### مشتری

- [ ] ساخت لینک رهگیری بعد از تأیید
- [ ] باز شدن SMS Composer
- [ ] شماره صحیح مشتری
- [ ] متن صحیح پیام
- [ ] لینک صحیح سفارش
- [ ] نمایش نام محصول
- [ ] نمایش تعداد
- [ ] نمایش قیمت
- [ ] نمایش مبلغ کل
- [ ] نمایش اطلاعات تحویل
- [ ] رد شدن لینک منقضی‌شده
- [ ] رد شدن Token نامعتبر

### آنلاین

- [ ] ایجاد Payment
- [ ] Redirect به ملت
- [ ] Callback
- [ ] Verify
- [ ] Settle
- [ ] Payment = paid
- [ ] Order = paid
- [ ] ارسال Notification به Bale

---

# 14. وضعیت فعلی

در پایان این مرحله، مسیر کارت‌به‌کارت به این شکل کار می‌کند:

```text
مشتری
 ↓
Checkout
 ↓
انتخاب کارت‌به‌کارت
 ↓
آپلود رسید
 ↓
Payment: waiting_confirmation
 ↓
Bale فروشنده
 ↓
نمایش خود تصویر رسید
 ↓
تأیید / رد
 ↓
اگر تأیید:
Payment: paid
Order: paid
 ↓
ساخت لینک امن و موقت سفارش
 ↓
SMS Composer فروشنده
 ↓
مشتری لینک را باز می‌کند
 ↓
صفحه جزئیات سفارش
```

پرداخت آنلاین نیز مسیر مستقل ملت را دارد و بعد از Verify/Settle موفق، فروشنده از طریق Bale مطلع می‌شود.
