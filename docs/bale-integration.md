# مستند اتصال سیستم فروشگاه به بله (Bale)

## هدف

سیستم فروشگاه برای هر فروشگاه امکان اتصال حساب فروشنده در پیام‌رسان بله به یک Bot را دارد تا رویدادهای مربوط به پرداخت از طریق بله به فروشنده اطلاع داده شود و فروشنده بتواند پرداخت کارت‌به‌کارت را از داخل بله تأیید یا رد کند.

## 1. چیزهایی که در بله ساخته شد

برای این قابلیت یک **Bot در بله** ساخته شد.

اطلاعات مورد نیاز:

- **Bot Token**: توکن محرمانه برای فراخوانی API بله.
- **Bot Username**: نام کاربری Bot برای ساخت لینک اتصال.
- **Webhook URL**: آدرس عمومی سایت که بله Updateهای Bot را به آن ارسال می‌کند.

Token نباید داخل Git یا کد قرار بگیرد و در فایل .env نگهداری می‌شود:

~~~env
BALE_BOT_TOKEN=...
BALE_BOT_USERNAME=...
~~~

این مقادیر در config/services.php خوانده می‌شوند.

## 2. Webhook چیست؟

به بله گفته شده است هر زمان اتفاقی برای Bot رخ داد، اطلاعات آن را به این آدرس ارسال کند:

~~~text
POST /api/bale/webhook
~~~

Route در routes/api.php تعریف شده و درخواست را به BaleWebhookController می‌فرستد.

مسیر کلی:

~~~text
Bale
  │
  │ HTTP POST
  ▼
/api/bale/webhook
  │
  ▼
BaleWebhookController
~~~

Webhook باید به URL عمومی و قابل دسترس از اینترنت اشاره کند و سایت باید HTTPS داشته باشد.

## 3. اتصال فروشگاه به Bot

فروشنده از پنل فروشگاه روی «اتصال به بله» کلیک می‌کند.

Controller مربوط:

~~~text
App\Http\Controllers\customer\BaleConnectionController
~~~

ابتدا یک Token موقت در ShopBaleConnectionToken ساخته می‌شود. این Token به shop_id و user_id وابسته است و حدود ۱۰ دقیقه اعتبار دارد.

سپس Deep Link ساخته می‌شود:

~~~text
https://ble.ir/{BOT_USERNAME}?start={TOKEN}
~~~

کاربر با باز کردن این لینک وارد Bot می‌شود.

## 4. تشخیص فروشگاه هنگام اتصال

وقتی کاربر لینک را باز می‌کند، Bot یک پیام /start دریافت می‌کند؛ Token داخل همین پیام قرار دارد.

Webhook:

1. متن /start را می‌خواند.
2. Token را جدا می‌کند.
3. Token را در shop_bale_connection_tokens پیدا می‌کند.
4. اعتبار Token و استفاده‌نشده بودن آن را بررسی می‌کند.
5. bale_user_id و bale_chat_id را از پیام استخراج می‌کند.
6. رکورد shop_bale_connections را ایجاد یا به‌روزرسانی می‌کند.
7. Token را با used_at مصرف‌شده علامت می‌زند.

در نتیجه سیستم می‌فهمد کدام Chat بله به کدام فروشگاه متصل است.

## 5. جداول اتصال

### shop_bale_connection_tokens

برای فرآیند اولیه اتصال است.

اطلاعات اصلی:

- shop_id
- user_id
- token
- expires_at
- used_at

این Token دائمی نیست.

### shop_bale_connections

اتصال واقعی فروشگاه به بله در این جدول نگهداری می‌شود.

اطلاعات اصلی:

- shop_id
- user_id
- bale_user_id
- bale_chat_id
- active
- connected_at

قطع اتصال با active=false انجام می‌شود.

## 6. ارسال پیام از Laravel به بله

تمام ارتباط خروجی با بله در این Service متمرکز شده است:

~~~text
app/Services/BaleService.php
~~~

API URL به شکل زیر ساخته می‌شود:

~~~text
https://tapi.bale.ai/bot{TOKEN}/{METHOD}
~~~

متدهای مورد استفاده:

- sendMessage
- sendPhoto
- answerCallbackQuery
- editMessageReplyMarkup

## 7. پرداخت آنلاین

پس از موفقیت پرداخت آنلاین، Event زیر اجرا می‌شود:

~~~text
PaymentWasSuccessful
~~~

Listener:

~~~text
SendPaymentSuccessToBale
~~~

مسیر:

~~~text
پرداخت موفق
   ↓
PaymentWasSuccessful
   ↓
SendPaymentSuccessToBale
   ↓
ShopBaleConnection
   ↓
BaleService
   ↓
sendMessage
   ↓
Bale
~~~

پیام شامل شماره سفارش، مبلغ، هزینه ارسال، نام مشتری، موبایل و شماره مرجع پرداخت است.

## 8. پرداخت کارت‌به‌کارت

وقتی Payment به وضعیت waiting_confirmation می‌رسد، Event زیر ایجاد می‌شود:

~~~text
CardToCardPaymentSubmitted
~~~

Listener:

~~~text
SendCardToCardPaymentToBale
~~~

مسیر:

~~~text
پرداخت کارت‌به‌کارت
   ↓
waiting_confirmation
   ↓
CardToCardPaymentSubmitted
   ↓
SendCardToCardPaymentToBale
   ↓
BaleService
   ↓
sendPhoto / sendMessage
   ↓
Bale
~~~

اگر رسید وجود داشته باشد، عکس رسید همراه متن و دکمه‌های تأیید/رد ارسال می‌شود. اگر فایل پیدا نشود، پیام متنی ارسال می‌شود و خطای فایل رسید نیز در Log ثبت می‌شود.

## 9. دکمه تأیید و رد پرداخت

Callback Data دکمه‌ها:

~~~text
payment:approve:123
payment:reject:123
~~~

با کلیک فروشنده، بله Callback Query را به همان Webhook ارسال می‌کند.

سیستم:

1. Callback را دریافت می‌کند.
2. Payment را پیدا می‌کند.
3. بررسی می‌کند Chat متعلق به اتصال فعال همان فروشگاه باشد.
4. بررسی می‌کند Payment هنوز waiting_confirmation باشد.
5. عملیات را داخل Transaction انجام می‌دهد.
6. با lockForUpdate از تأیید هم‌زمان یک پرداخت جلوگیری می‌کند.
7. دکمه‌های پیام را به‌روزرسانی می‌کند.
8. نتیجه را به فروشنده اعلام می‌کند.

## 10. امنیت

برای اجرای عملیات پرداخت، داشتن Payment ID به تنهایی کافی نیست.

سیستم shop_id و bale_chat_id و active=true را با هم بررسی می‌کند تا کاربر فروشگاه دیگر نتواند Payment را تغییر دهد.

## 11. فایل‌های اصلی

~~~text
config/
└── services.php
    └── bale

routes/
└── api.php
    └── /bale/webhook

app/
├── Services/
│   └── BaleService.php
│
├── Http/
│   └── Controllers/
│       └── customer/
│           ├── BaleConnectionController.php
│           └── BaleWebhookController.php
│
├── Listeners/
│   ├── SendPaymentSuccessToBale.php
│   └── SendCardToCardPaymentToBale.php
│
└── Models/
    ├── ShopBaleConnection.php
    └── ShopBaleConnectionToken.php
~~~

## 12. تنظیمات نصب روی سرور جدید

### .env

~~~env
BALE_BOT_TOKEN=توکن-بات
BALE_BOT_USERNAME=نام-کاربری-بات
PUBLIC_HTML_PATH=/path/to/public_html
~~~

PUBLIC_HTML_PATH مربوط به ساختار هاست اشتراکی است که public_html کنار پوشه Laravel قرار دارد و برای پیدا کردن فایل رسید استفاده می‌شود.

### Database

جداول زیر باید وجود داشته باشند:

~~~text
shop_bale_connections
shop_bale_connection_tokens
~~~

### Webhook

Bot بله باید به URL زیر متصل باشد:

~~~text
https://DOMAIN.com/api/bale/webhook
~~~

مثال:

~~~text
https://example.com/api/bale/webhook
~~~

## 13. عیب‌یابی

اگر اتصال انجام نمی‌شود:

1. BALE_BOT_TOKEN را بررسی کن.
2. BALE_BOT_USERNAME را بررسی کن.
3. HTTPS سایت را بررسی کن.
4. URL Webhook را بررسی کن.
5. مطمئن شو /api/bale/webhook از اینترنت قابل دسترسی است.
6. فعال بودن Bot در بله را بررسی کن.
7. اعتبار Token اتصال را بررسی کن.
8. Logهای Laravel را بررسی کن.

اگر فروشگاه متصل است ولی پیام دریافت نمی‌کند:

1. shop_bale_connections.active را بررسی کن.
2. bale_chat_id را بررسی کن.
3. BALE_BOT_TOKEN را بررسی کن.
4. Logهای BaleService را بررسی کن.
5. پاسخ API بله را بررسی کن.

## 14. دو مسیر مستقل ارتباط با بله

### دریافت اطلاعات از بله

~~~text
Bale
  ↓
Webhook
  ↓
Laravel
~~~

برای مثال، کلیک روی دکمه تأیید پرداخت.

### ارسال اطلاعات به بله

~~~text
Laravel
  ↓
Bale API
  ↓
Bale
  ↓
فروشنده
~~~

Webhook برای ارسال پیام استفاده نمی‌شود؛ ارسال از طریق API بله انجام می‌شود.

## 15. وضعیت فعلی و نکته توسعه

در نسخه فعلی، ارسال پیام Bale مستقیماً در Listener انجام می‌شود و در صورت خطا بعد از Retry کوتاه HTTP فقط Log ثبت می‌شود.

بنابراین سیستم فعلی هنوز تحویل تضمین‌شده اعلان ندارد.

برای نسخه نهایی بهتر است ارسال پیام به شکل Outbox + Queue پیاده شود:

~~~text
Payment
   ↓
Notification Outbox
   ↓
Queue
   ↓
Bale API
   ↓
Success → sent
   ↓
Failure → retry
   ↓
Repeated Failure → failed / قابل پیگیری
~~~

هدف این است که قطع موقت Bale، timeout شبکه یا خطای موقت سرور باعث از دست رفتن دائمی پیام فروشنده نشود.

نکته: پاسخ موفق API بله به معنی پذیرفته‌شدن درخواست توسط API است؛ نمی‌توان مشاهده یا خوانده‌شدن پیام توسط فروشنده را تضمین کرد.
