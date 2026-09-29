-- ==========================================================
-- SQL Schema Update for Reviews Table
-- Adds support for:
-- 1. Real Customer/Product Review Image (`image`)
-- 2. Customer ID reference (`customer_id`)
-- 3. Custom Review Date (`review_date`)
-- ==========================================================

ALTER TABLE `reviews` 
ADD COLUMN IF NOT EXISTS `image` VARCHAR(255) NULL AFTER `review`,
ADD COLUMN IF NOT EXISTS `customer_id` BIGINT UNSIGNED NULL AFTER `product_id`,
ADD COLUMN IF NOT EXISTS `review_date` DATETIME NULL AFTER `status`;

-- Performance Index for fast review retrieval on frontend
-- If your MySQL version does not support IF NOT EXISTS in CREATE INDEX:
-- ALTER TABLE `reviews` ADD INDEX `idx_reviews_product_status_date` (`product_id`, `status`, `created_at`);
