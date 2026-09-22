@extends('backEnd.layouts.master')
@section('title', 'Pending Products Approval')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

<style>
    .pending-manage-page {
        padding-bottom: 2.5rem;
    }
    
    /* Header Card */
    .pending-header-card {
        background: linear-gradient(135deg, #d97706 0%, #f59e0b 50%, #ea580c 100%);
        border-radius: 16px;
        padding: 22px 26px;
        color: #fff;
        margin-bottom: 22px;
        box-shadow: 0 10px 25px rgba(217, 119, 6, 0.25);
    }
    
    /* Stat Cards */
    .pending-stat-card {
        background: #fff;
        border-radius: 14px;
        padding: 16px 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .pending-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }
    .pending-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .pending-stat-icon.amber { background: #fffbeb; color: #d97706; }
    .pending-stat-icon.blue { background: #eff6ff; color: #2563eb; }

    /* Filter Card */
    .filter-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
        margin-bottom: 20px;
        padding: 18px 20px;
    }

    /* Main Table Card */
    .pending-table-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .pending-table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 12px 14px;
        white-space: nowrap;
        vertical-align: middle;
    }
    .pending-table tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .pending-table tbody tr:hover td {
        background: #fffbeb;
    }

    /* Product Thumbnail */
    .product-img {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        background: #f8fafc;
    }

    /* 3-Dot Dropdown Menu */
    .btn-3dot {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .btn-3dot:hover, .btn-3dot[aria-expanded="true"] {
        background: #d97706;
        border-color: #d97706;
        color: #fff;
        transform: scale(1.05);
    }
    .dropdown-menu-prod {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.12);
        padding: 6px;
        min-width: 175px;
    }
    .dropdown-menu-prod .dropdown-item {
        border-radius: 8px;
        padding: 7px 12px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
    }
    .dropdown-menu-prod .dropdown-item:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
</style>
@endsection

@section('content')
<div class="container-fluid pt-3 pending-manage-page">
    
    {{-- Header Banner --}}
    <div class="pending-header-card d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-white"><i class="fe-clock me-2"></i> Pending Product Approvals</h3>
            <p class="mb-0 text-white-50" style="font-size:14px;">ভেন্ডরদের আপলোড করা পণ্য যাচাই-বাছাই ও দ্রুত অনুমোদন/বাতিল করুন।</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                <i class="fe-arrow-left me-1"></i> All Vendor Products
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    @php
        $totalPending = $data->total();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-sm-6">
            <div class="pending-stat-card">
                <div class="pending-stat-icon amber"><i class="fe-clock"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Waiting for Approval</div>
                    <h4 class="mb-0 fw-bold text-warning">{{ $totalPending }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6">
            <div class="pending-stat-card">
                <div class="pending-stat-icon blue"><i class="fe-users"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Active Vendors on Page</div>
                    <h4 class="mb-0 fw-bold text-primary">{{ $data->pluck('vendor_id')->filter()->unique()->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Standardized Product Filter Bar --}}
    <div class="prod-filter-card">
        <form method="GET" action="{{ route('products.pending') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                {{-- Search --}}
                <div class="col-xxl-4 col-xl-3 col-lg-3 col-md-6 col-12">
                    <label class="prod-form-label">Search Product / Barcode</label>
                    <div class="position-relative">
                        <i class="fe-search position-absolute text-muted" style="left: 10px; top: 10px; font-size: 14px; pointer-events: none;"></i>
                        <input type="text" name="keyword" class="form-control form-control-sm ps-4" placeholder="Search by name, barcode..." value="{{ request('keyword') }}" style="padding-left: 32px !important; height: 36px; border-radius: 8px;">
                    </div>
                </div>

                @if(isset($categories) && $categories->count() > 0)
                <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-3 col-6">
                    <label class="prod-form-label">Category</label>
                    <select name="category_id" class="form-select form-select-sm" style="height: 36px; border-radius: 8px;">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Global Smart Date Filter Integration --}}
                <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-12">
                    <label class="prod-form-label">Submission Date</label>
                    @include('backEnd.layouts.partials.smart_date_filter')
                </div>

                {{-- Per Page & Action Buttons in single aligned group --}}
                <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-6 col-12">
                    <div class="d-flex align-items-end gap-1">
                        <div style="min-width: 65px; width: 65px;">
                            <label class="prod-form-label">Per Page</label>
                            <select name="per_page" class="form-select form-select-sm px-1 text-center" style="height: 36px; border-radius: 8px;" onchange="document.getElementById('filterForm').submit();">
                                @foreach([10, 15, 25, 50, 100, 200] as $size)
                                    <option value="{{ $size }}" {{ (request('per_page', 25) == $size) ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <label class="prod-form-label d-none d-md-block">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-warning text-dark fw-bold btn-sm flex-grow-1 d-flex align-items-center justify-content-center shadow-sm" style="height: 36px; border-radius: 8px; font-size: 13px;" title="Filter Products">
                                    <i class="fe-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('products.pending') }}" class="btn btn-light btn-sm border d-flex align-items-center justify-content-center" style="height: 36px; width: 36px; border-radius: 8px;" title="Reset Filters">
                                    <i class="fe-rotate-ccw"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Main Table Card --}}
    <div class="pending-table-card">
        <div class="table-responsive">
            <table class="table pending-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">SL</th>
                        <th style="width: 65px;">Image</th>
                        <th>Product Details</th>
                        <th>Vendor / Shop</th>
                        <th>Category</th>
                        <th>Price & Stock</th>
                        <th>Submission Date</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $key => $value)
                    <tr>
                        <td class="fw-bold text-muted">{{ $data->firstItem() + $key }}</td>
                        <td>
                            <img src="{{ asset($value->image ? $value->image->image : 'public/uploads/default/no-image.png') }}" class="product-img" alt="">
                        </td>
                        <td>
                            <div class="fw-bold text-dark" style="max-width:240px;">
                                <a href="{{ route('products.edit', $value->id) }}" class="text-dark text-decoration-none">
                                    {{ Str::limit($value->name, 45) }}
                                </a>
                            </div>
                            <span class="badge {{ $value->product_type === 'digital' ? 'bg-soft-purple text-purple' : 'bg-soft-info text-info' }} mt-1" style="font-size:10px;">
                                {{ $value->product_type === 'digital' ? '💾 Digital' : '📦 Physical' }}
                            </span>
                        </td>
                        <td>
                            @if($value->vendor)
                                <div class="fw-bold text-dark"><i class="fe-user text-primary me-1"></i>{{ $value->vendor->shop_name ?? $value->vendor->name }}</div>
                                <small class="text-muted">Vendor ID: #{{ $value->vendor->id }}</small>
                            @else
                                <span class="badge bg-soft-secondary text-secondary">Inhouse / Admin</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-soft-secondary text-secondary rounded-pill px-2.5 py-1">
                                {{ $value->category ? $value->category->name : 'Uncategorized' }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">৳{{ number_format($value->new_price, 2) }}</div>
                            <small class="text-muted">Stock: {{ $value->stock }}</small>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $value->created_at ? $value->created_at->format('d M, Y') : 'N/A' }}</div>
                            <small class="text-muted">{{ $value->created_at ? $value->created_at->format('h:i A') : '' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-soft-warning text-warning px-2.5 py-1 rounded-pill fw-bold" style="font-size:11px;">
                                <i class="fe-clock me-1"></i> Pending
                            </span>
                        </td>

                        {{-- 3-Dot Action Dropdown (Single Unified List, No Dividers) --}}
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-3dot" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                    <i class="fe-more-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-prod">
                                    <li>
                                        <form method="post" action="{{ route('products.approve') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $value->id }}">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fe-check-circle text-success"></i> Approve Product
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="post" action="{{ route('products.reject') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $value->id }}">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fe-x-circle text-warning"></i> Reject Product
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('products.edit', $value->id) }}">
                                            <i class="fe-edit-2 text-primary"></i> Review & Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form method="post" action="{{ route('products.destroy') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                            <button type="submit" class="dropdown-item text-danger delete-confirm">
                                                <i class="fe-trash-2"></i> Delete Product
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
                            <i class="fe-check-circle d-block mb-2 text-success" style="font-size:2rem;opacity:.5;"></i>
                            কোনো পেন্ডিং পণ্য নেই! সব পণ্য অনুমোদিত আছে।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Foot --}}
        <div class="p-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span class="text-muted small">
                Showing {{ $data->firstItem() ?? 0 }} to {{ $data->lastItem() ?? 0 }} of {{ $data->total() }} entries
            </span>
            <div class="mb-0">
                {{ $data->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd')}}/assets/libs/select2/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });
    });
</script>
@endsection