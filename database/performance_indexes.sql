-- =========================================================================
-- PERFORMANCE INDEXES SCRIPT FOR HIGH-SPEED E-COMMERCE (MYSQL / MARIADB)
-- Home Page, Single Product Page, Shop Page, Category Pages, Landing & Checkout
-- =========================================================================

-- 1. PRODUCTS TABLE INDEXES
ALTER TABLE `products` ADD INDEX `idx_products_slug` (`slug`);
ALTER TABLE `products` ADD INDEX `idx_products_home_topsale` (`status`, `approval_status`, `topsale`, `id`);
ALTER TABLE `products` ADD INDEX `idx_products_cat_lookup` (`status`, `approval_status`, `category_id`, `id`);
ALTER TABLE `products` ADD INDEX `idx_products_subcat_lookup` (`status`, `approval_status`, `subcategory_id`, `id`);
ALTER TABLE `products` ADD INDEX `idx_products_childcat_lookup` (`status`, `approval_status`, `childcategory_id`, `id`);
ALTER TABLE `products` ADD INDEX `idx_products_brand_lookup` (`status`, `approval_status`, `brand_id`, `id`);
ALTER TABLE `products` ADD INDEX `idx_products_shop_price` (`status`, `approval_status`, `new_price`);
ALTER TABLE `products` ADD INDEX `idx_products_shop_latest` (`status`, `approval_status`, `created_at`);
ALTER TABLE `products` ADD INDEX `idx_products_flashsale` (`status`, `approval_status`, `flashsale`);

-- 2. PRODUCT COLORS & SIZES INDEXES
ALTER TABLE `productcolors` ADD INDEX `idx_productcolors_product_id` (`product_id`);
ALTER TABLE `productsizes` ADD INDEX `idx_productsizes_product_id` (`product_id`);

-- 3. REVIEWS TABLE INDEXES
ALTER TABLE `reviews` ADD INDEX `idx_reviews_product_id_status` (`product_id`, `status`);
ALTER TABLE `reviews` ADD INDEX `idx_reviews_status` (`status`);

-- 4. CATEGORIES, SUBCATEGORIES, CHILDCATEGORIES INDEXES
ALTER TABLE `categories` ADD INDEX `idx_categories_slug` (`slug`);
ALTER TABLE `categories` ADD INDEX `idx_categories_status_front_view` (`status`, `front_view`, `id`);
ALTER TABLE `categories` ADD INDEX `idx_categories_status_parent_id` (`status`, `parent_id`);

ALTER TABLE `subcategories` ADD INDEX `idx_subcategories_slug` (`slug`);
ALTER TABLE `subcategories` ADD INDEX `idx_subcategories_cat_status` (`category_id`, `status`);

ALTER TABLE `childcategories` ADD INDEX `idx_childcategories_slug` (`slug`);
ALTER TABLE `childcategories` ADD INDEX `idx_childcategories_subcat_status` (`subcategory_id`, `status`);

-- 5. BANNERS & BRANDS INDEXES
ALTER TABLE `banners` ADD INDEX `idx_banners_status_category_id` (`status`, `category_id`, `id`);
ALTER TABLE `brands` ADD INDEX `idx_brands_status_id` (`status`, `id`);
ALTER TABLE `brands` ADD INDEX `idx_brands_slug` (`slug`);

-- 6. CAMPAIGNS & LANDING PAGES INDEXES
ALTER TABLE `campaigns` ADD INDEX `idx_campaigns_slug` (`slug`);
ALTER TABLE `campaigns` ADD INDEX `idx_campaigns_status` (`status`);
ALTER TABLE `campaign_product` ADD INDEX `idx_camp_prod_campaign_id` (`campaign_id`);
ALTER TABLE `campaign_product` ADD INDEX `idx_camp_prod_product_id` (`product_id`);
ALTER TABLE `campaign_reviews` ADD INDEX `idx_camp_reviews_campaign_id` (`campaign_id`);

-- 7. CHECKOUT & SETTINGS INDEXES
ALTER TABLE `shipping_charges` ADD INDEX `idx_shipping_charges_status` (`status`);
ALTER TABLE `payment_gateways` ADD INDEX `idx_payment_gateways_status_type` (`status`, `type`);
