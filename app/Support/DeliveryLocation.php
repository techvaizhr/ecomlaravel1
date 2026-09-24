<?php

namespace App\Support;

use App\Models\DeliveryDistrict;
use App\Models\DeliveryDivision;
use App\Models\DeliveryUpazila;

class DeliveryLocation
{
    public static function validateChain(?int $divisionId, ?int $districtId, ?int $upazilaId): bool
    {
        if (! $divisionId || ! $districtId || ! $upazilaId) {
            return false;
        }

        $district = DeliveryDistrict::query()
            ->whereKey($districtId)
            ->where('division_id', $divisionId)
            ->where('status', 1)
            ->first();

        if (! $district) {
            return false;
        }

        return DeliveryUpazila::query()
            ->whereKey($upazilaId)
            ->where('district_id', $districtId)
            ->where('status', 1)
            ->exists();
    }

    public static function shippingLabel(?int $divisionId, ?int $districtId, ?int $upazilaId): string
    {
        $upazila  = $upazilaId ? DeliveryUpazila::find($upazilaId) : null;
        $district = $districtId ? DeliveryDistrict::find($districtId) : null;
        $division = $divisionId ? DeliveryDivision::find($divisionId) : null;

        $parts = array_filter([
            $upazila?->name,
            $district?->name,
            $division?->name,
        ]);

        return $parts ? implode(', ', $parts) : '';
    }

    public static function chargeForDistrictId(?int $districtId): int
    {
        if (! $districtId) {
            return 0;
        }
        $district = DeliveryDistrict::query()->whereKey($districtId)->where('status', 1)->first();

        return $district ? (int) $district->delivery_charge : 0;
    }

    public static function restoreBanglaNames(): void
    {
        try {
            \DB::statement('ALTER TABLE `divisions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        } catch (\Throwable $e) {}

        try {
            \DB::statement('ALTER TABLE `districts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        } catch (\Throwable $e) {}

        try {
            \DB::statement('ALTER TABLE `upazilas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        } catch (\Throwable $e) {}

        $divisions = [
            1 => 'বরিশাল',
            2 => 'চট্টগ্রাম',
            3 => 'ঢাকা',
            4 => 'খুলনা',
            5 => 'ময়মনসিংহ',
            6 => 'রাজশাহী',
            7 => 'রংপুর',
            8 => 'সিলেট',
        ];

        foreach ($divisions as $id => $name) {
            \DB::table('divisions')->where('id', $id)->update(['name' => $name]);
        }

        $districts = [
            65  => ['name' => 'বরগুনা', 'div' => 1, 'sort' => 0],
            66  => ['name' => 'বরিশাল', 'div' => 1, 'sort' => 1],
            67  => ['name' => 'ভোলা', 'div' => 1, 'sort' => 2],
            68  => ['name' => 'ঝালকাঠি', 'div' => 1, 'sort' => 3],
            69  => ['name' => 'পটুয়াখালী', 'div' => 1, 'sort' => 4],
            70  => ['name' => 'পিরোজপুর', 'div' => 1, 'sort' => 5],
            71  => ['name' => 'বান্দরবান', 'div' => 2, 'sort' => 0],
            72  => ['name' => 'ব্রাহ্মণবাড়িয়া', 'div' => 2, 'sort' => 1],
            73  => ['name' => 'চাঁদপুর', 'div' => 2, 'sort' => 2],
            74  => ['name' => 'চট্টগ্রাম', 'div' => 2, 'sort' => 3],
            75  => ['name' => 'কুমিল্লা', 'div' => 2, 'sort' => 4],
            76  => ['name' => 'কক্সবাজার', 'div' => 2, 'sort' => 5],
            77  => ['name' => 'ফেনী', 'div' => 2, 'sort' => 6],
            78  => ['name' => 'খাগড়াছড়ি', 'div' => 2, 'sort' => 7],
            79  => ['name' => 'লক্ষ্মীপুর', 'div' => 2, 'sort' => 8],
            80  => ['name' => 'নোয়াখালী', 'div' => 2, 'sort' => 9],
            81  => ['name' => 'রাঙ্গামাটি', 'div' => 2, 'sort' => 10],
            82  => ['name' => 'ঢাকা', 'div' => 3, 'sort' => 0],
            83  => ['name' => 'ফরিদপুর', 'div' => 3, 'sort' => 1],
            84  => ['name' => 'গাজীপুর', 'div' => 3, 'sort' => 2],
            85  => ['name' => 'গোপালগঞ্জ', 'div' => 3, 'sort' => 3],
            86  => ['name' => 'কিশোরগঞ্জ', 'div' => 3, 'sort' => 4],
            87  => ['name' => 'মাদারীপুর', 'div' => 3, 'sort' => 5],
            88  => ['name' => 'মানিকগঞ্জ', 'div' => 3, 'sort' => 6],
            89  => ['name' => 'মুন্সিগঞ্জ', 'div' => 3, 'sort' => 7],
            90  => ['name' => 'নারায়ণগঞ্জ', 'div' => 3, 'sort' => 8],
            91  => ['name' => 'নরসিংদী', 'div' => 3, 'sort' => 9],
            92  => ['name' => 'রাজবাড়ী', 'div' => 3, 'sort' => 10],
            93  => ['name' => 'শরীয়তপুর', 'div' => 3, 'sort' => 11],
            94  => ['name' => 'টাঙ্গাইল', 'div' => 3, 'sort' => 12],
            95  => ['name' => 'বাগেরহাট', 'div' => 4, 'sort' => 0],
            96  => ['name' => 'চুয়াডাঙ্গা', 'div' => 4, 'sort' => 1],
            97  => ['name' => 'যশোর', 'div' => 4, 'sort' => 2],
            98  => ['name' => 'ঝিনাইদহ', 'div' => 4, 'sort' => 3],
            99  => ['name' => 'খুলনা', 'div' => 4, 'sort' => 4],
            100 => ['name' => 'কুষ্টিয়া', 'div' => 4, 'sort' => 5],
            101 => ['name' => 'মাগুরা', 'div' => 4, 'sort' => 6],
            102 => ['name' => 'মেহেরপুর', 'div' => 4, 'sort' => 7],
            103 => ['name' => 'নড়াইল', 'div' => 4, 'sort' => 8],
            104 => ['name' => 'সাতক্ষীরা', 'div' => 4, 'sort' => 9],
            105 => ['name' => 'জামালপুর', 'div' => 5, 'sort' => 0],
            106 => ['name' => 'ময়মনসিংহ', 'div' => 5, 'sort' => 1],
            107 => ['name' => 'নেত্রকোণা', 'div' => 5, 'sort' => 2],
            108 => ['name' => 'শেরপুর', 'div' => 5, 'sort' => 3],
            109 => ['name' => 'বগুড়া', 'div' => 6, 'sort' => 0],
            110 => ['name' => 'জয়পুরহাট', 'div' => 6, 'sort' => 1],
            111 => ['name' => 'নওগাঁ', 'div' => 6, 'sort' => 2],
            112 => ['name' => 'নাটোর', 'div' => 6, 'sort' => 3],
            113 => ['name' => 'চাঁপাইনবাবগঞ্জ', 'div' => 6, 'sort' => 4],
            114 => ['name' => 'পাবনা', 'div' => 6, 'sort' => 5],
            115 => ['name' => 'রাজশাহী', 'div' => 6, 'sort' => 6],
            116 => ['name' => 'সিরাজগঞ্জ', 'div' => 6, 'sort' => 7],
            117 => ['name' => 'দিনাজপুর', 'div' => 7, 'sort' => 0],
            118 => ['name' => 'গাইবান্ধা', 'div' => 7, 'sort' => 1],
            119 => ['name' => 'কুড়িগ্রাম', 'div' => 7, 'sort' => 2],
            120 => ['name' => 'লালমনিরহাট', 'div' => 7, 'sort' => 3],
            121 => ['name' => 'নীলফামারী', 'div' => 7, 'sort' => 4],
            122 => ['name' => 'পঞ্চগড়', 'div' => 7, 'sort' => 5],
            123 => ['name' => 'রংপুর', 'div' => 7, 'sort' => 6],
            124 => ['name' => 'ঠাকুরগাঁও', 'div' => 7, 'sort' => 7],
            125 => ['name' => 'হবিগঞ্জ', 'div' => 8, 'sort' => 0],
            126 => ['name' => 'মৌলভীবাজার', 'div' => 8, 'sort' => 1],
            127 => ['name' => 'সুনামগঞ্জ', 'div' => 8, 'sort' => 2],
            128 => ['name' => 'সিলেট', 'div' => 8, 'sort' => 3],
        ];

        foreach ($districts as $id => $item) {
            \DB::table('districts')
                ->where('id', $id)
                ->orWhere(function ($q) use ($item) {
                    $q->where('division_id', $item['div'])
                      ->where('sort_order', $item['sort']);
                })
                ->update(['name' => $item['name']]);
        }

        \Cache::forget('delivery_divisions_active');
    }
}
