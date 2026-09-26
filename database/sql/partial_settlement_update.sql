-- =========================================================================
-- Partial Settlement & Order Status Flow Schema Update
-- =========================================================================

-- 1. Add Partial Settlement Columns to `orders` Table
ALTER TABLE `orders`
ADD COLUMN IF NOT EXISTS `partial_type` VARCHAR(50) NULL AFTER `order_status`,
ADD COLUMN IF NOT EXISTS `partial_collected_amount` DECIMAL(12, 2) NULL AFTER `partial_type`,
ADD COLUMN IF NOT EXISTS `partial_returned_amount` DECIMAL(12, 2) NULL AFTER `partial_collected_amount`,
ADD COLUMN IF NOT EXISTS `partial_settled_at` TIMESTAMP NULL AFTER `partial_returned_amount`,
ADD COLUMN IF NOT EXISTS `partial_note` TEXT NULL AFTER `partial_settled_at`;

-- 2. Add Item-level Partial Delivery/Return Columns to `order_details` Table
ALTER TABLE `order_details`
ADD COLUMN IF NOT EXISTS `delivered_qty` INT NULL DEFAULT NULL AFTER `qty`,
ADD COLUMN IF NOT EXISTS `returned_qty` INT NOT NULL DEFAULT 0 AFTER `delivered_qty`;

-- 3. Insert / Update 15 Standard Order Statuses in `order_statuses` Table
INSERT INTO `order_statuses` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`)
VALUES
(1, 'New Order', 'new-order', '1', NOW(), NOW()),
(2, 'Hold', 'hold', '1', NOW(), NOW()),
(3, 'Confirmed', 'confirmed', '1', NOW(), NOW()),
(4, 'Packaging', 'packaging', '1', NOW(), NOW()),
(5, 'Courier Handover', 'courier-handover', '1', NOW(), NOW()),
(6, 'In Courier', 'in-courier', '1', NOW(), NOW()),
(7, 'Delivered', 'delivered', '1', NOW(), NOW()),
(8, 'Pending Partial', 'pending-partial', '1', NOW(), NOW()),
(9, 'Partial (Full Received)', 'partial-full-received', '1', NOW(), NOW()),
(10, 'Partial (Item Received)', 'partial-item-received', '1', NOW(), NOW()),
(11, 'Partial (Delivery Charge Only)', 'partial-delivery-charge-only', '1', NOW(), NOW()),
(12, 'Pending Return', 'pending-return', '1', NOW(), NOW()),
(13, 'Returned', 'returned', '1', NOW(), NOW()),
(14, 'Pre Order', 'pre-order', '1', NOW(), NOW()),
(15, 'Cancelled', 'cancelled', '1', NOW(), NOW())
ON DUPLICATE KEY UPDATE
`name` = VALUES(`name`),
`slug` = VALUES(`slug`),
`status` = VALUES(`status`),
`updated_at` = NOW();
