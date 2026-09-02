# Naming & Linux-Safety Change Log

این فایل گزارش تغییرات واقعی انجام‌شده در branch `payment-flow` است.

## تغییرات انجام‌شده

### Models
- `app/Models/category.php` → `app/Models/Category.php`
- `app/Models/article.php` → `app/Models/Article.php`
- `app/Models/menu.php` → `app/Models/Menu.php`
- `app/Models/comment.php` → `app/Models/Comment.php`
- `app/Models/Product.php`: `class product` → `class Product`
- referenceهای مدل‌ها در Controllerها و relationهای مدل‌ها به PascalCase اصلاح شدند.

### User relations
در `app/Models/User.php`:
- `menu()` → `menus()`
- `article()` → `articles()`
- `category()` → `categories()`
- `EmailGroup()` → `emailGroups()`
- `Page()` → `pages()`
- `product()` → `products()`

### Controllers
referenceهای مدل‌ها در این فایل‌ها اصلاح شدند:
- `app/Http/Controllers/front/IndexController.php`
- `app/Http/Controllers/admin/ProductController.php`
- `app/Http/Controllers/customer/ProductController.php`
- `app/Http/Controllers/admin/CategoryController.php`
- `app/Http/Controllers/customer/CategoryController.php`
- `app/Http/Controllers/admin/ArticleController.php`
- `app/Http/Controllers/admin/PageController.php`
- `app/Http/Controllers/admin/MenuController.php`

### Migration
- `Schema::dropIfExists('Orders')` → `Schema::dropIfExists('orders')`

## مسیرهای حذف‌شده
- `app/Models/category.php`
- `app/Models/article.php`
- `app/Models/menu.php`
- `app/Models/comment.php`

## باقی‌مانده برای مرحله بعد
پوشه‌های Controller (`front`, `admin`, `customer`) و پوشه‌های Blade (`Frontend`, `Backend`, `Customer`) هنوز rename نشده‌اند. این کار باید همراه با اصلاح namespace، route، `view()`, `@extends` و `@include` انجام شود.

## تست قبل از merge
```bash
composer dump-autoload
php artisan route:list
```
سپس checkout/payment و صفحات اصلی تست شوند و در صورت امکان روی Linux یا filesystem case-sensitive نیز بررسی شوند.
