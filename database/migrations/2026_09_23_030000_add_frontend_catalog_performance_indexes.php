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

        $cols = is_array($columns) ? $columns : [$columns];
        foreach ($cols as $col) {
            if (!Schema::hasColumn($table, $col)) {
                return;
            }
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
        // 1. PRODUCTS TABLE
        $this->addIndexSafely('products', 'idx_products_slug', 'slug');
        $this->addIndexSafely('products', 'idx_products_home_topsale', ['status', 'approval_status', 'topsale', 'id']);
        $this->addIndexSafely('products', 'idx_products_cat_lookup', ['status', 'approval_status', 'category_id', 'id']);
        $this->addIndexSafely('products', 'idx_products_subcat_lookup', ['status', 'approval_status', 'subcategory_id', 'id']);
        $this->addIndexSafely('products', 'idx_products_childcat_lookup', ['status', 'approval_status', 'childcategory_id', 'id']);
        $this->addIndexSafely('products', 'idx_products_brand_lookup', ['status', 'approval_status', 'brand_id', 'id']);
        $this->addIndexSafely('products', 'idx_products_shop_price', ['status', 'approval_status', 'new_price']);
        $this->addIndexSafely('products', 'idx_products_shop_latest', ['status', 'approval_status', 'created_at']);
        $this->addIndexSafely('products', 'idx_products_flashsale', ['status', 'approval_status', 'flashsale']);

        // 2. PRODUCT COLORS & SIZES
        $this->addIndexSafely('productcolors', 'idx_productcolors_product_id', 'product_id');
        $this->addIndexSafely('productsizes', 'idx_productsizes_product_id', 'product_id');

        // 3. REVIEWS TABLE
        $this->addIndexSafely('reviews', 'idx_reviews_product_id_status', ['product_id', 'status']);
        $this->addIndexSafely('reviews', 'idx_reviews_status', 'status');

        // 4. CATEGORIES, SUBCATEGORIES, CHILDCATEGORIES
        $this->addIndexSafely('categories', 'idx_categories_slug', 'slug');
        $this->addIndexSafely('categories', 'idx_categories_status_front_view', ['status', 'front_view', 'id']);
        $this->addIndexSafely('categories', 'idx_categories_status_parent_id', ['status', 'parent_id']);

        $this->addIndexSafely('subcategories', 'idx_subcategories_slug', 'slug');
        $this->addIndexSafely('subcategories', 'idx_subcategories_cat_status', ['category_id', 'status']);

        $this->addIndexSafely('childcategories', 'idx_childcategories_slug', 'slug');
        $this->addIndexSafely('childcategories', 'idx_childcategories_subcat_status', ['subcategory_id', 'status']);

        // 5. BANNERS & BRANDS
        $this->addIndexSafely('banners', 'idx_banners_status_category_id', ['status', 'category_id', 'id']);
        $this->addIndexSafely('brands', 'idx_brands_status_id', ['status', 'id']);
        $this->addIndexSafely('brands', 'idx_brands_slug', 'slug');

        // 6. CAMPAIGNS & LANDING PAGES
        $this->addIndexSafely('campaigns', 'idx_campaigns_slug', 'slug');
        $this->addIndexSafely('campaigns', 'idx_campaigns_status', 'status');
        $this->addIndexSafely('campaign_product', 'idx_camp_prod_campaign_id', 'campaign_id');
        $this->addIndexSafely('campaign_product', 'idx_camp_prod_product_id', 'product_id');
        $this->addIndexSafely('campaign_reviews', 'idx_camp_reviews_campaign_id', 'campaign_id');

        // 7. CHECKOUT & SETTINGS TABLES
        $this->addIndexSafely('shipping_charges', 'idx_shipping_charges_status', 'status');
        $this->addIndexSafely('payment_gateways', 'idx_payment_gateways_status_type', ['status', 'type']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexSafely('payment_gateways', 'idx_payment_gateways_status_type');
        $this->dropIndexSafely('shipping_charges', 'idx_shipping_charges_status');
        $this->dropIndexSafely('campaign_reviews', 'idx_camp_reviews_campaign_id');
        $this->dropIndexSafely('campaign_product', 'idx_camp_prod_product_id');
        $this->dropIndexSafely('campaign_product', 'idx_camp_prod_campaign_id');
        $this->dropIndexSafely('campaigns', 'idx_campaigns_status');
        $this->dropIndexSafely('campaigns', 'idx_campaigns_slug');
        $this->dropIndexSafely('brands', 'idx_brands_slug');
        $this->dropIndexSafely('brands', 'idx_brands_status_id');
        $this->dropIndexSafely('banners', 'idx_banners_status_category_id');
        $this->dropIndexSafely('childcategories', 'idx_childcategories_subcat_status');
        $this->dropIndexSafely('childcategories', 'idx_childcategories_slug');
        $this->dropIndexSafely('subcategories', 'idx_subcategories_cat_status');
        $this->dropIndexSafely('subcategories', 'idx_subcategories_slug');
        $this->dropIndexSafely('categories', 'idx_categories_status_parent_id');
        $this->dropIndexSafely('categories', 'idx_categories_status_front_view');
        $this->dropIndexSafely('categories', 'idx_categories_slug');
        $this->dropIndexSafely('reviews', 'idx_reviews_status');
        $this->dropIndexSafely('reviews', 'idx_reviews_product_id_status');
        $this->dropIndexSafely('productsizes', 'idx_productsizes_product_id');
        $this->dropIndexSafely('productcolors', 'idx_productcolors_product_id');
        $this->dropIndexSafely('products', 'idx_products_flashsale');
        $this->dropIndexSafely('products', 'idx_products_shop_latest');
        $this->dropIndexSafely('products', 'idx_products_shop_price');
        $this->dropIndexSafely('products', 'idx_products_brand_lookup');
        $this->dropIndexSafely('products', 'idx_products_childcat_lookup');
        $this->dropIndexSafely('products', 'idx_products_subcat_lookup');
        $this->dropIndexSafely('products', 'idx_products_cat_lookup');
        $this->dropIndexSafely('products', 'idx_products_home_topsale');
        $this->dropIndexSafely('products', 'idx_products_slug');
    }
};
