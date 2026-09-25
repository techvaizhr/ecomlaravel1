-- ==========================================================
-- Carrybee & Courier Store Management SQL Update (MariaDB / MySQL Compatible)
-- ==========================================================

-- ১. `courier_stores` টেবিল তৈরি
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
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `raw_data` LONGTEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_courier_default` (`courier_type`, `is_default`),
  INDEX `idx_courier_store` (`courier_type`, `store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ২. `courierapis` টেবিলে ৩টি কলাম যুক্ত করা (যদি পূর্বে না থাকে)
ALTER TABLE `courierapis` ADD `client_context` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `courierapis` ADD `webhook_secret` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `courierapis` ADD `default_store_id` VARCHAR(100) NULL DEFAULT NULL;


-- ৩. Carrybee এর প্রাথমিক রো ইনসার্ট (যদি না থাকে)
INSERT INTO `courierapis` (`type`, `url`, `status`, `webhook_url`, `webhook_secret`, `created_at`, `updated_at`)
SELECT 'carrybee', 'https://developers.carrybee.com', 0, 'https://dorozai.com/webhooks/carrybee?token=40489fe0-9386-4fc9-8e92-2b2fcb9d451c', '40489fe0-9386-4fc9-8e92-2b2fcb9d451c', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `courierapis` WHERE `type` = 'carrybee'
);
