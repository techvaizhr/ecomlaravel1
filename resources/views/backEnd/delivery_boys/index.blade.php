@extends('backEnd.layouts.master')
@section('title', 'Delivery Persons')

@section('css')
<style>
    /* Metric Cards */
    .metric-card {
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        background: #fff;
        border: 1px solid #edf2f7;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .metric-icon.blue { background: #e0f2fe; color: #0284c7; }
    .metric-icon.green { background: #dcfce7; color: #16a34a; }
    .metric-icon.purple { background: #f3e8ff; color: #9333ea; }
    .metric-icon.amber { background: #fef3c7; color: #d97706; }

    .card-modern {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
        overflow: hidden;
    }
    .table-modern th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 1rem 1.25rem;
        border-bottom: 2px solid #edf2f7;
        white-space: nowrap;
    }
    .table-modern td {
        vertical-align: middle;
        padding: 1rem 1.25rem;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tr:hover td { background-color: #fbfcfe; }
    .rider-avatar {
        width: 42px; height: 42px;
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        color: #fff;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1rem;
        margin-right: 12px;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(79, 70, 229, 0.25);
    }
    .badge-soft {
        padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-inactive { background: #f1f5f9; color: #64748b; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .amount-cell { font-variant-numeric: tabular-nums; font-weight: 700; color: #0f172a; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Top Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                <i data-feather="truck" class="text-primary me-2" style="width:26px;height:26px;"></i>
                Delivery Persons (Riders)
            </h4>
            <p class="text-muted small mb-0">Manage riders, delivery commission rates, wallets, and salaries.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.delivery-boys.withdrawals') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i data-feather="dollar-sign" class="me-1" style="width:16px;height:16px;"></i> Rider Withdrawals
            </a>
            <a href="{{ route('admin.delivery-boys.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i data-feather="plus" class="me-1" style="width:16px;height:16px;"></i> Add Delivery Person
            </a>
        </div>
    </div>

    <!-- Live Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon blue">
                    <i data-feather="users" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Riders</div>
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($stats['total_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon green">
                    <i data-feather="user-check" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Active Riders</div>
                    <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['active_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon amber">
                    <i data-feather="user-x" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Inactive Riders</div>
                    <h3 class="mb-0 fw-bold text-secondary">{{ number_format($stats['inactive_count'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon purple">
                    <i data-feather="dollar-sign" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Riders Wallet Balance</div>
                    <h3 class="mb-0 fw-bold text-primary">৳{{ number_format($stats['total_balance'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.delivery-boys.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="search" style="width:16px;"></i></span>
                            <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Search by name, phone or email..." value="{{ request('keyword') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="per_page" class="form-select">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / page</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / page</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / page</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / page</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / page</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All Records</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i data-feather="filter" class="me-1" style="width:16px;"></i> Filter
                        </button>
                        @if(request()->anyFilled(['keyword', 'status', 'per_page']))
                            <a href="{{ route('admin.delivery-boys.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                                <i data-feather="refresh-cw" style="width:16px;"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card card-modern">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Rider / Delivery Person</th>
                            <th>Phone</th>
                            <th>Commission / Delivery</th>
                            <th>Monthly Salary</th>
                            <th>Wallet Balance</th>
                            <th>Status</th>
                            <th class="text-end" width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $key => $r)
                            <tr>
                                <td class="text-muted small">{{ $rows->firstItem() ? $rows->firstItem() + $key : $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if(!empty($r->image))
                                            <img src="{{ asset($r->image) }}" alt="{{ $r->name }}" class="rounded-circle me-3" style="width: 42px; height: 42px; object-fit: cover; border: 2px solid #e2e8f0;">
                                        @else
                                            <div class="rider-avatar">{{ strtoupper(substr($r->name, 0, 1)) }}</div>
                                        @endif
                                        <div>
                                            <span class="fw-semibold text-dark d-block">{{ $r->name }}</span>
                                            @if($r->email)
                                                <span class="text-muted small">{{ $r->email }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="font-monospace text-muted">{{ $r->phone }}</td>
                                <td class="amount-cell">৳{{ number_format($r->commission_per_delivery, 2) }}</td>
                                <td class="amount-cell">৳{{ number_format($r->monthly_salary_amount ?? 0, 2) }}</td>
                                <td>
                                    <span class="badge bg-light text-primary border px-2 py-1 fw-bold fs-6">
                                        ৳{{ number_format($r->wallet_balance, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if($r->status)
                                        <span class="badge-soft badge-active"><span class="status-dot"></span> Active</span>
                                    @else
                                        <span class="badge-soft badge-inactive"><span class="status-dot"></span> Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 justify-content-end">
                                        <a href="{{ route('admin.delivery-boys.wallet', $r->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3" title="View Wallet Transactions">
                                            <i data-feather="pocket" class="me-1" style="width:14px;height:14px;"></i> Wallet
                                        </a>
                                        <a href="{{ route('admin.delivery-boys.edit', $r->id) }}" class="btn btn-sm btn-primary rounded-pill px-3" title="Edit Rider">
                                            <i data-feather="edit-2" class="me-1" style="width:14px;height:14px;"></i> Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i data-feather="inbox" class="mb-2 opacity-50" style="width:40px;height:40px;"></i>
                                    <p class="mb-0">No delivery persons found matching your criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rows->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ $rows->total() }} delivery persons
                    </div>
                    <div>
                        {{ $rows->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
