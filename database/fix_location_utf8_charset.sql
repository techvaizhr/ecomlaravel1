-- =======================================================================
-- Bangladesh Divisions & Districts UTF-8 Charset Fix (From ecomlaravel1.sql)
-- =======================================================================
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ১. টেবিলগুলোর ক্যারেক্টার সেট ও কলিশন utf8mb4 তে কনভার্ট (যাতে বাংলা '???' না হয়)
ALTER TABLE `divisions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `districts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `upazilas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ২. জেনারেল সেটিংসে টগল নিশ্চিত করা (ডিফল্ট 0 = অফ)
ALTER TABLE `general_settings` 
ADD COLUMN IF NOT EXISTS `checkout_location_enabled` TINYINT NOT NULL DEFAULT 0,
ADD COLUMN IF NOT EXISTS `campaign_location_enabled` TINYINT NOT NULL DEFAULT 0;

UPDATE `general_settings` SET `checkout_location_enabled` = 0, `campaign_location_enabled` = 0;

-- ৩. ৮টি বিভাগের নাম বাংলায় পুনরুদ্ধার (ecomlaravel1.sql অনুযায়ী)
UPDATE `divisions` SET `name` = 'বরিশাল' WHERE `id` = 1 OR `sort_order` = 10;
UPDATE `divisions` SET `name` = 'চট্টগ্রাম' WHERE `id` = 2 OR `sort_order` = 20;
UPDATE `divisions` SET `name` = 'ঢাকা' WHERE `id` = 3 OR `sort_order` = 30;
UPDATE `divisions` SET `name` = 'খুলনা' WHERE `id` = 4 OR `sort_order` = 40;
UPDATE `divisions` SET `name` = 'ময়মনসিংহ' WHERE `id` = 5 OR `sort_order` = 50;
UPDATE `divisions` SET `name` = 'রাজশাহী' WHERE `id` = 6 OR `sort_order` = 60;
UPDATE `divisions` SET `name` = 'রংপুর' WHERE `id` = 7 OR `sort_order` = 70;
UPDATE `divisions` SET `name` = 'সিলেট' WHERE `id` = 8 OR `sort_order` = 80;

-- ৪. ৬৪টি জেলার নাম বাংলায় পুনরুদ্ধার (ecomlaravel1.sql অনুযায়ী)
-- Barishal Division (1)
UPDATE `districts` SET `name` = 'বরগুনা' WHERE `id` = 65 OR (`division_id` = 1 AND `sort_order` = 0);
UPDATE `districts` SET `name` = 'বরিশাল' WHERE `id` = 66 OR (`division_id` = 1 AND `sort_order` = 1);
UPDATE `districts` SET `name` = 'ভোলা' WHERE `id` = 67 OR (`division_id` = 1 AND `sort_order` = 2);
UPDATE `districts` SET `name` = 'ঝালকাঠি' WHERE `id` = 68 OR (`division_id` = 1 AND `sort_order` = 3);
UPDATE `districts` SET `name` = 'পটুয়াখালী' WHERE `id` = 69 OR (`division_id` = 1 AND `sort_order` = 4);
UPDATE `districts` SET `name` = 'পিরোজপুর' WHERE `id` = 70 OR (`division_id` = 1 AND `sort_order` = 5);

-- Chattogram Division (2)
UPDATE `districts` SET `name` = 'বান্দরবান' WHERE `id` = 71 OR (`division_id` = 2 AND `sort_order` = 0);
UPDATE `districts` SET `name` = 'ব্রাহ্মণবাড়িয়া' WHERE `id` = 72 OR (`division_id` = 2 AND `sort_order` = 1);
UPDATE `districts` SET `name` = 'চাঁদপুর' WHERE `id` = 73 OR (`division_id` = 2 AND `sort_order` = 2);
UPDATE `districts` SET `name` = 'চট্টগ্রাম' WHERE `id` = 74 OR (`division_id` = 2 AND `sort_order` = 3);
UPDATE `districts` SET `name` = 'কুমিল্লা' WHERE `id` = 75 OR (`division_id` = 2 AND `sort_order` = 4);
UPDATE `districts` SET `name` = 'কক্সবাজার' WHERE `id` = 76 OR (`division_id` = 2 AND `sort_order` = 5);
UPDATE `districts` SET `name` = 'ফেনী' WHERE `id` = 77 OR (`division_id` = 2 AND `sort_order` = 6);
UPDATE `districts` SET `name` = 'খাগড়াছড়ি' WHERE `id` = 78 OR (`division_id` = 2 AND `sort_order` = 7);
UPDATE `districts` SET `name` = 'লক্ষ্মীপুর' WHERE `id` = 79 OR (`division_id` = 2 AND `sort_order` = 8);
UPDATE `districts` SET `name` = 'নোয়াখালী' WHERE `id` = 80 OR (`division_id` = 2 AND `sort_order` = 9);
UPDATE `districts` SET `name` = 'রাঙ্গামাটি' WHERE `id` = 81 OR (`division_id` = 2 AND `sort_order` = 10);

-- Dhaka Division (3)
UPDATE `districts` SET `name` = 'ঢাকা' WHERE `id` = 82 OR (`division_id` = 3 AND `sort_order` = 0);
UPDATE `districts` SET `name` = 'ফরিদপুর' WHERE `id` = 83 OR (`division_id` = 3 AND `sort_order` = 1);
UPDATE `districts` SET `name` = 'গাজীপুর' WHERE `id` = 84 OR (`division_id` = 3 AND `sort_order` = 2);
UPDATE `districts` SET `name` = 'গোপালগঞ্জ' WHERE `id` = 85 OR (`division_id` = 3 AND `sort_order` = 3);
UPDATE `districts` SET `name` = 'কিশোরগঞ্জ' WHERE `id` = 86 OR (`division_id` = 3 AND `sort_order` = 4);
UPDATE `districts` SET `name` = 'মাদারীপুর' WHERE `id` = 87 OR (`division_id` = 3 AND `sort_order` = 5);
UPDATE `districts` SET `name` = 'মানিকগঞ্জ' WHERE `id` = 88 OR (`division_id` = 3 AND `sort_order` = 6);
UPDATE `districts` SET `name` = 'মুন্সিগঞ্জ' WHERE `id` = 89 OR (`division_id` = 3 AND `sort_order` = 7);
UPDATE `districts` SET `name` = 'নারায়ণগঞ্জ' WHERE `id` = 90 OR (`division_id` = 3 AND `sort_order` = 8);
UPDATE `districts` SET `name` = 'নরসিংদী' WHERE `id` = 91 OR (`division_id` = 3 AND `sort_order` = 9);
UPDATE `districts` SET `name` = 'রাজবাড়ী' WHERE `id` = 92 OR (`division_id` = 3 AND `sort_order` = 10);
UPDATE `districts` SET `name` = 'শরীয়তপুর' WHERE `id` = 93 OR (`division_id` = 3 AND `sort_order` = 11);
UPDATE `districts` SET `name` = 'টাঙ্গাইল' WHERE `id` = 94 OR (`division_id` = 3 AND `sort_order` = 12);

-- Khulna Division (4)
UPDATE `districts` SET `name` = 'বাগেরহাট' WHERE `id` = 95 OR (`division_id` = 4 AND `sort_order` = 0);
UPDATE `districts` SET `name` = 'চুয়াডাঙ্গা' WHERE `id` = 96 OR (`division_id` = 4 AND `sort_order` = 1);
UPDATE `districts` SET `name` = 'যশোর' WHERE `id` = 97 OR (`division_id` = 4 AND `sort_order` = 2);
UPDATE `districts` SET `name` = 'ঝিনাইদহ' WHERE `id` = 98 OR (`division_id` = 4 AND `sort_order` = 3);
UPDATE `districts` SET `name` = 'খুলনা' WHERE `id` = 99 OR (`division_id` = 4 AND `sort_order` = 4);
UPDATE `districts` SET `name` = 'কুষ্টিয়া' WHERE `id` = 100 OR (`division_id` = 4 AND `sort_order` = 5);
UPDATE `districts` SET `name` = 'মাগুরা' WHERE `id` = 101 OR (`division_id` = 4 AND `sort_order` = 6);
UPDATE `districts` SET `name` = 'মেহেরপুর' WHERE `id` = 102 OR (`division_id` = 4 AND `sort_order` = 7);
UPDATE `districts` SET `name` = 'নড়াইল' WHERE `id` = 103 OR (`division_id` = 4 AND `sort_order` = 8);
UPDATE `districts` SET `name` = 'সাতক্ষীরা' WHERE `id` = 104 OR (`division_id` = 4 AND `sort_order` = 9);

-- Mymensingh Division (5)
UPDATE `districts` SET `name` = 'জামালপুর' WHERE `id` = 105 OR (`division_id` = 5 AND `sort_order` = 0);
UPDATE `districts` SET `name` = 'ময়মনসিংহ' WHERE `id` = 106 OR (`division_id` = 5 AND `sort_order` = 1);
UPDATE `districts` SET `name` = 'নেত্রকোণা' WHERE `id` = 107 OR (`division_id` = 5 AND `sort_order` = 2);
UPDATE `districts` SET `name` = 'শেরপুর' WHERE `id` = 108 OR (`division_id` = 5 AND `sort_order` = 3);

-- Rajshahi Division (6)
UPDATE `districts` SET `name` = 'বগুড়া' WHERE `id` = 109 OR (`division_id` = 6 AND `sort_order` = 0);
UPDATE `districts` SET `name` = 'জয়পুরহাট' WHERE `id` = 110 OR (`division_id` = 6 AND `sort_order` = 1);
UPDATE `districts` SET `name` = 'নওগাঁ' WHERE `id` = 111 OR (`division_id` = 6 AND `sort_order` = 2);
UPDATE `districts` SET `name` = 'নাটোর' WHERE `id` = 112 OR (`division_id` = 6 AND `sort_order` = 3);
UPDATE `districts` SET `name` = 'চাঁপাইনবাবগঞ্জ' WHERE `id` = 113 OR (`division_id` = 6 AND `sort_order` = 4);
UPDATE `districts` SET `name` = 'পাবনা' WHERE `id` = 114 OR (`division_id` = 6 AND `sort_order` = 5);
UPDATE `districts` SET `name` = 'রাজশাহী' WHERE `id` = 115 OR (`division_id` = 6 AND `sort_order` = 6);
UPDATE `districts` SET `name` = 'সিরাজগঞ্জ' WHERE `id` = 116 OR (`division_id` = 6 AND `sort_order` = 7);

-- Rangpur Division (7)
UPDATE `districts` SET `name` = 'দিনাজপুর' WHERE `id` = 117 OR (`division_id` = 7 AND `sort_order` = 0);
UPDATE `districts` SET `name` = 'গাইবান্ধা' WHERE `id` = 118 OR (`division_id` = 7 AND `sort_order` = 1);
UPDATE `districts` SET `name` = 'কুড়িগ্রাম' WHERE `id` = 119 OR (`division_id` = 7 AND `sort_order` = 2);
UPDATE `districts` SET `name` = 'লালমনিরহাট' WHERE `id` = 120 OR (`division_id` = 7 AND `sort_order` = 3);
UPDATE `districts` SET `name` = 'নীলফামারী' WHERE `id` = 121 OR (`division_id` = 7 AND `sort_order` = 4);
UPDATE `districts` SET `name` = 'পঞ্চগড়' WHERE `id` = 122 OR (`division_id` = 7 AND `sort_order` = 5);
UPDATE `districts` SET `name` = 'রংপুর' WHERE `id` = 123 OR (`division_id` = 7 AND `sort_order` = 6);
UPDATE `districts` SET `name` = 'ঠাকুরগাঁও' WHERE `id` = 124 OR (`division_id` = 7 AND `sort_order` = 7);

-- Sylhet Division (8)
UPDATE `districts` SET `name` = 'হবিগঞ্জ' WHERE `id` = 125 OR (`division_id` = 8 AND `sort_order` = 0);
UPDATE `districts` SET `name` = 'মৌলভীবাজার' WHERE `id` = 126 OR (`division_id` = 8 AND `sort_order` = 1);
UPDATE `districts` SET `name` = 'সুনামগঞ্জ' WHERE `id` = 127 OR (`division_id` = 8 AND `sort_order` = 2);
UPDATE `districts` SET `name` = 'সিলেট' WHERE `id` = 128 OR (`division_id` = 8 AND `sort_order` = 3);
