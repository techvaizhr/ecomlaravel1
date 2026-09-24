<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Helper to safely add an index if table and columns exist and index does not exist yet.
     */
    private function addIndexSafely(string $table, string $indexName, array|string $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $cols = is_array($columns) ? $columns : [$columns];
        foreach ($cols as $col) {
            if (!Schema::hasColumn($table, $col)) {
                return;
            }
        }

        try {
            $existingIndexes = collect(DB::select("SHOW INDEX FROM `{$table}`"))
                ->pluck('Key_name')
                ->unique()
                ->toArray();

            if (!in_array($indexName, $existingIndexes, true)) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($columns, $indexName) {
                    $tableBlueprint->index($columns, $indexName);
                });
            }
        } catch (\Throwable $e) {
            // Silently skip if DB does not permit or already indexed
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

        try {
            $existingIndexes = collect(DB::select("SHOW INDEX FROM `{$table}`"))
                ->pluck('Key_name')
                ->unique()
                ->toArray();

            if (in_array($indexName, $existingIndexes, true)) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($indexName) {
                    $tableBlueprint->dropIndex($indexName);
                });
            }
        } catch (\Throwable $e) {
            // Silently skip
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. PRODUCT VARIANT PRICES TABLE (Huge speed boost for product lists & details)
        $this->addIndexSafely('product_variant_prices', 'idx_pvp_product_id', 'product_id');
        $this->addIndexSafely('product_variant_prices', 'idx_pvp_lookup_full', ['product_id', 'color_id', 'size_id']);
        $this->addIndexSafely('product_variant_prices', 'idx_pvp_color_lookup', ['product_id', 'color_id']);
        $this->addIndexSafely('product_variant_prices', 'idx_pvp_size_lookup', ['product_id', 'size_id']);

        // 2. PRODUCT WHOLESALE PRICES TABLE
        $this->addIndexSafely('product_wholesale_prices', 'idx_pwp_product_id', 'product_id');
        $this->addIndexSafely('product_wholesale_prices', 'idx_pwp_product_qty', ['product_id', 'min_quantity']);

        // 3. REVIEWS TABLE (Optimizes withAvg ratings on every product card)
        $this->addIndexSafely('reviews', 'idx_reviews_prod_status_rating', ['product_id', 'status', 'ratting']);

        // 4. PRODUCTS TABLE (Vendor & Multi-category indexing)
        $this->addIndexSafely('products', 'idx_products_vendor_status_appr', ['vendor_id', 'status', 'approval_status']);
        $this->addIndexSafely('products', 'idx_products_cat_stat_appr', ['category_id', 'status', 'approval_status']);
        $this->addIndexSafely('products', 'idx_products_subcat_stat_appr', ['subcategory_id', 'status', 'approval_status']);
        $this->addIndexSafely('products', 'idx_products_childcat_stat_appr', ['childcategory_id', 'status', 'approval_status']);

        // 5. ORDER DETAILS TABLE (Speeds up order limit verification & cart checks)
        $this->addIndexSafely('order_details', 'idx_order_details_prod_order', ['product_id', 'order_id']);

        // 6. ORDERS TABLE (Speeds up dynamic rate limiting & customer order tracking)
        $this->addIndexSafely('orders', 'idx_orders_customer_created', ['customer_id', 'created_at']);
        if (Schema::hasColumn('orders', 'ip_address')) {
            $this->addIndexSafely('orders', 'idx_orders_ip_created', ['ip_address', 'created_at']);
        }

        // 7. VENDORS TABLE (Speeds up sellers page & homepage vendor list)
        $this->addIndexSafely('vendors', 'idx_vendors_stat_verif_id', ['status', 'verification_status', 'id']);

        // 8. INCOMPLETE ORDERS TABLE (Speeds up phone lookup for abandoned cart tracking)
        $this->addIndexSafely('incomplete_orders', 'idx_incomplete_orders_phone', 'phone');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexSafely('incomplete_orders', 'idx_incomplete_orders_phone');
        $this->dropIndexSafely('vendors', 'idx_vendors_stat_verif_id');
        $this->dropIndexSafely('orders', 'idx_orders_ip_created');
        $this->dropIndexSafely('orders', 'idx_orders_customer_created');
        $this->dropIndexSafely('order_details', 'idx_order_details_prod_order');
        $this->dropIndexSafely('products', 'idx_products_childcat_stat_appr');
        $this->dropIndexSafely('products', 'idx_products_subcat_stat_appr');
        $this->dropIndexSafely('products', 'idx_products_cat_stat_appr');
        $this->dropIndexSafely('products', 'idx_products_vendor_status_appr');
        $this->dropIndexSafely('reviews', 'idx_reviews_prod_status_rating');
        $this->dropIndexSafely('product_wholesale_prices', 'idx_pwp_product_qty');
        $this->dropIndexSafely('product_wholesale_prices', 'idx_pwp_product_id');
        $this->dropIndexSafely('product_variant_prices', 'idx_pvp_size_lookup');
        $this->dropIndexSafely('product_variant_prices', 'idx_pvp_color_lookup');
        $this->dropIndexSafely('product_variant_prices', 'idx_pvp_lookup_full');
        $this->dropIndexSafely('product_variant_prices', 'idx_pvp_product_id');
    }
};
