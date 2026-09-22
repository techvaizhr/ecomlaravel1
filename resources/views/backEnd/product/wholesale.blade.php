@extends('backEnd.layouts.master')
@section('title', 'Wholesale Products')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

<style>
    .ws-manage-page {
        padding-bottom: 2.5rem;
    }
    
    /* Header Card */
    .ws-header-card {
        background: linear-gradient(135deg, #059669 0%, #10b981 50%, #0d9488 100%);
        border-radius: 16px;
        padding: 22px 26px;
        color: #fff;
        margin-bottom: 22px;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.25);
    }
    
    /* Stat Cards */
    .ws-stat-card {
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
    .ws-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }
    .ws-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .ws-stat-icon.emerald { background: #ecfdf5; color: #10b981; }
    .ws-stat-icon.teal { background: #ccfbf1; color: #0d9488; }
    .ws-stat-icon.blue { background: #eff6ff; color: #2563eb; }

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
    .ws-table-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .ws-toolbar {
        padding: 14px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .ws-table thead th {
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
    .ws-table tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .ws-table tbody tr:hover td {
        background: #f0fdf4;
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

    /* Tier Price Badges */
    .tier-badge {
        display: inline-block;
        padding: 2px 6px;
        font-size: 11px;
        font-weight: 600;
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        border-radius: 6px;
        margin: 1px;
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
        background: #059669;
        border-color: #059669;
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
    .dropdown-menu-prod .dropdown-item.text-danger:hover {
        background: #fef2f2;
        color: #dc2626 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid pt-3 ws-manage-page">
    
    {{-- Header Banner --}}
    <div class="ws-header-card d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-white"><i class="fe-layers me-2"></i> Wholesale Products Hub</h3>
            <p class="mb-0 text-white-50" style="font-size:14px;">পাইকারি মূল্যের টায়ার এবং বাল্ক অর্ডার বিক্রয়যোগ্য পণ্যসমূহ পরিচালনা করুন।</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('products.create') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-success shadow-sm">
                <i class="fe-plus-circle me-1"></i> Add Wholesale Product
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    @php
        $totalWholesale = $data->total();
        $activeOnPage = $data->getCollection()->where('status', 1)->count();
        $catsCount = $data->getCollection()->pluck('category_id')->filter()->unique()->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="ws-stat-card">
                <div class="ws-stat-icon emerald"><i class="fe-layers"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Total Wholesale Items</div>
                    <h4 class="mb-0 fw-bold">{{ $totalWholesale }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="ws-stat-card">
                <div class="ws-stat-icon teal"><i class="fe-check-circle"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Active Products</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $activeOnPage }} <small class="text-muted" style="font-size:12px;">(on page)</small></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="ws-stat-card">
                <div class="ws-stat-icon blue"><i class="fe-tag"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Categories Represented</div>
                    <h4 class="mb-0 fw-bold text-primary">{{ $catsCount }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Standardized Product Filter Bar --}}
    <div class="prod-filter-card">
        <form method="GET" action="{{ route('admin.products.wholesale') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                {{-- Search --}}
                <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-12">
                    <label class="prod-form-label">Search Product / Barcode</label>
                    <div class="position-relative">
                        <i class="fe-search position-absolute text-muted" style="left: 10px; top: 10px; font-size: 14px; pointer-events: none;"></i>
                        <input type="text" name="keyword" class="form-control form-control-sm ps-4" placeholder="Search by name, barcode..." value="{{ request('keyword') }}" style="padding-left: 32px !important; height: 36px; border-radius: 8px;">
                    </div>
                </div>

                @if(isset($categories) && $categories->count() > 0)
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-3 col-6">
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

                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-3 col-6">
                    <label class="prod-form-label">Status</label>
                    <select name="status" class="form-select form-select-sm" style="height: 36px; border-radius: 8px;">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Global Smart Date Filter Integration --}}
                <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-12">
                    <label class="prod-form-label">Date Created</label>
                    @include('backEnd.layouts.partials.smart_date_filter')
                </div>

                {{-- Per Page & Action Buttons in single aligned group --}}
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-12">
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
                                <button type="submit" class="btn btn-sm btn-success flex-grow-1 fw-semibold d-flex align-items-center justify-content-center shadow-sm" style="height: 36px; border-radius: 8px; font-size: 13px; background:#059669; border-color:#059669;" title="Filter Products">
                                    <i class="fe-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.products.wholesale') }}" class="btn btn-light btn-sm border d-flex align-items-center justify-content-center" style="height: 36px; width: 36px; border-radius: 8px;" title="Reset Filters">
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
    <div class="ws-table-card">
        {{-- Bulk Actions Toolbar --}}
        <div class="ws-toolbar">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small fw-bold text-muted me-1">Bulk Actions:</span>
                <button type="button" data-url="{{ route('products.update_status') }}" data-status="1" class="btn btn-xs btn-outline-success rounded-pill px-2.5 update_status">
                    <i class="fe-check me-1"></i> Active
                </button>
                <button type="button" data-url="{{ route('products.update_status') }}" data-status="0" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 update_status">
                    <i class="fe-x me-1"></i> Inactive
                </button>
                <button type="button" data-url="{{ route('products.update_deals') }}" data-status="1" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 hotdeal_update">
                    <i class="fe-tag me-1"></i> Set Hot Deal
                </button>
                <button type="button" data-url="{{ route('products.update_deals') }}" data-status="0" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 hotdeal_update">
                    <i class="fe-x-circle me-1"></i> Remove Deal
                </button>
            </div>
            
            <div class="small text-muted fw-semibold">
                Showing {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} of {{ $data->total() }} Wholesale Products
            </div>
        </div>

        <div class="table-responsive">
            <table class="table ws-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 38px;">
                            <div class="form-check mb-0">
                                <input type="checkbox" class="form-check-input checkall" id="parentCheck">
                            </div>
                        </th>
                        <th style="width: 50px;">SL</th>
                        <th style="width: 65px;">Image</th>
                        <th>Product Details</th>
                        <th>Category</th>
                        <th>Wholesale Tiers</th>
                        <th>Base Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $key => $product)
                    <tr>
                        <td>
                            <div class="form-check mb-0">
                                <input type="checkbox" class="form-check-input checkbox" value="{{ $product->id }}">
                            </div>
                        </td>
                        <td class="fw-bold text-muted">{{ $data->firstItem() + $key }}</td>
                        <td>
                            <img src="{{ asset($product->image ? $product->image->image : 'public/uploads/default/no-image.png') }}" class="product-img" alt="">
                        </td>
                        <td>
                            <div class="fw-bold text-dark" style="max-width:250px;">
                                <a href="{{ route('products.edit', $product->id) }}" class="text-dark text-decoration-none">
                                    {{ Str::limit($product->name, 45) }}
                                </a>
                            </div>
                            <small class="text-muted" style="font-family:monospace;font-size:11px;">
                                Vendor: {{ $product->vendor ? (optional($product->vendor)->shop_name ?? optional($product->vendor)->name) : 'Inhouse' }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-soft-secondary text-secondary rounded-pill px-2.5 py-1">
                                {{ optional($product->category)->name ?? 'Uncategorized' }}
                            </span>
                        </td>
                        <td>
                            @if($product->wholesalePrices && $product->wholesalePrices->count() > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($product->wholesalePrices->take(2) as $tier)
                                        <span class="tier-badge" title="Min Qty: {{ $tier->min_quantity }}">
                                            {{ $tier->min_quantity }}+ pcs: ৳{{ number_format($tier->wholesale_price, 2) }}
                                        </span>
                                    @endforeach
                                    @if($product->wholesalePrices->count() > 2)
                                        <span class="badge bg-light text-muted border" style="font-size:10px;">+{{ $product->wholesalePrices->count() - 2 }} more</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small">No Tiers Configured</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">৳{{ number_format($product->new_price, 2) }}</div>
                            @if($product->old_price)
                                <small class="text-muted text-decoration-line-through">৳{{ number_format($product->old_price, 2) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($product->stock <= 0)
                                <span class="badge bg-soft-danger text-danger rounded-pill px-2.5 py-1 fw-bold">Out of Stock</span>
                            @else
                                <span class="badge bg-soft-success text-success rounded-pill px-2.5 py-1 fw-bold">{{ $product->stock }} in stock</span>
                            @endif
                        </td>
                        <td>
                            @if($product->status == 1)
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill fw-bold" style="font-size:11px;">Active</span>
                            @else
                                <span class="badge bg-soft-danger text-danger px-2.5 py-1 rounded-pill fw-bold" style="font-size:11px;">Inactive</span>
                            @endif
                        </td>

                        {{-- 3-Dot Action Dropdown (Single Unified List, No Dividers) --}}
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-3dot" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                    <i class="fe-more-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-prod">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('products.edit', $product->id) }}">
                                            <i class="fe-edit-2 text-primary"></i> Edit Product & Tiers
                                        </a>
                                    </li>
                                    <li>
                                        @if($product->status == 1)
                                            <form method="post" action="{{ route('products.inactive') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{ $product->id }}" name="hidden_id">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fe-eye-off text-warning"></i> Deactivate
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{ route('products.active') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{ $product->id }}" name="hidden_id">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fe-check-circle text-success"></i> Activate
                                                </button>
                                            </form>
                                        @endif
                                    </li>
                                    <li>
                                        <form method="post" action="{{ route('products.destroy') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" value="{{ $product->id }}" name="hidden_id">
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
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="fe-layers d-block mb-2 text-success" style="font-size:2rem;opacity:.5;"></i>
                            কোনো হোলসেল পণ্য পাওয়া যায়নি। ফিল্টার পরিবর্তন করুন বা নতুন পণ্য যোগ করুন।
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

        $(".checkall").on('change', function() {
            $(".checkbox").prop('checked', $(this).is(":checked"));
        });

        $(document).on('click', '.update_status, .hotdeal_update', function() {
            var url = $(this).attr('data-url');
            var status = $(this).attr('data-status');
            var product_ids = [];
            $(".checkbox:checked").each(function() {
                product_ids.push($(this).val());
            });

            if (product_ids.length === 0) {
                toastr.warning("অনুগ্রহ করে অন্তত একটি পণ্য নির্বাচন করুন!");
                return;
            }

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    product_ids: product_ids,
                    status: status
                },
                success: function(res) {
                    toastr.success(res.message || "Updated successfully!");
                    setTimeout(function() { location.reload(); }, 600);
                }
            });
        });
    });
</script>
@endsection
