-- ========================================================
-- Delivery Charge Settings & Product Weight System SQL
-- Database schema updates for 5 Delivery Settings Modes
-- ========================================================

-- 1. Create delivery_settings Table
CREATE TABLE IF NOT EXISTS `delivery_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `active_method` varchar(50) NOT NULL DEFAULT 'area_based',
  `flat_rate_amount` decimal(10,2) NOT NULL DEFAULT 80.00,
  `default_inside_charge` decimal(10,2) NOT NULL DEFAULT 60.00,
  `default_outside_charge` decimal(10,2) NOT NULL DEFAULT 120.00,
  `weight_base_cost` decimal(10,2) NOT NULL DEFAULT 60.00,
  `weight_base_kg` decimal(8,2) NOT NULL DEFAULT 1.00,
  `weight_extra_per_kg` decimal(10,2) NOT NULL DEFAULT 20.00,
  `weight_tiers_json` text DEFAULT NULL,
  `free_delivery_min_order` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Row if empty
INSERT INTO `delivery_settings` (`id`, `active_method`, `flat_rate_amount`, `default_inside_charge`, `default_outside_charge`, `weight_base_cost`, `weight_base_kg`, `weight_extra_per_kg`, `created_at`, `updated_at`)
SELECT 1, 'area_based', 80.00, 60.00, 120.00, 60.00, 1.00, 20.00, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `delivery_settings` WHERE `id` = 1);

-- 2. Add delivery_charge column to divisions table (if not exists)
ALTER TABLE `divisions` ADD COLUMN IF NOT EXISTS `delivery_charge` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `name`;

-- 3. Ensure delivery_charge column exists in districts table
ALTER TABLE `districts` ADD COLUMN IF NOT EXISTS `delivery_charge` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `name`;

-- 4. Add Weight & Delivery Override columns to products table
ALTER TABLE `products` 
  ADD COLUMN IF NOT EXISTS `weight` decimal(8,2) NOT NULL DEFAULT 0.00 AFTER `stock`,
  ADD COLUMN IF NOT EXISTS `delivery_charge_type` varchar(50) NOT NULL DEFAULT 'global' AFTER `free_delivery`,
  ADD COLUMN IF NOT EXISTS `delivery_charge_amount` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `delivery_charge_type`,
  ADD COLUMN IF NOT EXISTS `delivery_inside_dhaka` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `delivery_charge_amount`,
  ADD COLUMN IF NOT EXISTS `delivery_outside_dhaka` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `delivery_inside_dhaka`;
