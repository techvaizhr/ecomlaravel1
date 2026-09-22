@extends('backEnd.layouts.master')
@section('title', 'Manage Coupons')

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
    .coupon-badge {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        color: #4f46e5;
        background: #eef2ff;
        border: 1px dashed #6366f1;
        padding: 5px 10px;
        border-radius: 6px;
        letter-spacing: 1px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .coupon-badge:hover {
        background: #e0e7ff;
        border-color: #4338ca;
    }
    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-inactive { background: #f1f5f9; color: #64748b; }
    .badge-expired { background: #fee2e2; color: #991b1b; }
    
    .type-icon {
        width: 24px; height: 24px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        margin-right: 8px; font-size: 11px;
    }
    .type-fixed { background: #e0f2fe; color: #0284c7; }
    .type-percent { background: #fef3c7; color: #d97706; }

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
                <i data-feather="gift" class="text-primary" style="width: 24px; height: 24px;"></i>
                Coupons & Discounts
            </h4>
            <p class="text-muted small mb-0">Create, monitor and manage promotional discount coupons.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i data-feather="plus-circle" style="width: 16px; height: 16px;"></i>
                <span>Add Coupon</span>
            </a>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <div class="stat-icon"><i class="fas fa-ticket-alt"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Total Coupons</div>
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
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Expired</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['expired'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-gradient" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%);">
                <div class="stat-icon"><i class="fas fa-pause-circle"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Inactive</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['inactive'] ?? 0) }}</h3>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.coupons.index') }}" id="filterForm">
                <div class="row g-2 align-items-center">
                    {{-- Keyword Search --}}
                    <div class="col-12 col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i data-feather="search" style="width: 14px;"></i></span>
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control border-start-0" placeholder="Search coupon code...">
                        </div>
                    </div>

                    {{-- Type Filter --}}
                    <div class="col-6 col-md-2">
                        <select name="type" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Types</option>
                            <option value="flat" {{ request('type') == 'flat' ? 'selected' : '' }}>Fixed Amount</option>
                            <option value="percent" {{ request('type') == 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-6 col-md-2">
                        <select name="status" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                        @if(request()->anyFilled(['keyword', 'type', 'status', 'start_date', 'end_date', 'date_preset']))
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters"><i data-feather="rotate-ccw" style="width: 14px;"></i></a>
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
                        <th>Coupon Code</th>
                        <th>Discount Type</th>
                        <th>Discount Value</th>
                        <th>Min Purchase</th>
                        <th>Validity Period</th>
                        <th>Status</th>
                        <th width="100" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $key => $coupon)
                        <tr>
                            <td class="text-muted small">
                                @if(method_exists($coupons, 'firstItem'))
                                    {{ $coupons->firstItem() + $key }}
                                @else
                                    {{ $key + 1 }}
                                @endif
                            </td>
                            
                            {{-- Code --}}
                            <td>
                                <span class="coupon-badge" onclick="copyCouponCode('{{ $coupon->code }}')" title="Click to copy code">
                                    {{ $coupon->code }}
                                    <i data-feather="copy" style="width:12px; height:12px;" class="text-muted"></i>
                                </span>
                            </td>

                            {{-- Type --}}
                            <td>
                                @if(in_array($coupon->type, ['flat', 'fixed']))
                                    <div class="d-flex align-items-center">
                                        <span class="type-icon type-fixed"><i class="fas fa-dollar-sign"></i></span>
                                        <span class="fw-semibold">Fixed (Flat)</span>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center">
                                        <span class="type-icon type-percent"><i class="fas fa-percent"></i></span>
                                        <span class="fw-semibold">Percentage</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Value --}}
                            <td>
                                <span class="fw-bold text-dark fs-6">
                                    @if(in_array($coupon->type, ['percent', 'percentage']))
                                        {{ $coupon->value }}%
                                    @else
                                        ৳{{ number_format($coupon->value, 2) }}
                                    @endif
                                </span>
                            </td>

                            {{-- Min Purchase --}}
                            <td>
                                @if($coupon->min_purchase > 0)
                                    <span class="badge bg-light text-dark border">৳{{ number_format($coupon->min_purchase) }}</span>
                                @else
                                    <span class="text-muted small">No minimum</span>
                                @endif
                            </td>

                            {{-- Validity --}}
                            <td>
                                <div class="d-flex flex-column small">
                                    <span class="text-success">
                                        <i class="far fa-calendar-check me-1"></i> {{ $coupon->valid_from ? date('d M, Y', strtotime($coupon->valid_from)) : 'Anytime' }}
                                    </span>
                                    <span class="text-danger mt-1">
                                        <i class="far fa-calendar-times me-1"></i> {{ $coupon->valid_to ? date('d M, Y', strtotime($coupon->valid_to)) : 'Lifetime' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @php
                                    $isExpired = $coupon->valid_to && \Carbon\Carbon::parse($coupon->valid_to)->isPast();
                                @endphp

                                @if($isExpired)
                                    <span class="badge-soft badge-expired"><i class="fas fa-times-circle"></i> Expired</span>
                                @elseif($coupon->status)
                                    <span class="badge-soft badge-active"><i class="fas fa-check-circle"></i> Active</span>
                                @else
                                    <span class="badge-soft badge-inactive"><i class="fas fa-pause-circle"></i> Inactive</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn-action-icon text-primary" title="Edit Coupon">
                                        <i data-feather="edit-2" style="width:14px; height:14px;"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon text-danger" title="Delete Coupon">
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
                                    <i data-feather="gift" style="width: 48px; height: 48px; opacity: 0.35;" class="mb-2"></i>
                                    <p class="fw-bold mb-1">No Coupons Found</p>
                                    <small class="text-muted">Create new promotional discount codes to incentivize shoppers.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if(method_exists($coupons, 'links') && $coupons->hasPages())
        <div class="p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Showing {{ $coupons->firstItem() }} to {{ $coupons->lastItem() }} of {{ $coupons->total() }} entries
            </div>
            <div>
                {{ $coupons->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script>
    function copyCouponCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            toastr.success('Coupon code "' + code + '" copied to clipboard!');
        });
    }
</script>
@endpush