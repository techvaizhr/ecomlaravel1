<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Toastr;
use DB;
class RoleController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store','syncPermissions']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    
    public function index(Request $request)
    {
        $show_data = Role::withCount(['permissions', 'users'])->orderBy('id','DESC')->get();
        $totalPermissions = Permission::where('guard_name', 'admin')->count();
        if ($totalPermissions === 0) {
            $totalPermissions = Permission::count();
        }
        return view('backEnd.roles.index',compact('show_data', 'totalPermissions'));
    }

    /**
     * Auto generate & synchronize all standard system permissions into the database
     */
    public function syncPermissions(Request $request)
    {
        // Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $allPermissions = [
            // Dashboard
            'dashboard-view',

            // Users
            'user-list', 'user-create', 'user-edit', 'user-delete',

            // Roles
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

        $createdCount = 0;
        foreach ($allPermissions as $permName) {
            $existing = Permission::where('name', $permName)->where('guard_name', 'admin')->first();
            if (!$existing) {
                Permission::create(['name' => $permName, 'guard_name' => 'admin']);
                $createdCount++;
            }
        }

        // Also ensure Super Admin / Admin role has all permissions
        $adminRole = Role::where(function($q) {
            $q->where('name', 'Super Admin')->orWhere('name', 'admin')->orWhere('name', 'Admin');
        })->where('guard_name', 'admin')->first();

        if ($adminRole) {
            $allAdminPerms = Permission::where('guard_name', 'admin')->pluck('name')->toArray();
            $adminRole->syncPermissions($allAdminPerms);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Toastr::success('Success', count($allPermissions) . ' System permissions synchronized successfully!' . ($createdCount > 0 ? " ($createdCount new added)" : ""));
        return redirect()->back();
    }
    
    public function create()
    {
        // ✅ Get all permissions for admin guard
        // Clear cache first to ensure fresh data
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Try multiple queries to debug
        $permissionQuery = Permission::where('guard_name', 'admin');
        $permissionCount = $permissionQuery->count();
        
        $permission = $permissionQuery->orderBy('name', 'ASC')->get();
        
        // Debug: Log permission count and first few permissions
        \Log::info('Role Create - Query Count: ' . $permissionCount);
        \Log::info('Role Create - Collection Count: ' . $permission->count());
        \Log::info('Role Create - First 5 Permissions: ' . $permission->take(5)->pluck('name')->implode(', '));
        
        // If no permissions found, try without guard filter
        if ($permission->isEmpty()) {
            \Log::warning('Role Create - No admin guard permissions found, trying all permissions');
            $permission = Permission::orderBy('name', 'ASC')->get();
            \Log::info('Role Create - All Permissions Count: ' . $permission->count());
        }
        
        // Ensure we have a collection (not null)
        if (!$permission) {
            $permission = collect([]);
        }
        
        // Debug: Add permission count to view
        return view('backEnd.roles.create',compact('permission'))->with('permission_count', $permission->count());
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name,NULL,id,guard_name,admin',
            'permission' => 'required',
        ]);
    
        // ✅ Create role with admin guard_name (default for admin panel)
        $role = Role::create([
            'name' => $request->input('name'),
            'guard_name' => 'admin'
        ]);
        
        // ✅ Process permissions (handles both IDs and names)
        $permissionInput = $request->input('permission', []);
        $permissions = $this->processPermissions($permissionInput);
        
        if (empty($permissions)) {
            Toastr::error('Error', 'No valid permissions found. Please check your permission selection.');
            return redirect()->back()->withInput();
        }
        
        // ✅ Sync permissions with error handling
        try {
            $role->syncPermissions($permissions);
        } catch (\Exception $e) {
            \Log::error('Permission sync error', [
                'role_id' => $role->id,
                'permissions' => $permissions,
                'error' => $e->getMessage()
            ]);
            
            // Try to sync again after clearing cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $role->syncPermissions($permissions);
        }
        
        // ✅ Clear permission cache after create
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        Toastr::success('Success','Data store successfully');
        return redirect()->route('roles.index');
    }
    
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
    
        return view('backEnd.roles.show',compact('role','rolePermissions'));
    }
    public function edit($id)
    {
        // ✅ Load role with permissions (eager load)
        $edit_data = Role::with('permissions')->findOrFail($id);
        
        // Clear cache first to ensure fresh data
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // ✅ Get all permissions matching the role's guard_name
        // If role doesn't have guard_name, default to 'admin'
        $guardName = $edit_data->guard_name ?? 'admin';
        $permission = Permission::where('guard_name', $guardName)
            ->orderBy('name', 'ASC')
            ->get();
        
        // Debug: Log permission count
        \Log::info('Role Edit - Role ID: ' . $id . ', Guard: ' . $guardName . ', Permissions Count: ' . $permission->count());
        
        return view('backEnd.roles.edit',compact('edit_data','permission'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
        $input = $request->except('hidden_id', 'permission');
        $update_data = Role::find($request->hidden_id);
        if (!$update_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $update_data->update($input);
    
        // ✅ Process permissions (handles both IDs and names)
        $permissionInput = $request->input('permission', []);
        $permissions = $this->processPermissions($permissionInput);
        
        if (empty($permissions)) {
            Toastr::error('Error', 'No valid permissions found. Please check your permission selection.');
            return redirect()->back()->withInput();
        }
        
        // ✅ Clear permission cache before sync to avoid conflicts
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // ✅ Sync permissions with error handling
        try {
            $update_data->syncPermissions($permissions);
        } catch (\Exception $e) {
            \Log::error('Permission sync error', [
                'role_id' => $update_data->id,
                'permissions' => $permissions,
                'error' => $e->getMessage()
            ]);
            
            // If duplicate error, manually sync using DB
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                DB::table('role_has_permissions')
                    ->where('role_id', $update_data->id)
                    ->delete();
                $update_data->syncPermissions($permissions);
            } else {
                throw $e;
            }
        }
        
        // ✅ Clear permission cache after update
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        Toastr::success('Success','Data update successfully');
        return redirect()->route('roles.index');
    }
    public function destroy(Request $request)
    {
        $delete_data = Role::find($request->hidden_id);
        if (!$delete_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
    
    /**
     * ✅ Helper method to process permission input (handles both IDs and names)
     */
    private function processPermissions($permissionInput)
    {
        $permissionNames = [];
        
        foreach ($permissionInput as $value) {
            if (is_numeric($value)) {
                // If numeric, treat as ID - check admin guard first, then any guard
                $permission = Permission::where('id', (int)$value)
                    ->where('guard_name', 'admin')
                    ->first();
                
                // If not found with admin guard, try without guard check (for compatibility)
                if (!$permission) {
                    $permission = Permission::where('id', (int)$value)->first();
                }
                
                if ($permission) {
                    $permissionNames[] = $permission->name;
                }
            } elseif (is_string($value) && !empty(trim($value))) {
                // If string, check if it's a valid permission name
                $permission = Permission::where('name', trim($value))
                    ->where('guard_name', 'admin')
                    ->first();
                
                // If not found with admin guard, try without guard check
                if (!$permission) {
                    $permission = Permission::where('name', trim($value))->first();
                }
                
                if ($permission) {
                    $permissionNames[] = $permission->name;
                }
            }
        }
        
        // Remove duplicates and return
        return array_values(array_unique($permissionNames));
    }
}
