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

-- =========================================================================
-- 15 Standard Order Statuses Reference (Insert/Update if missing):
-- 1  - New Order
-- 2  - Hold (Protected)
-- 3  - Confirmed
-- 4  - Packaging
-- 5  - Courier Handover
-- 6  - In Courier
-- 7  - Delivered
-- 8  - Pending Partial (Requires manual settlement via modal)
-- 9  - Partial (Full Received) (Protected)
-- 10 - Partial (Item Received) (Protected)
-- 11 - Partial (Delivery Charge Only) (Protected)
-- 12 - Pending Return
-- 13 - Returned (Protected)
-- 14 - Pre Order (Protected)
-- 15 - Cancelled (Protected)
-- =========================================================================
