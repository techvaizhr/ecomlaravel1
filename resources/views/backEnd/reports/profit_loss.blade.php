@extends('backEnd.layouts.master')
@section('title', 'লাভ ও ক্ষতি রিপোর্ট (Profit & Loss Statement)')

@section('css')
<style>
    .pl-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 20px;
        transition: all 0.2s ease;
    }
    .pl-kpi-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 18px 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .pl-kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
    }
    .pl-kpi-card.kpi-revenue::before { background: linear-gradient(90deg, #3b82f6, #1d4ed8); }
    .pl-kpi-card.kpi-cogs::before { background: linear-gradient(90deg, #f59e0b, #b45309); }
    .pl-kpi-card.kpi-gross::before { background: linear-gradient(90deg, #06b6d4, #0e7490); }
    .pl-kpi-card.kpi-expense::before { background: linear-gradient(90deg, #ec4899, #be185d); }
    .pl-kpi-card.kpi-net-profit::before { background: linear-gradient(90deg, #10b981, #047857); }
    .pl-kpi-card.kpi-net-loss::before { background: linear-gradient(90deg, #ef4444, #b91c1c); }

    .pl-kpi-title {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 4px;
    }
    .pl-kpi-amount {
        font-size: 1.75rem;
        font-weight: 800;
        margin: 0;
        line-height: 1.2;
    }
    .pl-kpi-sub {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 6px;
    }

    /* Statement Table */
    .statement-table th {
        background: #f8fafc;
        font-weight: 700;
        color: #334155;
        padding: 12px 16px;
        font-size: 13px;
        border-bottom: 1px solid #cbd5e1;
    }
    .statement-table td {
        padding: 12px 16px;
        font-size: 13.5px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }
    .statement-row-highlight {
        background: #f8fafc;
        font-weight: 700;
    }
    .statement-net-profit {
        background: #ecfdf5 !important;
        border-top: 2px solid #10b981 !important;
        border-bottom: 2px solid #10b981 !important;
    }
    .statement-net-loss {
        background: #fef2f2 !important;
        border-top: 2px solid #ef4444 !important;
        border-bottom: 2px solid #ef4444 !important;
    }

    /* Print styling */
    @media print {
        .no-print, .main-sidebar, .topbar, .filter-card, .btn, .pagination {
            display: none !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
        .pl-card {
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
                <span class="badge bg-success px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                    <i class="fas fa-balance-scale me-1"></i> ফাইন্যান্সিয়াল স্টেটমেন্ট
                </span>
                <h4 class="m-0 fw-bold text-dark" style="font-size: 19px;">লাভ ও ক্ষতি রিপোর্ট (Profit & Loss)</h4>
            </div>
            <p class="text-muted small m-0 mt-1">
                সময়কাল: <strong class="text-dark">{{ $label }}</strong> | ডেলিভার্ড অর্ডার: <strong>{{ number_format($deliveredOrderCount) }}</strong> টি | বিক্রিত পণ্য: <strong>{{ number_format($totalDeliveredProductUnits) }}</strong> পিস
            </p>
        </div>

        {{-- Top Date Quick Switcher Buttons --}}
        <div class="d-flex flex-wrap align-items-center gap-1.5 no-print">
            <a href="{{ route('admin.reports.profit_loss', ['type' => 'today']) }}" class="btn btn-sm {{ $type == 'today' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">আজ</a>
            <a href="{{ route('admin.reports.profit_loss', ['type' => 'yesterday']) }}" class="btn btn-sm {{ $type == 'yesterday' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">গতকাল</a>
            <a href="{{ route('admin.reports.profit_loss', ['type' => 'last_7_days']) }}" class="btn btn-sm {{ $type == 'last_7_days' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">৭ দিন</a>
            <a href="{{ route('admin.reports.profit_loss', ['type' => 'this_month']) }}" class="btn btn-sm {{ $type == 'this_month' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">চলতি মাস</a>
            <a href="{{ route('admin.reports.profit_loss', ['type' => 'last_month']) }}" class="btn btn-sm {{ $type == 'last_month' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">গত মাস</a>
            <a href="{{ route('admin.reports.profit_loss', ['type' => 'this_year']) }}" class="btn btn-sm {{ $type == 'this_year' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">চলতি বছর</a>
            <a href="{{ route('admin.reports.profit_loss', ['type' => 'all']) }}" class="btn btn-sm {{ $type == 'all' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-outline-secondary bg-white' }}" style="font-size: 12px; border-radius: 6px;">সব সময়</a>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="pl-card p-3 mb-3 no-print">
        <form method="GET" action="{{ route('admin.reports.profit_loss') }}" id="profitLossFilterForm">
            <div class="row g-2.5 align-items-end">
                <div class="col-lg-3 col-md-4 col-6">
                    <label class="fw-bold text-secondary text-uppercase mb-1" style="font-size: 11px;">সময় নির্বাচন</label>
                    <select name="type" class="form-select form-select-sm" id="report-type" onchange="toggleCustomDates()">
                        <option value="today" {{ $type=='today' ? 'selected' : '' }}>আজ (Today)</option>
                        <option value="yesterday" {{ $type=='yesterday' ? 'selected' : '' }}>গতকাল (Yesterday)</option>
                        <option value="last_7_days" {{ $type=='last_7_days' ? 'selected' : '' }}>গত ৭ দিন</option>
                        <option value="this_month" {{ $type=='this_month' ? 'selected' : '' }}>চলতি মাস (This Month)</option>
                        <option value="last_month" {{ $type=='last_month' ? 'selected' : '' }}>গত মাস (Last Month)</option>
                        <option value="this_year" {{ $type=='this_year' ? 'selected' : '' }}>চলতি বছর (This Year)</option>
                        <option value="range" {{ $type=='range' ? 'selected' : '' }}>কাস্টম রেঞ্জ (Custom Date)</option>
                        <option value="all" {{ $type=='all' ? 'selected' : '' }}>সব সময় (All Time)</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6 custom-date-grp" style="{{ $type=='range' ? '' : 'display:none;' }}">
                    <label class="fw-bold text-secondary text-uppercase mb-1" style="font-size: 11px;">শুরুর তারিখ</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', optional($from)->format('Y-m-d')) }}">
                </div>
                <div class="col-lg-2 col-md-3 col-6 custom-date-grp" style="{{ $type=='range' ? '' : 'display:none;' }}">
                    <label class="fw-bold text-secondary text-uppercase mb-1" style="font-size: 11px;">শেষ তারিখ</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', optional($to)->format('Y-m-d')) }}">
                </div>

                <div class="col-lg-auto ms-auto d-flex gap-2">
                    <button class="btn btn-primary btn-sm px-3 fw-bold d-flex align-items-center gap-1.5" type="submit">
                        <i class="fas fa-sync-alt"></i> হিসাব আপডেট করুন
                    </button>
                    <button class="btn btn-outline-success btn-sm px-2.5 fw-bold d-flex align-items-center gap-1" type="submit" name="export" value="csv">
                        <i class="fas fa-file-excel"></i> CSV
                    </button>
                    <button class="btn btn-outline-secondary btn-sm px-2.5 fw-bold d-flex align-items-center gap-1" type="button" onclick="window.print()">
                        <i class="fas fa-print"></i> প্রিন্ট
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- KEY FINANCIAL KPI CARDS --}}
    <div class="row g-3 mb-3">
        {{-- 1. Net Delivered Revenue --}}
        <div class="col-xl-3 col-md-6">
            <div class="pl-kpi-card kpi-revenue">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="pl-kpi-title">প্রকৃত বিক্রয় আয় (Revenue)</div>
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" style="font-size: 10.5px;">ডেলিভার্ড</span>
                    </div>
                    <h3 class="pl-kpi-amount text-primary">৳{{ number_format($salesRevenue, 2) }}</h3>
                </div>
                <div class="pl-kpi-sub d-flex justify-content-between pt-2 border-top">
                    <span>ডেলিভার্ড অর্ডার:</span>
                    <strong class="text-dark">{{ number_format($deliveredOrderCount) }} টি</strong>
                </div>
            </div>
        </div>

        {{-- 2. COGS (Cost of Goods Sold) --}}
        <div class="col-xl-3 col-md-6">
            <div class="pl-kpi-card kpi-cogs">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="pl-kpi-title">পণ্যের ক্রয়মূল্য (COGS)</div>
                        <span class="badge bg-warning bg-opacity-10 text-dark fw-bold" style="font-size: 10.5px;">ক্রয় খরচ</span>
                    </div>
                    <h3 class="pl-kpi-amount text-warning" style="color: #d97706 !important;">৳{{ number_format($cogs, 2) }}</h3>
                </div>
                <div class="pl-kpi-sub d-flex justify-content-between pt-2 border-top">
                    <span>বিক্রিত পণ্য ইউনিট:</span>
                    <strong class="text-dark">{{ number_format($totalDeliveredProductUnits) }} পিস</strong>
                </div>
            </div>
        </div>

        {{-- 3. Operating Expenses & Reseller --}}
        <div class="col-xl-3 col-md-6">
            <div class="pl-kpi-card kpi-expense">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="pl-kpi-title">পরিচালন খরচ ও কমিশন</div>
                        <span class="badge bg-danger bg-opacity-10 text-danger fw-bold" style="font-size: 10.5px;">অন্যান্য খরচ</span>
                    </div>
                    <h3 class="pl-kpi-amount text-danger">৳{{ number_format($totalExpense + $resellerProfit, 2) }}</h3>
                </div>
                <div class="pl-kpi-sub d-flex justify-content-between pt-2 border-top">
                    <span>দোকান খরচ: ৳{{ number_format($totalExpense, 0) }}</span>
                    <span>রিসেলার: ৳{{ number_format($resellerProfit, 0) }}</span>
                </div>
            </div>
        </div>

        {{-- 4. Net Profit / Loss --}}
        <div class="col-xl-3 col-md-6">
            <div class="pl-kpi-card {{ $netProfit >= 0 ? 'kpi-net-profit' : 'kpi-net-loss' }}">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="pl-kpi-title">নিট লাভ / ক্ষতি (Net Profit)</div>
                        <span class="badge {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }} text-white fw-bold" style="font-size: 10.5px;">
                            {{ $netProfit >= 0 ? 'মুনাফা' : 'ক্ষতি' }} ({{ $netMargin }}%)
                        </span>
                    </div>
                    <h3 class="pl-kpi-amount {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        ৳{{ number_format($netProfit, 2) }}
                    </h3>
                </div>
                <div class="pl-kpi-sub d-flex justify-content-between pt-2 border-top">
                    <span>গ্রস মার্জিন: <strong>{{ $grossMargin }}%</strong></span>
                    <span>নিট মার্জিন: <strong>{{ $netMargin }}%</strong></span>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN SECTION: STATEMENT & EXPENSES --}}
    <div class="row g-3 mb-3">
        
        {{-- Itemized Financial Statement --}}
        <div class="col-lg-7">
            <div class="pl-card h-100 mb-0">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="m-0 fw-bold text-dark" style="font-size: 15px;">
                        <i class="fas fa-file-invoice-dollar text-primary me-1.5"></i> বিস্তারিত লাভ-ক্ষতি হিসাব বিবরণী (Financial Statement)
                    </h5>
                    <span class="badge bg-light text-dark border small">{{ $label }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table statement-table mb-0">
                        <tbody>
                            <tr>
                                <td width="60%">
                                    <div class="fw-bold text-dark">১. মোট সফল ডেলিভার্ড বিক্রয় (Delivered Sales GMV)</div>
                                    <small class="text-muted">স্ট্যাটাস ৭ (Delivered), ৯ (Partial Full), ১০ (Partial Item)</small>
                                </td>
                                <td class="text-end fw-semibold text-dark">৳{{ number_format($grossDeliveredSales, 2) }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="text-muted">বাদ: ডিসকাউন্ট ও মূল্য সমন্বয় (Discounts & Price Adjustments)</div>
                                </td>
                                <td class="text-end text-danger">- ৳{{ number_format(max(0, $grossDeliveredSales - $salesRevenue), 2) }}</td>
                            </tr>
                            <tr class="statement-row-highlight">
                                <td>
                                    <div class="fw-bold text-primary">প্রকৃত আদায়কৃত বিক্রয় আয় (Net Sales Revenue) [A]</div>
                                </td>
                                <td class="text-end fw-bold text-primary" style="font-size: 15px;">৳{{ number_format($salesRevenue, 2) }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="text-dark fw-medium">২. বাদ: বিক্রিত পণ্যের মোট ক্রয়মূল্য (Cost of Goods Sold - COGS) [B]</div>
                                    <small class="text-muted">শুধুমাত্র গ্রাহকের হাতে পৌঁছানো {{ number_format($totalDeliveredProductUnits) }} পিস পণ্যের ফ্যাক্টরি/ক্রয়মূল্য</small>
                                </td>
                                <td class="text-end text-warning fw-bold" style="color: #d97706 !important;">- ৳{{ number_format($cogs, 2) }}</td>
                            </tr>
                            <tr class="statement-row-highlight" style="background: #f0fdfa;">
                                <td>
                                    <div class="fw-bold text-dark">৩. মোট বিক্রয় মুনাফা (Gross Profit) [C = A - B]</div>
                                    <small class="text-muted">গ্রস প্রফিট মার্জিন: <strong class="text-dark">{{ $grossMargin }}%</strong></small>
                                </td>
                                <td class="text-end fw-bold text-dark" style="font-size: 16px;">৳{{ number_format($grossProfit, 2) }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="text-dark">৪. বাদ: দোকান/অফিস পরিচালন খরচ (Operating Expenses) [D]</div>
                                    <small class="text-muted">ভাড়া, বিজ্ঞাপন, প্যাকেজিং, স্টাফ বিল ইত্যাদি</small>
                                </td>
                                <td class="text-end text-danger">- ৳{{ number_format($totalExpense, 2) }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="text-dark">৫. বাদ: রিসেলার কমিশন ও লভ্যাংশ প্রদান (Reseller Payouts) [E]</div>
                                </td>
                                <td class="text-end text-danger">- ৳{{ number_format($resellerProfit, 2) }}</td>
                            </tr>
                            <tr class="{{ $netProfit >= 0 ? 'statement-net-profit' : 'statement-net-loss' }}">
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 15px;">
                                        ৬. প্রকৃত নিট লাভ / ক্ষতি (Net Profit / Loss) [C - D - E]
                                    </div>
                                    <small class="text-muted">নিট মুনাফা মার্জিন: <strong class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ $netMargin }}%</strong></small>
                                </td>
                                <td class="text-end fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.35rem;">
                                    ৳{{ number_format($netProfit, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Operating Expense Breakdown --}}
        <div class="col-lg-5">
            <div class="pl-card h-100 mb-0 d-flex flex-column justify-content-between">
                <div>
                    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="m-0 fw-bold text-dark" style="font-size: 14.5px;">
                            <i class="fas fa-tags text-danger me-1.5"></i> পরিচালন খরচের খাতসমূহ (Expense Breakdown)
                        </h5>
                        <span class="badge bg-danger text-white fw-bold">৳{{ number_format($totalExpense, 2) }}</span>
                    </div>
                    <div class="p-3">
                        @if(count($expenseCategories) > 0)
                            <div class="d-flex flex-column gap-3">
                                @foreach($expenseCategories as $ec)
                                    @php
                                        $expPct = $totalExpense > 0 ? round(($ec['amount'] / $totalExpense) * 100, 1) : 0;
                                    @endphp
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center small mb-1">
                                            <span class="fw-bold text-dark">{{ $ec['category'] }}</span>
                                            <span class="text-danger fw-semibold">৳{{ number_format($ec['amount'], 2) }} <span class="text-muted" style="font-size: 11px;">({{ $expPct }}%)</span></span>
                                        </div>
                                        <div class="progress" style="height: 5px; border-radius: 3px; background: #f1f5f9;">
                                            <div class="progress-bar bg-danger" style="width: {{ $expPct }}%;"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-receipt mb-2" style="font-size: 28px; opacity: 0.3;"></i>
                                <div class="small">এই সময়কালে কোনো খরচের এন্ট্রি পাওয়া যায়নি</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-3 border-top bg-light rounded-bottom">
                    <div class="d-flex justify-content-between align-items-center small">
                        <span class="text-muted">গ্রাহক থেকে সংগৃহীত ডেলিভারি ফি:</span>
                        <strong class="text-dark">৳{{ number_format($deliveredShippingIncome, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOP PROFITABLE PRODUCTS TABLE --}}
    <div class="pl-card">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="m-0 fw-bold text-dark" style="font-size: 15px;">
                <i class="fas fa-trophy text-warning me-1.5"></i> সর্বাধিক লাভজনক পণ্যসমূহ (Top Profitable Products in this period)
            </h5>
            <small class="text-muted">ডেলিভার্ড পণ্যের প্রফিট র‍্যাংকিং</small>
        </div>
        <div class="table-responsive">
            <table class="table statement-table mb-0">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="35%">পণ্যের বিবরণ</th>
                        <th width="12%" class="text-center">বিক্রিত পরিমাণ</th>
                        <th width="15%" class="text-end">গড় বিক্রয় মূল্য</th>
                        <th width="15%" class="text-end">মোট বিক্রয় আয়</th>
                        <th width="15%" class="text-end">অর্জিত মুনাফা (Profit)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $idx => $prod)
                        @php
                            $prodMargin = $prod['total_revenue'] > 0 ? round(($prod['profit'] / $prod['total_revenue']) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="text-muted fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $prod['image'] }}" class="rounded border" style="width: 36px; height: 36px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 13px;">{{ $prod['name'] }}</div>
                                        <small class="text-muted">ক্রয়মূল্য: ৳{{ number_format($prod['purchase_price'], 2) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2.5 py-1 fw-bold" style="font-size: 12px;">
                                    {{ number_format($prod['units_sold']) }} টি
                                </span>
                            </td>
                            <td class="text-end fw-medium text-dark">৳{{ number_format($prod['avg_sale_price'], 2) }}</td>
                            <td class="text-end fw-semibold text-primary">৳{{ number_format($prod['total_revenue'], 2) }}</td>
                            <td class="text-end">
                                <div class="fw-bold text-success" style="font-size: 13.5px;">+ ৳{{ number_format($prod['profit'], 2) }}</div>
                                <small class="text-muted" style="font-size: 10.5px;">মার্জিন: {{ $prodMargin }}%</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                কোনো বিক্রিত পণ্যের হিসাব পাওয়া যায়নি
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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