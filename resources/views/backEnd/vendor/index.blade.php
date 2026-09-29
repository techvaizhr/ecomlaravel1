@extends('backEnd.layouts.master')
@section('title', 'Vendor Management')

@section('css')
<style>
    /* Premium Vendor Management Design System */
    .vendor-header-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 22px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
        position: relative;
        overflow: hidden;
    }
    .vendor-header-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.25) 0%, rgba(99, 102, 241, 0.1) 60%, transparent 80%);
        border-radius: 50%;
        pointer-events: none;
    }

    .metric-badge-box {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 12px;
        padding: 10px 16px;
        text-align: center;
        min-width: 100px;
        transition: all 0.2s ease;
    }
    .metric-badge-box:hover {
        background: rgba(255, 255, 255, 0.14);
        transform: translateY(-2px);
    }
    .metric-badge-box h3 {
        color: #ffffff;
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 1px;
    }
    .metric-badge-box span {
        color: rgba(255, 255, 255, 0.75);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        font-weight: 600;
    }

    /* Filter Card */
    .filter-card-modern {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
        margin-bottom: 20px;
        padding: 18px 22px;
    }
    .form-label-modern {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .form-control-modern, .form-select-modern {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        padding: 8px 12px;
        font-size: 13.5px;
        color: #0f172a;
        transition: all 0.2s;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        background: #ffffff;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.12);
        outline: none;
    }

    /* Table & Card Container */
    .table-card-modern {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: visible;
    }
    .table-modern {
        margin-bottom: 0;
        vertical-align: middle;
    }
    .table-modern thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 14px 16px;
        white-space: nowrap;
    }
    .table-modern tbody td {
        padding: 13px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
        color: #1e293b;
    }
    .table-modern tbody tr:hover {
        background: #fafcff;
    }

    /* Vendor Avatars */
    .vendor-avatar {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        object-fit: cover;
        border: 1.5px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .vendor-avatar-initials {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0ea5e9, #6366f1);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(14, 165, 233, 0.25);
    }

    /* Status Pills */
    .badge-soft-verified { background: #dcfce7; color: #15803d; font-weight: 600; padding: 4px 10px; border-radius: 20px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px; }
    .badge-soft-pending { background: #fef3c7; color: #b45309; font-weight: 600; padding: 4px 10px; border-radius: 20px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px; }
    .badge-soft-rejected { background: #fee2e2; color: #b91c1c; font-weight: 600; padding: 4px 10px; border-radius: 20px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px; }

    /* 3-Dot Dropdown Action Styling */
    .vendor-action-dots {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }
    .vendor-action-dots:hover, .vendor-action-dots:focus, .dropdown.show .vendor-action-dots {
        background: #0ea5e9;
        color: #ffffff;
        border-color: #0ea5e9;
        box-shadow: 0 3px 8px rgba(14, 165, 233, 0.3);
    }
    .dropdown-menu-modern {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        padding: 6px 0;
        min-width: 220px;
    }
    .dropdown-menu-modern .dropdown-item {
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .dropdown-menu-modern .dropdown-item:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .dropdown-menu-modern .dropdown-item.login-as-item {
        background: #f0fdf4;
        color: #166534;
        font-weight: 700;
    }
    .dropdown-menu-modern .dropdown-item.login-as-item:hover {
        background: #dcfce7;
        color: #15803d;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER CARD --}}
    <div class="vendor-header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="text-white mb-1 fw-bold d-flex align-items-center gap-2">
                    <i class="fe-shopping-bag text-info"></i> All Vendors Management
                </h4>
                <p class="mb-0 text-white-50 font-size-13">
                    Multi-vendor accounts, shops, commission settings, product counts & 1-click vendor login.
                </p>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="metric-badge-box">
                    <h3>{{ $stats['total'] }}</h3>
                    <span>Total</span>
                </div>
                <div class="metric-badge-box">
                    <h3>{{ $stats['active'] }}</h3>
                    <span>Active</span>
                </div>
                <div class="metric-badge-box">
                    <h3>{{ $stats['verified'] }}</h3>
                    <span>Verified</span>
                </div>
                <div class="metric-badge-box">
                    <h3>{{ $stats['pending'] }}</h3>
                    <span>Pending KYC</span>
                </div>
                <a href="{{ route('admin.vendor.verification.index') }}" class="btn btn-warning rounded-pill px-3 shadow-sm font-size-13 fw-bold text-dark">
                    <i class="fe-shield me-1"></i> Verifications ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('admin.vendor.withdrawals.index') }}" class="btn btn-outline-light rounded-pill px-3 shadow-sm font-size-13 fw-bold">
                    <i class="fe-dollar-sign me-1"></i> Withdrawals
                </a>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-card-modern">
        <form method="GET" action="{{ route('admin.vendors.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label-modern">Search Keyword</label>
                    <input type="text" name="keyword" class="form-control form-control-modern" 
                           placeholder="Shop, owner, email, phone..." value="{{ request('keyword') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Account Status</label>
                    <select name="status" class="form-select form-select-modern">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive Only</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Verification</label>
                    <select name="verification_status" class="form-select form-select-modern">
                        <option value="">All Verification</option>
                        <option value="approved" {{ request('verification_status') === 'approved' ? 'selected' : '' }}>Verified</option>
                        <option value="pending" {{ request('verification_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('verification_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Sort By</label>
                    <select name="sort_by" class="form-select form-select-modern">
                        <option value="latest" {{ request('sort_by') === 'latest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort_by') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="products_high" {{ request('sort_by') === 'products_high' ? 'selected' : '' }}>Most Products</option>
                        <option value="name_asc" {{ request('sort_by') === 'name_asc' ? 'selected' : '' }}>Shop Name (A-Z)</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label-modern">Per Page</label>
                    <select name="per_page" class="form-select form-select-modern">
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary rounded-3 w-100 fw-bold font-size-13 py-2">
                            <i class="fe-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['keyword', 'status', 'verification_status', 'sort_by', 'per_page']))
                            <a href="{{ route('admin.vendors.index') }}" class="btn btn-light border rounded-3 px-3 py-2" title="Reset Filters">
                                <i class="fe-rotate-ccw"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- VENDORS TABLE --}}
    <div class="table-card-modern">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Shop & Vendor</th>
                        <th>Owner & Contact</th>
                        <th>Products</th>
                        <th>Balance</th>
                        <th>Commission</th>
                        <th>KYC Status</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                    <tr>
                        <td class="text-muted fw-semibold">
                            {{ $loop->iteration + ($vendors->currentPage() - 1) * $vendors->perPage() }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                @if($vendor->logo)
                                    <img src="{{ asset($vendor->logo) }}" alt="{{ $vendor->shop_name }}" class="vendor-avatar">
                                @else
                                    <div class="vendor-avatar-initials">
                                        {{ strtoupper(substr($vendor->shop_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark font-size-14">{{ $vendor->shop_name }}</div>
                                    <div class="d-flex align-items-center gap-1 mt-0.5">
                                        <span class="badge bg-light text-secondary border font-size-11" style="font-family: monospace;">ID: #{{ $vendor->id }}</span>
                                        @if($vendor->created_at)
                                            <small class="text-muted font-size-11">{{ $vendor->created_at->format('d M, Y') }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $vendor->owner_name }}</div>
                            <div class="text-muted font-size-12 mt-0.5">
                                <i class="fe-phone me-1 text-primary"></i> <a href="tel:{{ $vendor->phone }}" class="text-secondary text-decoration-none">{{ $vendor->phone }}</a>
                            </div>
                            @if($vendor->email)
                            <div class="text-muted font-size-12">
                                <i class="fe-mail me-1 text-info"></i> {{ $vendor->email }}
                            </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('products.index', ['vendor_id' => $vendor->id]) }}" class="badge bg-soft-primary text-primary border border-primary-subtle rounded-pill px-2.5 py-1 font-size-12 text-decoration-none" title="View Vendor Products">
                                <i class="fe-package me-1"></i> {{ $vendor->products_count ?? 0 }} Items
                            </a>
                        </td>
                        <td>
                            <span class="fw-bold text-success font-size-14">
                                ৳{{ number_format($vendor->wallet ? $vendor->wallet->balance : 0, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-soft-info text-info rounded-pill px-2.5 py-1 font-size-12 fw-semibold">
                                {{ $vendor->commission_rate }}%
                            </span>
                        </td>
                        <td>
                            @if($vendor->verification_status == 'approved')
                                <span class="badge-soft-verified"><i class="fe-check-circle"></i> Verified</span>
                            @elseif($vendor->verification_status == 'rejected')
                                <span class="badge-soft-rejected"><i class="fe-x-circle"></i> Rejected</span>
                            @else
                                <a href="{{ route('admin.vendor.verification.show', $vendor->id) }}" class="badge-soft-pending text-decoration-none" title="Review KYC Documents">
                                    <i class="fe-clock"></i> Pending KYC
                                </a>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.vendors.toggle-status', $vendor->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="badge rounded-pill border-0 px-2.5 py-1 font-size-12 {{ $vendor->status == 1 ? 'bg-success text-white' : 'bg-danger text-white' }}" style="cursor: pointer;" title="Click to toggle active/inactive status">
                                    {{ $vendor->status == 1 ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            {{-- 3-DOT ACTION DROPDOWN --}}
                            <div class="dropdown d-inline-block">
                                <button class="btn vendor-action-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Vendor Actions">
                                    <i class="fe-more-vertical font-size-16"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-modern">
                                    {{-- 1. LOGIN AS VENDOR --}}
                                    <li>
                                        <a class="dropdown-item login-as-item" href="{{ route('admin.vendors.login-as', $vendor->id) }}" target="_blank" onclick="return confirm('আপনি কি {{ $vendor->shop_name }} ({$vendor->owner_name}) হিসেবে ভেন্ডর প্যানেলে লগইন করতে চান?')">
                                            <i class="fe-log-in text-success font-size-15"></i>
                                            <span>Login as Vendor</span>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>

                                    {{-- 2. VIEW KYC & PROFILE --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.vendor.verification.show', $vendor->id) }}">
                                            <i class="fe-shield text-info font-size-15"></i>
                                            <span>View KYC & Profile</span>
                                        </a>
                                    </li>

                                    {{-- 3. VIEW PRODUCTS --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('products.index', ['vendor_id' => $vendor->id]) }}">
                                            <i class="fe-package text-primary font-size-15"></i>
                                            <span>Manage Products ({{ $vendor->products_count ?? 0 }})</span>
                                        </a>
                                    </li>

                                    {{-- 4. EDIT VENDOR --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.vendors.edit', $vendor->id) }}">
                                            <i class="fe-edit text-warning font-size-15"></i>
                                            <span>Edit Vendor & Commission</span>
                                        </a>
                                    </li>

                                    {{-- 5. TOGGLE STATUS --}}
                                    <li>
                                        <form method="POST" action="{{ route('admin.vendors.toggle-status', $vendor->id) }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item border-0 bg-transparent">
                                                <i class="fe-power {{ $vendor->status == 1 ? 'text-danger' : 'text-success' }} font-size-15"></i>
                                                <span>{{ $vendor->status == 1 ? 'Deactivate Account' : 'Activate Account' }}</span>
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>

                                    {{-- 6. DELETE VENDOR --}}
                                    <li>
                                        <form method="POST" action="{{ route('admin.vendors.destroy', $vendor->id) }}" onsubmit="return confirm('Are you sure you want to delete vendor ({{ $vendor->shop_name }})? All related data will be affected.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger border-0 bg-transparent">
                                                <i class="fe-trash-2 text-danger font-size-15"></i>
                                                <span>Delete Vendor</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fe-shopping-bag font-size-36 d-block mb-2 text-slate-300"></i>
                            <p class="mb-0 font-size-14 fw-semibold">No vendors found matching your criteria.</p>
                            <small class="text-muted">Try changing your search keywords or resetting filters.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages() || $vendors->total() > 0)
        <div class="p-3 bg-light border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="text-muted font-size-13">
                Showing <strong>{{ $vendors->firstItem() ?? 0 }}</strong> to <strong>{{ $vendors->lastItem() ?? 0 }}</strong> of <strong>{{ $vendors->total() }}</strong> Vendors
            </div>
            <div>
                {{ $vendors->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
