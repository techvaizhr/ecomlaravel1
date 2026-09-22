@extends('backEnd.layouts.master')
@section('title', 'Fund & Account Management')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<style>
    .fund-manage-page {
        padding-bottom: 2.5rem;
    }
    
    /* Header Card */
    .fund-header-card {
        background: linear-gradient(135deg, #065f46 0%, #059669 45%, #0284c7 100%);
        border-radius: 16px;
        padding: 22px 26px;
        color: #fff;
        margin-bottom: 22px;
        box-shadow: 0 10px 25px rgba(5, 150, 105, 0.22);
    }
    
    /* Stat Cards */
    .fund-stat-card {
        background: #fff;
        border-radius: 14px;
        padding: 16px 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }
    .fund-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }
    .fund-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .fund-stat-icon.emerald { background: #ecfdf5; color: #059669; }
    .fund-stat-icon.blue { background: #eff6ff; color: #2563eb; }
    .fund-stat-icon.rose { background: #fff1f2; color: #e11d48; }
    .fund-stat-icon.indigo { background: #eef2ff; color: #4f46e5; }

    /* Filter Card */
    .fund-filter-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
        margin-bottom: 20px;
        padding: 16px 20px;
    }
    .fund-form-label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 5px;
        display: block;
    }

    /* Main Table Card */
    .fund-table-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .fund-toolbar {
        padding: 14px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .fund-table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 12px 14px;
        white-space: nowrap;
        vertical-align: middle;
    }
    .fund-table tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .fund-table tbody tr:hover td {
        background: #f8fafc;
    }

    /* 3-Dot Dropdown Menu */
    .btn-3dot {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .btn-3dot:hover, .btn-3dot[aria-expanded="true"] {
        background: #059669;
        border-color: #059669;
        color: #fff;
        transform: scale(1.05);
    }
    .dropdown-menu-fund {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.12);
        padding: 6px;
        min-width: 165px;
    }
    .dropdown-menu-fund .dropdown-item {
        border-radius: 8px;
        padding: 7px 12px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
    }
    .dropdown-menu-fund .dropdown-item:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .dropdown-menu-fund .dropdown-item.text-danger:hover {
        background: #fef2f2;
        color: #dc2626 !important;
    }
</style>
@endsection

@php
    use Illuminate\Support\Facades\Auth;
    // Check if current user is Admin (Super Admin or has Admin role)
    $isAdmin = false;
    $user = Auth::guard('admin')->user();
    if ($user) {
        if ($user->id == 1) {
            $isAdmin = true;
        } else {
            $spatieRoles = $user->getRoleNames()->map(function($role) {
                return strtolower($role);
            })->toArray();
            $isAdmin = in_array('admin', $spatieRoles);
        }
    }
@endphp

@section('content')
<div class="container-fluid pt-3 fund-manage-page">

    {{-- Header Banner --}}
    <div class="fund-header-card d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-white"><i class="fe-dollar-sign me-2"></i> Fund & Account Management</h3>
            <p class="mb-0 text-white-50" style="font-size:14px;">ব্যবসায়িক ফান্ড ইন/আউট, উত্তোলন এবং তহবিলের সার্বিক ভারসাম্য পর্যবেক্ষণ করুন।</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-light rounded-pill px-3 py-2 fw-bold text-success shadow-sm" data-bs-toggle="modal" data-bs-target="#addFundModal">
                <i class="fe-plus-circle me-1"></i> Add Fund
            </button>
            <button type="button" class="btn btn-warning rounded-pill px-3 py-2 fw-bold text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#withdrawFundModal">
                <i class="fe-minus-circle me-1"></i> Withdraw
            </button>
            <button type="button" class="btn btn-outline-light rounded-pill px-3 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#exportFundModal">
                <i class="fe-download me-1"></i> Export CSV
            </button>
            <a href="{{ route('admin.fund.logs') }}" class="btn btn-info rounded-pill px-3 py-2 fw-bold text-white shadow-sm">
                <i class="fe-file-text me-1"></i> Audit Logs
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="fund-stat-card">
                <div class="fund-stat-icon emerald"><i class="fe-briefcase"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Available Fund Balance</div>
                    <h3 class="mb-0 fw-bold text-success">৳{{ number_format($balance, 2) }}</h3>
                    <small class="text-muted" style="font-size:11.5px;">(Total In: ৳{{ number_format($total_in, 2) }} - Out: ৳{{ number_format($total_out, 2) }})</small>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="fund-stat-card">
                <div class="fund-stat-icon blue"><i class="fe-arrow-down-left"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Total Inflow (+)</div>
                    <h3 class="mb-0 fw-bold text-primary">৳{{ number_format($total_in, 2) }}</h3>
                    <small class="text-muted" style="font-size:11.5px;">সর্বমোট জমাকৃত ফান্ড</small>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="fund-stat-card">
                <div class="fund-stat-icon rose"><i class="fe-arrow-up-right"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Total Outflow (-)</div>
                    <h3 class="mb-0 fw-bold text-danger">৳{{ number_format($total_out, 2) }}</h3>
                    <small class="text-muted" style="font-size:11.5px;">সর্বমোট খরচ ও উত্তোলন</small>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="fund-stat-card">
                <div class="fund-stat-icon indigo"><i class="fe-calendar"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">This Year ({{ $currentYear }})</div>
                    <h3 class="mb-0 fw-bold text-dark">৳{{ number_format($yearlyAdded, 2) }}</h3>
                    <small class="text-muted" style="font-size:11.5px;">চলতি মাসে যোগ: ৳{{ number_format($monthlyAdded, 2) }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Standardized Filter Bar with Smart Date & Per Page --}}
    <div class="fund-filter-card">
        <form method="GET" action="{{ route('admin.fund.index') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                {{-- Search --}}
                <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-12">
                    <label class="fund-form-label">Search Transaction</label>
                    <div class="position-relative">
                        <i class="fe-search position-absolute text-muted" style="left: 10px; top: 10px; font-size: 14px; pointer-events: none;"></i>
                        <input type="text" name="keyword" class="form-control form-control-sm ps-4" placeholder="Note, source, amount, ID..." value="{{ request('keyword') }}" style="padding-left: 32px !important; height: 36px; border-radius: 8px;">
                    </div>
                </div>

                {{-- Direction --}}
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-3 col-6">
                    <label class="fund-form-label">Type</label>
                    <select name="direction" class="form-select form-select-sm" style="height: 36px; border-radius: 8px;">
                        <option value="">All Types (IN / OUT)</option>
                        <option value="in" {{ request('direction') === 'in' ? 'selected' : '' }}>🟢 IN (+) Inflow</option>
                        <option value="out" {{ request('direction') === 'out' ? 'selected' : '' }}>🔴 OUT (-) Outflow</option>
                    </select>
                </div>

                {{-- Source --}}
                @if(isset($sources) && $sources->count() > 0)
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-3 col-6">
                    <label class="fund-form-label">Source</label>
                    <select name="source" class="form-select form-select-sm" style="height: 36px; border-radius: 8px;">
                        <option value="">All Sources</option>
                        @foreach($sources as $src)
                            <option value="{{ $src }}" {{ request('source') === $src ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $src)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Global Smart Date Filter Integration --}}
                <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-12">
                    <label class="fund-form-label">Transaction Date</label>
                    @include('backEnd.layouts.partials.smart_date_filter')
                </div>

                {{-- Per Page & Action Buttons in single aligned group --}}
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-12">
                    <div class="d-flex align-items-end gap-1">
                        <div style="min-width: 65px; width: 65px;">
                            <label class="fund-form-label">Per Page</label>
                            <select name="per_page" class="form-select form-select-sm px-1 text-center" style="height: 36px; border-radius: 8px;" onchange="document.getElementById('filterForm').submit();">
                                @foreach([10, 20, 25, 50, 100, 200] as $size)
                                    <option value="{{ $size }}" {{ (request('per_page', 25) == $size) ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <label class="fund-form-label d-none d-md-block">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-success btn-sm flex-grow-1 fw-semibold d-flex align-items-center justify-content-center shadow-sm" style="height: 36px; border-radius: 8px; font-size: 13px; background:#059669; border-color:#059669;" title="Filter Transactions">
                                    <i class="fe-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.fund.index') }}" class="btn btn-light btn-sm border d-flex align-items-center justify-content-center" style="height: 36px; width: 36px; border-radius: 8px;" title="Reset Filters">
                                    <i class="fe-rotate-ccw"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Main Fund History Table Card --}}
    <div class="fund-table-card">
        <div class="fund-toolbar">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small fw-bold text-muted"><i class="fe-list me-1"></i> Fund Transactions List</span>
            </div>
            
            <div class="small text-muted fw-semibold">
                Showing {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} Transactions
            </div>
        </div>

        <div class="table-responsive">
            <table class="table fund-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#ID</th>
                        <th style="width: 110px;">Type</th>
                        <th style="width: 140px;">Source</th>
                        <th>Amount (৳)</th>
                        <th>Note & Description</th>
                        <th>Date & Time</th>
                        @if($isAdmin)
                        <th class="text-end" style="width: 80px;">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td class="fw-bold text-muted">#{{ $t->id }}</td>
                        <td>
                            @if($t->direction === 'in')
                                <span class="badge bg-soft-success text-success px-2.5 py-1 rounded-pill fw-bold" style="font-size:11px;">
                                    <i class="fe-arrow-down-left me-1"></i> IN (+)
                                </span>
                            @else
                                <span class="badge bg-soft-danger text-danger px-2.5 py-1 rounded-pill fw-bold" style="font-size:11px;">
                                    <i class="fe-arrow-up-right me-1"></i> OUT (-)
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill text-capitalize" style="font-size:11px;">
                                {{ str_replace('_', ' ', $t->source ?? 'General') }}
                            </span>
                            @if($t->hasBeenEdited())
                                <span class="badge bg-soft-warning text-warning ms-1" title="This transaction has been edited" style="font-size:10px;">
                                    <i class="fe-edit-2"></i> Edited
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold fs-6 {{ $t->direction === 'in' ? 'text-success' : 'text-danger' }}">
                                {{ $t->direction === 'in' ? '+' : '-' }}৳{{ number_format($t->amount, 2) }}
                            </div>
                        </td>
                        <td>
                            <div class="text-dark">{{ $t->note ?: 'N/A' }}</div>
                            @if($t->updated_by)
                                <small class="text-muted d-block mt-0.5" style="font-size:11px;">
                                    <i class="fe-edit-2"></i> Last edited: {{ $t->updated_at ? $t->updated_at->format('d M Y, h:i A') : 'N/A' }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $t->created_at ? $t->created_at->format('d M, Y') : 'N/A' }}</div>
                            <small class="text-muted">{{ $t->created_at ? $t->created_at->format('h:i A') : '' }}</small>
                        </td>
                        @if($isAdmin)
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-3dot" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                    <i class="fe-more-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-fund">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.fund.edit', $t->id) }}">
                                            <i class="fe-edit-2 text-primary"></i> Edit Entry
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.fund.destroy', $t->id) }}" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger delete-confirm" onclick="return confirm('Are you sure you want to delete this fund transaction?');">
                                                <i class="fe-trash-2"></i> Delete Entry
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 7 : 6 }}" class="text-center py-5 text-muted">
                            <i class="fe-dollar-sign d-block mb-2" style="font-size:2.2rem;opacity:.35;"></i>
                            কোনো ফান্ড ট্রানজ্যাকশন পাওয়া যায়নি। ফিল্টার পরিবর্তন করুন বা নতুন ফান্ড যুক্ত করুন।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Foot --}}
        <div class="p-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span class="text-muted small">
                Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} entries
            </span>
            <div class="mb-0">
                {{ $transactions->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL 1: ADD FUND --}}
<div class="modal fade" id="addFundModal" tabindex="-1" aria-labelledby="addFundModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background:#059669;color:#fff;border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold text-white" id="addFundModalLabel">
                    <i class="fe-plus-circle me-1"></i> Add Fund to Account
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.fund.add') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount (৳) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-success">৳</span>
                            <input type="number" name="amount" class="form-control form-control-lg @error('amount') is-invalid @enderror" placeholder="e.g. 50000" step="0.01" min="0.01" required>
                        </div>
                        @error('amount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Note / Remarks <small class="text-muted">(Optional)</small></label>
                        <textarea name="note" class="form-control" rows="3" placeholder="ফান্ড যুক্ত করার কারণ বা বিবরণ লিখুন..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light" style="border-radius:0 0 16px 16px;">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold" style="background:#059669;border-color:#059669;">
                        <i class="fe-check me-1"></i> Confirm & Add Fund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: WITHDRAW FUND --}}
<div class="modal fade" id="withdrawFundModal" tabindex="-1" aria-labelledby="withdrawFundModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header bg-danger text-white" style="border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold text-white" id="withdrawFundModalLabel">
                    <i class="fe-minus-circle me-1"></i> Withdraw Fund
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.fund.withdraw') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-soft-warning d-flex align-items-center gap-2 mb-3" style="background:#fffbeb;color:#92400e;border:1px solid #fde68a;border-radius:10px;">
                        <i class="fe-info fs-5"></i>
                        <div>
                            <strong>Available Balance: ৳{{ number_format($balance, 2) }}</strong>
                            <div class="small">ব্যালেন্সের বেশি পরিমাণ উত্তোলন করা সম্ভব নয়।</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Withdraw Amount (৳) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-danger">৳</span>
                            <input type="number" name="amount" class="form-control form-control-lg" placeholder="e.g. 10000" step="0.01" min="0.01" max="{{ $balance > 0 ? $balance : 0 }}" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Note / Withdrawal Purpose <small class="text-muted">(Optional)</small></label>
                        <textarea name="note" class="form-control" rows="3" placeholder="উত্তোলনের কারণ বা বিবরণ লিখুন..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light" style="border-radius:0 0 16px 16px;">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="fe-check me-1"></i> Confirm Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 3: EXPORT REPORT --}}
<div class="modal fade" id="exportFundModal" tabindex="-1" aria-labelledby="exportFundModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header bg-dark text-white" style="border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold text-white" id="exportFundModalLabel">
                    <i class="fe-download me-1"></i> Export Fund Report (CSV)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.fund.export') }}" method="GET" id="fundExportForm">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Export Filter Range</label>
                        <select name="filter" id="export_filter_type" class="form-select">
                            <option value="year" selected>Yearly Report</option>
                            <option value="month">Monthly Report</option>
                            <option value="custom">Custom Date Range</option>
                        </select>
                    </div>

                    {{-- Year field --}}
                    <div class="mb-3" id="export_year_field">
                        <label class="form-label fw-semibold">Select Year</label>
                        <input type="number" name="year" class="form-control" value="{{ $currentYear }}" min="2000" max="2100">
                    </div>

                    {{-- Month field --}}
                    <div class="mb-3 d-none" id="export_month_field">
                        <label class="form-label fw-semibold">Select Month</label>
                        <select name="month" class="form-select">
                            @for($m=1;$m<=12;$m++)
                                <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Custom date range --}}
                    <div class="mb-0 d-none" id="export_custom_date_fields">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-semibold">From Date</label>
                                <input type="date" name="from_date" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">To Date</label>
                                <input type="date" name="to_date" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light" style="border-radius:0 0 16px 16px;">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fe-download me-1"></i> Download CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{asset('public/backEnd')}}/assets/libs/select2/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        const filterSelect     = document.getElementById('export_filter_type');
        const yearField        = document.getElementById('export_year_field');
        const monthField       = document.getElementById('export_month_field');
        const customDateFields = document.getElementById('export_custom_date_fields');

        if (filterSelect) {
            function updateExportFields() {
                const val = filterSelect.value;
                yearField.classList.add('d-none');
                monthField.classList.add('d-none');
                customDateFields.classList.add('d-none');

                if (val === 'year') {
                    yearField.classList.remove('d-none');
                } else if (val === 'month') {
                    yearField.classList.remove('d-none');
                    monthField.classList.remove('d-none');
                } else if (val === 'custom') {
                    customDateFields.classList.remove('d-none');
                }
            }

            filterSelect.addEventListener('change', updateExportFields);
            updateExportFields();
        }
    });
</script>
@endsection
