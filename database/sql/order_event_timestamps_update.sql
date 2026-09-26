-- =========================================================================
-- RAW SQL FOR ADDING ORDER LIFECYCLE EVENT TIMESTAMPS
-- Use this in phpMyAdmin or MySQL CLI if running migrations directly
-- =========================================================================

-- 1. Add Event Timestamps to orders table
ALTER TABLE `orders`
  ADD COLUMN IF NOT EXISTS `delivered_at` DATETIME NULL DEFAULT NULL AFTER `updated_at`,
  ADD COLUMN IF NOT EXISTS `returned_at` DATETIME NULL DEFAULT NULL AFTER `delivered_at`,
  ADD COLUMN IF NOT EXISTS `cancelled_at` DATETIME NULL DEFAULT NULL AFTER `returned_at`,
  ADD COLUMN IF NOT EXISTS `in_courier_at` DATETIME NULL DEFAULT NULL AFTER `cancelled_at`,
  ADD COLUMN IF NOT EXISTS `confirmed_at` DATETIME NULL DEFAULT NULL AFTER `in_courier_at`,
  ADD COLUMN IF NOT EXISTS `hold_at` DATETIME NULL DEFAULT NULL AFTER `confirmed_at`;

-- 2. Add Indexes for high-speed date range filtering
ALTER TABLE `orders`
  ADD INDEX IF NOT EXISTS `idx_orders_delivered_at` (`delivered_at`),
  ADD INDEX IF NOT EXISTS `idx_orders_returned_at` (`returned_at`),
  ADD INDEX IF NOT EXISTS `idx_orders_cancelled_at` (`cancelled_at`),
  ADD INDEX IF NOT EXISTS `idx_orders_in_courier_at` (`in_courier_at`);

-- 3. Populate existing historical data (Backfill based on current order_status and updated_at)
UPDATE `orders` SET `delivered_at` = `updated_at` WHERE `order_status` IN (7, 9, 10) AND `delivered_at` IS NULL;
UPDATE `orders` SET `returned_at` = `updated_at` WHERE `order_status` IN (11, 13) AND `returned_at` IS NULL;
UPDATE `orders` SET `cancelled_at` = `updated_at` WHERE `order_status` = 15 AND `cancelled_at` IS NULL;
UPDATE `orders` SET `in_courier_at` = `updated_at` WHERE `order_status` IN (5, 6) AND `in_courier_at` IS NULL;
