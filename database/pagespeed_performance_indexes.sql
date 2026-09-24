-- ==============================================================================
-- PageSpeed & Database Performance Optimization SQL Script
-- Ecommerce Super Fast Query Acceleration Indexes
-- ==============================================================================

-- 1. Product Variant Prices (Speeds up product catalog & details variant pricing)
ALTER TABLE `product_variant_prices` 
    ADD INDEX `idx_pvp_product_id` (`product_id`),
    ADD INDEX `idx_pvp_lookup_full` (`product_id`, `color_id`, `size_id`),
    ADD INDEX `idx_pvp_color_lookup` (`product_id`, `color_id`),
    ADD INDEX `idx_pvp_size_lookup` (`product_id`, `size_id`);

-- 2. Product Wholesale Prices (Speeds up wholesale pricing lookup)
ALTER TABLE `product_wholesale_prices`
    ADD INDEX `idx_pwp_product_id` (`product_id`),
    ADD INDEX `idx_pwp_product_qty` (`product_id`, `min_quantity`);

-- 3. Reviews (Speeds up average rating calculation across all product grids)
ALTER TABLE `reviews`
    ADD INDEX `idx_reviews_prod_status_rating` (`product_id`, `status`, `ratting`);

-- 4. Products Table (Speeds up vendor store & category/subcategory catalog queries)
ALTER TABLE `products`
    ADD INDEX `idx_products_vendor_status_appr` (`vendor_id`, `status`, `approval_status`),
    ADD INDEX `idx_products_cat_stat_appr` (`category_id`, `status`, `approval_status`),
    ADD INDEX `idx_products_subcat_stat_appr` (`subcategory_id`, `status`, `approval_status`),
    ADD INDEX `idx_products_childcat_stat_appr` (`childcategory_id`, `status`, `approval_status`);

-- 5. Order Details (Speeds up dynamic purchase limiting & customer order tracking)
ALTER TABLE `order_details`
    ADD INDEX `idx_order_details_prod_order` (`product_id`, `order_id`);

-- 6. Orders (Speeds up customer history & fraud/order frequency limit checks)
ALTER TABLE `orders`
    ADD INDEX `idx_orders_customer_created` (`customer_id`, `created_at`),
    ADD INDEX `idx_orders_ip_created` (`ip_address`, `created_at`);

-- 7. Vendors (Speeds up vendor listing on homepage and /sellers page)
ALTER TABLE `vendors`
    ADD INDEX `idx_vendors_stat_verif_id` (`status`, `verification_status`, `id`);

-- 8. Incomplete Orders (Speeds up abandoned cart phone lookup)
ALTER TABLE `incomplete_orders`
    ADD INDEX `idx_incomplete_orders_phone` (`phone`);

-- ==============================================================================
-- End of Script
-- ==============================================================================
