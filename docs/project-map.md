
# نقشه پروژه فروشگاه‌ساز

> این سند برای «یادآوری معماری و مسیر اجرا» است، نه مستند API.
> مرجع وضعیت فعلی: branch feature/theme-changes.
> هر تغییر معماری مهم باید این نقشه را هم به‌روزرسانی کند.

---

## 1. تصویر کلی

این پروژه یک فروشگاه‌ساز چندفروشگاهی است.

اصل مهم معماری:

~~~text
Domain Request
     ↓
  Shop Context
     ↓
     Shop
     ↓
┌────┴──────────────────────────────┐
│                                   │
Store Front                     Seller Panel
│                                   │
├─ Guest                            └─ shop_admin
│    └─ Session                         + shop_context
│
└─ Buyer
     └─ DB Order (buyer_id + shop_id)
~~~

یعنی «فروشگاه فعلی» از روی Domain مشخص می‌شود؛
ولی «وضعیت کاربر» و بعضی وضعیت‌های موقت در Auth/Session/DB نگه‌داری می‌شوند.

---

## 2. مهم‌ترین مفهوم: Shop Context

### مسیر

~~~text
Request
  ↓
shop.context middleware
  ↓
ResolveShopContext
  ↓
ShopHelper::getShop()
  ↓
request()->getHost()
  ↓
Cache: shop:domain:{host}
  ↓
Shop Model
~~~

اگر Shop در Cache نباشد، از DB پیدا و سپس Cache می‌شود.

در همان Request نیز Shop داخل request attribute با نام current_shop قرار می‌گیرد تا دوباره lookup نشود.

Middleware همان Shop را برای Viewها با نام shop share می‌کند.

پس Controllerهای Store لازم نیست برای هر View دستی shop را پاس بدهند.

---

## 3. سه مفهوم را قاطی نکنیم

| مفهوم | محل | معنی |
|---|---|---|
| Shop Context | Domain + Cache + Request | الان در کدام فروشگاه هستیم؟ |
| Auth Context | Laravel Guards | چه کسی وارد شده؟ |
| Temporary State | Session / DB | وضعیت موقت کاربر چیست؟ |

مثال:

~~~text
Domain = shop-a.com
        ↓
Shop = 12

buyer guard
        ↓
Buyer = 57

Session
        ↓
cart.shop.12
checkout_order_id.shop.12
~~~

این‌ها یکی نیستند.

---

## 4. ShopHelper

فایل:

~~~text
app/Helpers/ShopHelper.php
~~~

دو خانواده مسئولیت فعلی:

### A) پیدا کردن فروشگاه

~~~text
getShop()
getShopId()
forgetShopCache()
~~~

### B) وضعیت مهمان فروشگاه

~~~text
getGuestCart()
putGuestCart()
forgetGuestCart()

getCheckoutOrderId()
putCheckoutOrderId()
forgetCheckoutOrderId()
~~~

کلیدهای Session عمداً Shop-scoped هستند:

~~~text
cart.shop.12
cart.shop.18

checkout_order_id.shop.12
checkout_order_id.shop.18
~~~

بنابراین وضعیت مهمان چند فروشگاه با هم قاطی نمی‌شود.

> نکته: Cart قدیمی با کلید cart یک‌بار migrate می‌شود. چون Cart قدیمی Shop مشخصی نداشت، این migration فقط سازگاری بعد از Deploy است.

---

## 5. Routeها

فایل اصلی:

~~~text
routes/web.php
~~~

### Store Front

Routeهای عمومی فروشگاه زیر shop.context هستند، از جمله:

~~~text
/
product/{product}
blog/{article}
/buy
pay/*
checkout
payment/*
~~~

مسیر ذهنی:

~~~text
Route
 ↓
shop.context
 ↓
Controller
 ↓
Model / Service
 ↓
View
~~~

### Seller Panel

شروع URL:

~~~text
/shop
~~~

پنل با این زنجیره محافظت می‌شود:

~~~text
auth:shop_admin
+
verified
+
role:shop_owner
+
check.shop
~~~

---

## 6. دو Middleware مشابه ولی متفاوت

### ResolveShopContext

فایل:

~~~text
app/Http/Middleware/ResolveShopContext.php
~~~

سؤالش:

> از روی Domain بفهم الان کدام فروشگاه را داریم و آن را برای Request/View آماده کن.

این Middleware برای Store است.

### CheckShopContext

فایل:

~~~text
app/Http/Middleware/CheckShopContext.php
~~~

سؤالش:

> Shop Owner واردشده هنوز مجاز است در همین فروشگاه/Domain کار کند؟

از این‌ها استفاده می‌کند:

~~~text
shop_admin guard
+
session('shop_context')
~~~

پس:

~~~text
ResolveShopContext ≠ CheckShopContext
~~~

اولی Context فروشگاه را resolve می‌کند؛ دومی Context دسترسی فروشنده را بررسی می‌کند.

---

## 7. Buyer Context

فایل:

~~~text
app/Http/Middleware/CheckBuyerShopContext.php
~~~

مسیر:

~~~text
buyer guard
 ↓
buyer_shop_context
 ↓
current domain
 ↓
Shop
 ↓
buyer ↔ shop relation
 ↓
اجازه دسترسی
~~~

Login بودن به‌تنهایی کافی نیست؛ Buyer باید به همان Shop متصل باشد.

این Session با Guest Cart یکی نیست.

---

## 8. چهار حالت کاربر

### Guest

~~~text
auth('buyer') = false
        ↓
Session
        ↓
cart.shop.{shop_id}
~~~

### Buyer

~~~text
auth('buyer')
        ↓
Buyer
        ↓
Order
        ↓
buyer_id + shop_id
~~~

### Shop Owner

~~~text
auth('shop_admin')
        ↓
check.shop
        ↓
shop_context
        ↓
Customer Controllers
~~~

### Platform Admin

~~~text
auth('web')
+
role:admin
        ↓
Admin Controllers
~~~

---

## 9. Cart و Order

این بخش یکی از مهم‌ترین جاهای پروژه است.

### Guest

ابتدا:

~~~text
Product
 ↓
cart.shop.12
~~~

در Checkout:

~~~text
cart.shop.12
 ↓
Order
buyer_id = null
shop_id = 12
status = pending
 ↓
checkout_order_id.shop.12
~~~

Session فقط کمک می‌کند Cart/Checkout فعلی را پیدا کنیم؛ خود Order در DB است.

### Buyer

~~~text
Product
 ↓
Buyer pending Order
 ↓
buyer_id = X
shop_id = 12
~~~

در Buyer، Cart عملاً همان pending Order است.

---

## 10. Checkout

Controller:

~~~text
app/Http/Controllers/front/PaymentController.php
~~~

روند:

~~~text
Cart / pending Order
        ↓
checkout()
        ↓
اطلاعات گیرنده
        ↓
Order فعال
        ↓
Reservation
        ↓
payment.index
        ↓
انتخاب روش پرداخت
~~~

---

## 11. پرداخت آنلاین

~~~text
PaymentController@init
        ↓
Payment = pending
        ↓
bpPayRequest
        ↓
Payment = redirected
        ↓
بانک
        ↓
PaymentController@callback
        ↓
Verify
        ↓
Settle
        ↓
Payment = paid
        ↓
Order = paid
        ↓
PaymentWasSuccessful
        ↓
SendPaymentSuccessToBale
~~~

موفقیت واقعی پرداخت بعد از Verify و Settle مشخص می‌شود.

---

## 12. کارت‌به‌کارت

~~~text
PaymentController@cardToCard
        ↓
Order فعال
        ↓
محاسبه مبلغ
        ↓
Payment = waiting_confirmation
        ↓
PaymentReceipt
        ↓
Event
        ↓
CardToCardPaymentSubmitted
        ↓
SendCardToCardPaymentToBale
        ↓
Bale فروشنده
        ↓
تأیید / رد
~~~

تأیید:

~~~text
Payment = paid
Order = paid
~~~

رد:

~~~text
Payment = rejected
~~~

جزئیات این Flow در docs/payment-flow.md است.

---

## 13. OrderReservationService

فایل:

~~~text
app/Services/OrderReservationService.php
~~~

ذهنی:

~~~text
Order
 ↓
Reservation
 ↓
مهلت پرداخت
 ↓
یا Commit
یا Expire
~~~

Reservation با Payment یکی نیست.

اگر Reservation منقضی شود، منطق PaymentController می‌تواند Order را Cancel کند و در حالت Buyer محصولات را دوباره به pending Order برگرداند.

---

## 14. Event / Listener / Service

برای Notification خارجی این الگو را داریم:

~~~text
Business Action
      ↓
Event
      ↓
Listener
      ↓
External Service
~~~

مثال:

~~~text
Payment successful
 ↓
PaymentWasSuccessful
 ↓
SendPaymentSuccessToBale
 ↓
BaleService
~~~

یا:

~~~text
Card-to-card submitted
 ↓
CardToCardPaymentSubmitted
 ↓
SendCardToCardPaymentToBale
 ↓
BaleService
~~~

پس Controller نباید محل انباشته شدن کدهای Bale باشد.

---

## 15. فایل‌های کلیدی برای شناخت پروژه

اگر بعداً همه‌چیز یادت رفت، از این ترتیب بخوان:

### سطح 1 — ورود درخواست

~~~text
routes/web.php
app/Http/Kernel.php
~~~

### سطح 2 — Shop

~~~text
app/Helpers/ShopHelper.php
app/Http/Middleware/ResolveShopContext.php
app/Models/Shop.php
~~~

### سطح 3 — دسترسی

~~~text
app/Http/Middleware/CheckShopContext.php
app/Http/Middleware/CheckBuyerShopContext.php
~~~

### سطح 4 — خرید

~~~text
app/Http/Controllers/front/OrderController.php
app/Http/Controllers/front/PaymentController.php
app/Services/OrderReservationService.php
app/Models/Order.php
~~~

### سطح 5 — پرداخت

~~~text
app/Models/Payment.php
app/Events/
app/Listeners/
app/Services/BaleService.php
~~~

### سطح 6 — نمایش

~~~text
resources/views/Frontend/Store/
resources/views/Frontend/Shop/
app/View/Composers/UserDataComposer.php
~~~

---

## 16. وقتی Bug دیدیم از کجا شروع کنیم؟

| مشکل | اول کجا را ببینیم؟ |
|---|---|
| Shop اشتباه | ShopHelper → ResolveShopContext → Route |
| shop در View نیست | Route middleware → ResolveShopContext |
| Cart قاطی شده | ShopHelper → Session key |
| Buyer سفارش اشتباه می‌بیند | OrderController → shop_id |
| Seller به Shop اشتباه دسترسی دارد | CheckShopContext |
| Buyer به Shop اشتباه دسترسی دارد | CheckBuyerShopContext |
| Checkout خراب است | PaymentController@checkout |
| پرداخت آنلاین خراب است | PaymentController@init / callback |
| کارت‌به‌کارت خراب است | PaymentController@cardToCard → Event/Listener |
| Bale خراب است | Listener → BaleService |
| Reservation خراب است | OrderReservationService |
| Header/Cart مقدار غلط دارد | UserDataComposer |
| View داده عجیب دارد | Controller + View Composer + Layout |

---

## 17. تغییرات اخیر که باید در ذهنمان بماند

### Shop از Session/Queryهای پراکنده به Cache + Request منتقل شد

~~~text
Domain
 ↓
ShopHelper
 ↓
Cache
 ↓
Request Attribute
 ↓
View
~~~

### Shop برای Viewها Global شد

~~~text
ResolveShopContext
 ↓
View::share('shop', $shop)
~~~

### Guest Cart Shop-scoped شد

قبلاً:

~~~text
cart
~~~

الان:

~~~text
cart.shop.{shop_id}
~~~

### Checkout Order ID هم Shop-scoped شد

قبلاً:

~~~text
checkout_order_id
~~~

الان:

~~~text
checkout_order_id.shop.{shop_id}
~~~

### این فایل‌ها با منطق جدید هماهنگ شدند

~~~text
OrderController
PaymentController
UserDataComposer
~~~

---

## 18. نقشه معماری فعلی

~~~text
                         REQUEST
                            │
                            ▼
                     routes/web.php
                            │
             ┌──────────────┴──────────────┐
             │                             │
       Store Front                    Seller Panel
             │                             │
       shop.context                   shop_admin
             │                             │
      ResolveShopContext             CheckShopContext
             │                             │
             ▼                             ▼
        ShopHelper                  shop_context
             │
      ┌──────┴──────┐
      │             │
    Cache         Request
      │             │
      └──────┬──────┘
             ▼
            Shop
             │
     ┌───────┴────────┐
     │                │
   Guest             Buyer
     │                │
  Session           DB Order
     │                │
 cart.shop.X       buyer_id + shop_id
     │                │
     └───────┬────────┘
             ▼
          Checkout
             │
             ▼
     OrderReservationService
             │
             ▼
          Payment
       ┌─────┴─────┐
       │           │
     Online    Card-to-card
       │           │
     Bank        Receipt
       │           │
    Callback      Bale
       │           │
     Verify       Confirm
       │           │
       └─────┬─────┘
             ▼
       Payment / Order
             │
             ▼
          Events
             │
             ▼
          Bale / Customer
~~~

---

## 19. فعلاً به چه چیزهایی دست نزنیم؟

تا وقتی معماری را کامل مرور نکرده‌ایم، این بخش‌ها را بی‌دلیل Refactor نکنیم:

- CheckShopContext
- CheckBuyerShopContext
- Auth Guards
- Route structure
- Session keys
- Order statusها
- Payment statusها
- Reservation logic

این‌ها به هم وابستگی دارند.

---

## 20. نقشه راه مستندسازی

این سند فقط «نقشه مادر» است. جزئیات را باید به Flowهای جدا منتقل کنیم.

ترتیب پیشنهادی:

1. Product Flow
2. Article / Blog Flow
3. Buyer Authentication Flow
4. Seller Authentication Flow
5. Order Flow کامل
6. Payment Flow — موجود در docs/payment-flow.md
7. View / Layout Flow
8. Database Relationships
9. Deployment / cPanel Flow

---

## قانون طلایی

هر وقت نفهمیدیم یک کد چرا وجود دارد، این زنجیره را پیدا کنیم:

~~~text
Route
 ↓
Middleware
 ↓
Controller
 ↓
Service / Helper
 ↓
Model
 ↓
DB / Session / Cache
 ↓
View / Event
~~~

و سؤال اصلی:

> این داده از کجا وارد شد، چه کسی تغییرش داد، و آخرش کجا مصرف شد؟

این سؤال معمولاً مسیر واقعی کد را مشخص می‌کند.
