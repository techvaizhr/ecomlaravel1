<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'dashboard-view',

            // Users & Roles
            'user-list', 'user-create', 'user-edit', 'user-delete',
            'role-list', 'role-create', 'role-edit', 'role-delete',

            // Customers
            'customer-list', 'customer-create', 'customer-edit', 'customer-delete', 'customer-manage',

            // Orders & Sales
            'order-list', 'order-create', 'order-edit', 'order-delete', 'order-status', 'order-invoice', 'order-process', 'order-manage', 'fraud-check',

            // Products & Catalog
            'product-list', 'product-create', 'product-edit', 'product-delete', 'product-pending',
            'category-list', 'category-create', 'category-edit', 'category-delete',
            'subcategory-list', 'subcategory-create', 'subcategory-edit', 'subcategory-delete',
            'childcategory-list', 'childcategory-create', 'childcategory-edit', 'childcategory-delete',
            'brand-list', 'brand-create', 'brand-edit', 'brand-delete',
            'color-list', 'color-create', 'color-edit', 'color-delete',
            'size-list', 'size-create', 'size-edit', 'size-delete',

            // Purchases & Suppliers
            'purchase-list', 'purchase-create', 'purchase-edit', 'purchase-delete',
            'supplier-list', 'supplier-create', 'supplier-edit', 'supplier-delete',

            // Shipping & Delivery
            'shipping-list', 'shipping-create', 'shipping-edit', 'shipping-delete',
            'delivery-boy-list', 'delivery-withdrawal-list', 'delivery-location-list',

            // Finance & Accounts
            'fund-list', 'fund-create', 'fund-edit', 'fund-delete',
            'expense-list', 'expense-create', 'expense-edit', 'expense-delete',
            'expense-category-list', 'expense-category-create', 'expense-category-edit', 'expense-category-delete',

            // Vendors
            'vendor-list', 'vendor-create', 'vendor-edit', 'vendor-delete', 'vendor-verification', 'vendor-withdrawal',

            // Resellers
            'reseller-list', 'reseller-create', 'reseller-edit', 'reseller-delete', 'reseller-verification', 'reseller-withdrawal',

            // HR / CRM
            'employee-list', 'employee-create', 'employee-edit', 'employee-delete',
            'attendance-list', 'leave-list', 'salary-list', 'bonus-list', 'salary-payment-list',

            // Marketing & Promotions
            'campaign-list', 'campaign-create', 'campaign-edit', 'campaign-delete',
            'coupon-list', 'coupon-create', 'coupon-edit', 'coupon-delete',
            'banner-list', 'banner-create', 'banner-edit', 'banner-delete',
            'banner-category-list', 'banner-category-create', 'banner-category-edit', 'banner-category-delete',
            'popup-list', 'review-list', 'blog-list', 'sms-send',

            // Analytics & Reports
            'pixel-manage', 'report-view', 'order-report', 'purchase-report', 'expense-report', 'stock-report', 'profit-loss-report',

            // Settings & System
            'setting-list', 'setting-create', 'setting-edit', 'setting-delete',
            'social-list', 'social-create', 'social-edit', 'social-delete',
            'contact-list', 'contact-create', 'contact-edit', 'contact-delete',
            'contact-message-list', 'contact-message-edit', 'contact-message-delete',
            'page-list', 'page-create', 'page-edit', 'page-delete',
            'payment-gateway', 'sms-gateway', 'courierapi', 'api-manage',
            'email-setting-list', 'complaint-list', 'seo-manage', 'sitemap-manage', 'cache-clear', 'error-log-view'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'admin'],
                ['name' => $permission, 'guard_name' => 'admin']
            );
        }
    }
}
