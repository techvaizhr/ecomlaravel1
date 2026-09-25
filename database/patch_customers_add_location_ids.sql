-- ============================================================
-- PATCH: customers টেবিলে location columns যোগ করুন
-- কারণ: Order save হওয়ার সময় division_id, district_id,
--        upazila_id column না থাকলে error আসে।
--
-- phpMyAdmin-এ এই পুরো SQL টুকু copy করে run করুন।
-- ============================================================

-- Step 1: Column গুলো যোগ করুন (যদি না থাকে)
ALTER TABLE `customers`
  ADD COLUMN IF NOT EXISTS `division_id` BIGINT UNSIGNED NULL AFTER `area`,
  ADD COLUMN IF NOT EXISTS `district_id` BIGINT UNSIGNED NULL AFTER `division_id`,
  ADD COLUMN IF NOT EXISTS `upazila_id`  BIGINT UNSIGNED NULL AFTER `district_id`;

-- ============================================================
-- NOTE: যদি আপনার MySQL 5.7 বা পুরনো MariaDB হয় এবং
-- "IF NOT EXISTS" সাপোর্ট না করে, তাহলে নিচের SQL run করুন:
-- (আগে check করুন column আছে কিনা)
-- ============================================================
-- ALTER TABLE `customers`
--   ADD COLUMN `division_id` BIGINT UNSIGNED NULL AFTER `area`,
--   ADD COLUMN `district_id` BIGINT UNSIGNED NULL AFTER `division_id`,
--   ADD COLUMN `upazila_id`  BIGINT UNSIGNED NULL AFTER `district_id`;
-- ============================================================

-- Step 2: Verify — column গুলো যোগ হয়েছে কিনা দেখুন
SHOW COLUMNS FROM `customers` WHERE Field IN ('division_id', 'district_id', 'upazila_id');
