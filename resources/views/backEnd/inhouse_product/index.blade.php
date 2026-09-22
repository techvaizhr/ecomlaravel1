@extends('backEnd.layouts.master')
@section('title', 'Inhouse Products')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

<style>
    .prod-manage-page {
        padding-bottom: 2.5rem;
    }
    
    /* Header Card */
    .prod-header-card {
        background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
        border-radius: 16px;
        padding: 22px 26px;
        color: #fff;
        margin-bottom: 22px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.2);
    }
    
    /* Stat Cards */
    .prod-stat-card {
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
    .prod-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }
    .prod-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .prod-stat-icon.indigo { background: #eef2ff; color: #4f46e5; }
    .prod-stat-icon.emerald { background: #ecfdf5; color: #10b981; }
    .prod-stat-icon.rose { background: #fff1f2; color: #f43f5e; }

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
    .prod-table-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .prod-toolbar {
        padding: 14px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .prod-table thead th {
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
    .prod-table tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .prod-table tbody tr:hover td {
        background: #f8fafc;
    }

    /* Product Thumbnail */
    .prod-thumb {
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
        background: #4f46e5;
        border-color: #4f46e5;
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
<div class="container-fluid pt-3 prod-manage-page">
    
    {{-- Header Banner --}}
    <div class="prod-header-card d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-white"><i class="fe-package me-2"></i> Inhouse Products</h3>
            <p class="mb-0 text-white-50" style="font-size:14px;">নিজের স্টক এবং সরাসরি নিজস্ব ইনহাউজ পণ্যসমূহ পরিচালনা করুন।</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" style="background:#4f46e5;border-color:#4f46e5;">
                <i class="fe-plus-circle me-1"></i> Add New Product
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    @php
        $totalInhouse = $data->total();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="prod-stat-card">
                <div class="prod-stat-icon indigo"><i class="fe-package"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Total Inhouse Items</div>
                    <h4 class="mb-0 fw-bold">{{ $totalInhouse }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="prod-stat-card">
                <div class="prod-stat-icon emerald"><i class="fe-check-circle"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">In-Stock Products</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $data->where('stock', '>', 0)->count() }} <small class="text-muted" style="font-size:12px;">(on page)</small></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="prod-stat-card">
                <div class="prod-stat-icon rose"><i class="fe-alert-triangle"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Out of Stock Alert</div>
                    <h4 class="mb-0 fw-bold text-danger">{{ $data->where('stock', '<=', 0)->count() }} <small class="text-muted" style="font-size:12px;">(on page)</small></h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Standardized Product Filter Bar --}}
    <div class="prod-filter-card">
        <form method="GET" action="{{ route('inhouse.products.index') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                {{-- Search --}}
                <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-12">
                    <label class="prod-form-label">Search Product / Barcode</label>
                    <div class="position-relative">
                        <i class="fe-search position-absolute text-muted" style="left: 10px; top: 10px; font-size: 14px; pointer-events: none;"></i>
                        <input type="text" name="keyword" class="form-control form-control-sm ps-4" placeholder="Product name, barcode, ID..." value="{{ request('keyword') }}" style="padding-left: 32px !important; height: 36px; border-radius: 8px;">
                    </div>
                </div>

                {{-- Category --}}
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

                {{-- Status --}}
                <div class="col-xxl-1 col-xl-2 col-lg-2 col-md-3 col-6">
                    <label class="prod-form-label">Status</label>
                    <select name="status" class="form-select form-select-sm" style="height: 36px; border-radius: 8px;">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Stock Status --}}
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-3 col-6">
                    <label class="prod-form-label">Stock Status</label>
                    <select name="stock_status" class="form-select form-select-sm" style="height: 36px; border-radius: 8px;">
                        <option value="">All Stock</option>
                        <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock (>5)</option>
                        <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock (1-5)</option>
                        <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock (0)</option>
                    </select>
                </div>

                {{-- Global Smart Date Filter Integration --}}
                <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-6 col-12">
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
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-semibold d-flex align-items-center justify-content-center shadow-sm" style="height: 36px; border-radius: 8px; font-size: 13px;" title="Filter Products">
                                    <i class="fe-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('inhouse.products.index') }}" class="btn btn-light btn-sm border d-flex align-items-center justify-content-center" style="height: 36px; width: 36px; border-radius: 8px;" title="Reset Filters">
                                    <i class="fe-rotate-ccw"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Main Products Table Card --}}
    <div class="prod-table-card">
        {{-- Bulk Actions Toolbar --}}
        <div class="prod-toolbar">
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
                Showing {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} of {{ $data->total() }} Products
            </div>
        </div>

        <div class="table-responsive">
            <table class="table prod-table mb-0">
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
                        <th>Pricing</th>
                        <th>Stock Status</th>
                        <th>Flags</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $key => $value)
                    <tr>
                        <td>
                            <div class="form-check mb-0">
                                <input type="checkbox" class="form-check-input checkbox" value="{{ $value->id }}">
                            </div>
                        </td>
                        <td class="fw-bold text-muted">{{ $data->firstItem() + $key }}</td>
                        <td>
                            <img src="{{ asset($value->image ? $value->image->image : 'public/uploads/default/no-image.png') }}" class="prod-thumb" alt="">
                        </td>
                        <td>
                            <div class="fw-bold text-dark" style="max-width:260px;">
                                <a href="{{ route('products.edit', $value->id) }}" class="text-dark text-decoration-none">
                                    {{ Str::limit($value->name, 45) }}
                                </a>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge {{ $value->product_type === 'digital' ? 'bg-soft-purple text-purple' : 'bg-soft-info text-info' }}" style="font-size:10px;">
                                    {{ $value->product_type === 'digital' ? '💾 Digital' : '📦 Physical' }}
                                </span>
                                @if($value->pro_barcode)
                                    <small class="text-muted" style="font-family:monospace;font-size:11px;">#{{ $value->pro_barcode }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-soft-secondary text-secondary rounded-pill px-2.5 py-1">
                                {{ $value->category ? $value->category->name : 'Uncategorized' }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">৳{{ number_format($value->new_price, 2) }}</div>
                            @if($value->old_price && $value->old_price > $value->new_price)
                                <small class="text-muted text-decoration-line-through">৳{{ number_format($value->old_price, 2) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($value->stock <= 0)
                                <span class="badge bg-soft-danger text-danger rounded-pill px-2.5 py-1 fw-bold">Out of Stock</span>
                            @elseif($value->stock <= 5)
                                <span class="badge bg-soft-warning text-warning rounded-pill px-2.5 py-1 fw-bold">Low: {{ $value->stock }}</span>
                            @else
                                <span class="badge bg-soft-success text-success rounded-pill px-2.5 py-1 fw-bold">{{ $value->stock }} in stock</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @if($value->topsale == 1)
                                    <span class="badge bg-soft-danger text-danger" style="font-size:10px;">🔥 Hot Deal</span>
                                @endif
                                @if($value->feature_product == 1)
                                    <span class="badge bg-soft-primary text-primary" style="font-size:10px;">⭐ Featured</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($value->status == 1)
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
                                        <a class="dropdown-item" href="{{ route('inhouse.products.show', $value->id) }}">
                                            <i class="fe-eye text-info"></i> View Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('products.edit', $value->id) }}">
                                            <i class="fe-edit-2 text-primary"></i> Edit Product
                                        </a>
                                    </li>
                                    <li>
                                        @if($value->status == 1)
                                            <form method="post" action="{{ route('products.inactive') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fe-eye-off text-warning"></i> Deactivate
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{ route('products.active') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fe-check-circle text-success"></i> Activate
                                                </button>
                                            </form>
                                        @endif
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
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="fe-package d-block mb-2" style="font-size:2rem;opacity:.4;"></i>
                            কোনো ইনহাউজ পণ্য পাওয়া যায়নি। ফিল্টার পরিবর্তন করুন বা নতুন পণ্য যোগ করুন।
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

        // Select All Checkbox
        $(".checkall").on('change', function() {
            $(".checkbox").prop('checked', $(this).is(":checked"));
        });

        // Bulk status update
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
