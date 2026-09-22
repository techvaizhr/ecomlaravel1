@extends('backEnd.layouts.master')
@section('title', 'Rider Withdrawals')

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
    .metric-icon.red { background: #fee2e2; color: #dc2626; }

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
        width: 40px; height: 40px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #fff;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.95rem;
        margin-right: 12px;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(217, 119, 6, 0.25);
    }
    .badge-soft {
        padding: 5px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-pending { background: #fef9c3; color: #854d0e; }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .amount-highlight {
        font-variant-numeric: tabular-nums;
        font-weight: 700;
        font-size: 1rem;
        color: #0f172a;
    }
    .method-pill {
        display: inline-block;
        padding: 0.25rem 0.65rem;
        background: #f1f5f9;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        text-transform: capitalize;
    }
    .copy-btn {
        background: none;
        border: none;
        padding: 2px 6px;
        color: #64748b;
        cursor: pointer;
        transition: color 0.15s;
    }
    .copy-btn:hover { color: #0284c7; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Top Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                <i data-feather="credit-card" class="text-primary me-2" style="width:26px;height:26px;"></i>
                Rider Withdrawal Requests
            </h4>
            <p class="text-muted small mb-0">Audit, approve and reject payout requests submitted by delivery riders.</p>
        </div>
        <a href="{{ route('admin.delivery-boys.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i data-feather="arrow-left" class="me-1" style="width:16px;height:16px;"></i> Back to Riders
        </a>
    </div>

    <!-- Live Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon amber">
                    <i data-feather="clock" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Pending Requests</div>
                    <h3 class="mb-0 fw-bold text-warning">{{ number_format($stats['pending_count'] ?? 0) }}</h3>
                    <small class="text-muted">৳{{ number_format($stats['pending_amount'] ?? 0, 2) }}</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon green">
                    <i data-feather="check-circle" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Approved Payouts</div>
                    <h3 class="mb-0 fw-bold text-success">৳{{ number_format($stats['approved_amount'] ?? 0, 2) }}</h3>
                    <small class="text-muted">{{ number_format($stats['approved_count'] ?? 0) }} payouts</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon red">
                    <i data-feather="x-circle" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Rejected Requests</div>
                    <h3 class="mb-0 fw-bold text-danger">{{ number_format($stats['rejected_count'] ?? 0) }}</h3>
                    <small class="text-muted">Refunded to wallet</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon purple">
                    <i data-feather="pocket" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Admin Fund Balance</div>
                    <h3 class="mb-0 fw-bold text-primary">৳{{ number_format($stats['fund_balance'] ?? 0, 2) }}</h3>
                    <small class="text-muted">Available liquidity</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-modern mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.delivery-boys.withdrawals') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i data-feather="search" style="width:16px;"></i></span>
                            <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Rider, phone or account..." value="{{ request('keyword') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="payout_method" class="form-select">
                            <option value="">All Methods</option>
                            <option value="bkash" {{ request('payout_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                            <option value="nagad" {{ request('payout_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                            <option value="rocket" {{ request('payout_method') === 'rocket' ? 'selected' : '' }}>Rocket</option>
                            <option value="bank" {{ request('payout_method') === 'bank' ? 'selected' : '' }}>Bank</option>
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
                        @if(request()->anyFilled(['keyword', 'status', 'payout_method', 'per_page']))
                            <a href="{{ route('admin.delivery-boys.withdrawals') }}" class="btn btn-outline-secondary" title="Reset Filters">
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
                            <th>Rider Info</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Account / Payout No</th>
                            <th>Requested At</th>
                            <th>Status</th>
                            <th class="text-end" width="18%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $key => $w)
                            @php
                                $status = strtolower($w->status ?? '');
                            @endphp
                            <tr>
                                <td class="text-muted small">#{{ $w->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rider-avatar">
                                            {{ strtoupper(substr($w->deliveryBoy->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-semibold text-dark d-block">{{ $w->deliveryBoy->name ?? '—' }}</span>
                                            <span class="text-muted small font-monospace">{{ $w->deliveryBoy->phone ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="amount-highlight">৳{{ number_format($w->amount, 2) }}</span></td>
                                <td><span class="method-pill">{{ $w->payout_method ?? '—' }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="font-monospace fw-semibold text-dark">{{ $w->payout_number ?? '—' }}</span>
                                        @if($w->payout_number)
                                            <button type="button" class="copy-btn" onclick="copyNumber('{{ $w->payout_number }}', this)" title="Copy Number">
                                                <i data-feather="copy" style="width:14px;height:14px;"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @if($w->note)
                                        <small class="text-muted d-block mt-1">{{ $w->note }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-dark small d-block">{{ $w->created_at ? $w->created_at->format('d M, Y') : '—' }}</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $w->created_at ? $w->created_at->format('h:i A') : '' }}</span>
                                </td>
                                <td>
                                    @if($status === 'pending')
                                        <span class="badge-soft badge-pending"><span class="status-dot"></span> Pending</span>
                                    @elseif($status === 'approved')
                                        <span class="badge-soft badge-approved"><span class="status-dot"></span> Approved</span>
                                    @elseif($status === 'rejected')
                                        <span class="badge-soft badge-rejected"><span class="status-dot"></span> Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $w->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($status === 'pending')
                                        <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#approveModal{{ $w->id }}">
                                                <i data-feather="check" class="me-1" style="width:14px;height:14px;"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $w->id }}">
                                                <i data-feather="x" class="me-1" style="width:14px;height:14px;"></i> Reject
                                            </button>
                                        </div>

                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveModal{{ $w->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered text-start">
                                                <div class="modal-content border-0 shadow">
                                                    <form action="{{ route('admin.delivery-boys.withdrawals.approve') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $w->id }}">
                                                        <div class="modal-header border-bottom">
                                                            <h5 class="modal-title fw-bold text-success">
                                                                <i data-feather="check-circle" class="me-2"></i> Approve Payout Request #{{ $w->id }}
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body py-4">
                                                            <div class="p-3 bg-light rounded-3 mb-3">
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">Rider:</span>
                                                                    <span class="fw-bold">{{ $w->deliveryBoy->name ?? '—' }}</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">Payout Method:</span>
                                                                    <span class="fw-semibold text-capitalize">{{ $w->payout_method }}</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">Account Number:</span>
                                                                    <span class="font-monospace fw-bold text-primary">{{ $w->payout_number }}</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                                                    <span class="text-muted">Amount to Pay:</span>
                                                                    <span class="fw-bold fs-5 text-dark">৳{{ number_format($w->amount, 2) }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold">Admin Note / Transaction Ref (Optional)</label>
                                                                <input type="text" name="admin_note" class="form-control" placeholder="e.g., TrxID 8472938472 or Bank Tx Ref">
                                                            </div>
                                                            <div class="alert alert-warning py-2 mb-0 small">
                                                                <i data-feather="alert-triangle" class="me-1" style="width:14px;"></i> This will deduct ৳{{ number_format($w->amount, 2) }} from the rider wallet and admin fund.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top">
                                                            <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success rounded-pill px-4">Confirm & Approve</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $w->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered text-start">
                                                <div class="modal-content border-0 shadow">
                                                    <form action="{{ route('admin.delivery-boys.withdrawals.reject') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $w->id }}">
                                                        <div class="modal-header border-bottom">
                                                            <h5 class="modal-title fw-bold text-danger">
                                                                <i data-feather="alert-circle" class="me-2"></i> Reject Withdrawal #{{ $w->id }}
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body py-4">
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                                                                <textarea name="admin_note" class="form-control" rows="3" placeholder="Provide reason for rejecting this payout request..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top">
                                                            <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger rounded-pill px-4">Reject Request</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @if($w->admin_note)
                                            <span class="text-muted small fst-italic" title="{{ $w->admin_note }}">{{ Str::limit($w->admin_note, 25) }}</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i data-feather="inbox" class="mb-2 opacity-50" style="width:40px;height:40px;"></i>
                                    <p class="mb-0">No withdrawal requests found matching your filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rows->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ $rows->total() }} withdrawal records
                    </div>
                    <div>
                        {{ $rows->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function copyNumber(text, btn) {
    if (!navigator.clipboard) {
        var dummy = document.createElement("input");
        document.body.appendChild(dummy);
        dummy.setAttribute("value", text);
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);
    } else {
        navigator.clipboard.writeText(text);
    }
    
    var originalHTML = btn.innerHTML;
    btn.innerHTML = '<span class="text-success" style="font-size:12px;">Copied!</span>';
    setTimeout(function() {
        btn.innerHTML = originalHTML;
        feather.replace();
    }, 1500);
}
</script>
@endsection
