<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'checkout_location_enabled')) {
                $table->tinyInteger('checkout_location_enabled')->default(1)->after('checkout_otp_enabled')
                    ->comment('1 = Division/District/Upazila selector on checkout; 0 = Simple address field only');
            }
            if (!Schema::hasColumn('general_settings', 'campaign_location_enabled')) {
                $table->tinyInteger('campaign_location_enabled')->default(0)->after('checkout_location_enabled')
                    ->comment('1 = Division/District/Upazila selector on campaign LP; 0 = Simple address field only');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'checkout_location_enabled')) {
                $table->dropColumn('checkout_location_enabled');
            }
            if (Schema::hasColumn('general_settings', 'campaign_location_enabled')) {
                $table->dropColumn('campaign_location_enabled');
            }
        });
    }
};
