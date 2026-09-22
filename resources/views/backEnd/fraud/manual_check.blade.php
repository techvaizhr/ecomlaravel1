@extends('backEnd.layouts.master')
@section('title', 'Manual Fraud & Courier Check')

@section('css')
<style>
    :root {
        --fraud-primary: #4f46e5;
        --fraud-card-bg: #ffffff;
        --fraud-border: #e2e8f0;
        --fraud-text: #1e293b;
        --fraud-muted: #64748b;
    }

    .fraud-page-shell {
        padding: 24px 16px 40px;
        background-color: #f8fafc;
        min-height: 100vh;
    }

    /* Header Banner */
    .fraud-header-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 16px;
        padding: 28px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);
        position: relative;
        overflow: hidden;
    }
    .fraud-header-card::after {
        content: '';
        position: absolute;
        right: -20px;
        top: -20px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Search Box */
    .fraud-search-card {
        background: #ffffff;
        border: 1px solid var(--fraud-border);
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
    }
    .fraud-search-input {
        border-radius: 12px;
        padding: 14px 20px;
        font-size: 1.15rem;
        font-weight: 600;
        letter-spacing: 1px;
        border: 2px solid #cbd5e1;
        transition: all 0.2s;
    }
    .fraud-search-input:focus {
        border-color: var(--fraud-primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
        outline: none;
    }
    .fraud-search-btn {
        border-radius: 12px;
        padding: 14px 28px;
        font-weight: 700;
        font-size: 1.05rem;
        transition: all 0.2s;
    }

    /* Gauge / Score Circle */
    .fraud-circle-gauge {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        position: relative;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .fraud-circle-gauge .gauge-value {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1;
    }

    /* Status Recommendation Box */
    .fraud-advice-box {
        border-radius: 12px;
        padding: 16px 20px;
        margin-top: 16px;
        border-left: 5px solid;
    }
    .fraud-advice-safe {
        background: #ecfdf5;
        border-color: #10b981;
        color: #065f46;
    }
    .fraud-advice-warning {
        background: #fffbeb;
        border-color: #f59e0b;
        color: #92400e;
    }
    .fraud-advice-danger {
        background: #fef2f2;
        border-color: #ef4444;
        color: #991b1b;
    }

    /* Table & Cards */
    .fraud-card {
        background: #ffffff;
        border: 1px solid var(--fraud-border);
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }
    .fraud-card-header {
        padding: 16px 20px;
        background: #ffffff;
        border-bottom: 1px solid var(--fraud-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .fraud-card-title {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: var(--fraud-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .courier-logo-img {
        width: 50px;
        height: 36px;
        object-fit: contain;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 2px 4px;
    }
    .courier-table {
        margin-bottom: 0;
        vertical-align: middle;
    }
    .courier-table thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--fraud-border);
    }
    .courier-table tbody td {
        padding: 14px 16px;
        font-size: 0.9rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .rate-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 55px;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .progress-bar-custom {
        height: 8px;
        border-radius: 4px;
        background-color: #e2e8f0;
        overflow: hidden;
    }
</style>
@endsection

@section('content')
<div class="container-fluid fraud-page-shell">

    {{-- Top Hero Banner --}}
    <div class="fraud-header-card">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="h3 fw-bold mb-2 text-white d-flex align-items-center gap-2">
                    <i class="fas fa-shield-alt text-success"></i>
                    Manual Fraud & Courier Delivery History Check
                </h1>
                <p class="text-white-50 mb-0">
                    Search any customer phone number to instantly verify delivery success rate, cancellation ratio across top Bangladeshi courier services (Pathao, SteadFast, RedX, PaperFly, ParcelDex, CarryBee), and review your in-house store history.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('admin.fraud.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="fas fa-cog me-1"></i> Fraud API Settings
                </a>
            </div>
        </div>
    </div>

    {{-- Search Card --}}
    <div class="fraud-search-card text-center">
        <h5 class="fw-bold text-dark mb-2">গ্রাহকের মোবাইল নাম্বার দিয়ে ভেরিফাই করুন</h5>
        <p class="text-muted small mb-4">১১ ডিজিটের মোবাইল নম্বর লিখুন (যেমন: 017XXXXXXXX)</p>

        <form action="{{ route('manualFraud.check') }}" method="POST" class="mx-auto" style="max-width: 560px;">
            @csrf
            <div class="input-group mb-2">
                <span class="input-group-text bg-light px-3 border-end-0 text-muted">
                    <i class="fas fa-phone-alt fs-5"></i>
                </span>
                <input type="text" name="mobile" class="form-control fraud-search-input border-start-0"
                       value="{{ $mobile ?? '' }}" placeholder="01XXXXXXXXX" required autofocus maxlength="15">
                <button type="submit" class="btn btn-primary fraud-search-btn">
                    <i class="fas fa-search me-1"></i> চেক করুন
                </button>
            </div>
        </form>

        @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3 mt-3 d-inline-block px-4 py-2 small">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        </div>
        @endif
    </div>

    {{-- Results Area --}}
    @if(isset($data) && !empty($data))
    @php
        $summary      = $data['summary'] ?? [];
        $overallRate  = isset($summary['success_ratio']) ? round($summary['success_ratio']) : null;
        $totalParcels = (int) ($summary['total_parcel'] ?? 0);
        $successParcels = (int) ($summary['success_parcel'] ?? 0);
        $cancelledParcels = (int) ($summary['cancelled_parcel'] ?? 0);

        // Styling based on success rate
        if ($overallRate >= 80) {
            $gaugeBorder = '#10b981';
            $gaugeBg = '#ecfdf5';
            $gaugeText = '#059669';
            $adviceClass = 'fraud-advice-safe';
            $statusTitle = '✔ নিরাপদ গ্রাহক - ১০০% সুরক্ষিত';
            $statusDesc = 'এই কাস্টমারের কুরিয়ার পার্সেল রিসিভ করার রেকর্ড অত্যন্ত চমৎকার। আপনি নিশ্চিন্তে অর্ডার প্রসেস করতে পারেন।';
        } elseif ($overallRate >= 60) {
            $gaugeBorder = '#0284c7';
            $gaugeBg = '#e0f2fe';
            $gaugeText = '#0369a1';
            $adviceClass = 'fraud-advice-safe';
            $statusTitle = 'ℹ️ সন্তোষজনক ডেলিভারি স্কোর';
            $statusDesc = 'ডেলিভারি সফলতার হার ভালো। নিয়মিত প্রসেসে পার্সেল পাঠাতে পারেন।';
        } elseif ($overallRate >= 40) {
            $gaugeBorder = '#f59e0b';
            $gaugeBg = '#fffbeb';
            $gaugeText = '#d97706';
            $adviceClass = 'fraud-advice-warning';
            $statusTitle = '⚠ মধ্যম ঝুঁকি – ডেলিভারি চার্জ অগ্রিম নিন';
            $statusDesc = 'এই কাস্টমারের কিছু পার্সেল ক্যানসেল বা রিটার্ন হওয়ার রেকর্ড রয়েছে। অর্ডার কনফার্মেশনের সময় অবশ্যই ডেলিভারি চার্জ অগ্রিম নিন।';
        } else {
            $gaugeBorder = '#ef4444';
            $gaugeBg = '#fef2f2';
            $gaugeText = '#dc2626';
            $adviceClass = 'fraud-advice-danger';
            $statusTitle = '❗ উচ্চ ঝুঁকি – পার্সেল রিটার্নের সম্ভাবনা অনেক বেশি';
            $statusDesc = 'এই কাস্টমারের অধিকাংশ পার্সেল ক্যানসেল হয়েছে। পুরো পেমেন্ট অথবা ডেলিভারি চার্জ নিশ্চিত না হয়ে কোনো পার্সেল পাঠাবেন না!';
        }
    @endphp

    <div class="row g-4 mb-4">
        {{-- Left: Overall Summary Card --}}
        <div class="col-lg-4">
            <div class="fraud-card h-100">
                <div class="fraud-card-header bg-light">
                    <h6 class="fraud-card-title">
                        <i class="fas fa-chart-pie text-primary"></i> সার্বিক ডেলিভারি পরিসংখ্যান
                    </h6>
                    <span class="badge bg-white text-dark border font-monospace">{{ $mobile }}</span>
                </div>
                <div class="p-4 text-center">
                    {{-- Circle Gauge --}}
                    <div class="fraud-circle-gauge" style="border: 10px solid {{ $gaugeBorder }}; background-color: {{ $gaugeBg }};">
                        <div>
                            <div class="gauge-value" style="color: {{ $gaugeText }};">
                                {{ $overallRate !== null ? $overallRate.'%' : 'N/A' }}
                            </div>
                            <div class="text-muted small mt-1">সফলতার হার</div>
                        </div>
                    </div>

                    {{-- Metric Counts --}}
                    <div class="row g-2 text-center mt-3 pt-3 border-top">
                        <div class="col-4">
                            <div class="text-muted small">মোট পার্সেল</div>
                            <div class="fs-5 fw-bold text-dark">{{ $totalParcels }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">ডেলিভার্ড</div>
                            <div class="fs-5 fw-bold text-success">{{ $successParcels }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">ক্যানসেল</div>
                            <div class="fs-5 fw-bold text-danger">{{ $cancelledParcels }}</div>
                        </div>
                    </div>

                    {{-- Action Advice Box --}}
                    <div class="fraud-advice-box {{ $adviceClass }} text-start">
                        <div class="fw-bold mb-1 fs-6">{{ $statusTitle }}</div>
                        <p class="mb-0 small">{{ $statusDesc }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Courier Breakdown Table --}}
        <div class="col-lg-8">
            <div class="fraud-card h-100">
                <div class="fraud-card-header">
                    <h6 class="fraud-card-title">
                        <i class="fas fa-truck text-success"></i> কুরিয়ার ভিত্তিক বিস্তারিত রেকর্ড
                    </h6>
                    <span class="text-muted small">৬টি প্রধান কুরিয়ারের ডাটাবেজ সমন্বিত</span>
                </div>
                <div class="table-responsive">
                    <table class="table courier-table align-middle">
                        <thead>
                            <tr>
                                <th>কুরিয়ার সার্ভিস</th>
                                <th class="text-center">মোট অর্ডার</th>
                                <th class="text-center">ডেলিভার্ড</th>
                                <th class="text-center">বাতিল</th>
                                <th width="150">সফলতার হার</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $couriers = [
                                    'pathao'    => ['name' => 'Pathao Courier', 'logo' => 'public/assets/images/courier/pathao-logo.png'],
                                    'steadfast' => ['name' => 'SteadFast Courier', 'logo' => 'public/assets/images/courier/steadfast-logo.png'],
                                    'redx'      => ['name' => 'RedX Delivery', 'logo' => 'public/assets/images/courier/redx-logo.png'],
                                    'paperfly'  => ['name' => 'PaperFly', 'logo' => 'public/assets/images/courier/paperfly-logo.png'],
                                    'parceldex' => ['name' => 'ParcelDex', 'logo' => 'public/assets/images/courier/parceldex-logo.png'],
                                    'carrybee'  => ['name' => 'CarryBee', 'logo' => 'public/assets/images/courier/carrybee-logo.webp'],
                                ];
                            @endphp

                            @foreach($couriers as $key => $meta)
                            @php
                                $cData = $data[$key] ?? [];
                                $cS = (int) ($cData['success_parcel'] ?? 0);
                                $cC = (int) ($cData['cancelled_parcel'] ?? 0);
                                $cT = (int) ($cData['total_parcel'] ?? ($cS + $cC));
                                $cRate = isset($cData['success_ratio']) ? round($cData['success_ratio']) : ($cT > 0 ? round(($cS / $cT) * 100) : 0);

                                $logoUrl = !empty($cData['logo']) ? $cData['logo'] : asset($meta['logo']);
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $logoUrl }}" class="courier-logo-img" alt="{{ $meta['name'] }}" onerror="this.style.display='none'">
                                        <span class="fw-semibold text-dark">{{ $meta['name'] }}</span>
                                    </div>
                                </td>
                                <td class="text-center fw-bold text-dark">{{ $cT }}</td>
                                <td class="text-center fw-bold text-success">{{ $cS }}</td>
                                <td class="text-center fw-bold text-danger">{{ $cC }}</td>
                                <td>
                                    @if($cT > 0)
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="small fw-bold {{ $cRate >= 80 ? 'text-success' : ($cRate >= 50 ? 'text-warning' : 'text-danger') }}">{{ $cRate }}%</span>
                                            <span class="small text-muted" style="font-size: 0.75rem;">
                                                {{ $cRate >= 80 ? 'চমৎকার' : ($cRate >= 50 ? 'সাধারণ' : 'ঝুঁকিপূর্ণ') }}
                                            </span>
                                        </div>
                                        <div class="progress-bar-custom">
                                            <div class="h-100 {{ $cRate >= 80 ? 'bg-success' : ($cRate >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $cRate }}%;"></div>
                                        </div>
                                    @else
                                        <span class="text-muted small">রেকর্ড নেই</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Local Store In-House Order History --}}
    @if(isset($localOrders) && count($localOrders) > 0)
    <div class="fraud-card mb-4">
        <div class="fraud-card-header bg-light">
            <h6 class="fraud-card-title">
                <i class="fas fa-store text-primary"></i> আপনার ওয়েবসাইটের নিজস্ব অর্ডার হিস্ট্রি (In-House Orders)
                <span class="badge bg-primary text-white ms-2">{{ count($localOrders) }} Orders</span>
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ইনভয়েস</th>
                        <th>অর্ডারের তারিখ</th>
                        <th>গ্রাহকের নাম ও ঠিকানা</th>
                        <th>পণ্যসমূহ</th>
                        <th>মোট টাকা</th>
                        <th>অর্ডার স্ট্যাটাস</th>
                        <th class="text-end">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($localOrders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.order.invoice', ['invoice_id' => $order->invoice_id]) }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                #{{ $order->invoice_id }}
                            </a>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $order->created_at->format('d M, Y') }}</div>
                            <div class="small text-muted">{{ $order->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $order->shipping->name ?? $order->name ?? 'Guest' }}</div>
                            <div class="small text-muted">{{ $order->shipping->address ?? $order->address ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="small text-muted">{{ $order->orderdetails->count() }} টি পণ্য</div>
                        </td>
                        <td class="fw-bold text-dark">
                            ৳{{ number_format(($order->amount ?? 0) + ($order->shipping_charge ?? 0), 0) }}
                        </td>
                        <td>
                            <span class="badge" style="background-color: {{ $order->orderstatus->color ?? '#64748b' }}; color: #ffffff;">
                                {{ $order->orderstatus->name ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.order.invoice', ['invoice_id' => $order->invoice_id]) }}" target="_blank" class="btn btn-sm btn-light border" title="ইনভয়েস দেখুন">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Merchant Fraud Reports --}}
    @if(isset($reports) && count($reports) > 0)
    <div class="fraud-card border-danger">
        <div class="fraud-card-header bg-danger text-white">
            <h6 class="fraud-card-title text-white">
                <i class="fas fa-exclamation-triangle"></i> মার্চেন্ট কমিউনিটি ফ্রড রিপোর্ট (Fraud Reports from Other Merchants)
                <span class="badge bg-white text-danger ms-2">{{ count($reports) }} Reports</span>
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>কুরিয়ার</th>
                        <th>মার্চেন্ট / কাস্টমার নাম</th>
                        <th>অভিযোগের বিবরণ</th>
                        <th>তারিখ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $r)
                    <tr>
                        <td>
                            <span class="fw-bold text-dark">{{ $r['courierName'] ?? '—' }}</span>
                        </td>
                        <td class="fw-semibold text-dark">{{ $r['name'] ?? '—' }}</td>
                        <td class="text-danger small">{{ $r['details'] ?? '—' }}</td>
                        <td class="small text-muted">
                            {{ !empty($r['created_at']) ? \Carbon\Carbon::parse($r['created_at'])->format('d M Y, h:i A') : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @else
    {{-- Initial Empty State / Feature Information --}}
    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-database fs-4"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">৬টি কুরিয়ার ডাটাবেজ চেক</h6>
                <p class="text-muted small mb-0">SteadFast, Pathao, RedX, PaperFly, ParcelDex এবং CarryBee-এর ডেলিভারি ডাটা একসাথে বিশ্লেষণ করে ফলাফল প্রদান করে।</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white">
                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-percent fs-4"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">সঠিক ডেলিভারি রেশিও</h6>
                <p class="text-muted small mb-0">কাস্টমার কতগুলো পার্সেল গ্রহণ করেছে এবং কতগুলো ক্যানসেল করেছে তার সঠিক শতকরা হার ও ঝুঁকি নির্দেশক দেখায়।</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-shield-alt fs-4"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">রিটার্ন ও ক্ষতি কমান</h6>
                <p class="text-muted small mb-0">অর্ডার পাঠানোর আগেই ঝুঁকিপূর্ণ কাস্টমার চিহ্নিত করে অগ্রিম ডেলিভারি চার্জ গ্রহণের মাধ্যমে ব্যবসার আর্থিক ক্ষতি রোধ করুন।</p>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection