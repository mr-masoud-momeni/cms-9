-- =========================================================
-- Production Database Schema Changes
-- =========================================================
-- This file is cumulative and safe to run repeatedly.
-- Run it manually in phpMyAdmin on the production database.
-- It changes schema only; it does not import or modify product images/data.
--
-- IMPORTANT:
-- This file records Production schema changes separately from
-- Laravel migrations used for local development.
-- =========================================================

SET @db = DATABASE();


-- =========================================================
-- 1. Product: unit
-- تاریخ دقیق ثبت تغییر در این فایل مشخص نیست.
-- =========================================================

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE products ADD COLUMN unit VARCHAR(50) NOT NULL DEFAULT ''عدد'' AFTER price',
        'SELECT 1'
    )
    FROM information_schema.columns
    WHERE table_schema = @db
      AND table_name = 'products'
      AND column_name = 'unit'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- =========================================================
-- 2. Product: stock
-- تاریخ دقیق ثبت تغییر در این فایل مشخص نیست.
-- =========================================================

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE products ADD COLUMN stock INT UNSIGNED NOT NULL DEFAULT 0 AFTER unit',
        'SELECT 1'
    )
    FROM information_schema.columns
    WHERE table_schema = @db
      AND table_name = 'products'
      AND column_name = 'stock'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- =========================================================
-- 3. Order: reservation
-- تاریخ دقیق ثبت تغییر در این فایل مشخص نیست.
-- =========================================================

-- زمان شروع رزرو سفارش
SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE orders ADD COLUMN reserved_at TIMESTAMP NULL AFTER paid_at',
        'SELECT 1'
    )
    FROM information_schema.columns
    WHERE table_schema = @db
      AND table_name = 'orders'
      AND column_name = 'reserved_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- زمان پایان رزرو سفارش
SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE orders ADD COLUMN reservation_expires_at TIMESTAMP NULL AFTER reserved_at',
        'SELECT 1'
    )
    FROM information_schema.columns
    WHERE table_schema = @db
      AND table_name = 'orders'
      AND column_name = 'reservation_expires_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- =========================================================
-- 4. Legacy: حذف Page Builder قدیمی
-- تاریخ دقیق ثبت تغییر در این فایل مشخص نیست.
-- =========================================================
-- جدول pages دیگر توسط معماری فعلی store/article استفاده نمی‌شود.

DROP TABLE IF EXISTS pages;


-- =========================================================
-- 5. Shop: اطلاعات فروشگاه
-- تاریخ دقیق ثبت تغییر در این فایل مشخص نیست.
-- =========================================================

-- لوگوی فروشگاه
SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE shops ADD COLUMN logo VARCHAR(255) NULL AFTER name',
        'SELECT 1'
    )
    FROM information_schema.columns
    WHERE table_schema = @db
      AND table_name = 'shops'
      AND column_name = 'logo'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- توضیحات فروشگاه
SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE shops ADD COLUMN description TEXT NULL AFTER logo',
        'SELECT 1'
    )
    FROM information_schema.columns
    WHERE table_schema = @db
      AND table_name = 'shops'
      AND column_name = 'description'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- =========================================================
-- 6. Shipping: هزینه ارسال
-- 2026-09-24
-- =========================================================

-- هزینه ثابت ارسال در تنظیمات فروشگاه
SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE shops ADD COLUMN shipping_cost BIGINT UNSIGNED NOT NULL DEFAULT 0 AFTER buyer_login_required',
        'SELECT 1'
    )
    FROM information_schema.columns
    WHERE table_schema = @db
      AND table_name = 'shops'
      AND column_name = 'shipping_cost'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- هزینه ارسال ذخیره‌شده روی سفارش
-- این مقدار هنگام ثبت/پرداخت سفارش snapshot می‌شود تا
-- تغییر هزینه ارسال فروشگاه روی سفارش‌های قبلی اثر نگذارد.
SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE orders ADD COLUMN shipping_amount BIGINT UNSIGNED NOT NULL DEFAULT 0 AFTER total',
        'SELECT 1'
    )
    FROM information_schema.columns
    WHERE table_schema = @db
      AND table_name = 'orders'
      AND column_name = 'shipping_amount'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
