-- ========================================================
-- Delivery Charge Settings & Custom Charges SQL
-- phpMyAdmin বা MySQL CLI-তে রান করুন
-- ========================================================

-- ১. delivery_settings টেবিল তৈরি
DROP TABLE IF EXISTS `delivery_settings`;

CREATE TABLE `delivery_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `active_method` varchar(50) NOT NULL DEFAULT 'area_based',
  `flat_rate_amount` decimal(10,2) NOT NULL DEFAULT 100.00,
  `weight_base_cost` decimal(10,2) NOT NULL DEFAULT 60.00,
  `weight_base_kg` decimal(8,2) NOT NULL DEFAULT 1.00,
  `weight_extra_per_kg` decimal(10,2) NOT NULL DEFAULT 20.00,
  `weight_tiers_json` text DEFAULT NULL,
  `free_delivery_min_order` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ডিফল্ট ডাটা ইনসার্ট
INSERT INTO `delivery_settings` (`id`, `active_method`, `flat_rate_amount`, `weight_base_cost`, `weight_base_kg`, `weight_extra_per_kg`, `created_at`, `updated_at`) 
VALUES (1, 'area_based', 100.00, 60.00, 1.00, 20.00, NOW(), NOW());

-- ২. custom_delivery_charges টেবিল তৈরি (একাধিক কাস্টম চার্জের জন্য)
CREATE TABLE IF NOT EXISTS `custom_delivery_charges` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ৩. divisions টেবিলে delivery_charge কলাম নিশ্চিত করা
ALTER TABLE `divisions` ADD COLUMN IF NOT EXISTS `delivery_charge` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `name`;

-- ৪. districts টেবিলে delivery_charge কলাম নিশ্চিত করা
ALTER TABLE `districts` ADD COLUMN IF NOT EXISTS `delivery_charge` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `name`;

-- ৫. products টেবিলে weight কলাম নিশ্চিত করা
ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `weight` decimal(8,2) NOT NULL DEFAULT 0.00 AFTER `stock`;
