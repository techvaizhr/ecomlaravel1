<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'partial_type')) {
                $table->string('partial_type', 50)->nullable()->after('order_status');
            }
            if (!Schema::hasColumn('orders', 'partial_collected_amount')) {
                $table->decimal('partial_collected_amount', 12, 2)->nullable()->after('partial_type');
            }
            if (!Schema::hasColumn('orders', 'partial_returned_amount')) {
                $table->decimal('partial_returned_amount', 12, 2)->nullable()->after('partial_collected_amount');
            }
            if (!Schema::hasColumn('orders', 'partial_settled_at')) {
                $table->timestamp('partial_settled_at')->nullable()->after('partial_returned_amount');
            }
            if (!Schema::hasColumn('orders', 'partial_note')) {
                $table->text('partial_note')->nullable()->after('partial_settled_at');
            }
        });

        Schema::table('order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('order_details', 'delivered_qty')) {
                $table->integer('delivered_qty')->nullable()->after('qty');
            }
            if (!Schema::hasColumn('order_details', 'returned_qty')) {
                $table->integer('returned_qty')->default(0)->after('delivered_qty');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'partial_type',
                'partial_collected_amount',
                'partial_returned_amount',
                'partial_settled_at',
                'partial_note',
            ]);
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn([
                'delivered_qty',
                'returned_qty',
            ]);
        });
    }
};
