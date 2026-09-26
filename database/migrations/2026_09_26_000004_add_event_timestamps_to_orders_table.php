<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds dedicated lifecycle event timestamps to orders table for daily operational tracking and realization accounting.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->dateTime('delivered_at')->nullable()->after('updated_at')->index();
            }
            if (!Schema::hasColumn('orders', 'returned_at')) {
                $table->dateTime('returned_at')->nullable()->after('delivered_at')->index();
            }
            if (!Schema::hasColumn('orders', 'cancelled_at')) {
                $table->dateTime('cancelled_at')->nullable()->after('returned_at')->index();
            }
            if (!Schema::hasColumn('orders', 'in_courier_at')) {
                $table->dateTime('in_courier_at')->nullable()->after('cancelled_at')->index();
            }
            if (!Schema::hasColumn('orders', 'confirmed_at')) {
                $table->dateTime('confirmed_at')->nullable()->after('in_courier_at');
            }
            if (!Schema::hasColumn('orders', 'hold_at')) {
                $table->dateTime('hold_at')->nullable()->after('confirmed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = ['delivered_at', 'returned_at', 'cancelled_at', 'in_courier_at', 'confirmed_at', 'hold_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
