<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Convert tables to utf8mb4 so Bengali characters never become '???'
        if (Schema::hasTable('divisions')) {
            try {
                DB::statement('ALTER TABLE `divisions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('districts')) {
            try {
                DB::statement('ALTER TABLE `districts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('upazilas')) {
            try {
                DB::statement('ALTER TABLE `upazilas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            } catch (\Throwable $e) {}
        }

        // 2. Set default location cascade toggles to 0 (OFF) in general_settings
        // This ensures the primary shipping area (ঢাকার ভিতরে / বাইরে) is cleanly used without forced cascade fields
        if (Schema::hasTable('general_settings')) {
            try {
                if (Schema::hasColumn('general_settings', 'checkout_location_enabled')) {
                    DB::table('general_settings')->update([
                        'checkout_location_enabled' => 0,
                        'campaign_location_enabled' => 0,
                    ]);
                }
            } catch (\Throwable $e) {}
        }

        // 3. Fix Division Names so they are clean Bengali
        if (Schema::hasTable('divisions')) {
            $divisionMap = [
                ['name' => 'ঢাকা', 'sort' => 30],
                ['name' => 'চট্টগ্রাম', 'sort' => 20],
                ['name' => 'রাজশাহী', 'sort' => 60],
                ['name' => 'খুলনা', 'sort' => 40],
                ['name' => 'বরিশাল', 'sort' => 10],
                ['name' => 'সিলেট', 'sort' => 80],
                ['name' => 'রংপুর', 'sort' => 70],
                ['name' => 'ময়মনসিংহ', 'sort' => 50],
            ];

            foreach ($divisionMap as $item) {
                DB::table('divisions')
                    ->where('sort_order', $item['sort'])
                    ->update(['name' => $item['name']]);
            }
        }
    }

    public function down(): void
    {
    }
};
