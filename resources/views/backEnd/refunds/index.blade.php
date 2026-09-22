@extends('backEnd.layouts.master')
@section('title', 'Refund Management')

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
        --rf-purple: #8b5cf6;
        --rf-purple-light: #f5f3ff;
        --rf-border: #e2e8f0;
        --rf-card-bg: #ffffff;
        --rf-text-main: #1e293b;
        --rf-text-muted: #64748b;
    }

    .refund-page-shell {
        padding: 24px 16px 40px;
        background-color: #f8fafc;
        min-height: 100vh;
        font-family: inherit;
    }

    /* Page Header */
    .rf-header-card {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);
        position: relative;
        overflow: hidden;
    }
    .rf-header-card::after {
        content: '';
        position: absolute;
        right: -30px;
        top: -30px;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .rf-header-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .rf-badge-total {
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .rf-header-sub {
        color: #cbd5e1;
        font-size: 0.9rem;
        margin-top: 6px;
        margin-bottom: 0;
    }

    /* Stat Cards */
    .rf-stat-card {
        background: var(--rf-card-bg);
        border: 1px solid var(--rf-border);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
        text-decoration: none !important;
        color: inherit;
        position: relative;
        overflow: hidden;
        height: 100%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }
    .rf-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08);
    }
    .rf-stat-card.active {
        border-color: var(--rf-primary);
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.25);
    }
    .rf-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .rf-stat-info .rf-stat-title {
        font-size: 0.82rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--rf-text-muted);
        margin-bottom: 4px;
    }
    .rf-stat-info .rf-stat-num {
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--rf-text-main);
        line-height: 1.2;
    }
    .rf-stat-info .rf-stat-amount {
        font-size: 0.8rem;
        color: var(--rf-text-muted);
        margin-top: 4px;
    }

    /* Filter Card */
    .rf-filter-card {
        background: var(--rf-card-bg);
        border: 1px solid var(--rf-border);
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }
    .rf-form-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--rf-text-main);
        margin-bottom: 6px;
    }
    .rf-form-control, .rf-form-select {
        border: 1px solid var(--rf-border);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.88rem;
        transition: all 0.2s;
        background-color: #ffffff;
        width: 100%;
    }
    .rf-form-control:focus, .rf-form-select:focus {
        border-color: var(--rf-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    /* Main Table Card */
    .rf-table-card {
        background: var(--rf-card-bg);
        border: 1px solid var(--rf-border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
    }
    .rf-table-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--rf-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fdfdfe;
    }
    .rf-table-title {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: var(--rf-text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rf-custom-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }
    .rf-custom-table thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--rf-border);
        white-space: nowrap;
    }
    .rf-custom-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
        color: var(--rf-text-main);
    }
    .rf-custom-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Pill Badges */
    .rf-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .rf-badge-pending {
        background-color: var(--rf-warning-light);
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .rf-badge-approved {
        background-color: var(--rf-info-light);
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .rf-badge-processed {
        background-color: var(--rf-success-light);
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .rf-badge-rejected {
        background-color: var(--rf-danger-light);
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* Action Buttons */
    .rf-btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        border: 1px solid var(--rf-border);
        background: #ffffff;
        color: #475569;
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
    }
    .rf-btn-icon:hover {
        background: var(--rf-primary-light);
        color: var(--rf-primary);
        border-color: #c7d2fe;
    }
    .rf-btn-icon.btn-approve:hover {
        background: var(--rf-info-light);
        color: #0284c7;
        border-color: #bae6fd;
    }
    .rf-btn-icon.btn-process:hover {
        background: var(--rf-success-light);
        color: #059669;
        border-color: #a7f3d0;
    }
    .rf-btn-icon.btn-reject:hover {
        background: var(--rf-danger-light);
        color: #dc2626;
        border-color: #fecaca;
    }
    .rf-btn-copy {
        background: none;
        border: none;
        padding: 2px 6px;
        color: #94a3b8;
        cursor: pointer;
        font-size: 0.8rem;
        transition: color 0.15s;
    }
    .rf-btn-copy:hover {
        color: var(--rf-primary);
    }

    /* Pagination Footer */
    .rf-pagination-bar {
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        background: #ffffff;
        border-top: 1px solid var(--rf-border);
    }

    /* Modal styling */
    .rf-modal-content {
        border-radius: 16px;
        border: 1px solid var(--rf-border);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }
    .rf-modal-header {
        padding: 18px 24px;
        background: #f8fafc;
        border-bottom: 1px solid var(--rf-border);
    }
    .rf-modal-body {
        padding: 24px;
    }
    .rf-modal-footer {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid var(--rf-border);
    }
</style>
@endsection

@section('content')
<div class="container-fluid refund-page-shell">

    {{-- 1. Top Header Banner --}}
    <div class="rf-header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="rf-header-title">
                    <i class="fas fa-undo-alt text-warning"></i>
                    Refunds Management
                    <span class="rf-badge-total">{{ $totalCount }} Requests</span>
                </h1>
                <p class="rf-header-sub">Review customer return requests, approve/reject refunds, and process payments securely.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.refunds.index') }}" class="btn btn-light btn-sm fw-semibold rounded-pill px-3 shadow-sm">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Metric Counters / Status Cards --}}
    @php
        $pendingData   = $statusCounts->get('pending');
        $approvedData  = $statusCounts->get('approved');
        $processedData = $statusCounts->get('processed');
        $rejectedData  = $statusCounts->get('rejected');
    @endphp
    <div class="row g-3 mb-4">
        {{-- All --}}
        <div class="col-xl-2 col-md-4 col-sm-6">
            <a href="{{ route('admin.refunds.index') }}" class="rf-stat-card {{ !request('status') ? 'active' : '' }}">
                <div class="rf-stat-info">
                    <div class="rf-stat-title">All Refunds</div>
                    <div class="rf-stat-num">{{ $totalCount }}</div>
                    <div class="rf-stat-amount">৳{{ number_format($totalAmount, 0) }}</div>
                </div>
                <div class="rf-stat-icon" style="background:#f1f5f9; color:#475569;">
                    <i class="fas fa-layer-group"></i>
                </div>
            </a>
        </div>
        {{-- Pending --}}
        <div class="col-xl-2 col-md-4 col-sm-6">
            <a href="{{ route('admin.refunds.index', ['status' => 'pending']) }}" class="rf-stat-card {{ request('status') === 'pending' ? 'active' : '' }}">
                <div class="rf-stat-info">
                    <div class="rf-stat-title">Pending</div>
                    <div class="rf-stat-num text-warning">{{ $pendingData->total ?? 0 }}</div>
                    <div class="rf-stat-amount">৳{{ number_format($pendingData->total_amount ?? 0, 0) }}</div>
                </div>
                <div class="rf-stat-icon" style="background:var(--rf-warning-light); color:var(--rf-warning);">
                    <i class="fas fa-clock"></i>
                </div>
            </a>
        </div>
        {{-- Approved --}}
        <div class="col-xl-2 col-md-4 col-sm-6">
            <a href="{{ route('admin.refunds.index', ['status' => 'approved']) }}" class="rf-stat-card {{ request('status') === 'approved' ? 'active' : '' }}">
                <div class="rf-stat-info">
                    <div class="rf-stat-title">Approved</div>
                    <div class="rf-stat-num text-info">{{ $approvedData->total ?? 0 }}</div>
                    <div class="rf-stat-amount">৳{{ number_format($approvedData->total_amount ?? 0, 0) }}</div>
                </div>
                <div class="rf-stat-icon" style="background:var(--rf-info-light); color:var(--rf-info);">
                    <i class="fas fa-check-circle"></i>
                </div>
            </a>
        </div>
        {{-- Processed --}}
        <div class="col-xl-3 col-md-6 col-sm-6">
            <a href="{{ route('admin.refunds.index', ['status' => 'processed']) }}" class="rf-stat-card {{ request('status') === 'processed' ? 'active' : '' }}">
                <div class="rf-stat-info">
                    <div class="rf-stat-title">Processed / Paid</div>
                    <div class="rf-stat-num text-success">{{ $processedData->total ?? 0 }}</div>
                    <div class="rf-stat-amount">Paid ৳{{ number_format($processedData->total_amount ?? 0, 0) }}</div>
                </div>
                <div class="rf-stat-icon" style="background:var(--rf-success-light); color:var(--rf-success);">
                    <i class="fas fa-money-check-alt"></i>
                </div>
            </a>
        </div>
        {{-- Rejected --}}
        <div class="col-xl-3 col-md-6 col-sm-6">
            <a href="{{ route('admin.refunds.index', ['status' => 'rejected']) }}" class="rf-stat-card {{ request('status') === 'rejected' ? 'active' : '' }}">
                <div class="rf-stat-info">
                    <div class="rf-stat-title">Rejected</div>
                    <div class="rf-stat-num text-danger">{{ $rejectedData->total ?? 0 }}</div>
                    <div class="rf-stat-amount">৳{{ number_format($rejectedData->total_amount ?? 0, 0) }}</div>
                </div>
                <div class="rf-stat-icon" style="background:var(--rf-danger-light); color:var(--rf-danger);">
                    <i class="fas fa-times-circle"></i>
                </div>
            </a>
        </div>
    </div>

    {{-- 3. Advanced Filter Toolbar --}}
    <div class="rf-filter-card">
        <form method="GET" action="{{ route('admin.refunds.index') }}" id="refundFilterForm">
            <div class="row g-2 align-items-end">
                {{-- Search --}}
                <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                    <label class="rf-form-label">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="keyword" class="rf-form-control border-start-0 ps-0"
                               placeholder="Refund ID, Phone, Invoice #..." value="{{ request('keyword') }}">
                    </div>
                </div>

                {{-- Status --}}
                <div class="col-xl-2 col-lg-2 col-md-3 col-6">
                    <label class="rf-form-label">Status</label>
                    <select name="status" class="rf-form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>💳 Processed</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                    </select>
                </div>

                {{-- Refund Method --}}
                <div class="col-xl-2 col-lg-2 col-md-3 col-6">
                    <label class="rf-form-label">Method</label>
                    <select name="refund_method" class="rf-form-select">
                        <option value="">All Methods</option>
                        <option value="bkash" {{ request('refund_method') == 'bkash' ? 'selected' : '' }}>bKash</option>
                        <option value="nagad" {{ request('refund_method') == 'nagad' ? 'selected' : '' }}>Nagad</option>
                        <option value="bank" {{ request('refund_method') == 'bank' ? 'selected' : '' }}>Bank</option>
                        <option value="manual" {{ request('refund_method') == 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="original_payment" {{ request('refund_method') == 'original_payment' ? 'selected' : '' }}>Original</option>
                    </select>
                </div>

                {{-- Smart Global Date Range Filter --}}
                <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                    <label class="rf-form-label">Date Filter</label>
                    @include('backEnd.layouts.partials.smart_date_filter')
                </div>

                {{-- Per Page & Actions --}}
                <div class="col-xl-2 col-lg-2 col-md-6 col-12">
                    <div class="d-flex align-items-end gap-1">
                        <div style="width: 70px;">
                            <label class="rf-form-label">Per Page</label>
                            <select name="per_page" class="rf-form-select px-2" onchange="document.getElementById('refundFilterForm').submit();">
                                @foreach([10, 15, 25, 50, 100, 200] as $size)
                                    <option value="{{ $size }}" {{ (request('per_page', $perPage ?? 15) == $size) ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <label class="rf-form-label d-none d-md-block">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold" style="height: 38px; border-radius: 8px;" title="Filter Results">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('admin.refunds.index') }}" class="btn btn-light btn-sm border d-flex align-items-center justify-content-center" style="height: 38px; width: 38px; border-radius: 8px;" title="Reset Filters">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- 4. Main Refunds Table --}}
    <div class="rf-table-card">
        <div class="rf-table-header">
            <h6 class="rf-table-title">
                <i class="fas fa-list text-primary"></i>
                Refund Request List
                <span class="badge bg-light text-dark border">{{ $data->total() }} records found</span>
            </h6>
            <div class="text-muted small">
                Showing {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} of {{ $data->total() }}
            </div>
        </div>

        <div class="table-responsive">
            <table class="rf-custom-table">
                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th>Refund / Invoice</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Refund Method</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th width="120" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $refund)
                    @php
                        $totalRefund = $refund->amount + $refund->shipping_charge;
                    @endphp
                    <tr>
                        <td>
                            <span class="text-muted">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <span class="fw-bold text-dark font-monospace">#{{ $refund->refund_id }}</span>
                                <button type="button" class="rf-btn-copy" onclick="copyToClipboard('{{ $refund->refund_id }}')" title="Copy Refund ID">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                            @if($refund->order)
                            <div class="mt-1 small">
                                <a href="{{ route('admin.order.invoice', ['invoice_id' => $refund->order->invoice_id]) }}" target="_blank" class="text-decoration-none fw-semibold text-primary">
                                    <i class="fas fa-file-invoice me-1"></i>INV-{{ $refund->order->invoice_id }}
                                </a>
                            </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $refund->customer->name ?? 'Guest Customer' }}</div>
                            <div class="text-muted small d-flex align-items-center gap-1">
                                <i class="fas fa-phone-alt text-muted" style="font-size: 0.7rem;"></i>
                                <span>{{ $refund->customer->phone ?? '—' }}</span>
                                @if($refund->customer->phone ?? false)
                                <button type="button" class="rf-btn-copy" onclick="copyToClipboard('{{ $refund->customer->phone }}')" title="Copy Phone Number">
                                    <i class="far fa-copy"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">৳{{ number_format($totalRefund, 2) }}</div>
                            @if($refund->shipping_charge > 0)
                            <div class="text-muted small" style="font-size: 0.75rem;">
                                (Items: ৳{{ number_format($refund->amount, 0) }} + Ship: ৳{{ number_format($refund->shipping_charge, 0) }})
                            </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.75rem;">
                                {{ str_replace('_', ' ', $refund->refund_method) }}
                            </span>
                            @if($refund->refund_account)
                            <div class="font-monospace text-muted small mt-1 d-flex align-items-center gap-1">
                                <span>{{ $refund->refund_account }}</span>
                                <button type="button" class="rf-btn-copy" onclick="copyToClipboard('{{ $refund->refund_account }}')" title="Copy Account">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                            @endif
                            @if($refund->transaction_id)
                            <div class="text-success small font-monospace mt-1">
                                <i class="fas fa-receipt me-1"></i>{{ $refund->transaction_id }}
                            </div>
                            @endif
                        </td>
                        <td>
                            @if($refund->status == 'pending')
                                <span class="rf-badge rf-badge-pending"><i class="fas fa-hourglass-half"></i> Pending</span>
                            @elseif($refund->status == 'approved')
                                <span class="rf-badge rf-badge-approved"><i class="fas fa-check"></i> Approved</span>
                            @elseif($refund->status == 'processed')
                                <span class="rf-badge rf-badge-processed"><i class="fas fa-check-double"></i> Processed</span>
                            @elseif($refund->status == 'rejected')
                                <span class="rf-badge rf-badge-rejected"><i class="fas fa-times"></i> Rejected</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark small fw-medium">{{ $refund->created_at->format('d M, Y') }}</div>
                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $refund->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                {{-- View Details --}}
                                <a href="{{ route('admin.refunds.show', $refund->id) }}" class="rf-btn-icon" title="View Full Details">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Pending Actions: Approve / Reject --}}
                                @if($refund->status == 'pending')
                                    <button type="button" class="rf-btn-icon btn-approve" data-bs-toggle="modal" data-bs-target="#approveModal{{ $refund->id }}" title="Approve Refund">
                                        <i class="fas fa-check text-info"></i>
                                    </button>
                                    <button type="button" class="rf-btn-icon btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $refund->id }}" title="Reject Request">
                                        <i class="fas fa-times text-danger"></i>
                                    </button>
                                @elseif($refund->status == 'approved')
                                    <button type="button" class="rf-btn-icon btn-process" data-bs-toggle="modal" data-bs-target="#processModal{{ $refund->id }}" title="Process & Disburse Payment">
                                        <i class="fas fa-credit-card text-success"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="py-4">
                                <div class="mb-3 text-muted" style="font-size: 3rem;">
                                    <i class="fas fa-inbox opacity-50"></i>
                                </div>
                                <h6 class="fw-bold text-dark">No refund records found</h6>
                                <p class="text-muted small">No refund applications match your selected filter criteria.</p>
                                <a href="{{ route('admin.refunds.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-undo me-1"></i> Clear Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Bar --}}
        <div class="rf-pagination-bar">
            <div class="text-muted small">
                Showing <strong>{{ $data->firstItem() ?? 0 }}</strong> to <strong>{{ $data->lastItem() ?? 0 }}</strong> of <strong>{{ $data->total() }}</strong> entries
            </div>
            <div>
                {{ $data->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

{{-- Dynamic Modals for Row Actions --}}
@foreach($data as $refund)
@php $totalRefund = $refund->amount + $refund->shipping_charge; @endphp

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal{{ $refund->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rf-modal-content">
            <div class="modal-header rf-modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold mb-0 text-white">
                    <i class="fas fa-check-circle me-2"></i> Approve Refund #{{ $refund->refund_id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.refunds.approve', $refund->id) }}" method="POST">
                @csrf
                <div class="modal-body rf-modal-body">
                    <div class="alert alert-info border-0 rounded-3 small mb-3">
                        <i class="fas fa-info-circle me-1"></i> You are approving a refund of <strong>৳{{ number_format($totalRefund, 2) }}</strong> for Customer <strong>{{ $refund->customer->name ?? 'Guest' }}</strong>.
                    </div>
                    <div class="mb-3">
                        <label class="rf-form-label">Internal Admin Note (Optional)</label>
                        <textarea name="admin_note" class="rf-form-control" rows="3" placeholder="Reason or reference notes for approval...">{{ $refund->admin_note }}</textarea>
                    </div>
                </div>
                <div class="modal-footer rf-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="fas fa-check me-1"></i> Approve Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal{{ $refund->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rf-modal-content">
            <div class="modal-header rf-modal-header bg-danger text-white">
                <h5 class="modal-title fs-6 fw-bold mb-0 text-white">
                    <i class="fas fa-times-circle me-2"></i> Reject Refund #{{ $refund->refund_id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.refunds.reject', $refund->id) }}" method="POST">
                @csrf
                <div class="modal-body rf-modal-body">
                    <div class="alert alert-danger border-0 rounded-3 small mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i> Rejecting this request will notify the customer and terminate the refund process.
                    </div>
                    <div class="mb-3">
                        <label class="rf-form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="admin_note" class="rf-form-control" rows="3" required placeholder="State clear reasons for rejection...">{{ $refund->admin_note }}</textarea>
                    </div>
                </div>
                <div class="modal-footer rf-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                        <i class="fas fa-times me-1"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Process Modal --}}
<div class="modal fade" id="processModal{{ $refund->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rf-modal-content">
            <div class="modal-header rf-modal-header bg-success text-white">
                <h5 class="modal-title fs-6 fw-bold mb-0 text-white">
                    <i class="fas fa-credit-card me-2"></i> Disburse Refund Payment #{{ $refund->refund_id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.refunds.process', $refund->id) }}" method="POST">
                @csrf
                <div class="modal-body rf-modal-body">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Payable Amount:</span>
                            <span class="fw-bold text-success fs-6">৳{{ number_format($totalRefund, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Customer Target Account:</span>
                            <span class="font-monospace fw-bold text-dark">{{ $refund->refund_account ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="rf-form-label">Payment Method Used <span class="text-danger">*</span></label>
                        <select name="refund_method" class="rf-form-select" required>
                            <option value="bkash" {{ $refund->refund_method == 'bkash' ? 'selected' : '' }}>bKash</option>
                            <option value="nagad" {{ $refund->refund_method == 'nagad' ? 'selected' : '' }}>Nagad</option>
                            <option value="bank" {{ $refund->refund_method == 'bank' ? 'selected' : '' }}>Bank Account</option>
                            <option value="manual" {{ $refund->refund_method == 'manual' ? 'selected' : '' }}>Manual / Cash</option>
                            <option value="original_payment" {{ $refund->refund_method == 'original_payment' ? 'selected' : '' }}>Original Gateway</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="rf-form-label">Customer Account / Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="refund_account" class="rf-form-control" required value="{{ $refund->refund_account }}">
                    </div>

                    <div class="mb-3">
                        <label class="rf-form-label">Account Holder Name (Optional)</label>
                        <input type="text" name="refund_account_name" class="rf-form-control" value="{{ $refund->refund_account_name }}" placeholder="e.g. Md. Jahid Hasan">
                    </div>

                    <div class="mb-3">
                        <label class="rf-form-label">Transaction / Reference ID <span class="text-danger">*</span></label>
                        <input type="text" name="transaction_id" class="rf-form-control font-monospace" required placeholder="e.g. TRX-9J8A7B6C" value="{{ $refund->transaction_id }}">
                    </div>
                </div>
                <div class="modal-footer rf-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                        <i class="fas fa-check-double me-1"></i> Mark as Processed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

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
