<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('general_settings') && ! Schema::hasColumn('general_settings', 'homepage_categories_enabled')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->boolean('homepage_categories_enabled')->default(true);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('general_settings') && Schema::hasColumn('general_settings', 'homepage_categories_enabled')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->dropColumn('homepage_categories_enabled');
            });
        }
    }
};
