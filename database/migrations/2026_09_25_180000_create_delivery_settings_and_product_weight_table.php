<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create delivery_settings table
        if (!Schema::hasTable('delivery_settings')) {
            Schema::create('delivery_settings', function (Blueprint $table) {
                $table->id();
                $table->string('active_method', 50)->default('area_based'); // area_based, flat_rate, free_delivery, weight_based
                $table->decimal('flat_rate_amount', 10, 2)->default(80.00);
                $table->decimal('default_inside_charge', 10, 2)->default(60.00);
                $table->decimal('default_outside_charge', 10, 2)->default(120.00);
                $table->decimal('weight_base_cost', 10, 2)->default(60.00);
                $table->decimal('weight_base_kg', 8, 2)->default(1.00);
                $table->decimal('weight_extra_per_kg', 10, 2)->default(20.00);
                $table->text('weight_tiers_json')->nullable();
                $table->decimal('free_delivery_min_order', 10, 2)->nullable();
                $table->timestamps();
            });

            // Insert initial default configuration
            DB::table('delivery_settings')->insert([
                'active_method'          => 'area_based',
                'flat_rate_amount'       => 80.00,
                'default_inside_charge'  => 60.00,
                'default_outside_charge' => 120.00,
                'weight_base_cost'       => 60.00,
                'weight_base_kg'         => 1.00,
                'weight_extra_per_kg'    => 20.00,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);
        }

        // 2. Add delivery_charge column to divisions if not present
        if (Schema::hasTable('divisions')) {
            if (!Schema::hasColumn('divisions', 'delivery_charge')) {
                Schema::table('divisions', function (Blueprint $table) {
                    $table->decimal('delivery_charge', 10, 2)->default(0.00)->after('name');
                });
            }
        }

        // 3. Ensure delivery_charge column exists in districts
        if (Schema::hasTable('districts')) {
            if (!Schema::hasColumn('districts', 'delivery_charge')) {
                Schema::table('districts', function (Blueprint $table) {
                    $table->decimal('delivery_charge', 10, 2)->default(0.00)->after('name');
                });
            }
        }

        // 4. Add weight & delivery override columns to products table
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'weight')) {
                    $table->decimal('weight', 8, 2)->default(0.00)->after('stock');
                }
                if (!Schema::hasColumn('products', 'delivery_charge_type')) {
                    $table->string('delivery_charge_type', 50)->default('global')->after('free_delivery'); // global, free, flat, area_based, weight_based, custom_amount
                }
                if (!Schema::hasColumn('products', 'delivery_charge_amount')) {
                    $table->decimal('delivery_charge_amount', 10, 2)->default(0.00)->after('delivery_charge_type');
                }
                if (!Schema::hasColumn('products', 'delivery_inside_dhaka')) {
                    $table->decimal('delivery_inside_dhaka', 10, 2)->default(0.00)->after('delivery_charge_amount');
                }
                if (!Schema::hasColumn('products', 'delivery_outside_dhaka')) {
                    $table->decimal('delivery_outside_dhaka', 10, 2)->default(0.00)->after('delivery_inside_dhaka');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_settings');

        if (Schema::hasTable('divisions') && Schema::hasColumn('divisions', 'delivery_charge')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->dropColumn('delivery_charge');
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $cols = ['weight', 'delivery_charge_type', 'delivery_charge_amount', 'delivery_inside_dhaka', 'delivery_outside_dhaka'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('products', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }
    }
};
