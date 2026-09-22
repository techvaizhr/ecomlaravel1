@extends('backEnd.layouts.master')
@section('title', 'Banner & Slider Management')

@section('css')
<style>
    .stat-card-gradient {
        border: none;
        border-radius: 14px;
        color: #fff;
        padding: 1.25rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card-gradient:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .stat-card-gradient .stat-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2.5rem;
        opacity: 0.22;
    }
    .card-modern {
        border: none;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        background: #fff;
    }
    .table-modern th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.9rem 1rem;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .table-modern td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:hover td { background-color: #f8fafc; }
    
    .banner-preview-img {
        width: 110px;
        height: 55px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .banner-preview-img:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }
    
    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-inactive { background: #f1f5f9; color: #64748b; }

    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        transition: all 0.2s;
    }
    .btn-action-icon:hover {
        background: #f1f5f9;
        color: #0f172a;
        transform: translateY(-1px);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i data-feather="image" class="text-primary" style="width: 24px; height: 24px;"></i>
                Banner & Sliders
            </h4>
            <p class="text-muted small mb-0">Manage promotional home banners, slider images, and popup banners.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('banners.create') }}" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i data-feather="plus-circle" style="width: 16px; height: 16px;"></i>
                <span>Add Banner</span>
            </a>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                <div class="stat-icon"><i class="fas fa-images"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Total Banners</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['total'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Active</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['active'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%);">
                <div class="stat-icon"><i class="fas fa-pause-circle"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Inactive</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['inactive'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Categories</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['categories'] ?? 0) }}</h3>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('banners.index') }}" id="filterForm">
                <div class="row g-2 align-items-center">
                    {{-- Keyword Search --}}
                    <div class="col-12 col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i data-feather="search" style="width: 14px;"></i></span>
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control border-start-0" placeholder="Search link / category...">
                        </div>
                    </div>

                    {{-- Category Filter --}}
                    <div class="col-6 col-md-2">
                        <select name="category_id" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-6 col-md-2">
                        <select name="status" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- Global Date Filter --}}
                    <div class="col-12 col-md-3">
                        @include('backEnd.layouts.partials.smart_date_filter')
                    </div>

                    {{-- Per Page & Submit --}}
                    <div class="col-12 col-md-2 d-flex gap-2 justify-content-end align-items-center">
                        <select name="per_page" class="form-select form-select-sm" style="width: 75px;" onchange="document.getElementById('filterForm').submit()" title="Records per page">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
                        @if(request()->anyFilled(['keyword', 'category_id', 'status', 'start_date', 'end_date', 'date_preset']))
                            <a href="{{ route('banners.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters"><i data-feather="rotate-ccw" style="width: 14px;"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MAIN TABLE CARD --}}
    <div class="card card-modern">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th width="140">Preview</th>
                        <th>Category</th>
                        <th>Target Link</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th width="100" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $key => $value)
                        <tr>
                            <td class="text-muted small">
                                @if(method_exists($data, 'firstItem'))
                                    {{ $data->firstItem() + $key }}
                                @else
                                    {{ $key + 1 }}
                                @endif
                            </td>
                            
                            {{-- Image --}}
                            <td>
                                @if($value->image)
                                    <a href="{{ asset($value->image) }}" target="_blank" title="Click to view full image">
                                        <img src="{{ asset($value->image) }}" class="banner-preview-img" alt="Banner Image">
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted">No Image</span>
                                @endif
                            </td>

                            {{-- Category --}}
                            <td>
                                <span class="badge bg-light text-primary border px-2 py-1 fw-bold">
                                    {{ $value->category ? $value->category->name : 'General' }}
                                </span>
                            </td>

                            {{-- Link --}}
                            <td>
                                @if($value->link)
                                    <a href="{{ $value->link }}" target="_blank" class="text-dark small text-truncate d-inline-block" style="max-width: 250px;" title="{{ $value->link }}">
                                        {{ $value->link }} <i data-feather="external-link" style="width: 12px; height: 12px;" class="text-muted ms-1"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">--</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($value->status == 1)
                                    <span class="badge-soft badge-active"><i class="fas fa-check-circle"></i> Active</span>
                                @else
                                    <span class="badge-soft badge-inactive"><i class="fas fa-pause-circle"></i> Inactive</span>
                                @endif
                            </td>

                            {{-- Created Date --}}
                            <td class="text-muted small">
                                {{ $value->created_at ? $value->created_at->format('d M, Y h:i A') : 'N/A' }}
                            </td>

                            {{-- Actions --}}
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('banners.edit', $value->id) }}" class="btn-action-icon text-primary" title="Edit Banner">
                                        <i data-feather="edit-2" style="width:14px; height:14px;"></i>
                                    </a>
                                    
                                    <form action="{{ route('banners.destroy') }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this banner?');">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $value->id }}">
                                        <button type="submit" class="btn-action-icon text-danger" title="Delete Banner">
                                            <i data-feather="trash-2" style="width:14px; height:14px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i data-feather="image" style="width: 48px; height: 48px; opacity: 0.35;" class="mb-2"></i>
                                    <p class="fw-bold mb-1">No Banners Found</p>
                                    <small class="text-muted">Create eye-catching banners to engage with your customers.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if(method_exists($data, 'links') && $data->hasPages())
        <div class="p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} entries
            </div>
            <div>
                {{ $data->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush