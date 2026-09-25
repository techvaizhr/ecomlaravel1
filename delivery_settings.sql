-- ========================================================
-- Delivery Charge Settings & Custom Delivery Charges SQL
-- Run this in phpMyAdmin or MySQL CLI
-- ========================================================

-- 1. Create delivery_settings Table (Without legacy inside/outside logic)
CREATE TABLE IF NOT EXISTS `delivery_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `active_method` varchar(50) NOT NULL DEFAULT 'area_based',
  `flat_rate_amount` decimal(10,2) NOT NULL DEFAULT 100.00,
  `default_area_charge` decimal(10,2) NOT NULL DEFAULT 100.00,
  `weight_base_cost` decimal(10,2) NOT NULL DEFAULT 60.00,
  `weight_base_kg` decimal(8,2) NOT NULL DEFAULT 1.00,
  `weight_extra_per_kg` decimal(10,2) NOT NULL DEFAULT 20.00,
  `weight_tiers_json` text DEFAULT NULL,
  `free_delivery_min_order` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Row if table is empty
INSERT INTO `delivery_settings` (`id`, `active_method`, `flat_rate_amount`, `default_area_charge`, `weight_base_cost`, `weight_base_kg`, `weight_extra_per_kg`, `created_at`, `updated_at`)
SELECT 1, 'area_based', 100.00, 100.00, 60.00, 1.00, 20.00, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `delivery_settings` WHERE `id` = 1);

-- 2. Create custom_delivery_charges Table (Multiple Custom Charges)
CREATE TABLE IF NOT EXISTS `custom_delivery_charges` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Add delivery_charge column to divisions table (if not exists)
ALTER TABLE `divisions` ADD COLUMN IF NOT EXISTS `delivery_charge` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `name`;

-- 4. Ensure delivery_charge column exists in districts table
ALTER TABLE `districts` ADD COLUMN IF NOT EXISTS `delivery_charge` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `name`;

-- 5. Add Weight & Custom Delivery columns to products table
ALTER TABLE `products` 
  ADD COLUMN IF NOT EXISTS `weight` decimal(8,2) NOT NULL DEFAULT 0.00 AFTER `stock`,
  ADD COLUMN IF NOT EXISTS `delivery_charge_type` varchar(50) NOT NULL DEFAULT 'global' AFTER `free_delivery`,
  ADD COLUMN IF NOT EXISTS `custom_delivery_charge_id` bigint(20) UNSIGNED NULL DEFAULT NULL AFTER `delivery_charge_type`;
