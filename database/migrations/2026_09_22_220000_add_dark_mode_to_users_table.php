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
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'dark_mode')) {
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('dark_mode')->default(0)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'dark_mode')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('dark_mode');
            });
        }
    }
};
