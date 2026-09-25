-- ==========================================================
-- Carrybee & Courier Store Management SQL Update
-- Date: 2026-09-26
-- Database: MySQL / MariaDB
-- ==========================================================

-- 1. Create `courier_stores` Table (For Carrybee, Steadfast, Pathao, RedX pickup stores)
CREATE TABLE IF NOT EXISTS `courier_stores` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `courier_type` VARCHAR(50) NOT NULL COMMENT 'carrybee, pathao, steadfast, redx, etc.',
  `store_id` VARCHAR(100) NOT NULL COMMENT 'Store ID from courier API or custom identifier',
  `store_name` VARCHAR(191) NOT NULL,
  `contact_person_name` VARCHAR(191) NULL DEFAULT NULL,
  `contact_person_number` VARCHAR(50) NULL DEFAULT NULL,
  `contact_person_secondary_number` VARCHAR(50) NULL DEFAULT NULL,
  `address` TEXT NULL DEFAULT NULL,
  `city_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `city_name` VARCHAR(100) NULL DEFAULT NULL,
  `zone_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `zone_name` VARCHAR(100) NULL DEFAULT NULL,
  `area_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `area_name` VARCHAR(100) NULL DEFAULT NULL,
  `lat` DECIMAL(10, 7) NULL DEFAULT NULL,
  `lng` DECIMAL(10, 7) NULL DEFAULT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Default pickup store for this courier',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `raw_data` LONGTEXT NULL DEFAULT NULL COMMENT 'Full JSON payload from courier API',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_courier_default` (`courier_type`, `is_default`),
  INDEX `idx_courier_store` (`courier_type`, `store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2. Add New Columns to `courierapis` Table (if they do not already exist)
-- Column: client_context (used by Carrybee)
SET @dbname = DATABASE();
SET @tablename = "courierapis";
SET @columnname = "client_context";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN `", @columnname, "` VARCHAR(255) NULL DEFAULT NULL AFTER `client_secret` COMMENT 'Client-Context for Carrybee';")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Column: webhook_secret
SET @columnname = "webhook_secret";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN `", @columnname, "` VARCHAR(255) NULL DEFAULT NULL AFTER `webhook_url` COMMENT 'Webhook secret token';")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Column: default_store_id
SET @columnname = "default_store_id";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN `", @columnname, "` VARCHAR(100) NULL DEFAULT NULL AFTER `status` COMMENT 'Default pickup store id';")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;


-- 3. Insert Initial Carrybee Configuration Row (If Not Exists)
INSERT INTO `courierapis` (`type`, `url`, `status`, `webhook_url`, `webhook_secret`, `created_at`, `updated_at`)
SELECT 'carrybee', 'https://developers.carrybee.com', 0, 'https://dorozai.com/webhooks/carrybee?token=40489fe0-9386-4fc9-8e92-2b2fcb9d451c', '40489fe0-9386-4fc9-8e92-2b2fcb9d451c', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `courierapis` WHERE `type` = 'carrybee'
);

-- 4. Mark Migrations as completed in Laravel migrations table
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_26_000001_create_courier_stores_table', (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM (SELECT `batch` FROM `migrations`) as m)
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_09_26_000001_create_courier_stores_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_26_000002_add_carrybee_fields_to_courierapis_table', (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM (SELECT `batch` FROM `migrations`) as m)
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_09_26_000002_add_carrybee_fields_to_courierapis_table'
);
