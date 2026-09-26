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

        // Ensure 15 Standard Order Statuses exist in order_statuses table
        $statuses = [
            1  => ['name' => 'New Order', 'slug' => 'new-order', 'status' => '1'],
            2  => ['name' => 'Hold', 'slug' => 'hold', 'status' => '1'],
            3  => ['name' => 'Confirmed', 'slug' => 'confirmed', 'status' => '1'],
            4  => ['name' => 'Packaging', 'slug' => 'packaging', 'status' => '1'],
            5  => ['name' => 'Courier Handover', 'slug' => 'courier-handover', 'status' => '1'],
            6  => ['name' => 'In Courier', 'slug' => 'in-courier', 'status' => '1'],
            7  => ['name' => 'Delivered', 'slug' => 'delivered', 'status' => '1'],
            8  => ['name' => 'Pending Partial', 'slug' => 'pending-partial', 'status' => '1'],
            9  => ['name' => 'Partial (Full Received)', 'slug' => 'partial-full-received', 'status' => '1'],
            10 => ['name' => 'Partial (Item Received)', 'slug' => 'partial-item-received', 'status' => '1'],
            11 => ['name' => 'Partial (Delivery Charge Only)', 'slug' => 'partial-delivery-charge-only', 'status' => '1'],
            12 => ['name' => 'Pending Return', 'slug' => 'pending-return', 'status' => '1'],
            13 => ['name' => 'Returned', 'slug' => 'returned', 'status' => '1'],
            14 => ['name' => 'Pre Order', 'slug' => 'pre-order', 'status' => '1'],
            15 => ['name' => 'Cancelled', 'slug' => 'cancelled', 'status' => '1'],
        ];

        foreach ($statuses as $id => $data) {
            $existing = \Illuminate\Support\Facades\DB::table('order_statuses')->where('id', $id)->first();
            if ($existing) {
                \Illuminate\Support\Facades\DB::table('order_statuses')->where('id', $id)->update([
                    'name'       => $data['name'],
                    'slug'       => $data['slug'],
                    'status'     => $data['status'],
                    'updated_at' => now(),
                ]);
            } else {
                \Illuminate\Support\Facades\DB::table('order_statuses')->insert([
                    'id'         => $id,
                    'name'       => $data['name'],
                    'slug'       => $data['slug'],
                    'status'     => $data['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
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
