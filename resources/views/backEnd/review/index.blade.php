@extends('backEnd.layouts.master')
@section('title', 'Manage Customer Reviews')

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
    
    .avatar-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #e0e7ff;
        color: #4338ca;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
    }
    .star-gold { color: #f59e0b; }
    .star-muted { color: #cbd5e1; }

    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-pending { background: #fef3c7; color: #d97706; }

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
                <i data-feather="star" class="text-warning" style="width: 24px; height: 24px;"></i>
                Customer Product Reviews
            </h4>
            <p class="text-muted small mb-0">Monitor customer feedback, ratings, and approve testimonials.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @can('review-create')
            <a href="{{ route('reviews.create') }}" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i data-feather="plus-circle" style="width: 16px; height: 16px;"></i>
                <span>Add Review</span>
            </a>
            @endcan
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <div class="stat-icon"><i class="fas fa-comments"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Total Reviews</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['total'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Active / Published</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['active'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Pending Review</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['pending'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);">
                <div class="stat-icon"><i class="fas fa-star"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Avg Rating</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ $stats['avg_rating'] ?? 5.0 }} <small class="fs-6 opacity-75">/ 5</small></h3>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('reviews.index') }}" id="filterForm">
                <div class="row g-2 align-items-center">
                    {{-- Keyword Search --}}
                    <div class="col-12 col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i data-feather="search" style="width: 14px;"></i></span>
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control border-start-0" placeholder="Search customer, product, review...">
                        </div>
                    </div>

                    {{-- Rating Filter --}}
                    <div class="col-6 col-md-2">
                        <select name="ratting" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Ratings</option>
                            <option value="5" {{ request('ratting') == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5 Star)</option>
                            <option value="4" {{ request('ratting') == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ (4 Star)</option>
                            <option value="3" {{ request('ratting') == 3 ? 'selected' : '' }}>⭐⭐⭐ (3 Star)</option>
                            <option value="2" {{ request('ratting') == 2 ? 'selected' : '' }}>⭐⭐ (2 Star)</option>
                            <option value="1" {{ request('ratting') == 1 ? 'selected' : '' }}>⭐ (1 Star)</option>
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-6 col-md-2">
                        <select name="status" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
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
                        @if(request()->anyFilled(['keyword', 'ratting', 'status', 'start_date', 'end_date', 'date_preset']))
                            <a href="{{ route('reviews.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters"><i data-feather="rotate-ccw" style="width: 14px;"></i></a>
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
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Rating</th>
                        <th width="30%">Review Comment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th width="120" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($show_data as $key => $value)
                        <tr>
                            <td class="text-muted small">
                                @if(method_exists($show_data, 'firstItem'))
                                    {{ $show_data->firstItem() + $key }}
                                @else
                                    {{ $key + 1 }}
                                @endif
                            </td>
                            
                            {{-- Customer --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($value->name ?: 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $value->name ?: 'Anonymous' }}</div>
                                        <small class="text-muted">{{ $value->email ?: 'No email' }}</small>
                                    </div>
                                </div>
                            </td>

                            {{-- Product --}}
                            <td>
                                @if($value->product)
                                    <a href="{{ route('product', $value->product->slug ?? '#') }}" target="_blank" class="fw-semibold text-primary text-truncate d-inline-block" style="max-width: 200px;" title="{{ $value->product->name }}">
                                        {{ $value->product->name }} <i data-feather="external-link" style="width: 12px; height: 12px;"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>

                            {{-- Rating --}}
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $value->ratting ? 'star-gold' : 'star-muted' }}" style="font-size: 13px;"></i>
                                    @endfor
                                    <span class="fw-bold ms-1 small text-dark">{{ $value->ratting }}.0</span>
                                </div>
                            </td>

                            {{-- Comment --}}
                            <td>
                                <p class="mb-0 text-muted small text-break" style="line-height: 1.4;">
                                    {{ $value->review }}
                                </p>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($value->status == 'active')
                                    <span class="badge-soft badge-active"><i class="fas fa-check-circle"></i> Active</span>
                                @else
                                    <span class="badge-soft badge-pending"><i class="fas fa-clock"></i> Pending</span>
                                @endif
                            </td>

                            {{-- Created Date --}}
                            <td class="text-muted small">
                                {{ $value->created_at ? $value->created_at->format('d M, Y') : 'N/A' }}
                            </td>

                            {{-- Actions --}}
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @if($value->status == 'active')
                                        <form action="{{ route('reviews.inactive') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="btn-action-icon text-warning" title="Make Pending / Inactive">
                                                <i data-feather="pause-circle" style="width:14px; height:14px;"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('reviews.active') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="btn-action-icon text-success" title="Approve & Publish">
                                                <i data-feather="check-circle" style="width:14px; height:14px;"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('reviews.edit', $value->id) }}" class="btn-action-icon text-primary" title="Edit Review">
                                        <i data-feather="edit-2" style="width:14px; height:14px;"></i>
                                    </a>
                                    
                                    <form action="{{ route('reviews.destroy') }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this review?');">
                                        @csrf
                                        <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                        <button type="submit" class="btn-action-icon text-danger" title="Delete Review">
                                            <i data-feather="trash-2" style="width:14px; height:14px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i data-feather="star" style="width: 48px; height: 48px; opacity: 0.35;" class="mb-2"></i>
                                    <p class="fw-bold mb-1">No Reviews Found</p>
                                    <small class="text-muted">Customer reviews and ratings will show up here once submitted.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if(method_exists($show_data, 'links') && $show_data->hasPages())
        <div class="p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Showing {{ $show_data->firstItem() }} to {{ $show_data->lastItem() }} of {{ $show_data->total() }} entries
            </div>
            <div>
                {{ $show_data->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush