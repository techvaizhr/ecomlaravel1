<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Make address column nullable on shippings table
        if (Schema::hasTable('shippings') && Schema::hasColumn('shippings', 'address')) {
            try {
                DB::statement("ALTER TABLE `shippings` MODIFY COLUMN `address` VARCHAR(256) NULL DEFAULT NULL;");
            } catch (\Throwable $e) {
                // Ignore if already nullable or driver differences
            }
        }

        // 2. Ensure general_settings columns exist and default to 1 (always enabled)
        if (Schema::hasTable('general_settings')) {
            Schema::table('general_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('general_settings', 'checkout_location_enabled')) {
                    $table->tinyInteger('checkout_location_enabled')->default(1)->after('checkout_otp_enabled');
                }
                if (!Schema::hasColumn('general_settings', 'campaign_location_enabled')) {
                    $table->tinyInteger('campaign_location_enabled')->default(1)->after('checkout_location_enabled');
                }
            });

            DB::table('general_settings')->update([
                'checkout_location_enabled' => 1,
                'campaign_location_enabled' => 1,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep non-destructive
    }
};
