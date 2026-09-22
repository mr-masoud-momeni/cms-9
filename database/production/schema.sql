-- Production database schema updates
-- This file is cumulative and safe to run repeatedly.
-- Run it manually in phpMyAdmin on the production database.
-- It changes schema only; it does not import or modify product images/data.

SET @db = DATABASE();

-- products.unit
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

-- products.stock
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

-- orders.reserved_at
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

-- orders.reservation_expires_at
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
