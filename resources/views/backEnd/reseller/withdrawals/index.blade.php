@extends('backEnd.layouts.master')
@section('title', 'Reseller Withdrawal Requests')

@section('css')
<style>
    /* Header Card */
    .withdrawal-header-card {
        background: linear-gradient(135deg, #059669 0%, #10b981 50%, #14b8a6 100%);
        border-radius: 16px;
        padding: 24px;
        color: #ffffff;
        margin-bottom: 20px;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.15);
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
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
        outline: none;
    }

    /* Table */
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

    /* Method Pill */
    .method-pill {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-transform: capitalize;
    }
    .method-bkash { background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; }
    .method-nagad { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
    .method-rocket { background: #faf5ff; color: #7e22ce; border: 1px solid #f3e8ff; }
    .method-bank { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
    .method-other { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

    /* Action Circle Buttons */
    .action-circle-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: #f1f5f9;
        color: #475569;
        transition: all 0.2s;
        text-decoration: none;
        font-size: 13px;
    }
    .action-circle-btn:hover { transform: translateY(-2px); }
    .btn-action-approve:hover { background: #dcfce7; color: #15803d; }
    .btn-action-reject:hover { background: #fee2e2; color: #dc2626; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER CARD --}}
    <div class="withdrawal-header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="text-white mb-1 fw-bold">
                    <i class="fe-dollar-sign me-2"></i> Reseller Withdrawal Payouts
                </h4>
                <p class="mb-0 text-white-50 font-size-13">
                    Review and process payout requests to reseller mobile wallets and bank accounts.
                </p>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="metric-badge-box" style="background: rgba(245, 158, 11, 0.3);">
                    <h3>৳{{ number_format($stats['pending_amount'], 2) }}</h3>
                    <span>Pending ({{ $stats['pending_count'] }})</span>
                </div>
                <div class="metric-badge-box">
                    <h3>৳{{ number_format($stats['approved_amount'], 2) }}</h3>
                    <span>Approved Payouts</span>
                </div>
                <div class="metric-badge-box">
                    <h3>৳{{ number_format($stats['fund_balance'], 2) }}</h3>
                    <span>Admin Fund Balance</span>
                </div>
                <a href="{{ route('admin.resellers.index') }}" class="btn btn-outline-light rounded-pill px-3 shadow-sm font-size-13 fw-bold">
                    <i class="fe-arrow-left me-1"></i> All Resellers
                </a>
            </div>
        </div>
    </div>

    {{-- FILTERS CARD --}}
    <div class="filter-card-modern">
        <form method="GET" action="{{ route('admin.reseller.withdrawals.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label-modern">Search Keyword</label>
                    <input type="text" name="keyword" class="form-control form-control-modern" 
                           placeholder="Reseller name, shop, account no, phone..." value="{{ request('keyword') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Status</label>
                    <select name="status" class="form-select form-select-modern">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Requests</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Payout Method</label>
                    <select name="payout_method" class="form-select form-select-modern">
                        <option value="">All Methods</option>
                        <option value="bkash" {{ request('payout_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                        <option value="nagad" {{ request('payout_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                        <option value="rocket" {{ request('payout_method') === 'rocket' ? 'selected' : '' }}>Rocket</option>
                        <option value="bank" {{ request('payout_method') === 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-modern">Date Filter</label>
                    @include('backEnd.layouts.partials.smart_date_filter', ['startKey' => 'date_from', 'endKey' => 'date_to'])
                </div>
                <div class="col-md-1">
                    <label class="form-label-modern">Per Page</label>
                    <select name="per_page" class="form-select form-select-modern">
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary rounded-3 w-100 fw-bold font-size-13 py-2" style="background: #059669; border-color: #059669;">
                            <i class="fe-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['keyword', 'status', 'payout_method', 'date_from', 'per_page']))
                            <a href="{{ route('admin.reseller.withdrawals.index') }}" class="btn btn-light border rounded-3 px-3 py-2" title="Reset Filters">
                                <i class="fe-rotate-ccw"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- WITHDRAWALS TABLE --}}
    <div class="table-card-modern">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Reseller Details</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Account Details</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 120px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                    <tr>
                        <td class="text-muted fw-semibold">
                            {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}
                        </td>
                        <td>
                            <div class="fw-bold text-dark font-size-14">{{ $row->user->name ?? 'Reseller #' . $row->user_id }}</div>
                            @if($row->user && $row->user->shop_name)
                                <span class="text-muted font-size-12">Shop: {{ $row->user->shop_name }}</span>
                            @endif
                            <div class="text-muted font-size-11">{{ $row->user->email ?? '' }}</div>
                        </td>
                        <td>
                            <span class="fw-bold text-dark font-size-15">
                                ৳{{ number_format($row->amount, 2) }}
                            </span>
                            @if(isset($row->charge) && $row->charge > 0)
                                <div class="text-muted font-size-11">Charge: ৳{{ number_format($row->charge, 2) }}</div>
                            @endif
                        </td>
                        <td>
                            @php
                                $methodLower = strtolower($row->payout_method ?? 'other');
                                $pillClass = 'method-other';
                                if($methodLower === 'bkash') $pillClass = 'method-bkash';
                                elseif($methodLower === 'nagad') $pillClass = 'method-nagad';
                                elseif($methodLower === 'rocket') $pillClass = 'method-rocket';
                                elseif($methodLower === 'bank') $pillClass = 'method-bank';
                            @endphp
                            <span class="method-pill {{ $pillClass }}">
                                {{ ucfirst($row->payout_method ?? 'N/A') }}
                            </span>
                        </td>
                        <td>
                            <div class="font-size-13">
                                @if($row->account_name)
                                    <div class="fw-semibold text-dark">{{ $row->account_name }}</div>
                                @endif
                                @if($row->account_number)
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <span class="text-dark font-monospace font-size-12 fw-bold">{{ $row->account_number }}</span>
                                        <button type="button" class="btn btn-sm btn-light border p-0 d-inline-flex align-items-center justify-content-center" 
                                                onclick="copyToClipboard('{{ $row->account_number }}', this)" 
                                                title="Copy Account Number" style="width: 22px; height: 22px; border-radius: 4px;">
                                            <i class="fe-copy font-size-11 text-muted"></i>
                                        </button>
                                    </div>
                                @endif
                                @if($row->note)
                                    <div class="text-muted fst-italic font-size-11 mt-1">"{{ Str::limit($row->note, 30) }}"</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="text-dark font-size-13">{{ $row->created_at ? $row->created_at->format('d M, Y') : 'N/A' }}</div>
                            <span class="text-muted font-size-11">{{ $row->created_at ? $row->created_at->format('h:i A') : '' }}</span>
                        </td>
                        <td>
                            @if($row->status === 'approved')
                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill font-size-12 fw-bold">
                                    <i class="fe-check-circle me-1"></i> Approved
                                </span>
                                @if($row->processed_at)
                                    <div class="text-muted font-size-11 mt-1">{{ $row->processed_at->format('d M, Y') }}</div>
                                @endif
                            @elseif($row->status === 'rejected')
                                <span class="badge bg-soft-danger text-danger px-2 py-1 rounded-pill font-size-12 fw-bold">
                                    <i class="fe-x-circle me-1"></i> Rejected
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-2 py-1 rounded-pill font-size-12 fw-bold">
                                    <i class="fe-clock me-1"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($row->status === 'pending')
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="action-circle-btn btn-action-approve" title="Approve Payout" data-bs-toggle="modal" data-bs-target="#modalApproveResellerWithdraw{{ $row->id }}">
                                        <i class="fe-check"></i>
                                    </button>
                                    <button type="button" class="action-circle-btn btn-action-reject" title="Reject Payout" data-bs-toggle="modal" data-bs-target="#modalRejectResellerWithdraw{{ $row->id }}">
                                        <i class="fe-x"></i>
                                    </button>
                                </div>

                                {{-- Approve Modal --}}
                                <div class="modal fade text-start" id="modalApproveResellerWithdraw{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form action="{{ route('admin.reseller.withdrawals.approve', $row->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title fw-bold"><i class="fe-check-circle me-1"></i> Approve ৳{{ number_format($row->amount, 2) }} Payout</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="alert alert-light border mb-3">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="text-muted">Reseller:</span>
                                                            <strong class="text-dark">{{ $row->user->name ?? 'Reseller' }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="text-muted">Method:</span>
                                                            <strong class="text-dark">{{ ucfirst($row->payout_method) }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span class="text-muted">Account No:</span>
                                                            <strong class="text-dark">{{ $row->account_number }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label font-size-13 fw-semibold">Admin Note / Transaction Ref (Optional)</label>
                                                        <textarea name="admin_note" class="form-control" rows="2" placeholder="e.g. Paid via bKash TrxID: 9X87YW..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success fw-semibold">Confirm Payout Approval</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Reject Modal --}}
                                <div class="modal fade text-start" id="modalRejectResellerWithdraw{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form action="{{ route('admin.reseller.withdrawals.reject', $row->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title fw-bold"><i class="fe-x-circle me-1"></i> Reject ৳{{ number_format($row->amount, 2) }} Withdrawal</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="text-muted font-size-13 mb-3">
                                                        The requested amount of <strong>৳{{ number_format($row->amount, 2) }}</strong> will be automatically refunded back to the reseller's wallet balance.
                                                    </p>
                                                    <div class="mb-3">
                                                        <label class="form-label font-size-13 fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                                                        <textarea name="admin_note" class="form-control" rows="3" required placeholder="e.g. Incorrect account number or mismatch beneficiary name..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger fw-semibold">Confirm Rejection</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted font-size-12 fst-italic">Completed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fe-dollar-sign font-size-36 d-block mb-2 text-slate-300"></i>
                            <p class="mb-0 font-size-14 fw-semibold">No withdrawal requests found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($data->hasPages() || $data->total() > 0)
        <div class="p-3 bg-light border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="text-muted font-size-13">
                Showing <strong>{{ $data->firstItem() ?? 0 }}</strong> to <strong>{{ $data->lastItem() ?? 0 }}</strong> of <strong>{{ $data->total() }}</strong> Payouts
            </div>
            <div>
                {{ $data->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
    function copyToClipboard(text, btn) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(function() {
            if (typeof toastr !== 'undefined') {
                toastr.success('Account number copied: ' + text);
            }
            var icon = btn.querySelector('i');
            if (icon) {
                icon.className = 'fe-check text-success font-size-11';
                setTimeout(function() {
                    icon.className = 'fe-copy font-size-11 text-muted';
                }, 2000);
            }
        }).catch(function() {
            var temp = document.createElement('input');
            temp.value = text;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            if (typeof toastr !== 'undefined') {
                toastr.success('Account number copied: ' + text);
            }
        });
    }
</script>
@endsection