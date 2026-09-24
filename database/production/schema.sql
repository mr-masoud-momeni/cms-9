-- Production database schema changes
-- Run manually in phpMyAdmin when the corresponding Laravel migrations
-- cannot be executed directly on Production.

-- 2026-09-24
-- Ensure the main multi-tenant tables have an index beginning with shop_id.
-- Run the inspection query first. Only execute the ALTER statements for
-- tables that do not already have a suitable shop_id index.

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

-- Expected index names for newly-created indexes:
-- products_shop_id_index
-- orders_shop_id_index
-- categories_shop_id_index
-- payments_shop_id_index
-- gateways_shop_id_index

-- Apply only the missing ones:
-- ALTER TABLE products ADD INDEX products_shop_id_index (shop_id);
-- ALTER TABLE orders ADD INDEX orders_shop_id_index (shop_id);
-- ALTER TABLE categories ADD INDEX categories_shop_id_index (shop_id);
-- ALTER TABLE payments ADD INDEX payments_shop_id_index (shop_id);
-- ALTER TABLE gateways ADD INDEX gateways_shop_id_index (shop_id);

-- articles already defines an explicit shop_id index in its Laravel migration.
