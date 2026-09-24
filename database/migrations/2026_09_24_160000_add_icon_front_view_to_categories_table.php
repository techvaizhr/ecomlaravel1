<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add 'icon' column if missing
        if (!Schema::hasColumn('categories', 'icon')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('icon')->nullable()->after('image');
            });
        }

        // Add 'front_view' column if missing
        if (!Schema::hasColumn('categories', 'front_view')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->tinyInteger('front_view')->default(0)->after('status');
            });
        }
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'icon')) {
                $table->dropColumn('icon');
            }
            if (Schema::hasColumn('categories', 'front_view')) {
                $table->dropColumn('front_view');
            }
        });
    }
};
