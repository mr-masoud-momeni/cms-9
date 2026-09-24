# خریدار — ورود به صفحه اصلی فروشگاه

## فلو: ورود به دامنه فروشگاه

کاربر دامنه فروشگاه را باز می‌کند.

```
دامنه فروشگاه
    ↓
Route /
    ↓
shop.context
    ↓
IndexController@shop
    ↓
Product + Article
    ↓
Frontend.Store.index
```

### 1. Route

**مسیر:** `/`

**Route name:** `index.show`

**تعریف:** `routes/web.php`

### 2. Middleware

**`shop.context`**

قبل از اجرای کنترلر، فروشگاه فعلی از روی دامنه درخواست پیدا می‌شود و در درخواست و Viewها قرار می‌گیرد.

**کلاس:** `app/Http/Middleware/ResolveShopContext.php`

### 3. Controller

**`IndexController@shop`**

**فایل:** `app/Http/Controllers/front/IndexController.php`

در این بخش:

- فروشگاه فعلی با `ShopHelper::getShop()` گرفته می‌شود.
- محصولات همان فروشگاه، جدیدترین‌ها، با صفحه‌بندی ۹تایی خوانده می‌شوند.
- ۱۲ مقاله آخر همان فروشگاه خوانده می‌شود.
- تعداد کل محصولات و مقالات محاسبه می‌شود.
- همه این اطلاعات به View صفحه اصلی ارسال می‌شوند.

### 4. داده‌های ورودی View

View این داده‌ها را دریافت می‌کند:

- `shop` → فروشگاه فعلی
- `products` → محصولات فروشگاه
- `articles` → مقالات فروشگاه
- `productCount` → تعداد محصولات
- `postCount` → تعداد مقالات

### 5. View

**`resources/views/Frontend/Store/index.blade.php`**

این View صفحه اصلی فروشگاه را نمایش می‌دهد.

### اگر بخواهم صفحه اصلی را تغییر بدهم

- مسیر و فرآیند ورود → `routes/web.php`
- منطق دریافت محصولات و مقالات → `IndexController@shop`
- ظاهر صفحه → `Frontend/Store/index.blade.php`
- تشخیص فروشگاه از روی دامنه → `ShopHelper::getShop()`
