<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageOptimizer;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors with comprehensive filtering, sorting, and pagination.
     */
    public function index(Request $request)
    {
        $query = Vendor::with(['wallet'])->withCount('products');
        
        // Keyword Search (Shop Name, Owner Name, Email, Phone)
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function($q) use ($keyword) {
                $q->where('shop_name', 'like', "%{$keyword}%")
                  ->orWhere('owner_name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', (int)$request->status);
        }

        // Verification Status Filter
        if ($request->filled('verification_status')) {
            if ($request->verification_status === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('verification_status')
                      ->orWhere('verification_status', 'pending')
                      ->orWhereNotIn('verification_status', ['approved', 'rejected']);
                });
            } else {
                $query->where('verification_status', $request->verification_status);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->oldest('id');
                break;
            case 'products_high':
                $query->orderByDesc('products_count');
                break;
            case 'name_asc':
                $query->orderBy('shop_name', 'asc');
                break;
            default:
                $query->latest('id');
                break;
        }

        // Per-page logic
        $perPage = $request->get('per_page', 20);
        if ($perPage === 'all' || $perPage == -1) {
            $perPage = max(Vendor::count(), 1);
        } else {
            $perPage = max((int)$perPage, 10);
        }

        $vendors = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total'    => Vendor::count(),
            'active'   => Vendor::where('status', 1)->count(),
            'inactive' => Vendor::where('status', 0)->count(),
            'verified' => Vendor::where('verification_status', 'approved')->count(),
            'pending'  => Vendor::where(function ($q) {
                $q->whereNull('verification_status')
                    ->orWhere('verification_status', 'pending')
                    ->orWhereNotIn('verification_status', ['approved', 'rejected']);
            })->count(),
            'rejected' => Vendor::where('verification_status', 'rejected')->count(),
        ];

        return view('backEnd.vendor.index', compact('vendors', 'stats'));
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        $user = User::where('vendor_id', $id)->first();
        
        return view('backEnd.vendor.edit', compact('vendor', 'user'));
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'shop_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email,' . $request->hidden_id,
            'phone' => 'required|string|max:55|unique:vendors,phone,' . $request->hidden_id,
            'slug' => 'required|string|max:255|unique:vendors,slug,' . $request->hidden_id,
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $vendor = Vendor::findOrFail($request->hidden_id);
        $input = $request->except('hidden_id', 'password', 'logo', 'banner');
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $input['logo'] = ImageOptimizer::storeLogo($request->file('logo'), 'public/uploads/vendor/logo/');
            if ($vendor->logo && File::exists($vendor->logo)) {
                File::delete($vendor->logo);
            }
        } else {
            $input['logo'] = $vendor->logo;
        }

        if ($request->hasFile('banner')) {
            $input['banner'] = ImageOptimizer::storeBanner($request->file('banner'), 'public/uploads/vendor/banner/');
            if ($vendor->banner && File::exists($vendor->banner)) {
                File::delete($vendor->banner);
            }
        } else {
            $input['banner'] = $vendor->banner;
        }

        $input['status'] = $request->status ? 1 : 0;
        $vendor->update($input);

        // Update user account if exists
        $user = User::where('vendor_id', $vendor->id)->first();
        if ($user) {
            $userInput = [
                'name' => $request->owner_name,
                'email' => $request->email,
            ];
            
            if (!empty($request->password)) {
                $userInput['password'] = Hash::make($request->password);
            }
            
            $user->update($userInput);
        }

        Toastr::success('Vendor updated successfully', 'Success');
        return redirect()->route('admin.vendors.index');
    }

    /**
     * Remove the specified vendor.
     */
    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        
        // Delete logo and banner
        if ($vendor->logo && File::exists($vendor->logo)) {
            File::delete($vendor->logo);
        }
        if ($vendor->banner && File::exists($vendor->banner)) {
            File::delete($vendor->banner);
        }
        
        // Delete associated user
        $user = User::where('vendor_id', $id)->first();
        if ($user) {
            $user->delete();
        }
        
        // Delete vendor
        $vendor->delete();

        Toastr::success('Vendor deleted successfully', 'Success');
        return redirect()->route('admin.vendors.index');
    }

    /**
     * Toggle vendor status.
     */
    public function toggleStatus($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->status = $vendor->status == 1 ? 0 : 1;
        $vendor->save();

        Toastr::success('Vendor status updated successfully', 'Success');
        return redirect()->back();
    }

    /**
     * Approve vendor verification
     */
    public function approveVerification(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);
        
        $vendor->verification_status = 'approved';
        $vendor->verified_at = now();
        $vendor->verification_note = $request->admin_note ?? null;
        $vendor->save();

        Toastr::success('Vendor verification approved successfully', 'Success');
        return redirect()->back();
    }

    /**
     * Reject vendor verification
     */
    public function rejectVerification(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $vendor = Vendor::findOrFail($id);
        
        $vendor->verification_status = 'rejected';
        $vendor->verification_note = $request->rejection_reason;
        $vendor->save();

        Toastr::success('Vendor verification rejected', 'Success');
        return redirect()->back();
    }

    /**
     * Impersonate / Login as vendor directly from admin panel.
     */
    public function loginAsVendor($id)
    {
        $vendor = Vendor::findOrFail($id);

        // Find associated user account
        $user = User::where('vendor_id', $vendor->id)->first();
        if (!$user && $vendor->email) {
            $user = User::where('email', $vendor->email)->first();
            if ($user) {
                $user->vendor_id = $vendor->id;
                $user->shop_name = $vendor->shop_name;
                $user->save();
            }
        }

        // If user record doesn't exist, automatically generate one for this vendor
        if (!$user) {
            $user = User::create([
                'name'      => $vendor->owner_name ?: $vendor->shop_name,
                'email'     => $vendor->email ?: ('vendor_' . $vendor->id . '@shop.local'),
                'password'  => Hash::make('Vendor@' . $vendor->id . '#' . rand(1000, 9999)),
                'status'    => 1,
                'vendor_id' => $vendor->id,
                'shop_name' => $vendor->shop_name,
            ]);
        }

        // Ensure user has vendor role
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $role = Role::firstOrCreate(
                ['name' => 'vendor', 'guard_name' => 'admin'],
                ['name' => 'vendor', 'guard_name' => 'admin']
            );
            if (!$user->hasRole('vendor')) {
                $user->assignRole($role);
            }
        }

        // Track who impersonated
        $currentAdmin = Auth::guard('admin')->user();
        if ($currentAdmin && !$currentAdmin->vendor_id) {
            session(['admin_impersonator_id' => $currentAdmin->id]);
        }

        // Log in as vendor user
        Auth::guard('admin')->login($user);

        Toastr::success("স্বাগতম! আপনি {$vendor->shop_name} ({$vendor->owner_name})-এর ভেন্ডর ড্যাশবোর্ডে লগইন করেছেন।", "Login as Vendor");
        return redirect()->route('vendor.dashboard');
    }

    /**
     * Switch back to Admin Panel from Vendor Impersonation.
     */
    public function switchBackToAdmin()
    {
        $adminId = session('admin_impersonator_id');
        if ($adminId) {
            $adminUser = User::find($adminId);
            if ($adminUser) {
                Auth::guard('admin')->login($adminUser);
                session()->forget('admin_impersonator_id');
                Toastr::success('অ্যাডমিন প্যানেলে ফিরে এসেছেন।', 'Welcome Back');
                return redirect()->route('admin.vendors.index');
            }
        }
        return redirect()->route('admin.dashboard');
    }
}
