-- =========================================================================
-- Smart Delivery Location & Optional Address Update
-- Run this SQL in phpMyAdmin or your MySQL client:
-- =========================================================================

-- ১. Shippings টেবিলের address কলামকে nullable করা (যাতে সম্পূর্ণ ঠিকানা অপশনাল হলেও এরর না হয়)
ALTER TABLE `shippings` MODIFY COLUMN `address` VARCHAR(256) NULL DEFAULT NULL;

-- ২. General Settings এ টগল ফিল্ড নিশ্চিত করা ও স্থায়ীভাবে ১ (Active) করা
ALTER TABLE `general_settings` 
ADD COLUMN IF NOT EXISTS `checkout_location_enabled` TINYINT NOT NULL DEFAULT 1 COMMENT '1 = Location Selector ON',
ADD COLUMN IF NOT EXISTS `campaign_location_enabled` TINYINT NOT NULL DEFAULT 1 COMMENT '1 = Location Selector ON';

-- ৩. ভ্যালু ১ (অন) সেট করা
UPDATE `general_settings` 
SET `checkout_location_enabled` = 1, 
    `campaign_location_enabled` = 1;
