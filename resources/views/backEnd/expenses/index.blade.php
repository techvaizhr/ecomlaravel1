@extends('backEnd.layouts.master')
@section('title', 'Expenses Management')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<style>
    .exp-manage-page {
        padding-bottom: 2.5rem;
    }
    
    /* Header Card */
    .exp-header-card {
        background: linear-gradient(135deg, #9f1239 0%, #e11d48 50%, #7c3aed 100%);
        border-radius: 16px;
        padding: 22px 26px;
        color: #fff;
        margin-bottom: 22px;
        box-shadow: 0 10px 25px rgba(225, 29, 72, 0.22);
    }
    
    /* Stat Cards */
    .exp-stat-card {
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
    .exp-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }
    .exp-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .exp-stat-icon.emerald { background: #ecfdf5; color: #059669; }
    .exp-stat-icon.rose { background: #fff1f2; color: #e11d48; }
    .exp-stat-icon.amber { background: #fffbeb; color: #d97706; }
    .exp-stat-icon.purple { background: #f5f3ff; color: #7c3aed; }

    /* Filter Card */
    .exp-filter-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
        margin-bottom: 20px;
        padding: 16px 20px;
    }
    .exp-form-label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 5px;
        display: block;
    }

    /* Main Table Card */
    .exp-table-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .exp-toolbar {
        padding: 14px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .exp-table thead th {
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
    .exp-table tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .exp-table tbody tr:hover td {
        background: #fff1f2;
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
        background: #e11d48;
        border-color: #e11d48;
        color: #fff;
        transform: scale(1.05);
    }
    .dropdown-menu-exp {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.12);
        padding: 6px;
        min-width: 165px;
    }
    .dropdown-menu-exp .dropdown-item {
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
    .dropdown-menu-exp .dropdown-item:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .dropdown-menu-exp .dropdown-item.text-danger:hover {
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
<div class="container-fluid pt-3 exp-manage-page">

    {{-- Header Banner --}}
    <div class="exp-header-card d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-white"><i class="fe-credit-card me-2"></i> Expenses Hub</h3>
            <p class="mb-0 text-white-50" style="font-size:14px;">অফিস, পরিবহন, মার্কেটিং এবং অন্যান্য ব্যয়ের হিসাব সংরক্ষণ ও পর্যবেক্ষণ করুন।</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="fe-plus-circle me-1"></i> Add Expense
            </button>
            <button type="button" class="btn btn-outline-light rounded-pill px-3 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#exportExpenseModal">
                <i class="fe-download me-1"></i> Export Report
            </button>
            <a href="{{ route('admin.expenses.logs') }}" class="btn btn-info rounded-pill px-3 py-2 fw-bold text-white shadow-sm">
                <i class="fe-file-text me-1"></i> Audit Logs
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="exp-stat-card">
                <div class="exp-stat-icon emerald"><i class="fe-briefcase"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Available Fund Balance</div>
                    <h3 class="mb-0 fw-bold text-success">৳{{ number_format($balance, 2) }}</h3>
                    <small class="text-muted" style="font-size:11.5px;">তহবিলে বর্তমান স্থিতি</small>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="exp-stat-card">
                <div class="exp-stat-icon rose"><i class="fe-calendar"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">This Year Expense ({{ $currentYear }})</div>
                    <h3 class="mb-0 fw-bold text-danger">৳{{ number_format($yearlyExpense, 2) }}</h3>
                    <small class="text-muted" style="font-size:11.5px;">চলতি বছরে মোট ব্যয়</small>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="exp-stat-card">
                <div class="exp-stat-icon amber"><i class="fe-pie-chart"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">This Month ({{ \Carbon\Carbon::create()->month($currentMonth)->format('F') }})</div>
                    <h3 class="mb-0 fw-bold text-warning">৳{{ number_format($monthlyExpense, 2) }}</h3>
                    <small class="text-muted" style="font-size:11.5px;">চলতি মাসের সর্বমোট খরচ</small>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="exp-stat-card">
                <div class="exp-stat-icon purple"><i class="fe-clock"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Today's Expense</div>
                    <h3 class="mb-0 fw-bold text-purple">৳{{ number_format($todayExpense, 2) }}</h3>
                    <small class="text-muted" style="font-size:11.5px;">{{ now()->format('d M, Y') }} এর খরচ</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Standardized Filter Bar with Smart Date & Per Page --}}
    <div class="exp-filter-card">
        <form method="GET" action="{{ route('admin.expenses.index') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                {{-- Search --}}
                <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-12">
                    <label class="exp-form-label">Search Expense</label>
                    <div class="position-relative">
                        <i class="fe-search position-absolute text-muted" style="left: 10px; top: 10px; font-size: 14px; pointer-events: none;"></i>
                        <input type="text" name="keyword" class="form-control form-control-sm ps-4" placeholder="Title, category, note, amount..." value="{{ request('keyword') }}" style="padding-left: 32px !important; height: 36px; border-radius: 8px;">
                    </div>
                </div>

                {{-- Category --}}
                @if(isset($categories) && $categories->count() > 0)
                <div class="col-xxl-3 col-xl-2 col-lg-2 col-md-3 col-6">
                    <label class="exp-form-label">Category</label>
                    <select name="category" class="form-select form-select-sm" style="height: 36px; border-radius: 8px;">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Global Smart Date Filter Integration --}}
                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-12">
                    <label class="exp-form-label">Expense Date</label>
                    @include('backEnd.layouts.partials.smart_date_filter')
                </div>

                {{-- Per Page & Action Buttons in single aligned group --}}
                <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-6 col-12">
                    <div class="d-flex align-items-end gap-1">
                        <div style="min-width: 65px; width: 65px;">
                            <label class="exp-form-label">Per Page</label>
                            <select name="per_page" class="form-select form-select-sm px-1 text-center" style="height: 36px; border-radius: 8px;" onchange="document.getElementById('filterForm').submit();">
                                @foreach([10, 20, 25, 50, 100, 200] as $size)
                                    <option value="{{ $size }}" {{ (request('per_page', 25) == $size) ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <label class="exp-form-label d-none d-md-block">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-danger btn-sm flex-grow-1 fw-semibold d-flex align-items-center justify-content-center shadow-sm" style="height: 36px; border-radius: 8px; font-size: 13px; background:#e11d48; border-color:#e11d48;" title="Filter Expenses">
                                    <i class="fe-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.expenses.index') }}" class="btn btn-light btn-sm border d-flex align-items-center justify-content-center" style="height: 36px; width: 36px; border-radius: 8px;" title="Reset Filters">
                                    <i class="fe-rotate-ccw"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Main Expenses Table Card --}}
    <div class="exp-table-card">
        <div class="exp-toolbar">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small fw-bold text-muted"><i class="fe-list me-1"></i> Expense Records</span>
            </div>
            
            <div class="small text-muted fw-semibold">
                Showing {{ $expenses->firstItem() ?? 0 }} - {{ $expenses->lastItem() ?? 0 }} of {{ $expenses->total() }} Expenses
            </div>
        </div>

        <div class="table-responsive">
            <table class="table exp-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">SL</th>
                        <th style="width: 120px;">Expense Date</th>
                        <th>Title & Description</th>
                        <th>Category</th>
                        <th class="text-end">Amount (৳)</th>
                        <th>Note & Remarks</th>
                        @if($isAdmin)
                        <th class="text-end" style="width: 80px;">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $key => $exp)
                    <tr>
                        <td class="fw-bold text-muted">{{ $expenses->firstItem() + $key }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d M, Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($exp->expense_date)->format('l') }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $exp->title }}</div>
                            @if($exp->updated_by)
                                <small class="text-muted d-block mt-0.5" style="font-size:11px;">
                                    <i class="fe-edit-2"></i> Last edited: {{ $exp->updated_at ? $exp->updated_at->format('d M Y, h:i A') : 'N/A' }}
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($exp->category)
                                <span class="badge bg-soft-info text-info rounded-pill px-2.5 py-1">
                                    {{ $exp->category }}
                                </span>
                            @else
                                <span class="badge bg-soft-secondary text-secondary rounded-pill px-2 py-0.5" style="font-size:10.5px;">General</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="fw-bold fs-6 text-danger">
                                -৳{{ number_format($exp->amount, 2) }}
                            </div>
                        </td>
                        <td>
                            <div class="text-muted small" style="max-width:300px;">
                                {{ $exp->note ?: '-' }}
                            </div>
                        </td>
                        @if($isAdmin)
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-3dot" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                    <i class="fe-more-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-exp">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.expenses.edit', $exp->id) }}">
                                            <i class="fe-edit-2 text-primary"></i> Edit Expense
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.expenses.destroy', $exp->id) }}" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger delete-confirm" onclick="return confirm('Are you sure you want to delete this expense? Funds will be reverted.');">
                                                <i class="fe-trash-2"></i> Delete Expense
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
                            <i class="fe-credit-card d-block mb-2 text-danger" style="font-size:2.2rem;opacity:.35;"></i>
                            কোনো খরচের রেকর্ড পাওয়া যায়নি। ফিল্টার পরিবর্তন করুন অথবা নতুন খরচ যোগ করুন।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Foot --}}
        <div class="p-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span class="text-muted small">
                Showing {{ $expenses->firstItem() ?? 0 }} to {{ $expenses->lastItem() ?? 0 }} of {{ $expenses->total() }} entries
            </span>
            <div class="mb-0">
                {{ $expenses->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL 1: ADD EXPENSE --}}
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background:#e11d48;color:#fff;border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold text-white" id="addExpenseModalLabel">
                    <i class="fe-plus-circle me-1"></i> Record New Expense
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.expenses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-soft-info d-flex align-items-center gap-2 mb-3" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;border-radius:10px;">
                        <i class="fe-info fs-5"></i>
                        <div>
                            <strong>Available Fund: ৳{{ number_format($balance, 2) }}</strong>
                            <div class="small">ব্যয় যোগ করার সাথে সাথে স্বয়ংক্রিয়ভাবে ফান্ড থেকে কর্তন করা হবে।</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Expense Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" placeholder="e.g. Office Rent, Electricity Bill, Packaging..." value="{{ old('title') }}" required>
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Amount (৳) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-danger">৳</span>
                                <input type="number" step="0.01" min="0.01" max="{{ $balance > 0 ? $balance : 0 }}" name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="0.00" value="{{ old('amount') }}" required>
                            </div>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" value="{{ old('expense_date', now()->format('Y-m-d')) }}" required>
                            @error('expense_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <small class="text-muted">(Optional)</small></label>
                        <input type="text" name="category" list="category_list" class="form-control" placeholder="Select or type category (e.g. Marketing, Utility, Salary...)" value="{{ old('category') }}">
                        <datalist id="category_list">
                            @if(isset($categories))
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            @endif
                        </datalist>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Note / Remarks <small class="text-muted">(Optional)</small></label>
                        <textarea name="note" class="form-control" rows="2" placeholder="খরচের অতিরিক্ত তথ্য বা ভাউচার নম্বর...">{{ old('note') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light" style="border-radius:0 0 16px 16px;">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold" style="background:#e11d48;border-color:#e11d48;">
                        <i class="fe-check me-1"></i> Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: EXPORT EXPENSES --}}
<div class="modal fade" id="exportExpenseModal" tabindex="-1" aria-labelledby="exportExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header bg-dark text-white" style="border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold text-white" id="exportExpenseModalLabel">
                    <i class="fe-download me-1"></i> Export Expense Report (CSV)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.expenses.export') }}" method="GET" target="_blank">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">নির্দিষ্ট তারিখের সীমার মধ্যে হওয়া খরচের বিস্তারিত CSV রিপোর্ট ডাউনলোড করুন।</p>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
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
        $('.select2').select2({ width: '100%' });
    });
</script>
@endsection
