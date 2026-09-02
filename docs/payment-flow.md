# مستندات سیستم پرداخت

> این مستند مربوط به branch `payment-flow` است و وضعیت فعلی پیاده‌سازی را توضیح می‌دهد.

## 1. معماری کلی

فرآیند خرید به این شکل است:

```text
سبد خرید
   ↓
اطلاعات گیرنده
   ↓
صفحه انتخاب روش پرداخت
   ├── پرداخت آنلاین
   │      ↓
   │   درگاه بانک ملت
   │      ↓
   │   callback → verify → settle
   │      ↓
   │   Payment = paid
   │   Order = paid
   │
   └── کارت‌به‌کارت
          ↓
       نمایش حساب فروشگاه
          ↓
       آپلود رسید
          ↓
       Payment = waiting_confirmation
          ↓
       Order = 2 (در انتظار تأیید)
```

---

# 2. فایل‌های اصلی

| فایل | مسئولیت |
|---|---|
| `app/Http/Controllers/front/PaymentController.php` | کنترل اصلی Checkout و پرداخت |
| `app/Models/Payment.php` | مدل تراکنش پرداخت |
| `app/Models/Order.php` | مدل سفارش |
| `app/Models/Gateway.php` | تنظیمات درگاه آنلاین |
| `app/Models/PaymentReceipt.php` | رسید پرداخت کارت‌به‌کارت |
| `resources/views/Frontend/Shop/Pay/Cart.blade.php` | نمایش سبد خرید |
| `resources/views/Frontend/Shop/Pay/payment.blade.php` | انتخاب روش پرداخت |
| `resources/views/Frontend/Shop/Pay/card-to-card.blade.php` | فرم کارت‌به‌کارت |
| `resources/views/Frontend/Shop/Pay/card-to-card-success.blade.php` | نتیجه ثبت رسید |
| `routes/web.php` | Routeهای پرداخت |
| `database/migrations/2026_09_02_190000_allow_guest_orders.php` | امکان ایجاد سفارش بدون buyer |

---

# 3. PaymentController

مسیر:

```text
app/Http/Controllers/front/PaymentController.php
```

این کنترلر قلب سیستم پرداخت است. مسئولیت آن فقط پرداخت بانکی نیست؛ ساخت سفارش Checkout، محاسبه مبلغ، کارت‌به‌کارت و callback بانک را نیز مدیریت می‌کند.

## 3.1 `checkout()`

```php
public function checkout(Request $request)
```

وقتی کاربر اطلاعات گیرنده را ثبت می‌کند اجرا می‌شود.

### کارهایی که انجام می‌دهد

1. فروشگاه فعلی را با `Shop::current()` پیدا می‌کند.
2. اطلاعات گیرنده را Validate می‌کند.
3. اگر Buyer لاگین باشد، سفارش باز (`status = 0`) همان فروشگاه را پیدا می‌کند.
4. اگر مهمان باشد:
   - اگر `buyer_login_required` فعال باشد، به Login هدایت می‌شود.
   - Cart از Session خوانده می‌شود.
   - اگر سفارش Checkout قبلی وجود داشته باشد، همان سفارش استفاده می‌شود.
   - در غیر این صورت یک Order ساخته می‌شود.
   - محصولات Cart به Order متصل می‌شوند.
   - `checkout_order_id` در Session ذخیره می‌شود.
5. اطلاعات گیرنده روی Order ذخیره می‌شود.
6. کاربر به `payment.index` منتقل می‌شود.

### نکته مهم

برای Guest، `buyer_id` برابر `null` است. بنابراین Migration مربوط به Guest Order ضروری است.

---

## 3.2 `index()`

```php
public function index()
```

صفحه انتخاب روش پرداخت را نمایش می‌دهد.

اطلاعاتی که به View می‌دهد:

- `$order`
- `$totalAmount`
- `$gateway`
- `$bankAccount`

در اینجا Gateway فعال فروشگاه و حساب بانکی فروشگاه بررسی می‌شوند تا روش‌های پرداخت قابل استفاده مشخص شوند.

---

## 3.3 `cardToCardForm()`

```php
public function cardToCardForm()
```

فرم پرداخت کارت‌به‌کارت را نمایش می‌دهد.

ابتدا Order فعلی را پیدا می‌کند و سپس وجود `bankAccount` فروشگاه را بررسی می‌کند.

اگر حساب بانکی تنظیم نشده باشد، کاربر به صفحه انتخاب روش پرداخت برمی‌گردد.

---

# 4. پرداخت کارت‌به‌کارت

## 4.1 `cardToCard()`

```php
public function cardToCard(Request $request)
```

این متد زمانی اجرا می‌شود که کاربر رسید انتقال وجه را ارسال می‌کند.

### Validation

```php
'receipt' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
'tracking_code' => 'nullable|string|max:100',
'description' => 'nullable|string|max:1000',
```

حداکثر حجم رسید 5MB است.

### روند ثبت رسید

1. Order فعلی پیدا می‌شود.
2. مبلغ سفارش محاسبه می‌شود.
3. Payment موجود برای Order پیدا یا ایجاد می‌شود.
4. تصویر رسید در این مسیر ذخیره می‌شود:

```text
public/uploads/payment-receipts
```

5. رکورد `PaymentReceipt` ساخته یا به‌روزرسانی می‌شود.
6. Payment به وضعیت زیر می‌رود:

```text
waiting_confirmation
```

یعنی پول هنوز توسط فروشگاه تأیید نشده است.

7. Order به وضعیت `2` می‌رود تا دیگر به‌عنوان سبد خرید فعال نمایش داده نشود.
8. اگر کاربر مهمان باشد، موارد زیر از Session حذف می‌شوند:

```php
session()->forget('cart');
session()->forget('checkout_order_id');
```

9. صفحه موفقیت ثبت رسید نمایش داده می‌شود.

### چرا Order حذف نمی‌شود؟

چون این Order دیگر سبد خرید نیست؛ یک سفارش واقعی است که باید توسط فروشگاه بررسی و تأیید شود.

---

# 5. پرداخت آنلاین

## 5.1 `init()`

```php
public function init(Request $request)
```

این متد تراکنش آنلاین را ایجاد و کاربر را به درگاه بانک ملت می‌فرستد.

### مراحل

1. Order فعال پیدا می‌شود.
2. Gateway فعال فروشگاه پیدا می‌شود.
3. مبلغ سفارش محاسبه می‌شود.
4. Payment ساخته می‌شود:

```text
method = online
status = pending
```

5. متد `bpPayRequest` بانک ملت فراخوانی می‌شود.
6. در صورت موفقیت، `ref_id` ذخیره و Payment به:

```text
redirected
```

تغییر می‌کند.

7. کاربر به Gateway URL بانک هدایت می‌شود.

---

# 6. Callback بانک ملت

## 6.1 `callback()`

```php
public function callback(Request $request)
```

بانک پس از پایان تراکنش این Route را فراخوانی می‌کند.

اطلاعات مهم:

```text
ResCode
SaleOrderId
RefId
SaleReferenceId
```

### روند تأیید

```text
Callback
   ↓
پیدا کردن Payment
   ↓
بررسی ResCode
   ↓
bpVerifyRequest
   ↓
bpSettleRequest
   ↓
Payment = paid
   ↓
Order = paid
   ↓
پاک کردن Cart
   ↓
PaymentWasSuccessful event
```

اگر Verify یا Settle شکست بخورد، Payment به `failed` می‌رود.

---

# 7. وضعیت‌های Payment

| وضعیت | مفهوم |
|---|---|
| `pending` | Payment ایجاد شده ولی هنوز پرداخت/ارسال به بانک کامل نشده |
| `redirected` | کاربر به درگاه آنلاین فرستاده شده |
| `waiting_confirmation` | رسید کارت‌به‌کارت ثبت شده و منتظر تأیید فروشگاه است |
| `paid` | پرداخت تأیید نهایی شده |
| `rejected` | پرداخت/رسید رد شده |
| `failed` | عملیات پرداخت یا ارتباط با بانک شکست خورده |

---

# 8. وضعیت Order

در پیاده‌سازی فعلی بخش‌هایی از سیستم از مقادیر عددی برای وضعیت Order استفاده می‌کنند:

```text
0 = سبد خرید / سفارش پرداخت‌نشده
1 = پرداخت‌شده
2 = در انتظار تأیید کارت‌به‌کارت
```

> `Order` همچنین Constantهایی با نام‌های `STATUS_PENDING`, `STATUS_PAID`, ... دارد. این دو روش نام‌گذاری در آینده بهتر است یکپارچه شوند تا احتمال خطا کم شود.

---

# 9. Guest Checkout

تنظیم زیر در Shop وجود دارد:

```php
buyer_login_required
```

اگر `true` باشد، مهمان اجازه ادامه Checkout ندارد و باید Login کند.

اگر `false` باشد، کاربر بدون حساب می‌تواند خرید کند.

برای Guest Order:

```text
buyer_id = null
```

و شناسه Order در Session نگهداری می‌شود:

```text
checkout_order_id
```

---

# 10. Sessionهای مهم

### Cart

```text
cart
```

ساختار کلی:

```php
[
    product_id => quantity,
]
```

### Checkout Order

```text
checkout_order_id
```

برای Guest مشخص می‌کند Order فعلی Checkout کدام است.

### پاک‌سازی

بعد از پرداخت آنلاین موفق یا ثبت موفق رسید کارت‌به‌کارت، Sessionهای مربوط به Cart باید پاک شوند.

---

# 11. Routeهای اصلی

Routeهای پرداخت در:

```text
routes/web.php
```

قرار دارند و به‌صورت کلی شامل این مراحل هستند:

```text
order.index
   ↓
payment.checkout
   ↓
payment.index
   ├── card-to-card form
   │      ↓
   │   card-to-card submit
   │
   └── online init
          ↓
       Mellat
          ↓
       payments.callback
```

---

# 12. فایل‌های View

## `Cart.blade.php`

نمایش سبد خرید و فرم اطلاعات گیرنده.

## `payment.blade.php`

صفحه انتخاب روش پرداخت و نمایش گزینه‌های آنلاین / کارت‌به‌کارت.

## `card-to-card.blade.php`

فرم آپلود رسید، شماره پیگیری و توضیحات.

## `card-to-card-success.blade.php`

پیغام موفقیت ثبت رسید و شماره سفارش.

شماره سفارش از Payment خوانده می‌شود:

```blade
{{ $payment->order_id }}
```

---

# 13. Migration مربوط به Guest Order

فایل:

```text
database/migrations/2026_09_02_190000_allow_guest_orders.php
```

وظیفه این Migration این است که `orders.buyer_id` را nullable کند.

بدون اجرای این Migration، ساخت Order برای Guest با خطای زیر مواجه می‌شود:

```text
Column 'buyer_id' cannot be null
```

---

# 14. محل ذخیره رسید

رسید کارت‌به‌کارت در مسیر زیر ذخیره می‌شود:

```text
public/uploads/payment-receipts/
```

و مسیر نسبی آن در `PaymentReceipt` ذخیره می‌شود:

```text
uploads/payment-receipts/receipt_xxx.jpg
```

---

# 15. تست پیشنهادی

قبل از Merge کردن `payment-flow` با `master` این سناریوها تست شوند:

### تست 1 — Guest + کارت‌به‌کارت

- `buyer_login_required = false`
- محصول به Cart اضافه شود.
- اطلاعات گیرنده ثبت شود.
- کارت‌به‌کارت انتخاب شود.
- رسید آپلود شود.
- Payment باید `waiting_confirmation` باشد.
- Order باید `status = 2` باشد.
- Cart باید خالی شود.
- سفارش نباید حذف شود.

### تست 2 — کاربر لاگین + کارت‌به‌کارت

- محصول در Cart باشد.
- رسید ثبت شود.
- Order نباید دوباره در Cart نمایش داده شود.
- Order باید باقی بماند تا مدیر آن را بررسی کند.

### تست 3 — Guest + پرداخت آنلاین

- Guest Checkout فعال باشد.
- کاربر به بانک منتقل شود.
- Callback دریافت شود.
- Verify و Settle موفق شوند.
- Payment = `paid`
- Order = `1`
- Cart پاک شود.

### تست 4 — پرداخت آنلاین ناموفق

- Callback با `ResCode != 0` تست شود.
- Payment باید `failed` شود.
- Order نباید به وضعیت پرداخت‌شده برود.

---

# 16. نکات مهم برای توسعه بعدی

1. وضعیت‌های Order باید از حالت عددی به Constant/Enum یکپارچه تبدیل شوند.
2. وضعیت `2` بهتر است در Model نام‌گذاری مشخص داشته باشد.
3. برای کارت‌به‌کارت باید پنل مدیریت فروشگاه برای مشاهده و تأیید/رد PaymentReceipt اضافه شود.
4. بعد از تأیید کارت‌به‌کارت باید Payment به `paid` و Order به وضعیت پرداخت‌شده منتقل شود.
5. هنگام رد رسید، باید امکان ثبت دلیل رد و ارسال آن به مشتری وجود داشته باشد.
6. Event مربوط به موفقیت پرداخت باید هم برای پرداخت آنلاین و هم برای تأیید کارت‌به‌کارت استفاده شود.
7. ارسال اعلان به مدیر فروشگاه از طریق Bale باید به مرحله ثبت موفق رسید و پرداخت موفق آنلاین متصل شود.

---

# 17. قانون کار با Branch

تغییرات مربوط به این بخش فعلاً روی:

```text
payment-flow
```

انجام می‌شوند.

تا زمانی که تست‌های پرداخت کامل نشده‌اند، روی `master` Merge نکنید.

بعد از تأیید نهایی:

```bash
git switch master
git pull origin master
git merge payment-flow
git push origin master
```
