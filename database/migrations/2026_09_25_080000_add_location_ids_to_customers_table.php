<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'division_id')) {
                $table->unsignedBigInteger('division_id')->nullable()->after('area');
            }
            if (!Schema::hasColumn('customers', 'district_id')) {
                $table->unsignedBigInteger('district_id')->nullable()->after('division_id');
            }
            if (!Schema::hasColumn('customers', 'upazila_id')) {
                $table->unsignedBigInteger('upazila_id')->nullable()->after('district_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('customers', 'division_id')) $cols[] = 'division_id';
            if (Schema::hasColumn('customers', 'district_id')) $cols[] = 'district_id';
            if (Schema::hasColumn('customers', 'upazila_id'))  $cols[] = 'upazila_id';
            if ($cols) $table->dropColumn($cols);
        });
    }
};
