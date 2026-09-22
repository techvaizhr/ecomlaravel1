<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Helper to safely add an index if it doesn't already exist.
     */
    private function addIndexSafely(string $table, string $indexName, array|string $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $existingIndexes = collect(DB::select("SHOW INDEX FROM `{$table}`"))
            ->pluck('Key_name')
            ->unique()
            ->toArray();

        if (!in_array($indexName, $existingIndexes, true)) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($columns, $indexName) {
                $tableBlueprint->index($columns, $indexName);
            });
        }
    }

    /**
     * Helper to safely drop an index if it exists.
     */
    private function dropIndexSafely(string $table, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $existingIndexes = collect(DB::select("SHOW INDEX FROM `{$table}`"))
            ->pluck('Key_name')
            ->unique()
            ->toArray();

        if (in_array($indexName, $existingIndexes, true)) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($indexName) {
                $tableBlueprint->dropIndex($indexName);
            });
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. ORDERS TABLE INDEXES
        $this->addIndexSafely('orders', 'idx_orders_order_status', 'order_status');
        $this->addIndexSafely('orders', 'idx_orders_status_id', ['order_status', 'id']);
        $this->addIndexSafely('orders', 'idx_orders_invoice_id', 'invoice_id');
        $this->addIndexSafely('orders', 'idx_orders_customer_id', 'customer_id');
        if (Schema::hasColumn('orders', 'user_id')) {
            $this->addIndexSafely('orders', 'idx_orders_user_id', 'user_id');
        }
        $this->addIndexSafely('orders', 'idx_orders_created_at', 'created_at');
        if (Schema::hasColumn('orders', 'consignment_id')) {
            $this->addIndexSafely('orders', 'idx_orders_consignment_id', 'consignment_id');
        }
        if (Schema::hasColumn('orders', 'traffic_source')) {
            $this->addIndexSafely('orders', 'idx_orders_traffic_source', 'traffic_source');
        }

        // 2. ORDER DETAILS TABLE INDEXES
        $this->addIndexSafely('order_details', 'idx_order_details_order_id', 'order_id');
        $this->addIndexSafely('order_details', 'idx_order_details_product_id', 'product_id');

        // 3. SHIPPINGS TABLE INDEXES
        $this->addIndexSafely('shippings', 'idx_shippings_order_id', 'order_id');
        $this->addIndexSafely('shippings', 'idx_shippings_phone', 'phone');
        $this->addIndexSafely('shippings', 'idx_shippings_customer_id', 'customer_id');

        // 4. PAYMENTS TABLE INDEXES
        $this->addIndexSafely('payments', 'idx_payments_order_id', 'order_id');
        if (Schema::hasColumn('payments', 'customer_id')) {
            $this->addIndexSafely('payments', 'idx_payments_customer_id', 'customer_id');
        }
        if (Schema::hasColumn('payments', 'payment_status')) {
            $this->addIndexSafely('payments', 'idx_payments_payment_status', 'payment_status');
        }

        // 5. CUSTOMERS TABLE INDEXES
        $this->addIndexSafely('customers', 'idx_customers_phone', 'phone');

        // 6. ORDER STATUSES TABLE INDEXES
        $this->addIndexSafely('order_statuses', 'idx_order_statuses_slug', 'slug');
        if (Schema::hasColumn('order_statuses', 'status')) {
            $this->addIndexSafely('order_statuses', 'idx_order_statuses_status', 'status');
        }

        // 7. COURIER APIS TABLE INDEXES
        if (Schema::hasTable('courierapis') && Schema::hasColumn('courierapis', 'type') && Schema::hasColumn('courierapis', 'status')) {
            $this->addIndexSafely('courierapis', 'idx_courierapis_type_status', ['type', 'status']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexSafely('orders', 'idx_orders_order_status');
        $this->dropIndexSafely('orders', 'idx_orders_status_id');
        $this->dropIndexSafely('orders', 'idx_orders_invoice_id');
        $this->dropIndexSafely('orders', 'idx_orders_customer_id');
        $this->dropIndexSafely('orders', 'idx_orders_user_id');
        $this->dropIndexSafely('orders', 'idx_orders_created_at');
        $this->dropIndexSafely('orders', 'idx_orders_consignment_id');
        $this->dropIndexSafely('orders', 'idx_orders_traffic_source');

        $this->dropIndexSafely('order_details', 'idx_order_details_order_id');
        $this->dropIndexSafely('order_details', 'idx_order_details_product_id');

        $this->dropIndexSafely('shippings', 'idx_shippings_order_id');
        $this->dropIndexSafely('shippings', 'idx_shippings_phone');
        $this->dropIndexSafely('shippings', 'idx_shippings_customer_id');

        $this->dropIndexSafely('payments', 'idx_payments_order_id');
        $this->dropIndexSafely('payments', 'idx_payments_customer_id');
        $this->dropIndexSafely('payments', 'idx_payments_payment_status');

        $this->dropIndexSafely('customers', 'idx_customers_phone');

        $this->dropIndexSafely('order_statuses', 'idx_order_statuses_slug');
        $this->dropIndexSafely('order_statuses', 'idx_order_statuses_status');

        $this->dropIndexSafely('courierapis', 'idx_courierapis_type_status');
    }
};
