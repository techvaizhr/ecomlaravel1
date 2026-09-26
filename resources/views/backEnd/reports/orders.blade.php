@extends('backEnd.layouts.master')
@section('title', 'অর্ডার অ্যানালাইসিস ও রিপোর্ট')

@section('css')
<style>
    .report-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 20px;
        transition: all 0.2s ease;
    }
    .report-card:hover {
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.07);
    }
    .kpi-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
    }
    .kpi-card.kpi-primary::before { background: linear-gradient(90deg, #3b82f6, #2563eb); }
    .kpi-card.kpi-success::before { background: linear-gradient(90deg, #10b981, #059669); }
    .kpi-card.kpi-warning::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .kpi-card.kpi-danger::before { background: linear-gradient(90deg, #ef4444, #dc2626); }
    .kpi-card.kpi-info::before { background: linear-gradient(90deg, #06b6d4, #0891b2); }
    .kpi-card.kpi-purple::before { background: linear-gradient(90deg, #8b5cf6, #7c3aed); }

    .kpi-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
    }
    .kpi-title {
        font-size: 12.5px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 4px;
    }
    .kpi-value {
        font-size: 1.6rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.2;
    }
    .kpi-sub {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 6px;
    }

    /* Filter Toolbar */
    .filter-box {
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 16px;
    }
    .filter-label {
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 4px;
        text-transform: uppercase;
    }
    .filter-control {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 13px;
        padding: 6px 10px;
        background-color: #fff;
    }
    .filter-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
    }

    /* Status Grid Pills */
    .status-analytics-pill {
        border-radius: 8px;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .status-analytics-pill:hover {
        border-color: #3b82f6;
        background: #f0f7ff;
        transform: translateY(-1px);
    }
    .status-analytics-pill.active {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }

    /* Table Styles */
    .table-report th {
        background: #f1f5f9;
        color: #334155;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 10px 12px;
        border-bottom: 1px solid #cbd5e1;
        white-space: nowrap;
    }
    .table-report td {
        padding: 10px 12px;
        font-size: 12.5px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }
    .table-report tbody tr:hover td {
        background-color: #f8fafc;
    }

    /* Print styling */
    @media print {
        .no-print, .main-sidebar, .topbar, .filter-card, .btn, .pagination {
            display: none !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
        .report-card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3 px-3 px-lg-4">

    {{-- HEADER BAR --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-2 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                    <i class="fas fa-chart-line me-1"></i> লাইভ অ্যানালাইসিস
                </span>
                <h4 class="m-0 fw-bold text-dark" style="font-size: 19px;">অর্ডার ও বিক্রয় রিপোর্ট</h4>
            </div>
            <p class="text-muted small m-0 mt-1">
                সময়কাল: <strong class="text-dark">{{ $label }}</strong> | মোট ফিল্টার্ড অর্ডার: <strong>{{ number_format($totalOrders) }}</strong> টি
            </p>
        </div>

        {{-- Top Date Quick Switcher Buttons --}}
        <div class="d-flex flex-wrap align-items-center gap-1.5 no-print">
            @php
                $currentStatusParam = request('order_status');
                $currentCourierParam = request('courier');
            @endphp
            <a href="{{ route('admin.reports.orders', ['type' => 'today', 'order_status' => $currentStatusParam, 'courier' => $currentCourierParam]) }}" class="btn btn-sm {{ $type == 'today' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">আজ</a>
            <a href="{{ route('admin.reports.orders', ['type' => 'yesterday', 'order_status' => $currentStatusParam, 'courier' => $currentCourierParam]) }}" class="btn btn-sm {{ $type == 'yesterday' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">গতকাল</a>
            <a href="{{ route('admin.reports.orders', ['type' => 'last_7_days', 'order_status' => $currentStatusParam, 'courier' => $currentCourierParam]) }}" class="btn btn-sm {{ $type == 'last_7_days' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">৭ দিন</a>
            <a href="{{ route('admin.reports.orders', ['type' => 'this_month', 'order_status' => $currentStatusParam, 'courier' => $currentCourierParam]) }}" class="btn btn-sm {{ $type == 'this_month' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">চলতি মাস</a>
            <a href="{{ route('admin.reports.orders', ['type' => 'last_month', 'order_status' => $currentStatusParam, 'courier' => $currentCourierParam]) }}" class="btn btn-sm {{ $type == 'last_month' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">গত মাস</a>
            <a href="{{ route('admin.reports.orders', ['type' => 'this_year', 'order_status' => $currentStatusParam, 'courier' => $currentCourierParam]) }}" class="btn btn-sm {{ $type == 'this_year' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">চলতি বছর</a>
            <a href="{{ route('admin.reports.orders', ['type' => 'all', 'order_status' => $currentStatusParam, 'courier' => $currentCourierParam]) }}" class="btn btn-sm {{ $type == 'all' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">সব সময়</a>
        </div>
    </div>

    {{-- ADVANCED FILTER FORM --}}
    <div class="report-card p-3 mb-3 no-print">
        <form method="GET" action="{{ route('admin.reports.orders') }}" id="orderReportFilterForm">
            <div class="row g-2.5 align-items-end">
                
                {{-- Date Type --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="filter-label">তারিখ ফিল্টার</label>
                    <select name="type" class="form-select filter-control" id="report-type" onchange="toggleCustomDates()">
                        <option value="today" {{ $type=='today' ? 'selected' : '' }}>আজ (Today)</option>
                        <option value="yesterday" {{ $type=='yesterday' ? 'selected' : '' }}>গতকাল (Yesterday)</option>
                        <option value="last_7_days" {{ $type=='last_7_days' ? 'selected' : '' }}>গত ৭ দিন</option>
                        <option value="this_month" {{ $type=='this_month' ? 'selected' : '' }}>চলতি মাস (This Month)</option>
                        <option value="last_month" {{ $type=='last_month' ? 'selected' : '' }}>গত মাস (Last Month)</option>
                        <option value="this_year" {{ $type=='this_year' ? 'selected' : '' }}>চলতি বছর (This Year)</option>
                        <option value="range" {{ $type=='range' ? 'selected' : '' }}>কাস্টম রেঞ্জ (Custom)</option>
                        <option value="all" {{ $type=='all' ? 'selected' : '' }}>সব সময় (All Time)</option>
                    </select>
                </div>

                {{-- Custom Date Inputs --}}
                <div class="col-lg-2 col-md-3 col-6 custom-date-grp" style="{{ $type=='range' ? '' : 'display:none;' }}">
                    <label class="filter-label">শুরুর তারিখ</label>
                    <input type="date" name="from_date" class="form-control filter-control" value="{{ request('from_date', optional($from)->format('Y-m-d')) }}">
                </div>
                <div class="col-lg-2 col-md-3 col-6 custom-date-grp" style="{{ $type=='range' ? '' : 'display:none;' }}">
                    <label class="filter-label">শেষ তারিখ</label>
                    <input type="date" name="to_date" class="form-control filter-control" value="{{ request('to_date', optional($to)->format('Y-m-d')) }}">
                </div>

                {{-- Status Filter --}}
                <div class="col-lg-3 col-md-3 col-6">
                    <label class="filter-label">অর্ডার স্ট্যাটাস</label>
                    <select name="order_status" class="form-select filter-control">
                        <option value="all">সকল স্ট্যাটাস (All Statuses)</option>
                        <optgroup label="── গ্রুপ ফিল্টার ──">
                            <option value="delivered_group" {{ request('order_status')=='delivered_group' ? 'selected' : '' }}>✅ সফল ডেলিভারি গ্রুপ (৭, ৯, ১০)</option>
                            <option value="returned_group" {{ request('order_status')=='returned_group' ? 'selected' : '' }}>↩️ রিটার্নড গ্রুপ (১৩, ১০, ১১)</option>
                            <option value="pending_action_group" {{ request('order_status')=='pending_action_group' ? 'selected' : '' }}>⚠️ পেন্ডিং অ্যাকশন (৮ আংশিক, ১২ রিটার্ন)</option>
                            <option value="in_courier_group" {{ request('order_status')=='in_courier_group' ? 'selected' : '' }}>🚚 কুরিয়ারে চলমান (৫, ৬)</option>
                        </optgroup>
                        <optgroup label="── নির্দিষ্ট ১৫টি স্ট্যাটাস ──">
                            @foreach($orderStatuses as $st)
                                <option value="{{ $st->id }}" {{ request('order_status') == (string)$st->id ? 'selected' : '' }}>
                                    {{ $st->id }}. {{ $st->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                {{-- Courier Filter --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="filter-label">কুরিয়ার সার্ভিস</label>
                    <select name="courier" class="form-select filter-control">
                        <option value="all">সকল কুরিয়ার</option>
                        <option value="steadfast" {{ strtolower(request('courier'))=='steadfast' ? 'selected' : '' }}>Steadfast Courier</option>
                        <option value="pathao" {{ strtolower(request('courier'))=='pathao' ? 'selected' : '' }}>Pathao Courier</option>
                        <option value="carrybee" {{ strtolower(request('courier'))=='carrybee' ? 'selected' : '' }}>Carrybee</option>
                        <option value="redx" {{ strtolower(request('courier'))=='redx' ? 'selected' : '' }}>RedX Logistics</option>
                        <option value="none" {{ strtolower(request('courier'))=='none' ? 'selected' : '' }}>অন্যান্য / সরাসরি</option>
                    </select>
                </div>

                {{-- Keyword Search --}}
                <div class="col-lg-3 col-md-4 col-12">
                    <label class="filter-label">অনুসন্ধান (ইনভয়েস/মোবাইল/নাম)</label>
                    <input type="text" name="search" class="form-control filter-control" placeholder="Invoice #, Mobile, Customer..." value="{{ request('search') }}">
                </div>

                {{-- Action Buttons --}}
                <div class="col-lg-auto col-md-4 col-12 ms-auto d-flex gap-2">
                    <button class="btn btn-primary btn-sm px-3 fw-bold d-flex align-items-center gap-1.5" type="submit">
                        <i class="fas fa-filter"></i> ফিল্টার করুন
                    </button>
                    <button class="btn btn-outline-success btn-sm px-2.5 fw-bold d-flex align-items-center gap-1" type="submit" name="export" value="csv" title="CSV / Excel ডাউনলোড">
                        <i class="fas fa-file-excel"></i> CSV
                    </button>
                    <button class="btn btn-outline-secondary btn-sm px-2.5 fw-bold d-flex align-items-center gap-1" type="button" onclick="window.print()" title="প্রিন্ট করুন">
                        <i class="fas fa-print"></i> প্রিন্ট
                    </button>
                    @if(request()->hasAny(['order_status', 'courier', 'search', 'from_date', 'to_date']))
                        <a href="{{ route('admin.reports.orders', ['type' => $type]) }}" class="btn btn-outline-danger btn-sm px-2" title="ফিল্টার রিসেট">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- KEY METRIC STAT CARDS (TOP ANALYTICS) --}}
    <div class="row g-3 mb-3">
        {{-- 1. Total Orders --}}
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-primary">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="kpi-title">মোট অর্ডার (Total Orders)</div>
                    <div class="kpi-icon bg-light-primary text-primary" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                </div>
                <div>
                    <h3 class="kpi-value">{{ number_format($totalOrders) }} <span style="font-size: 13px; font-weight: normal; color: #64748b;">টি</span></h3>
                    <div class="kpi-sub d-flex justify-content-between">
                        <span>মোট বুকিং মান (GMV):</span>
                        <strong class="text-dark">৳{{ number_format($totalGMV, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Net Delivered Revenue --}}
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-success">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="kpi-title">প্রকৃত বিক্রয় আয় (Delivered Sales)</div>
                    <div class="kpi-icon text-success" style="background: #ecfdf5; color: #059669;">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div>
                    <h3 class="kpi-value text-success">৳{{ number_format($deliveredRevenue, 2) }}</h3>
                    <div class="kpi-sub d-flex justify-content-between">
                        <span>সফল ডেলিভার্ড অর্ডার:</span>
                        <strong class="text-success">{{ number_format($deliveredCount) }} টি</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Delivery Success Rate --}}
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-info">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="kpi-title">ডেলিভারি সাকসেস রেট</div>
                    <div class="kpi-icon text-info" style="background: #ecfeff; color: #0891b2;">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="kpi-value text-info">{{ $successRate }}%</h3>
                        <span class="small text-muted">({{ $deliveredCount }} ডেল. / {{ $returnedCount }} রিটার্ন)</span>
                    </div>
                    <div class="progress mt-2" style="height: 6px; border-radius: 4px; background: #e2e8f0;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, $successRate) }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Pending Action & Returned --}}
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-warning">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="kpi-title">পেন্ডিং অ্যাকশন ও কুরিয়ার</div>
                    <div class="kpi-icon text-warning" style="background: #fffbeb; color: #d97706;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="small text-muted">আংশিক/রিটার্ন পেন্ডিং:</span>
                        <span class="badge bg-warning text-dark fw-bold">{{ $pendingActionCount }} টি</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small text-muted">কুরিয়ারে চলমান:</span>
                        <span class="badge bg-info text-white fw-bold">{{ $inCourierCount }} টি</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STATUS BREAKDOWN & COURIER DISTRIBUTION ROW --}}
    <div class="row g-3 mb-3 no-print">
        {{-- Status Breakdown Grid --}}
        <div class="col-lg-8">
            <div class="report-card p-3 h-100 mb-0">
                <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                    <h6 class="m-0 fw-bold text-dark" style="font-size: 13.5px;">
                        <i class="fas fa-layer-group text-primary me-1.5"></i> ১৫টি স্ট্যাটাস-ভিত্তিক পার্সেল ও বিক্রয় সারসংক্ষেপ
                    </h6>
                    <small class="text-muted">ক্লিক করে ফিল্টার করুন</small>
                </div>
                <div class="row g-2 pt-1">
                    @foreach($statusBreakdown as $sb)
                        @php
                            $isActiveFilter = request('order_status') == (string)$sb['id'];
                            $badgeColor = '#64748b';
                            if(in_array($sb['id'], [7, 9, 10])) $badgeColor = '#059669';
                            elseif(in_array($sb['id'], [13, 11])) $badgeColor = '#dc2626';
                            elseif(in_array($sb['id'], [8, 12])) $badgeColor = '#d97706';
                            elseif(in_array($sb['id'], [5, 6])) $badgeColor = '#2563eb';
                            elseif($sb['id'] == 1) $badgeColor = '#0284c7';
                        @endphp
                        <div class="col-xl-4 col-md-6 col-12">
                            <a href="{{ route('admin.reports.orders', array_merge(request()->query(), ['order_status' => $sb['id']])) }}" 
                               class="status-analytics-pill {{ $isActiveFilter ? 'active' : '' }}">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle" style="width: 8px; height: 8px; background-color: {{ $badgeColor }}; flex-shrink:0;"></span>
                                    <div class="text-truncate" style="max-width: 140px;">
                                        <div class="fw-semibold text-dark" style="font-size: 12px;">{{ $sb['id'] }}. {{ $sb['name'] }}</div>
                                        <small class="text-muted" style="font-size: 10.5px;">৳{{ number_format($sb['revenue'], 0) }}</small>
                                    </div>
                                </div>
                                <span class="badge {{ $sb['count'] > 0 ? 'bg-primary' : 'bg-light text-muted border' }} rounded-pill" style="font-size: 11px;">
                                    {{ $sb['count'] }}
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Courier Distribution Summary --}}
        <div class="col-lg-4">
            <div class="report-card p-3 h-100 mb-0 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                        <h6 class="m-0 fw-bold text-dark" style="font-size: 13.5px;">
                            <i class="fas fa-truck-loading text-success me-1.5"></i> কুরিয়ার পার্সেল বন্টন
                        </h6>
                    </div>
                    <div class="d-flex flex-column gap-2 pt-1">
                        @foreach($courierBreakdown as $cName => $cCount)
                            @php
                                $cPercent = $totalOrders > 0 ? round(($cCount / $totalOrders) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-semibold text-dark">{{ $cName }}</span>
                                    <span class="text-muted">{{ $cCount }} টি ({{ $cPercent }}%)</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 3px; background: #e2e8f0;">
                                    <div class="progress-bar bg-primary" style="width: {{ $cPercent }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-3 border-top mt-2">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>মোট ডেলিভারি ফি:</span>
                        <strong class="text-dark">৳{{ number_format($totalShipping, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>মোট ডিসকাউন্ট:</span>
                        <strong class="text-danger">৳{{ number_format($totalDiscount, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAILED ORDER TABLE CARD --}}
    <div class="report-card">
        <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h5 class="m-0 fw-bold text-dark" style="font-size: 15px;">
                    <i class="fas fa-list-alt text-primary me-1.5"></i> বিস্তারিত অর্ডার তালিকা (Detailed Orders)
                </h5>
                <small class="text-muted">
                    দেখানো হচ্ছে: <strong>{{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }}</strong> (মোট <strong>{{ $orders->total() }}</strong> টি অর্ডার)
                </small>
            </div>
            <div class="d-flex align-items-center gap-2 no-print">
                <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> প্রিন্ট রিপোর্ট
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-report mb-0">
                <thead>
                    <tr>
                        <th width="4%">#</th>
                        <th width="12%">ইনভয়েস / তারিখ</th>
                        <th width="18%">গ্রাহকের তথ্য</th>
                        <th width="20%">পণ্য ও পরিমাণ</th>
                        <th width="14%">কুরিয়ার / ট্র্যাকিং</th>
                        <th width="14%" class="text-end">অর্ডার মান ও রাজস্ব</th>
                        <th width="12%">স্ট্যাটাস</th>
                        <th width="6%" class="text-center no-print">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $key => $order)
                        @php
                            $customerName  = $order->shipping->name ?? ($order->customer->name ?? $order->customer_name ?? 'Guest');
                            $customerPhone = $order->shipping->phone ?? ($order->customer->phone ?? $order->phone ?? '—');
                            $customerAddr  = $order->shipping->address ?? ($order->customer->address ?? '');

                            $netRevenue = $order->amount ?? 0;
                            $statusId   = (int) $order->order_status;
                            if ($statusId === 9 && $order->partial_collected_amount > 0) $netRevenue = $order->partial_collected_amount;
                            elseif ($statusId === 10 && $order->partial_collected_amount > 0) $netRevenue = $order->partial_collected_amount;
                            elseif ($statusId === 11) $netRevenue = ($order->partial_collected_amount > 0 ? $order->partial_collected_amount : ($order->shipping_charge ?? 0));
                            elseif (in_array($statusId, [13, 15])) $netRevenue = 0;

                            $statusName = $order->status->name ?? 'স্ট্যাটাস #' . $statusId;
                            $statusBadgeClass = 'bg-secondary';
                            if (in_array($statusId, [7, 9, 10])) $statusBadgeClass = 'bg-success';
                            elseif (in_array($statusId, [13, 11, 15])) $statusBadgeClass = 'bg-danger';
                            elseif (in_array($statusId, [8, 12, 2])) $statusBadgeClass = 'bg-warning text-dark';
                            elseif (in_array($statusId, [5, 6])) $statusBadgeClass = 'bg-primary';
                            elseif ($statusId == 1) $statusBadgeClass = 'bg-info text-dark';

                            $courierName = $order->courier ?: ($order->steadfast_consignment_id ? 'Steadfast' : ($order->pathao_consignment_id ? 'Pathao' : ($order->carrybee_consignment_id ? 'Carrybee' : ($order->redx_tracking_id ? 'RedX' : '—'))));
                            $trackingId  = $order->courier_tracking_id ?: ($order->steadfast_consignment_id ?: ($order->pathao_consignment_id ?: ($order->carrybee_consignment_id ?: ($order->redx_tracking_id ?: '—'))));
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $orders->firstItem() + $key }}</td>
                            <td>
                                <a href="{{ route('admin.order.invoice', ['id' => $order->id]) }}" target="_blank" class="fw-bold text-primary text-decoration-none" style="font-size: 13px;">
                                    #{{ $order->invoice_id ?: $order->id }}
                                </a>
                                <div class="text-muted" style="font-size: 11px;">
                                    <i class="far fa-clock me-1"></i> {{ optional($order->created_at)->format('d M, Y h:i A') }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $customerName }}</div>
                                <div class="text-muted small"><i class="fas fa-phone-alt me-1 text-secondary" style="font-size: 10px;"></i>{{ $customerPhone }}</div>
                                @if($customerAddr)
                                    <div class="text-truncate text-muted" style="max-width: 170px; font-size: 11px;" title="{{ $customerAddr }}">
                                        <i class="fas fa-map-marker-alt me-1 text-danger" style="font-size: 10px;"></i>{{ Str::limit($customerAddr, 25) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($order->orderdetails && $order->orderdetails->count() > 0)
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($order->orderdetails->take(2) as $od)
                                            @php
                                                $qty = $od->delivered_qty !== null ? $od->delivered_qty : $od->qty;
                                            @endphp
                                            <div class="d-flex align-items-center gap-1.5 text-truncate" style="max-width: 220px;" title="{{ $od->product_name ?? optional($od->product)->name }}">
                                                <span class="badge bg-light text-dark border px-1 py-0.5" style="font-size: 10px;">×{{ $qty }}</span>
                                                <span class="text-dark fw-medium text-truncate" style="font-size: 11.5px;">{{ Str::limit($od->product_name ?? optional($od->product)->name ?? 'পণ্য', 20) }}</span>
                                            </div>
                                        @endforeach
                                        @if($order->orderdetails->count() > 2)
                                            <small class="text-muted" style="font-size: 10.5px;">+{{ $order->orderdetails->count() - 2 }} টি অতিরিক্ত পণ্য</small>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-semibold" style="font-size: 11px;">
                                    <i class="fas fa-truck me-1 text-primary"></i> {{ $courierName }}
                                </span>
                                @if($trackingId !== '—')
                                    <div class="text-muted font-monospace mt-1" style="font-size: 10.5px;">
                                        ID: <span class="text-dark fw-bold">{{ $trackingId }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="fw-bold text-dark" style="font-size: 13.5px;">৳{{ number_format($order->amount, 2) }}</div>
                                @if($netRevenue != $order->amount)
                                    <small class="text-success fw-bold d-block" style="font-size: 11px;">
                                        আদায়: ৳{{ number_format($netRevenue, 2) }}
                                    </small>
                                @endif
                                <div class="text-muted" style="font-size: 10.5px;">
                                    ডেলিভারি: ৳{{ number_format($order->shipping_charge ?? 0, 0) }}
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $statusBadgeClass }} rounded-pill px-2 py-1" style="font-size: 11px;">
                                    {{ $statusId }}. {{ $statusName }}
                                </span>
                                @if($order->partial_type)
                                    <div class="mt-1">
                                        <span class="badge bg-info text-white" style="font-size: 9.5px;">{{ ucfirst(str_replace('_', ' ', $order->partial_type)) }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center no-print">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.order.invoice', ['id' => $order->id]) }}" target="_blank" class="btn btn-light border btn-xs text-primary" title="ইনভয়েস">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                    <a href="{{ route('admin.order.process', ['id' => $order->id]) }}" class="btn btn-light border btn-xs text-dark" title="প্রসেস">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <i class="fas fa-box-open text-muted" style="font-size: 22px;"></i>
                                    </div>
                                    <h6 class="text-dark fw-bold mb-1">কোনো অর্ডার পাওয়া যায়নি</h6>
                                    <small class="text-muted">দয়া করে তারিখ অথবা ফিল্টার পরিবর্তন করে আবার চেষ্টা করুন।</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($orders->hasPages())
            <div class="p-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2 no-print">
                <small class="text-muted">
                    পৃষ্ঠা {{ $orders->currentPage() }} এর {{ $orders->lastPage() }}
                </small>
                <div>
                    {{ $orders->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleCustomDates() {
        var type = document.getElementById('report-type').value;
        var customGrp = document.querySelectorAll('.custom-date-grp');
        customGrp.forEach(function(el) {
            if (type === 'range') {
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        });
    }
</script>
@endpush