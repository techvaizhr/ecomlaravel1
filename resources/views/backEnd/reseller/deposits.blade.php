@extends('backEnd.layouts.master')
@section('title', 'Reseller Deposits')

@section('css')
<style>
    /* Header Card */
    .deposit-header-card {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #075985 100%);
        border-radius: 16px;
        padding: 24px;
        color: #ffffff;
        margin-bottom: 20px;
        box-shadow: 0 10px 25px rgba(2, 132, 199, 0.15);
    }
    .metric-badge-box {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 12px;
        padding: 12px 18px;
        text-align: center;
        min-width: 120px;
    }
    .metric-badge-box h3 {
        color: #ffffff;
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 2px;
    }
    .metric-badge-box span {
        color: rgba(255, 255, 255, 0.85);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    /* Filter Card */
    .filter-card-modern {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
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
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        outline: none;
    }

    /* Table Card */
    .table-card-modern {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .table-modern thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 13px 16px;
        white-space: nowrap;
    }
    .table-modern tbody td {
        padding: 13px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
        vertical-align: middle;
        color: #1e293b;
    }
    .table-modern tbody tr:hover {
        background: #fafcff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER CARD --}}
    <div class="deposit-header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="text-white mb-1 fw-bold">
                    <i class="fe-credit-card me-2"></i> Reseller Wallet Deposits
                </h4>
                <p class="mb-0 text-white-50 font-size-13">
                    Review and verify wallet recharge and advance balance deposits by resellers.
                </p>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="metric-badge-box" style="background: rgba(245, 158, 11, 0.3);">
                    <h3>৳{{ number_format($stats['pending_amount'], 2) }}</h3>
                    <span>Pending ({{ $stats['pending_count'] }})</span>
                </div>
                <div class="metric-badge-box">
                    <h3>৳{{ number_format($stats['completed_amount'], 2) }}</h3>
                    <span>Completed Deposits</span>
                </div>
                <div class="metric-badge-box">
                    <h3>৳{{ number_format($stats['total_amount'], 2) }}</h3>
                    <span>Total Volume</span>
                </div>
                <a href="{{ route('admin.resellers.index') }}" class="btn btn-outline-light rounded-pill px-3 shadow-sm font-size-13 fw-bold">
                    <i class="fe-arrow-left me-1"></i> All Resellers
                </a>
            </div>
        </div>
    </div>

    {{-- FILTERS CARD --}}
    <div class="filter-card-modern">
        <form method="GET" action="{{ route('admin.reseller-deposits.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label-modern">Search Reseller / TrxID</label>
                    <input type="text" name="keyword" class="form-control form-control-modern" 
                           placeholder="Reseller name, email, TrxID..." value="{{ request('keyword') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Status</label>
                    <select name="status" class="form-select form-select-modern">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed / Paid</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-modern" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-modern" value="{{ request('date_to') }}">
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
                        @if(request()->anyFilled(['keyword', 'status', 'date_from', 'date_to', 'per_page']))
                            <a href="{{ route('admin.reseller-deposits.index') }}" class="btn btn-light border rounded-3 px-3 py-2" title="Reset Filters">
                                <i class="fe-rotate-ccw"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- DEPOSITS TABLE --}}
    <div class="table-card-modern">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Reseller Profile</th>
                        <th>Deposit Amount</th>
                        <th>Transaction ID</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deposits as $d)
                    <tr>
                        <td class="text-muted fw-semibold">
                            {{ $loop->iteration + ($deposits->currentPage() - 1) * $deposits->perPage() }}
                        </td>
                        <td>
                            <div class="fw-bold text-dark font-size-14">{{ $d->user->name ?? 'N/A' }}</div>
                            @if($d->user && $d->user->shop_name)
                                <span class="text-muted font-size-12">{{ $d->user->shop_name }}</span>
                            @endif
                            <div class="text-muted font-size-11">{{ $d->user->email ?? '' }}</div>
                        </td>
                        <td>
                            <span class="fw-bold text-success font-size-15">
                                ৳{{ number_format($d->amount, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-dark font-monospace font-size-12 fw-semibold">
                                {{ $d->transaction_id ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-dark font-size-13">{{ $d->created_at ? $d->created_at->format('d M, Y') : 'N/A' }}</div>
                            <span class="text-muted font-size-11">{{ $d->created_at ? $d->created_at->format('h:i A') : '' }}</span>
                        </td>
                        <td>
                            @if($d->status === 'completed')
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill font-size-12 fw-bold">
                                    <i class="fe-check-circle me-1"></i> Completed
                                </span>
                            @elseif($d->status === 'failed')
                                <span class="badge bg-soft-danger text-danger px-2 py-1 rounded-pill font-size-12 fw-bold">
                                    <i class="fe-x-circle me-1"></i> Failed
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-2 py-1 rounded-pill font-size-12 fw-bold">
                                    <i class="fe-clock me-1"></i> Pending Verification
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($d->status === 'pending')
                                <form action="{{ route('admin.reseller-deposits.mark-paid', $d->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Have you received this payment? Marking as paid will immediately credit ৳{{ number_format($d->amount, 2) }} to the reseller wallet.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold font-size-12 shadow-sm">
                                        <i class="fe-check me-1"></i> Mark as Paid
                                    </button>
                                </form>
                            @else
                                <span class="text-muted font-size-12 fst-italic">Verified</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fe-credit-card font-size-36 d-block mb-2 text-slate-300"></i>
                            <p class="mb-0 font-size-14 fw-semibold">No deposit transactions found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deposits->hasPages() || $deposits->total() > 0)
        <div class="p-3 bg-light border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="text-muted font-size-13">
                Showing <strong>{{ $deposits->firstItem() ?? 0 }}</strong> to <strong>{{ $deposits->lastItem() ?? 0 }}</strong> of <strong>{{ $deposits->total() }}</strong> Deposits
            </div>
            <div>
                {{ $deposits->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
