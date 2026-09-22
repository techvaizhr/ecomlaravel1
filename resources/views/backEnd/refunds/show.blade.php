@extends('backEnd.layouts.master')
@section('title', 'Refund Details #' . $refund->refund_id)

@section('css')
<style>
    :root {
        --rf-primary: #4f46e5;
        --rf-primary-light: #eef2ff;
        --rf-success: #10b981;
        --rf-success-light: #ecfdf5;
        --rf-warning: #f59e0b;
        --rf-warning-light: #fffbeb;
        --rf-danger: #ef4444;
        --rf-danger-light: #fef2f2;
        --rf-info: #06b6d4;
        --rf-info-light: #ecfeff;
        --rf-border: #e2e8f0;
        --rf-card-bg: #ffffff;
        --rf-text-main: #1e293b;
        --rf-text-muted: #64748b;
    }

    .refund-page-shell {
        padding: 24px 16px 40px;
        background-color: #f8fafc;
        min-height: 100vh;
    }

    .rf-card {
        background: var(--rf-card-bg);
        border: 1px solid var(--rf-border);
        border-radius: 14px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .rf-card-header {
        padding: 16px 20px;
        background: #ffffff;
        border-bottom: 1px solid var(--rf-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .rf-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--rf-text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .rf-card-body {
        padding: 20px;
    }

    .rf-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .rf-badge-pending { background-color: var(--rf-warning-light); color: #b45309; border: 1px solid #fde68a; }
    .rf-badge-approved { background-color: var(--rf-info-light); color: #0369a1; border: 1px solid #bae6fd; }
    .rf-badge-processed { background-color: var(--rf-success-light); color: #047857; border: 1px solid #a7f3d0; }
    .rf-badge-rejected { background-color: var(--rf-danger-light); color: #b91c1c; border: 1px solid #fecaca; }

    .rf-amount-hero {
        font-size: 2rem;
        font-weight: 800;
        color: var(--rf-primary);
        line-height: 1.1;
    }

    .rf-info-tile {
        background: #f8fafc;
        border: 1px solid var(--rf-border);
        border-radius: 10px;
        padding: 12px 16px;
    }
    .rf-info-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--rf-text-muted);
        font-weight: 600;
        margin-bottom: 4px;
    }
    .rf-info-val {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--rf-text-main);
    }

    .rf-thumb {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid var(--rf-border);
    }

    .rf-btn-copy {
        background: none;
        border: none;
        padding: 2px 6px;
        color: #94a3b8;
        cursor: pointer;
        font-size: 0.85rem;
    }
    .rf-btn-copy:hover { color: var(--rf-primary); }

    .rf-modal-content {
        border-radius: 16px;
        border: 1px solid var(--rf-border);
        overflow: hidden;
    }
</style>
@endsection

@section('content')
@php
    $totalRefund = $refund->amount + $refund->shipping_charge;
@endphp
<div class="container-fluid refund-page-shell">

    {{-- Top Action Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.refunds.index') }}" class="btn btn-light border rounded-pill px-3 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Refunds
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">
                    Refund Request <span class="font-monospace text-primary">#{{ $refund->refund_id }}</span>
                </h4>
                <div class="text-muted small">Submitted on {{ $refund->created_at->format('d M, Y \a\t h:i A') }}</div>
            </div>
        </div>

        <div>
            @if($refund->status == 'pending')
                <span class="rf-badge rf-badge-pending"><i class="fas fa-hourglass-half"></i> Pending Review</span>
            @elseif($refund->status == 'approved')
                <span class="rf-badge rf-badge-approved"><i class="fas fa-check"></i> Approved</span>
            @elseif($refund->status == 'processed')
                <span class="rf-badge rf-badge-processed"><i class="fas fa-check-double"></i> Processed & Paid</span>
            @elseif($refund->status == 'rejected')
                <span class="rf-badge rf-badge-rejected"><i class="fas fa-times"></i> Rejected</span>
            @endif
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-8">
            {{-- Summary Hero Card --}}
            <div class="rf-card">
                <div class="rf-card-body">
                    <div class="row align-items-center g-3">
                        <div class="col-sm-6">
                            <div class="rf-info-label">Total Refund Claim</div>
                            <div class="rf-amount-hero">৳{{ number_format($totalRefund, 2) }}</div>
                            <div class="text-muted small mt-1">
                                Product Amount: ৳{{ number_format($refund->amount, 2) }} 
                                @if($refund->shipping_charge > 0)
                                + Shipping: ৳{{ number_format($refund->shipping_charge, 2) }}
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <div class="rf-info-label">Order Reference</div>
                            @if($refund->order)
                            <a href="{{ route('admin.order.invoice', ['invoice_id' => $refund->order->invoice_id]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                <i class="fas fa-file-invoice me-1"></i> View Invoice #{{ $refund->order->invoice_id }}
                            </a>
                            @else
                            <span class="text-muted">No order linked</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Customer Reason --}}
            <div class="rf-card">
                <div class="rf-card-header">
                    <h6 class="rf-card-title">
                        <i class="fas fa-comment-dots text-warning"></i> Customer Return Reason
                    </h6>
                </div>
                <div class="rf-card-body">
                    <div class="p-3 bg-light rounded-3 text-dark border">
                        "{{ $refund->reason }}"
                    </div>
                </div>
            </div>

            {{-- Order Item Breakdown --}}
            <div class="rf-card">
                <div class="rf-card-header">
                    <h6 class="rf-card-title">
                        <i class="fas fa-box-open text-primary"></i> Order Items Breakdown
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60">Item</th>
                                <th>Product Name</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($refund->order->orderdetails ?? [] as $item)
                            @php
                                $img = $item->product->image->image ?? $item->image->image ?? null;
                            @endphp
                            <tr>
                                <td>
                                    <img src="{{ $img ? asset($img) : asset('public/no-image.png') }}" class="rf-thumb" alt="Product" onerror="this.src='{{ asset('public/no-image.png') }}'">
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->product_name }}</div>
                                </td>
                                <td class="text-center fw-bold">{{ $item->qty }}</td>
                                <td class="text-end">৳{{ number_format($item->sale_price, 2) }}</td>
                                <td class="text-end fw-bold">৳{{ number_format($item->sale_price * $item->qty, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Admin Notes --}}
            @if($refund->admin_note)
            <div class="rf-card">
                <div class="rf-card-header">
                    <h6 class="rf-card-title">
                        <i class="fas fa-sticky-note text-secondary"></i> Admin Notes / Processing Logs
                    </h6>
                </div>
                <div class="rf-card-body">
                    <p class="mb-1 text-dark">{{ $refund->admin_note }}</p>
                    @if($refund->processedBy)
                    <div class="text-muted small mt-2">
                        <i class="fas fa-user-check me-1"></i> Recorded by: <strong>{{ $refund->processedBy->name }}</strong>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            {{-- Quick Action Card --}}
            <div class="rf-card">
                <div class="rf-card-header bg-light">
                    <h6 class="rf-card-title">
                        <i class="fas fa-bolt text-warning"></i> Actions
                    </h6>
                </div>
                <div class="rf-card-body">
                    @if($refund->status == 'pending')
                        <button type="button" class="btn btn-primary w-100 mb-2 py-2 fw-semibold rounded-3" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="fas fa-check-circle me-1"></i> Approve Refund
                        </button>
                        <button type="button" class="btn btn-outline-danger w-100 py-2 fw-semibold rounded-3" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times-circle me-1"></i> Reject Request
                        </button>
                    @elseif($refund->status == 'approved')
                        <button type="button" class="btn btn-success w-100 py-2 fw-semibold rounded-3" data-bs-toggle="modal" data-bs-target="#processModal">
                            <i class="fas fa-credit-card me-1"></i> Process & Disburse Payment
                        </button>
                    @elseif($refund->status == 'processed')
                        <div class="alert alert-success border-0 rounded-3 text-center mb-0">
                            <i class="fas fa-check-double fs-4 mb-2 d-block"></i>
                            <strong>Payment Completed</strong>
                            <div class="small text-muted mt-1">Processed on {{ $refund->processed_at ? $refund->processed_at->format('d M, Y h:i A') : 'N/A' }}</div>
                        </div>
                    @else
                        <div class="alert alert-secondary border-0 rounded-3 text-center mb-0">
                            <i class="fas fa-ban fs-4 mb-2 d-block"></i>
                            <strong>Refund Rejected</strong>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Payment Target Details --}}
            <div class="rf-card">
                <div class="rf-card-header">
                    <h6 class="rf-card-title">
                        <i class="fas fa-wallet text-success"></i> Customer Payout Details
                    </h6>
                </div>
                <div class="rf-card-body">
                    <div class="mb-3">
                        <div class="rf-info-label">Payment Channel</div>
                        <span class="badge bg-light text-dark border text-uppercase fs-6">
                            {{ str_replace('_', ' ', $refund->refund_method) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <div class="rf-info-label">Account / Mobile Number</div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="font-monospace fw-bold text-dark fs-6">{{ $refund->refund_account ?? '—' }}</span>
                            @if($refund->refund_account)
                            <button type="button" class="rf-btn-copy" onclick="copyToClipboard('{{ $refund->refund_account }}')" title="Copy Account">
                                <i class="far fa-copy"></i>
                            </button>
                            @endif
                        </div>
                    </div>

                    @if($refund->refund_account_name)
                    <div class="mb-3">
                        <div class="rf-info-label">Account Name</div>
                        <div class="fw-semibold text-dark">{{ $refund->refund_account_name }}</div>
                    </div>
                    @endif

                    @if($refund->transaction_id)
                    <div class="p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 mt-3">
                        <div class="rf-info-label text-success">Disbursement Transaction ID</div>
                        <div class="d-flex align-items-center gap-1 font-monospace fw-bold text-success">
                            <span>{{ $refund->transaction_id }}</span>
                            <button type="button" class="rf-btn-copy text-success" onclick="copyToClipboard('{{ $refund->transaction_id }}')" title="Copy TrxID">
                                <i class="far fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Customer Card --}}
            <div class="rf-card">
                <div class="rf-card-header">
                    <h6 class="rf-card-title">
                        <i class="fas fa-user text-info"></i> Customer Information
                    </h6>
                </div>
                <div class="rf-card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            {{ mb_substr($refund->customer->name ?? 'G', 0, 1) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $refund->customer->name ?? 'Guest' }}</div>
                            <div class="text-muted small">{{ $refund->customer->phone ?? '—' }}</div>
                        </div>
                    </div>

                    @if($refund->customer->email ?? false)
                    <div class="mb-2 text-muted small">
                        <i class="fas fa-envelope me-2 text-primary"></i> {{ $refund->customer->email }}
                    </div>
                    @endif

                    @if($refund->customer->address ?? false)
                    <div class="text-muted small">
                        <i class="fas fa-map-marker-alt me-2 text-danger"></i> {{ $refund->customer->address }}{{ $refund->customer->district ? ', ' . $refund->customer->district : '' }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals for Show Page --}}
{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rf-modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold mb-0 text-white">Approve Refund Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.refunds.approve', $refund->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted mb-3">Confirm approval for refund amount of <strong>৳{{ number_format($totalRefund, 2) }}</strong>?</p>
                    <label class="fw-semibold text-dark small mb-1">Admin Approval Note (Optional)</label>
                    <textarea name="admin_note" class="form-control" rows="3">{{ $refund->admin_note }}</textarea>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Confirm Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rf-modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fs-6 fw-bold mb-0 text-white">Reject Refund Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.refunds.reject', $refund->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <label class="fw-semibold text-dark small mb-1">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea name="admin_note" class="form-control" rows="3" required placeholder="Explain why the refund cannot be processed...">{{ $refund->admin_note }}</textarea>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Process Modal --}}
<div class="modal fade" id="processModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rf-modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fs-6 fw-bold mb-0 text-white">Disburse Refund Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.refunds.process', $refund->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-semibold text-dark small mb-1">Payment Method <span class="text-danger">*</span></label>
                        <select name="refund_method" class="form-select" required>
                            <option value="bkash" {{ $refund->refund_method == 'bkash' ? 'selected' : '' }}>bKash</option>
                            <option value="nagad" {{ $refund->refund_method == 'nagad' ? 'selected' : '' }}>Nagad</option>
                            <option value="bank" {{ $refund->refund_method == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="manual" {{ $refund->refund_method == 'manual' ? 'selected' : '' }}>Manual / Cash</option>
                            <option value="original_payment" {{ $refund->refund_method == 'original_payment' ? 'selected' : '' }}>Original Gateway</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-semibold text-dark small mb-1">Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="refund_account" class="form-control" required value="{{ $refund->refund_account }}">
                    </div>

                    <div class="mb-3">
                        <label class="fw-semibold text-dark small mb-1">Account Holder Name (Optional)</label>
                        <input type="text" name="refund_account_name" class="form-control" value="{{ $refund->refund_account_name }}">
                    </div>

                    <div class="mb-3">
                        <label class="fw-semibold text-dark small mb-1">Transaction / Reference ID <span class="text-danger">*</span></label>
                        <input type="text" name="transaction_id" class="form-control font-monospace" required placeholder="e.g. TRX-98765432" value="{{ $refund->transaction_id }}">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">Mark as Processed</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    function copyToClipboard(text) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(function() {
            if (typeof toastr !== 'undefined') {
                toastr.success('Copied: ' + text);
            } else {
                alert('Copied: ' + text);
            }
        }).catch(function(err) {
            console.error('Could not copy text: ', err);
        });
    }
</script>
@endsection
