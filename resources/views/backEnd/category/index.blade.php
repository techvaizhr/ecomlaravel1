@extends('backEnd.layouts.master')
@section('title', 'Categories Master Hub')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    /* ================= MASTER HUB STYLES ================= */
    :root {
        --hub-indigo: #4f46e5;
        --hub-violet: #7c3aed;
        --hub-cyan: #06b6d4;
        --hub-emerald: #10b981;
        --hub-amber: #f59e0b;
        --hub-rose: #f43f5e;
    }

    .cat-master-hero {
        background: linear-gradient(135deg, #312e81 0%, #4338ca 45%, #6366f1 80%, #06b6d4 100%);
        border-radius: 20px;
        padding: 26px 30px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 14px 34px rgba(79, 70, 229, 0.28);
        position: relative;
        overflow: hidden;
    }
    .cat-master-hero::before {
        content: "";
        position: absolute;
        top: -60px;
        right: -60px;
        width: 240px;
        height: 240px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        pointer-events: none;
    }
    .cat-master-hero::after {
        content: "";
        position: absolute;
        bottom: -50px;
        left: 20%;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Stat Cards */
    .cat-hub-stat {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.25s ease;
        height: 100%;
    }
    .cat-hub-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }
    .cat-hub-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .cat-hub-icon.indigo { background: #eef2ff; color: #4f46e5; }
    .cat-hub-icon.emerald { background: #ecfdf5; color: #10b981; }
    .cat-hub-icon.amber { background: #fffbeb; color: #f59e0b; }
    .cat-hub-icon.purple { background: #f5f3ff; color: #8b5cf6; }

    /* Custom Nav Tabs */
    .cat-nav-tabs {
        border: none;
        background: #f1f5f9;
        padding: 6px;
        border-radius: 14px;
        display: inline-flex;
        gap: 6px;
        width: auto;
    }
    .cat-nav-tabs .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        font-size: 13.5px;
        padding: 10px 20px;
        border-radius: 10px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cat-nav-tabs .nav-link:hover {
        color: #1e293b;
        background: rgba(255, 255, 255, 0.6);
    }
    .cat-nav-tabs .nav-link.active {
        background: #ffffff;
        color: #4f46e5;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.12);
        font-weight: 700;
    }
    .cat-nav-tabs .nav-badge {
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 999px;
        background: #e2e8f0;
        color: #475569;
        font-weight: 700;
    }
    .cat-nav-tabs .nav-link.active .nav-badge {
        background: #eef2ff;
        color: #4f46e5;
    }

    /* Master Table Container */
    .cat-content-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .cat-content-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 18px 24px;
    }
    .cat-content-card .card-body {
        padding: 22px 24px;
    }

    /* Modern Tables */
    .table-hub thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 14px 16px;
        white-space: nowrap;
    }
    .table-hub tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-hub tbody tr:hover td {
        background: #f8fafc;
    }

    /* Thumbnails */
    .hub-thumb {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        overflow: hidden;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        flex-shrink: 0;
    }
    .hub-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .hub-thumb .fallback-char {
        font-weight: 800;
        font-size: 16px;
        color: #4f46e5;
    }

    /* Badges & Pills */
    .slug-chip {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 11px;
        color: #64748b;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 6px;
        display: inline-block;
    }
    .parent-pill {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        font-size: 11.5px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .count-pill {
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        font-size: 11.5px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }
    .count-pill:hover {
        background: #e0e7ff;
    }
    .front-badge {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border: 1px solid #fcd34d;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Action Buttons */
    .act-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        background: #f8fafc;
        color: #64748b;
    }
    .act-btn:hover {
        transform: translateY(-1px);
    }
    .act-btn.edit:hover { background: #eef2ff; color: #4f46e5; border-color: #c7d2fe; }
    .act-btn.delete:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
    .act-btn.toggle-on:hover { background: #fffbeb; color: #d97706; border-color: #fde68a; }
    .act-btn.toggle-off:hover { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
    .act-btn.add-child:hover { background: #ecfeff; color: #0891b2; border-color: #a5f3fc; }

    /* Hierarchy Tree Explorer */
    .tree-box {
        background: #f8fafc;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 20px;
    }
    .tree-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        transition: all 0.2s ease;
        overflow: hidden;
    }
    .tree-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    }
    .tree-header {
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        background: #ffffff;
    }
    .tree-header:hover {
        background: #fafafa;
    }
    .tree-branch-body {
        padding: 14px 18px 16px 36px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
    }
    .subcat-node {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 10px 14px;
        margin-bottom: 8px;
    }
    .childcat-chip {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 3px 4px 3px 0;
    }
    .childcat-chip:hover {
        background: #e2e8f0;
    }

    /* Modal Form Styling */
    .modal-hub .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }
    .modal-hub .modal-header {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: #ffffff;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        padding: 18px 24px;
    }
    .modal-hub .modal-header.subcat {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }
    .modal-hub .modal-header.childcat {
        background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    }
    .modal-hub .modal-body {
        padding: 24px;
    }
    .modal-hub .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
    }
</style>
@endsection

@section('content')
<div class="container-fluid pt-3">
    
    @php
        $totalCats = $categories->count();
        $activeCats = $categories->where('status', 1)->count();
        $totalSubcats = $subcategories->count();
        $activeSubcats = $subcategories->where('status', 1)->count();
        $totalChildcats = $childcategories->count();
        $activeChildcats = $childcategories->where('status', 1)->count();
    @endphp

    {{-- Master Hero Header --}}
    <div class="cat-master-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-white text-primary fw-bold px-2 py-1 rounded-pill" style="font-size:11px;">ALL-IN-ONE HUB</span>
                <h3 class="mb-0 fw-bold text-white"><i class="fe-grid me-2"></i> Category Catalog Hub</h3>
            </div>
            <p class="mb-0 text-white-50" style="font-size:14px;">Main Categories, Subcategories এবং Childcategories এক জায়গা থেকেই সহজে ভিউ, অ্যাড ও ম্যানেজ করুন।</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-light rounded-pill px-3 py-2 fw-bold text-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fe-plus-circle me-1"></i> Add Category
            </button>
            <button type="button" class="btn btn-outline-light rounded-pill px-3 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addSubcategoryModal">
                <i class="fe-folder-plus me-1"></i> Add Subcategory
            </button>
            <button type="button" class="btn btn-outline-light rounded-pill px-3 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addChildcategoryModal">
                <i class="fe-tag me-1"></i> Add Childcategory
            </button>
        </div>
    </div>

    {{-- Stats Summary Row --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="cat-hub-stat" onclick="switchTab('tab-categories')" style="cursor:pointer;">
                <div class="cat-hub-icon indigo">
                    <i class="fe-grid"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Main Categories</div>
                    <h4 class="mb-0 fw-bold text-dark">{{ $totalCats }} <span class="fs-6 fw-normal text-success">({{ $activeCats }} Active)</span></h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="cat-hub-stat" onclick="switchTab('tab-subcategories')" style="cursor:pointer;">
                <div class="cat-hub-icon emerald">
                    <i class="fe-folder"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Subcategories</div>
                    <h4 class="mb-0 fw-bold text-dark">{{ $totalSubcats }} <span class="fs-6 fw-normal text-success">({{ $activeSubcats }} Active)</span></h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="cat-hub-stat" onclick="switchTab('tab-childcategories')" style="cursor:pointer;">
                <div class="cat-hub-icon purple">
                    <i class="fe-tag"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Childcategories</div>
                    <h4 class="mb-0 fw-bold text-dark">{{ $totalChildcats }} <span class="fs-6 fw-normal text-success">({{ $activeChildcats }} Active)</span></h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="cat-hub-stat" onclick="switchTab('tab-tree')" style="cursor:pointer;">
                <div class="cat-hub-icon amber">
                    <i class="fe-layers"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Catalog Hierarchy</div>
                    <h4 class="mb-0 fw-bold text-dark">3-Tier Depth <span class="fs-6 fw-normal text-primary">Explorer</span></h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation Tabs --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <ul class="nav cat-nav-tabs" id="catalogTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-btn-categories" data-bs-toggle="tab" data-bs-target="#tab-categories" type="button" role="tab">
                    <i class="fe-grid"></i> Main Categories <span class="nav-badge">{{ $totalCats }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-btn-subcategories" data-bs-toggle="tab" data-bs-target="#tab-subcategories" type="button" role="tab">
                    <i class="fe-folder"></i> Subcategories <span class="nav-badge">{{ $totalSubcats }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-btn-childcategories" data-bs-toggle="tab" data-bs-target="#tab-childcategories" type="button" role="tab">
                    <i class="fe-tag"></i> Childcategories <span class="nav-badge">{{ $totalChildcats }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-btn-tree" data-bs-toggle="tab" data-bs-target="#tab-tree" type="button" role="tab">
                    <i class="fe-git-merge"></i> Tree Explorer
                </button>
            </li>
        </ul>
    </div>

    {{-- Main Content Area --}}
    <div class="tab-content" id="catalogTabContent">
        
        {{-- ================= TAB 1: MAIN CATEGORIES ================= --}}
        <div class="tab-pane fade show active" id="tab-categories" role="tabpanel">
            <div class="cat-content-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="fe-grid me-2 text-primary"></i> Main Categories Directory</h5>
                        <small class="text-muted">দোকানের মূল প্যারেন্ট ক্যাটাগরি তালিকা</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fe-plus me-1"></i> Add Category
                    </button>
                </div>
                <div class="card-body">
                    <table id="datatable-categories" class="table table-hub w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th style="width: 60px;">Image</th>
                                <th>Category Name</th>
                                <th>Subcategories</th>
                                <th>Home Display</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $value)
                            <tr>
                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="hub-thumb">
                                        @if($value->image && file_exists(public_path($value->image)))
                                            <img src="{{ asset($value->image) }}" alt="{{ $value->name }}">
                                        @else
                                            <span class="fallback-char">{{ strtoupper(substr($value->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $value->name }}</div>
                                    <span class="slug-chip">/{{ $value->slug }}</span>
                                </td>
                                <td>
                                    <span class="count-pill" onclick="filterSubcategoriesByParent('{{ $value->name }}')" title="View subcategories under {{ $value->name }}">
                                        <i class="fe-folder"></i> {{ $value->subcategories_count }} Subcategories
                                    </span>
                                </td>
                                <td>
                                    @if ($value->front_view == 1)
                                        <span class="front-badge"><i class="fe-star"></i> Featured</span>
                                    @else
                                        <span class="text-muted small"><i class="fe-minus"></i> Regular</span>
                                    @endif
                                </td>
                                <td>
                                    @if($value->status == 1)
                                        <span class="badge bg-soft-success text-success px-2.5 py-1.5 rounded-pill fw-bold" style="font-size:11px;">
                                            <i class="fe-check-circle me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger px-2.5 py-1.5 rounded-pill fw-bold" style="font-size:11px;">
                                            <i class="fe-x-circle me-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        {{-- Quick Add Subcategory under this category --}}
                                        <button type="button" class="act-btn add-child" title="Add Subcategory under {{ $value->name }}" onclick="openAddSubcategoryFor({{ $value->id }})">
                                            <i class="fe-folder-plus"></i>
                                        </button>

                                        {{-- Status Toggle --}}
                                        @if($value->status == 1)
                                            <form method="post" action="{{route('categories.inactive')}}" class="d-inline"> 
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <input type="hidden" name="redirect_to" value="{{ url()->current() }}#tab-categories">
                                                <button type="submit" class="act-btn toggle-on" title="Deactivate">
                                                    <i class="fe-eye-off"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{route('categories.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <input type="hidden" name="redirect_to" value="{{ url()->current() }}#tab-categories">
                                                <button type="submit" class="act-btn toggle-off" title="Activate">
                                                    <i class="fe-eye"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Quick Edit Modal Trigger --}}
                                        <button type="button" class="act-btn edit" title="Edit Category" onclick="openEditCategoryModal({{ $value->id }}, '{{ addslashes($value->name) }}', '{{ $value->status }}', '{{ $value->front_view }}')">
                                            <i class="fe-edit-2"></i>
                                        </button>

                                        {{-- Delete --}}
                                        @can('category-delete')
                                        <form method="post" action="{{route('categories.destroy')}}" class="d-inline">
                                            @csrf
                                            <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                            <button type="submit" class="act-btn delete delete-confirm" title="Delete">
                                                <i class="fe-trash-2"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= TAB 2: SUBCATEGORIES ================= --}}
        <div class="tab-pane fade" id="tab-subcategories" role="tabpanel">
            <div class="cat-content-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="fe-folder me-2 text-success"></i> Subcategories Directory</h5>
                        <small class="text-muted">ক্যাটাগরির অধীনস্থ সকল সাব-ক্যাটাগরি তালিকা</small>
                    </div>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addSubcategoryModal">
                        <i class="fe-plus me-1"></i> Add Subcategory
                    </button>
                </div>
                <div class="card-body">
                    <table id="datatable-subcategories" class="table table-hub w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th style="width: 60px;">Image</th>
                                <th>Subcategory Name</th>
                                <th>Parent Category</th>
                                <th>Childcategories</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subcategories as $value)
                            <tr>
                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="hub-thumb">
                                        @if($value->image && file_exists(public_path($value->image)))
                                            <img src="{{ asset($value->image) }}" alt="{{ $value->subcategoryName }}">
                                        @else
                                            <span class="fallback-char" style="color:#10b981;">{{ strtoupper(substr($value->subcategoryName, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $value->subcategoryName }}</div>
                                    <span class="slug-chip">/{{ $value->slug }}</span>
                                </td>
                                <td>
                                    @if($value->category)
                                        <span class="parent-pill">
                                            <i class="fe-grid text-primary"></i> {{ $value->category->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="count-pill" onclick="filterChildcategoriesByParent('{{ $value->subcategoryName }}')" title="View childcategories under {{ $value->subcategoryName }}">
                                        <i class="fe-tag"></i> {{ $value->childcategories_count }} Childcategories
                                    </span>
                                </td>
                                <td>
                                    @if($value->status == 1)
                                        <span class="badge bg-soft-success text-success px-2.5 py-1.5 rounded-pill fw-bold" style="font-size:11px;">
                                            <i class="fe-check-circle me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger px-2.5 py-1.5 rounded-pill fw-bold" style="font-size:11px;">
                                            <i class="fe-x-circle me-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        {{-- Quick Add Childcategory shortcut --}}
                                        <button type="button" class="act-btn add-child" title="Add Childcategory under {{ $value->subcategoryName }}" onclick="openAddChildcategoryFor({{ $value->category_id ?? 0 }}, {{ $value->id }})">
                                            <i class="fe-tag"></i>
                                        </button>

                                        {{-- Status Toggle --}}
                                        @if($value->status == 1)
                                            <form method="post" action="{{route('subcategories.inactive')}}" class="d-inline"> 
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="submit" class="act-btn toggle-on" title="Deactivate">
                                                    <i class="fe-eye-off"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{route('subcategories.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="submit" class="act-btn toggle-off" title="Activate">
                                                    <i class="fe-eye"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Quick Edit Modal Trigger --}}
                                        <button type="button" class="act-btn edit" title="Edit Subcategory" onclick="openEditSubcategoryModal({{ $value->id }}, '{{ addslashes($value->subcategoryName) }}', {{ $value->category_id ?? 0 }}, '{{ $value->status }}')">
                                            <i class="fe-edit-2"></i>
                                        </button>

                                        {{-- Delete --}}
                                        @can('subcategory-delete')
                                        <form method="post" action="{{route('subcategories.destroy')}}" class="d-inline">
                                            @csrf
                                            <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                            <button type="submit" class="act-btn delete delete-confirm" title="Delete">
                                                <i class="fe-trash-2"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= TAB 3: CHILDCATEGORIES ================= --}}
        <div class="tab-pane fade" id="tab-childcategories" role="tabpanel">
            <div class="cat-content-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="fe-tag me-2 text-purple" style="color:#7c3aed;"></i> Childcategories Directory</h5>
                        <small class="text-muted">সাব-ক্যাটাগরির অধীনস্থ ৩য় স্তরের চাইল্ড ক্যাটাগরি তালিকা</small>
                    </div>
                    <button type="button" class="btn btn-purple btn-sm rounded-pill px-3 text-white" style="background:#7c3aed;" data-bs-toggle="modal" data-bs-target="#addChildcategoryModal">
                        <i class="fe-plus me-1"></i> Add Childcategory
                    </button>
                </div>
                <div class="card-body">
                    <table id="datatable-childcategories" class="table table-hub w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th>Childcategory Name</th>
                                <th>Hierarchy Pathway</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($childcategories as $value)
                            <tr>
                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $value->childcategoryName }}</div>
                                    <span class="slug-chip">/{{ $value->slug }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        @if(optional($value->subcategory)->category)
                                            <span class="parent-pill" style="font-size:11px;">
                                                <i class="fe-grid text-primary"></i> {{ $value->subcategory->category->name }}
                                            </span>
                                            <i class="fe-chevron-right text-muted" style="font-size:11px;"></i>
                                        @endif
                                        @if($value->subcategory)
                                            <span class="parent-pill" style="font-size:11px; background:#f0fdf4; border-color:#bbf7d0; color:#166534;">
                                                <i class="fe-folder text-success"></i> {{ $value->subcategory->subcategoryName }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted">Unassigned</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($value->status == 1)
                                        <span class="badge bg-soft-success text-success px-2.5 py-1.5 rounded-pill fw-bold" style="font-size:11px;">
                                            <i class="fe-check-circle me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger px-2.5 py-1.5 rounded-pill fw-bold" style="font-size:11px;">
                                            <i class="fe-x-circle me-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        {{-- Status Toggle --}}
                                        @if($value->status == 1)
                                            <form method="post" action="{{route('childcategories.inactive')}}" class="d-inline"> 
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="submit" class="act-btn toggle-on" title="Deactivate">
                                                    <i class="fe-eye-off"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{route('childcategories.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="submit" class="act-btn toggle-off" title="Activate">
                                                    <i class="fe-eye"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Quick Edit Modal Trigger --}}
                                        <button type="button" class="act-btn edit" title="Edit Childcategory" onclick="openEditChildcategoryModal({{ $value->id }}, '{{ addslashes($value->childcategoryName) }}', {{ optional($value->subcategory)->category_id ?? 0 }}, {{ $value->subcategory_id ?? 0 }}, '{{ $value->status }}')">
                                            <i class="fe-edit-2"></i>
                                        </button>

                                        {{-- Delete --}}
                                        @can('childcategory-delete')
                                        <form method="post" action="{{route('childcategories.destroy')}}" class="d-inline">
                                            @csrf
                                            <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                            <button type="submit" class="act-btn delete delete-confirm" title="Delete">
                                                <i class="fe-trash-2"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= TAB 4: HIERARCHY TREE EXPLORER ================= --}}
        <div class="tab-pane fade" id="tab-tree" role="tabpanel">
            <div class="cat-content-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="fe-layers me-2 text-warning"></i> Interactive Hierarchy Tree</h5>
                        <small class="text-muted">ক্যাটাগরি থেকে সাব ও চাইল্ড ক্যাটাগরির ভিজ্যুয়াল কাঠামোগত সম্পর্ক দেখুন</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" id="treeSearchInput" class="form-control form-control-sm rounded-pill" placeholder="🔍 Filter tree branches..." style="width: 220px;">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="expandAllTree(true)">Expand All</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="expandAllTree(false)">Collapse All</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tree-box">
                        @forelse($treeCategories as $cat)
                        <div class="tree-card tree-item-node" data-title="{{ strtolower($cat->name) }}">
                            <div class="tree-header" data-bs-toggle="collapse" data-bs-target="#tree-collapse-{{ $cat->id }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="hub-thumb" style="width:36px; height:36px; border-radius:8px;">
                                        @if($cat->image && file_exists(public_path($cat->image)))
                                            <img src="{{ asset($cat->image) }}" alt="{{ $cat->name }}">
                                        @else
                                            <span class="fallback-char" style="font-size:13px;">{{ strtoupper(substr($cat->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark fs-5">{{ $cat->name }}</span>
                                        <span class="badge bg-soft-primary text-primary ms-2 rounded-pill">{{ $cat->allSubcategories->count() }} Subcategories</span>
                                        @if($cat->front_view == 1)
                                            <span class="badge bg-soft-warning text-warning ms-1"><i class="fe-star"></i> Home</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2" style="font-size:11px;" onclick="event.stopPropagation(); openAddSubcategoryFor({{ $cat->id }})">
                                        <i class="fe-plus"></i> Subcategory
                                    </button>
                                    <i class="fe-chevron-down text-muted transition-arrow"></i>
                                </div>
                            </div>
                            
                            <div class="collapse show" id="tree-collapse-{{ $cat->id }}">
                                <div class="tree-branch-body">
                                    @forelse($cat->allSubcategories as $sub)
                                    <div class="subcat-node tree-sub-node" data-title="{{ strtolower($sub->subcategoryName) }}">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fe-folder text-success"></i>
                                                <span class="fw-bold text-dark">{{ $sub->subcategoryName }}</span>
                                                <span class="badge bg-light text-muted border rounded-pill" style="font-size:10.5px;">{{ $sub->allChildcategories->count() }} Child</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-xs btn-outline-purple rounded-pill px-2 py-0" style="font-size:10.5px; border-color:#c4b5fd; color:#7c3aed;" onclick="openAddChildcategoryFor({{ $cat->id }}, {{ $sub->id }})">
                                                    <i class="fe-plus"></i> Child
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Childcategories Pills --}}
                                        <div class="d-flex flex-wrap align-items-center">
                                            @forelse($sub->allChildcategories as $child)
                                            <span class="childcat-chip tree-child-node" data-title="{{ strtolower($child->childcategoryName) }}">
                                                <i class="fe-tag text-purple" style="font-size:11px;"></i> {{ $child->childcategoryName }}
                                            </span>
                                            @empty
                                            <span class="text-muted small fst-italic ps-2">No childcategories added yet.</span>
                                            @endforelse
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-muted small py-2">No subcategories attached to this category yet. Click <strong>+ Subcategory</strong> to add one.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">No categories created yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL 1: ADD CATEGORY                                    --}}
{{-- ========================================================= --}}
<div class="modal fade modal-hub" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white fw-bold"><i class="fe-plus-circle me-2"></i> Add New Main Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ url()->current() }}#tab-categories">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" name="name" placeholder="e.g. Electronics, Fashion, Men's Wear" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Image (Thumbnail)</label>
                            <input type="file" class="form-control rounded-3" name="image" accept="image/*">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Icon (Optional)</label>
                            <input type="file" class="form-control rounded-3" name="icon" accept="image/*">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Homepage Display</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="front_view" value="1" id="catFrontView" style="cursor:pointer; width:2.5em; height:1.3em;">
                                <label class="form-check-label ms-2 small fw-semibold" for="catFrontView">Show on Home</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3" name="status" required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fe-check me-1"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL 2: ADD SUBCATEGORY                                 --}}
{{-- ========================================================= --}}
<div class="modal fade modal-hub" id="addSubcategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header subcat">
                <h5 class="modal-title text-white fw-bold"><i class="fe-folder-plus me-2"></i> Add New Subcategory</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('subcategories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ url()->current() }}#tab-subcategories">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Parent Category <span class="text-danger">*</span></label>
                        <select class="form-select rounded-3" name="category_id" id="subcat_parent_id" required>
                            <option value="">-- Select Parent Category --</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Subcategory Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" name="subcategoryName" placeholder="e.g. Smart Phones, T-Shirts" required>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-7">
                            <label class="form-label fw-bold">Image (Optional)</label>
                            <input type="file" class="form-control rounded-3" name="image" accept="image/*">
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3" name="status" required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fe-check me-1"></i> Save Subcategory
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL 3: ADD CHILDCATEGORY                               --}}
{{-- ========================================================= --}}
<div class="modal fade modal-hub" id="addChildcategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header childcat">
                <h5 class="modal-title text-white fw-bold"><i class="fe-tag me-2"></i> Add New Childcategory</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('childcategories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ url()->current() }}#tab-childcategories">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">1. Select Category</label>
                        <select class="form-select rounded-3" id="child_modal_cat_select">
                            <option value="">-- Choose Category first --</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">2. Parent Subcategory <span class="text-danger">*</span></label>
                        <select class="form-select rounded-3" name="subcategory_id" id="child_modal_subcat_select" required>
                            <option value="">-- Choose Category first --</option>
                            @foreach($allSubcategories as $sub)
                                <option value="{{ $sub->id }}" data-category="{{ $sub->category_id }}">{{ $sub->subcategoryName }} ({{ optional($sub->category)->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Childcategory Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" name="childcategoryName" placeholder="e.g. 5G Mobiles, Cotton T-Shirts" required>
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select class="form-select rounded-3" name="status" required>
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple text-white rounded-pill px-4 fw-bold shadow-sm" style="background:#7c3aed;">
                        <i class="fe-check me-1"></i> Save Childcategory
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL 4: EDIT CATEGORY MODAL                             --}}
{{-- ========================================================= --}}
<div class="modal fade modal-hub" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white fw-bold"><i class="fe-edit me-2"></i> Edit Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('categories.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="hidden_id" id="edit_cat_id">
                <input type="hidden" name="redirect_to" value="{{ url()->current() }}#tab-categories">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" name="name" id="edit_cat_name" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Change Image</label>
                            <input type="file" class="form-control rounded-3" name="image" accept="image/*">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Change Icon</label>
                            <input type="file" class="form-control rounded-3" name="icon" accept="image/*">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Homepage Display</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="front_view" value="1" id="edit_cat_front_view" style="cursor:pointer; width:2.5em; height:1.3em;">
                                <label class="form-check-label ms-2 small fw-semibold" for="edit_cat_front_view">Show on Home</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3" name="status" id="edit_cat_status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fe-check me-1"></i> Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL 5: EDIT SUBCATEGORY MODAL                          --}}
{{-- ========================================================= --}}
<div class="modal fade modal-hub" id="editSubcategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header subcat">
                <h5 class="modal-title text-white fw-bold"><i class="fe-edit me-2"></i> Edit Subcategory</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('subcategories.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="edit_subcat_id">
                <input type="hidden" name="redirect_to" value="{{ url()->current() }}#tab-subcategories">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Parent Category <span class="text-danger">*</span></label>
                        <select class="form-select rounded-3" name="category_id" id="edit_subcat_parent_id" required>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Subcategory Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" name="subcategoryName" id="edit_subcat_name" required>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-7">
                            <label class="form-label fw-bold">Change Image</label>
                            <input type="file" class="form-control rounded-3" name="image" accept="image/*">
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3" name="status" id="edit_subcat_status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fe-check me-1"></i> Update Subcategory
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL 6: EDIT CHILDCATEGORY MODAL                        --}}
{{-- ========================================================= --}}
<div class="modal fade modal-hub" id="editChildcategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header childcat">
                <h5 class="modal-title text-white fw-bold"><i class="fe-edit me-2"></i> Edit Childcategory</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('childcategories.update') }}" method="POST">
                @csrf
                <input type="hidden" name="hidden_id" id="edit_child_id">
                <input type="hidden" name="redirect_to" value="{{ url()->current() }}#tab-childcategories">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Parent Subcategory <span class="text-danger">*</span></label>
                        <select class="form-select rounded-3" name="subcategory_id" id="edit_child_subcat_id" required>
                            @foreach($allSubcategories as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->subcategoryName }} ({{ optional($sub->category)->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Childcategory Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" name="childcategoryName" id="edit_child_name" required>
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select class="form-select rounded-3" name="status" id="edit_child_status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple text-white rounded-pill px-4 fw-bold shadow-sm" style="background:#7c3aed;">
                        <i class="fe-check me-1"></i> Update Childcategory
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

<script>
    var dtCategories, dtSubcategories, dtChildcategories;

    $(document).ready(function() {
        // Initialize DataTables
        dtCategories = $('#datatable-categories').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search categories...",
                paginate: { previous: "<i class='fe-arrow-left'></i>", next: "<i class='fe-arrow-right'></i>" }
            }
        });

        dtSubcategories = $('#datatable-subcategories').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search subcategories...",
                paginate: { previous: "<i class='fe-arrow-left'></i>", next: "<i class='fe-arrow-right'></i>" }
            }
        });

        dtChildcategories = $('#datatable-childcategories').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search childcategories...",
                paginate: { previous: "<i class='fe-arrow-left'></i>", next: "<i class='fe-arrow-right'></i>" }
            }
        });

        // Tab activation based on URL hash
        var hash = window.location.hash;
        if (hash) {
            var tabTrigger = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (tabTrigger) {
                var tab = new bootstrap.Tab(tabTrigger);
                tab.show();
            }
        }

        // Tab click event to update URL hash
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("data-bs-target");
            if (history.pushState) {
                history.pushState(null, null, target);
            } else {
                location.hash = target;
            }
            // Recalculate datatable column widths
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
        });

        // Cascading Category -> Subcategory dropdown in Add Childcategory Modal
        $('#child_modal_cat_select').on('change', function() {
            var catId = $(this).val();
            var $subSelect = $('#child_modal_subcat_select');
            
            if (catId) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('ajax-product-subcategory') }}?category_id=" + catId,
                    success: function(res) {
                        $subSelect.empty();
                        $subSelect.append('<option value="">-- Select Subcategory --</option>');
                        if (res) {
                            $.each(res, function(key, value) {
                                $subSelect.append('<option value="' + key + '">' + value + '</option>');
                            });
                        }
                    }
                });
            } else {
                // Restore all options
                $subSelect.empty();
                $subSelect.append('<option value="">-- Choose Category first --</option>');
                @foreach($allSubcategories as $sub)
                    $subSelect.append('<option value="{{ $sub->id }}">{{ $sub->subcategoryName }} ({{ optional($sub->category)->name }})</option>');
                @endforeach
            }
        });

        // Tree visual filter search
        $('#treeSearchInput').on('keyup', function() {
            var term = $(this).val().toLowerCase().trim();
            if (!term) {
                $('.tree-item-node').show();
                $('.tree-sub-node').show();
                $('.tree-child-node').show();
                return;
            }

            $('.tree-item-node').each(function() {
                var catText = $(this).attr('data-title') || '';
                var matchCat = catText.indexOf(term) > -1;
                var hasMatchingChild = false;

                $(this).find('.tree-sub-node').each(function() {
                    var subText = $(this).attr('data-title') || '';
                    var matchSub = subText.indexOf(term) > -1;
                    var matchChildInSub = false;

                    $(this).find('.tree-child-node').each(function() {
                        var childText = $(this).attr('data-title') || '';
                        if (childText.indexOf(term) > -1) {
                            matchChildInSub = true;
                            $(this).show();
                        }
                    });

                    if (matchSub || matchChildInSub || matchCat) {
                        $(this).show();
                        hasMatchingChild = true;
                    } else {
                        $(this).hide();
                    }
                });

                if (matchCat || hasMatchingChild) {
                    $(this).show();
                    $(this).find('.collapse').addClass('show');
                } else {
                    $(this).hide();
                }
            });
        });
    });

    // Switch Tab helper
    function switchTab(tabId) {
        var tabTrigger = document.querySelector('button[data-bs-target="#' + tabId + '"]');
        if (tabTrigger) {
            var tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }

    // Filter subcategories by Parent Category Name
    function filterSubcategoriesByParent(catName) {
        switchTab('tab-subcategories');
        dtSubcategories.search(catName).draw();
    }

    // Filter childcategories by Parent Subcategory Name
    function filterChildcategoriesByParent(subcatName) {
        switchTab('tab-childcategories');
        dtChildcategories.search(subcatName).draw();
    }

    // Expand/Collapse Tree
    function expandAllTree(expand) {
        if (expand) {
            $('.tree-box .collapse').addClass('show');
        } else {
            $('.tree-box .collapse').removeClass('show');
        }
    }

    // Quick Add Subcategory prefilled with Parent Category
    function openAddSubcategoryFor(catId) {
        $('#subcat_parent_id').val(catId);
        var modal = new bootstrap.Modal(document.getElementById('addSubcategoryModal'));
        modal.show();
    }

    // Quick Add Childcategory prefilled with Parent Category and Subcategory
    function openAddChildcategoryFor(catId, subcatId) {
        if (catId) {
            $('#child_modal_cat_select').val(catId).trigger('change');
            setTimeout(function() {
                $('#child_modal_subcat_select').val(subcatId);
            }, 300);
        } else {
            $('#child_modal_subcat_select').val(subcatId);
        }
        var modal = new bootstrap.Modal(document.getElementById('addChildcategoryModal'));
        modal.show();
    }

    // Quick Edit Category Modal
    function openEditCategoryModal(id, name, status, frontView) {
        $('#edit_cat_id').val(id);
        $('#edit_cat_name').val(name);
        $('#edit_cat_status').val(status);
        $('#edit_cat_front_view').prop('checked', frontView == 1 || frontView == '1');
        var modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        modal.show();
    }

    // Quick Edit Subcategory Modal
    function openEditSubcategoryModal(id, name, parentId, status) {
        $('#edit_subcat_id').val(id);
        $('#edit_subcat_name').val(name);
        $('#edit_subcat_parent_id').val(parentId);
        $('#edit_subcat_status').val(status);
        var modal = new bootstrap.Modal(document.getElementById('editSubcategoryModal'));
        modal.show();
    }

    // Quick Edit Childcategory Modal
    function openEditChildcategoryModal(id, name, catId, subcatId, status) {
        $('#edit_child_id').val(id);
        $('#edit_child_name').val(name);
        $('#edit_child_subcat_id').val(subcatId);
        $('#edit_child_status').val(status);
        var modal = new bootstrap.Modal(document.getElementById('editChildcategoryModal'));
        modal.show();
    }
</script>
@endsection