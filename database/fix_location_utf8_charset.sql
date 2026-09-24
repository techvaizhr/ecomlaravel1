-- =======================================================================
-- Bangladesh Location UTF-8 Charset Fix & Clean Area Selection
-- =======================================================================
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ১. টেবিলগুলোর ক্যারেক্টার সেট ও কলিশন utf8mb4 তে কনভার্ট (যাতে বাংলা নাম '???' না হয়)
ALTER TABLE `divisions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `districts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `upazilas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ২. বিভাগ, জেলা, উপজেলা ক্যাস্কেড ডিফল্টভাবে বন্ধ রাখা (0 = OFF)
-- ফলে চেকআউট এবং ল্যান্ডিং পেজে সরাসরি এরিয়া (ঢাকার ভিতরে / ঢাকার বাইরে) সিলেক্ট হবে
ALTER TABLE `general_settings` 
ADD COLUMN IF NOT EXISTS `checkout_location_enabled` TINYINT NOT NULL DEFAULT 0,
ADD COLUMN IF NOT EXISTS `campaign_location_enabled` TINYINT NOT NULL DEFAULT 0;

UPDATE `general_settings` SET `checkout_location_enabled` = 0, `campaign_location_enabled` = 0;

-- ৩. ৮টি বিভাগের নাম বাংলায় ও নির্ভুলভাবে আপডেট
UPDATE `divisions` SET `name` = 'ঢাকা' WHERE `sort_order` = 30 OR `id` = 3;
UPDATE `divisions` SET `name` = 'চট্টগ্রাম' WHERE `sort_order` = 20 OR `id` = 2;
UPDATE `divisions` SET `name` = 'রাজশাহী' WHERE `sort_order` = 60 OR `id` = 6;
UPDATE `divisions` SET `name` = 'খুলনা' WHERE `sort_order` = 40 OR `id` = 4;
UPDATE `divisions` SET `name` = 'বরিশাল' WHERE `sort_order` = 10 OR `id` = 1;
UPDATE `divisions` SET `name` = 'সিলেট' WHERE `sort_order` = 80 OR `id` = 8;
UPDATE `divisions` SET `name` = 'রংপুর' WHERE `sort_order` = 70 OR `id` = 7;
UPDATE `divisions` SET `name` = 'ময়মনসিংহ' WHERE `sort_order` = 50 OR `id` = 5;
