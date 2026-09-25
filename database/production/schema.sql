-- Production database schema changes
-- Run manually in phpMyAdmin when the corresponding Laravel migrations
-- cannot be executed directly on Production.

-- 2026-09-24
-- Ensure the main multi-tenant tables have an index beginning with shop_id.
-- This is safe to run even when MySQL/InnoDB has already created an index
-- for the foreign key: an existing leading shop_id index is detected first.

SELECT
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    SEQ_IN_INDEX
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN (
      'products',
      'orders',
      'categories',
      'payments',
      'gateways',
      'articles'
  )
  AND COLUMN_NAME = 'shop_id'
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE products ADD INDEX products_shop_id_index (shop_id)',
        'SELECT 1'
    )
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'products'
      AND COLUMN_NAME = 'shop_id'
      AND SEQ_IN_INDEX = 1
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE orders ADD INDEX orders_shop_id_index (shop_id)',
        'SELECT 1'
    )
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'orders'
      AND COLUMN_NAME = 'shop_id'
      AND SEQ_IN_INDEX = 1
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE categories ADD INDEX categories_shop_id_index (shop_id)',
        'SELECT 1'
    )
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'categories'
      AND COLUMN_NAME = 'shop_id'
      AND SEQ_IN_INDEX = 1
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE payments ADD INDEX payments_shop_id_index (shop_id)',
        'SELECT 1'
    )
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'payments'
      AND COLUMN_NAME = 'shop_id'
      AND SEQ_IN_INDEX = 1
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE gateways ADD INDEX gateways_shop_id_index (shop_id)',
        'SELECT 1'
    )
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'gateways'
      AND COLUMN_NAME = 'shop_id'
      AND SEQ_IN_INDEX = 1
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- articles already defines an explicit shop_id index in its Laravel migration.


-- 2026-09-25
-- Add seller phone number to users.
ALTER TABLE users
    ADD COLUMN phone VARCHAR(20) NULL AFTER email,
    ADD UNIQUE KEY users_phone_unique (phone);
