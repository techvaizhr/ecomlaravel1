@extends('backEnd.layouts.master')
@section('title', 'ডেলিভারি চার্জ সেটিংস ও ম্যানেজমেন্ট')

@section('css')
<style>
    .delivery-settings-page {
        padding: 10px 0 40px;
    }
    
    /* Method Section Cards */
    .delivery-mode-option {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        margin-bottom: 16px;
        transition: all 0.25s ease;
        overflow: hidden;
    }
    .delivery-mode-option:hover {
        border-color: #93c5fd;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.06);
    }
    .delivery-mode-option.active {
        border-color: #2563eb;
        background: #f8fafc;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.1);
    }
    
    .delivery-mode-header {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
    }
    .delivery-mode-header:hover {
        background: #f1f5f9;
    }
    .delivery-mode-option.active .delivery-mode-header {
        background: #eff6ff;
    }
    
    .mode-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        flex-shrink: 0;
    }
    .mode-icon.free { background: #dcfce7; color: #16a34a; }
    .mode-icon.flat { background: #fef3c7; color: #d97706; }
    .mode-icon.weight { background: #f3e8ff; color: #9333ea; }
    .mode-icon.area { background: #e0f2fe; color: #0284c7; }

    .mode-title {
        font-size: 15.5px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2px;
    }
    .mode-desc {
        font-size: 12.5px;
        color: #64748b;
        margin-bottom: 0;
    }
    
    .mode-radio-input {
        width: 20px;
        height: 20px;
        accent-color: #2563eb;
        cursor: pointer;
    }

    /* Sub Settings Body */
    .mode-sub-body {
        padding: 16px 20px 20px;
        border-top: 1px dashed #cbd5e1;
        background: #ffffff;
    }
    
    /* Division Accordions */
    .division-accordion-item {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        margin-bottom: 12px;
        overflow: hidden;
    }
    .division-accordion-header {
        background: #f8fafc;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
    }
    .division-accordion-header:hover {
        background: #f1f5f9;
    }
    .district-table th {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #475569;
        background: #f8fafc;
        padding: 10px 14px;
    }
    .district-table td {
        padding: 10px 14px;
        vertical-align: middle;
        font-size: 13.5px;
    }
    .charge-input {
        max-width: 140px;
        font-weight: 600;
        border-radius: 6px;
    }

    /* Card Wrapper */
    .section-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .section-card-header {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .section-card-header h5 {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-card-body {
        padding: 20px;
    }

    /* Visual Target Picker Styles */
    .target-nav-pills {
        gap: 8px;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 12px;
    }
    .target-nav-pills .nav-link {
        border-radius: 20px;
        font-weight: 600;
        font-size: 13.5px;
        padding: 7px 16px;
        color: #475569;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .target-nav-pills .nav-link:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .target-nav-pills .nav-link.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
    }
    .target-nav-pills .nav-link.active .badge {
        background-color: #ffffff !important;
        color: #2563eb !important;
    }
    .target-nav-pills .nav-link .badge {
        font-size: 11px;
    }
    
    .target-picker-container {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        overflow: hidden;
    }
    .target-picker-toolbar {
        padding: 8px 12px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .target-search-box {
        position: relative;
        flex-grow: 1;
        max-width: 320px;
    }
    .target-search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }
    .target-search-box input {
        padding-left: 34px;
        border-radius: 20px;
        font-size: 12.5px;
    }
    .target-selected-chips-bar {
        padding: 6px 12px;
        background: #f8fafc;
        border-bottom: 1px dashed #e2e8f0;
        min-height: 38px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }
    .target-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        border-radius: 16px;
        padding: 2px 8px 2px 4px;
        font-size: 11.5px;
        font-weight: 600;
        animation: fadeIn 0.15s ease-in;
    }
    .target-chip img, .target-chip .chip-placeholder {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        object-fit: cover;
    }
    .target-chip .chip-remove {
        cursor: pointer;
        color: #93c5fd;
        font-size: 12px;
        margin-left: 2px;
        line-height: 1;
        transition: color 0.15s;
    }
    .target-chip .chip-remove:hover {
        color: #ef4444;
    }
    
    .target-card-grid {
        max-height: 250px;
        overflow-y: auto;
        padding: 10px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 8px;
    }
    .target-item-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 7px 10px;
        display: flex;
        align-items: center;
        gap: 9px;
        cursor: pointer;
        user-select: none;
        background: #ffffff;
        transition: all 0.15s ease;
        position: relative;
    }
    .target-item-card:hover {
        border-color: #93c5fd;
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.04);
    }
    .target-item-card.selected {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.12);
    }
    .target-item-thumb {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        object-fit: cover;
        background: #f1f5f9;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
    }
    .target-item-fallback {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #e2e8f0;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        flex-shrink: 0;
    }
    .target-item-info {
        flex-grow: 1;
        min-width: 0;
    }
    .target-item-name {
        font-size: 12.5px;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 1px;
    }
    .target-item-sub {
        font-size: 11px;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .target-item-checkbox {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #ffffff;
        flex-shrink: 0;
        transition: all 0.15s;
    }
    .target-item-card.selected .target-item-checkbox {
        background: #2563eb;
        border-color: #2563eb;
    }
</style>
@endsection

@section('content')
<div class="container-fluid delivery-settings-page">
    
    {{-- Page Header --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1 text-dark fw-bold">
                        <i class="fe-truck text-primary me-2"></i>ডেলিভারি চার্জ সেটিংস ও ম্যানেজমেন্ট
                    </h4>
                    <p class="text-muted mb-0 small">
                        সিস্টেমের সক্রিয় ডেলিভারি মোড (ফ্রি শিপিং, ফ্ল্যাট রেট, ওজন ভিত্তিক বা এরিয়া ভিত্তিক) এবং কাস্টম ডেলিভারি চার্জ নির্ধারণ করুন।
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.delivery.divisions.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fe-map-pin me-1"></i> ডেলিভারি লোকেশন
                    </a>
                </div>
            </div>
        </div>
    </div>

    @php
        $activeMode = old('active_method', $setting->active_method ?: 'area_based');
    @endphp

    {{-- Main Delivery Mode Settings Form --}}
    <form action="{{ route('admin.delivery.settings.update') }}" method="POST" id="deliveryModeForm">
        @csrf

        <div class="section-card">
            <div class="section-card-header">
                <h5>
                    <i class="fe-settings text-primary"></i>
                    <span>১. সক্রিয় ডেলিভারি মেথড নির্বাচন ও কনফিগারেশন</span>
                </h5>
                <span class="badge bg-primary rounded-pill px-3 py-1">১টি অপশন কার্যকর থাকবে</span>
            </div>
            <div class="section-card-body">
                
                {{-- OPTION 1: FREE SHIPPING --}}
                <div class="delivery-mode-option {{ $activeMode === 'free_delivery' ? 'active' : '' }}" id="mode_card_free">
                    <div class="delivery-mode-header" onclick="selectDeliveryMode('free_delivery')">
                        <div class="d-flex align-items-center gap-3">
                            <div class="mode-icon free">
                                <i class="fe-gift"></i>
                            </div>
                            <div>
                                <div class="mode-title">ফ্রি ডেলিভারি (Free Delivery)</div>
                                <p class="mode-desc">সারা দেশের সকল অর্ডারে সম্পূর্ণ ফ্রি ডেলিভারি (৳০) কার্যকর থাকবে।</p>
                            </div>
                        </div>
                        <div>
                            <input type="radio" name="active_method" id="radio_free_delivery" value="free_delivery" class="mode-radio-input" {{ $activeMode === 'free_delivery' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                {{-- OPTION 2: FLAT RATE --}}
                <div class="delivery-mode-option {{ $activeMode === 'flat_rate' ? 'active' : '' }}" id="mode_card_flat">
                    <div class="delivery-mode-header" onclick="selectDeliveryMode('flat_rate')">
                        <div class="d-flex align-items-center gap-3">
                            <div class="mode-icon flat">
                                <i class="fe-layers"></i>
                            </div>
                            <div>
                                <div class="mode-title">ফ্ল্যাট রেট (Flat Rate)</div>
                                <p class="mode-desc">সারা দেশের সকল জেলা ও এলাকার অর্ডারে একটি ফিক্সড নির্দিষ্ট চার্জ প্রযোজ্য হবে।</p>
                            </div>
                        </div>
                        <div>
                            <input type="radio" name="active_method" id="radio_flat_rate" value="flat_rate" class="mode-radio-input" {{ $activeMode === 'flat_rate' ? 'checked' : '' }}>
                        </div>
                    </div>
                    {{-- Flat Rate Inline Sub Body --}}
                    <div class="mode-sub-body" id="sub_body_flat_rate" style="{{ $activeMode === 'flat_rate' ? '' : 'display:none;' }}">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-dark mb-1">
                                    <i class="fe-tag text-warning me-1"></i>নির্দিষ্ট ফ্ল্যাট রেট চার্জ (৳) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0" name="flat_rate_amount" class="form-control fw-bold" value="{{ old('flat_rate_amount', $setting->flat_rate_amount ?? 100.00) }}" placeholder="100.00">
                                </div>
                                <small class="text-muted d-block mt-1">ফ্ল্যাট রেট একটিভ থাকলে সকল জেলার ডেলিভারি চার্জ এটি হবে।</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- OPTION 3: WEIGHT BASED --}}
                <div class="delivery-mode-option {{ $activeMode === 'weight_based' ? 'active' : '' }}" id="mode_card_weight">
                    <div class="delivery-mode-header" onclick="selectDeliveryMode('weight_based')">
                        <div class="d-flex align-items-center gap-3">
                            <div class="mode-icon weight">
                                <i class="fe-shield"></i>
                            </div>
                            <div>
                                <div class="mode-title">ওজন ভিত্তিক ডেলিভারি (Weight Based)</div>
                                <p class="mode-desc">কার্টের মোট পণ্যের ওজনের (কেজি) ওপর ভিত্তি করে চার্জ স্বয়ংক্রিয়ভাবে গণনা হবে।</p>
                            </div>
                        </div>
                        <div>
                            <input type="radio" name="active_method" id="radio_weight_based" value="weight_based" class="mode-radio-input" {{ $activeMode === 'weight_based' ? 'checked' : '' }}>
                        </div>
                    </div>
                    {{-- Weight Based Inline Sub Body --}}
                    <div class="mode-sub-body" id="sub_body_weight_based" style="{{ $activeMode === 'weight_based' ? '' : 'display:none;' }}">
                        <label class="form-label fw-bold text-dark mb-2">
                            <i class="fe-sliders text-purple me-1"></i>ওজনভিত্তিক ডেলিভারি প্যারামিটার
                        </label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-2 border rounded-3 bg-light">
                                    <small class="fw-semibold text-muted d-block mb-1">বেস ওজন (Base KG)</small>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" min="0.1" name="weight_base_kg" class="form-control fw-bold" value="{{ old('weight_base_kg', $setting->weight_base_kg ?? 1.00) }}">
                                        <span class="input-group-text">KG</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded-3 bg-light">
                                    <small class="fw-semibold text-muted d-block mb-1">বেস ওজনের চার্জ (৳)</small>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" step="0.01" min="0" name="weight_base_cost" class="form-control fw-bold" value="{{ old('weight_base_cost', $setting->weight_base_cost ?? 60.00) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded-3 bg-light">
                                    <small class="fw-semibold text-muted d-block mb-1">প্রতি অতিরিক্ত কেজি চার্জ (৳)</small>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" step="0.01" min="0" name="weight_extra_per_kg" class="form-control fw-bold" value="{{ old('weight_extra_per_kg', $setting->weight_extra_per_kg ?? 20.00) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            * উদাহরণ: ১ম {{ $setting->weight_base_kg ?? 1 }} কেজির জন্য ৳{{ $setting->weight_base_cost ?? 60 }}, এরপর প্রতি অতিরিক্ত কেজির জন্য ৳{{ $setting->weight_extra_per_kg ?? 20 }} যোগ হবে।
                        </small>
                    </div>
                </div>

                {{-- OPTION 4: AREA BASED (DIVISION & DISTRICT OVERRIDES) --}}
                <div class="delivery-mode-option {{ $activeMode === 'area_based' ? 'active' : '' }}" id="mode_card_area">
                    <div class="delivery-mode-header" onclick="selectDeliveryMode('area_based')">
                        <div class="d-flex align-items-center gap-3">
                            <div class="mode-icon area">
                                <i class="fe-map-pin"></i>
                            </div>
                            <div>
                                <div class="mode-title">এরিয়া ভিত্তিক (Area Based)</div>
                                <p class="mode-desc">বিভাগ ও জেলা অনুযায়ী আলাদা ডেলিভারি চার্জ কার্যকর হবে (বিভাগ রেট ও জেলা ওভাররাইড)।</p>
                            </div>
                        </div>
                        <div>
                            <input type="radio" name="active_method" id="radio_area_based" value="area_based" class="mode-radio-input" {{ $activeMode === 'area_based' ? 'checked' : '' }}>
                        </div>
                    </div>
                    {{-- Area Based Inline Sub Body (Division & District Accordions) --}}
                    <div class="mode-sub-body p-3" id="sub_body_area_based" style="{{ $activeMode === 'area_based' ? '' : 'display:none;' }}">
                        
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><i class="fe-map text-primary me-1"></i>বিভাগ ও জেলা ভিত্তিক রেট কনফিগারেশন</h6>
                                <small class="text-muted">
                                    বিভাগে যে চার্জ দেবেন, সেই বিভাগের সকল জেলায় তা কার্যকর হবে। কোনো জেলায় ভিন্ন চার্জ দিতে চাইলে জেলা বক্সে ওভাররাইড করুন।
                                </small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" id="districtFilterInput" class="form-control form-control-sm rounded-pill" placeholder="জেলা সার্চ করুন..." style="width: 200px;">
                            </div>
                        </div>

                        {{-- Division Accordions (Default Collapsed/Hide as requested) --}}
                        <div class="division-list-container">
                            @foreach($divisions as $div)
                            <div class="division-accordion-item" data-div-id="{{ $div->id }}">
                                <div class="division-accordion-header" data-bs-toggle="collapse" data-bs-target="#collapse_div_{{ $div->id }}" aria-expanded="false">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-soft-primary text-primary fs-6 px-2.5 py-1">
                                            <i class="fe-map-pin me-1"></i>{{ $div->name }} বিভাগ
                                        </span>
                                        <span class="text-muted small">({{ $div->districts->count() }}টি জেলা)</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation();">
                                        <span class="fw-semibold small text-dark">বিভাগীয় চার্জ:</span>
                                        <div class="input-group input-group-sm" style="width: 130px;">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" step="0.01" min="0" name="divisions[{{ $div->id }}][charge]" class="form-control charge-input" value="{{ (float) ($div->delivery_charge ?? 0) }}" placeholder="0.00">
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light border ms-2" data-bs-toggle="collapse" data-bs-target="#collapse_div_{{ $div->id }}" title="জেলাসমূহ দেখতে বা লুকাতে ক্লিক করুন">
                                            <i class="fe-chevron-down"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Collapsed by default (no 'show' class) --}}
                                <div class="collapse" id="collapse_div_{{ $div->id }}">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered district-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px;">#</th>
                                                    <th>জেলার নাম (District)</th>
                                                    <th>বিভাগ</th>
                                                    <th style="width: 230px;">নির্দিষ্ট জেলা ওভাররাইড চার্জ (District Rate)</th>
                                                    <th style="width: 130px;">কার্যকর চার্জ</th>
                                                    <th style="width: 80px;" class="text-center">অ্যাকশন</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($div->districts as $idx => $dist)
                                                @php
                                                    $distCharge = (float) ($dist->delivery_charge ?? 0);
                                                    $divCharge  = (float) ($div->delivery_charge ?? 0);
                                                    $effectiveCharge = $distCharge > 0 ? $distCharge : $divCharge;
                                                @endphp
                                                <tr class="district-row" data-name="{{ strtolower($dist->name) }}">
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td><strong>{{ $dist->name }}</strong></td>
                                                    <td><span class="text-muted">{{ $div->name }}</span></td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">৳</span>
                                                            <input type="number" step="0.01" min="0" name="districts[{{ $dist->id }}][charge]" id="dist_input_{{ $dist->id }}" class="form-control charge-input" value="{{ $distCharge > 0 ? $distCharge : '' }}" placeholder="বিভাগীয় চার্জ (৳{{ $divCharge }})">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $distCharge > 0 ? 'bg-success' : 'bg-secondary' }} px-2 py-1">
                                                            ৳{{ $effectiveCharge }} {{ $distCharge > 0 ? '(কাস্টম)' : '(বিভাগীয়)' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-xs btn-outline-primary btn-save-district" data-id="{{ $dist->id }}" title="শুধু এই জেলা সেভ করুন">
                                                            <i class="fe-save"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>

                {{-- Save Button for Settings --}}
                <div class="text-end mt-4 pt-2 border-top">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                        <i class="fe-check-circle me-1"></i> সেটিংস সংরক্ষণ করুন (Save Delivery Settings)
                    </button>
                </div>

            </div>
        </div>
    </form>

    {{-- SECTION 2: CUSTOM DELIVERY CHARGES (At Bottom / Niche) --}}
    <div class="section-card">
        <div class="section-card-header">
            <div>
                <h5>
                    <i class="fe-tag text-danger"></i>
                    <span>২. কাস্টম ডেলিভারি চার্জেস (Custom Delivery Charges)</span>
                </h5>
                <small class="text-muted">
                    ক্যাটাগরি, ব্র্যান্ড বা নির্দিষ্ট প্রোডাক্টের জন্য কাস্টম ডেলিভারি চার্জ নির্ধারণ করুন। কার্ট বা অর্ডারে এই চার্জ সবসময় সর্বোচ্চ অগ্রাধিকার (Priority) পাবে।
                </small>
            </div>
            <div>
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCustomChargeModal">
                    <i class="fe-plus-circle me-1"></i> নতুন কাস্টম চার্জ যোগ করুন
                </button>
            </div>
        </div>
        <div class="section-card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45px;">#</th>
                            <th>কাস্টম চার্জের নাম</th>
                            <th>প্রযোজ্য শর্ত (ক্যাটাগরি / ব্র্যান্ড / প্রোডাক্ট)</th>
                            <th style="width: 140px;">চার্জের পরিমাণ</th>
                            <th style="width: 100px;" class="text-center">স্ট্যাটাস</th>
                            <th style="width: 110px;" class="text-center">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customCharges as $idx => $cc)
                        @php
                            $ccCatIds   = is_array($cc->category_ids) ? $cc->category_ids : [];
                            $ccBrandIds = is_array($cc->brand_ids) ? $cc->brand_ids : [];
                            $ccProdIds  = is_array($cc->product_ids) ? $cc->product_ids : [];

                            $matchedCats   = !empty($ccCatIds) ? $categories->whereIn('id', $ccCatIds) : collect();
                            $matchedBrands = !empty($ccBrandIds) ? $brands->whereIn('id', $ccBrandIds) : collect();
                            $matchedProds  = !empty($ccProdIds) ? $products->whereIn('id', $ccProdIds) : collect();
                            $hasConditions = $matchedCats->isNotEmpty() || $matchedBrands->isNotEmpty() || $matchedProds->isNotEmpty();
                        @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <strong class="text-dark">{{ $cc->name }}</strong>
                            </td>
                            <td>
                                @if(!$hasConditions)
                                    <span class="badge bg-light text-muted border">কোনো নির্দিষ্ট শর্ত নেই (সাধারণ)</span>
                                @else
                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        @if($matchedCats->isNotEmpty())
                                            <div class="mb-1">
                                                <small class="text-muted fw-bold d-block" style="font-size: 11px;">ক্যাটাগরি:</small>
                                                @foreach($matchedCats as $c)
                                                    <span class="badge bg-soft-primary text-primary me-1">
                                                        <i class="fe-grid me-1"></i>{{ $c->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($matchedBrands->isNotEmpty())
                                            <div class="mb-1 ms-2">
                                                <small class="text-muted fw-bold d-block" style="font-size: 11px;">ব্র্যান্ড:</small>
                                                @foreach($matchedBrands as $b)
                                                    <span class="badge bg-soft-success text-success me-1">
                                                        <i class="fe-award me-1"></i>{{ $b->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($matchedProds->isNotEmpty())
                                            <div class="mb-1 ms-2">
                                                <small class="text-muted fw-bold d-block" style="font-size: 11px;">নির্দিষ্ট প্রোডাক্ট:</small>
                                                @foreach($matchedProds as $p)
                                                    <span class="badge bg-soft-warning text-dark me-1" title="{{ $p->name }}">
                                                        <i class="fe-box me-1"></i>{{ \Illuminate\Support\Str::limit($p->name, 25) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-soft-success text-success fs-6 px-2.5 py-1">
                                    ৳{{ number_format($cc->amount, 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($cc->status)
                                    <span class="badge bg-success">সক্রিয়</span>
                                @else
                                    <span class="badge bg-danger">নিষ্ক্রিয়</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editCustomChargeModal_{{ $cc->id }}" title="এডিট করুন">
                                    <i class="fe-edit"></i>
                                </button>
                                <form action="{{ route('admin.delivery.settings.custom-charge.destroy', $cc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত এই কাস্টম চার্জটি ডিলিট করতে চান?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger" title="ডিলিট করুন">
                                        <i class="fe-trash-2"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- EDIT MODAL WITH VISUAL TARGET SELECTOR --}}
                        <div class="modal fade" id="editCustomChargeModal_{{ $cc->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <form action="{{ route('admin.delivery.settings.custom-charge.update', $cc->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold text-dark">
                                                <i class="fe-edit text-primary me-1"></i>কাস্টম ডেলিভারি চার্জ সম্পাদনা
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            
                                            {{-- Top Section: Basic Info --}}
                                            <div class="row g-3 mb-4 pb-3 border-bottom">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold text-dark">কাস্টম চার্জের নাম <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control fw-semibold" required value="{{ $cc->name }}" placeholder="যেমন: এক্সপ্রেস ডেলিভারি, ফার্নিচার ইত্যাদি">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold text-dark">ডেলিভারি চার্জের পরিমাণ (৳) <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text fw-bold">৳</span>
                                                        <input type="number" step="0.01" min="0" name="amount" class="form-control fw-bold text-primary" required value="{{ $cc->amount }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold text-dark">স্ট্যাটাস</label>
                                                    <select name="status" class="form-select fw-semibold">
                                                        <option value="1" {{ $cc->status == 1 ? 'selected' : '' }}>সক্রিয় (Active)</option>
                                                        <option value="0" {{ $cc->status == 0 ? 'selected' : '' }}>নিষ্ক্রিয় (Inactive)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Visual Targeting Section --}}
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-0">
                                                            <i class="fe-crosshair text-danger me-1"></i>প্রযোজ্য শর্ত নির্বাচন করুন (ক্যাটাগরি / ব্র্যান্ড / পণ্য)
                                                        </h6>
                                                        <small class="text-muted">পণ্য বা ব্র্যান্ড বা ক্যাটাগরি সার্চ করে ক্লিক করুন। সিলেক্ট করা আইটেমগুলোতে এই চার্জ অগ্রাধিকার পাবে।</small>
                                                    </div>
                                                </div>

                                                {{-- Tab Navs --}}
                                                <ul class="nav target-nav-pills mb-3" id="editTab_{{ $cc->id }}" role="tablist">
                                                    <li class="nav-item">
                                                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#edit_cats_tab_{{ $cc->id }}" type="button">
                                                            <i class="fe-grid"></i> ক্যাটাগরি সমূহ 
                                                            <span class="badge bg-primary text-white rounded-pill ms-1 cat-count">{{ count($ccCatIds) }}</span>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item">
                                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#edit_brands_tab_{{ $cc->id }}" type="button">
                                                            <i class="fe-award"></i> ব্র্যান্ড সমূহ 
                                                            <span class="badge bg-success text-white rounded-pill ms-1 brand-count">{{ count($ccBrandIds) }}</span>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item">
                                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#edit_prods_tab_{{ $cc->id }}" type="button">
                                                            <i class="fe-box"></i> নির্দিষ্ট প্রোডাক্টসমূহ 
                                                            <span class="badge bg-warning text-dark rounded-pill ms-1 prod-count">{{ count($ccProdIds) }}</span>
                                                        </button>
                                                    </li>
                                                </ul>

                                                {{-- Tab Panes --}}
                                                <div class="tab-content">
                                                    
                                                    {{-- CATEGORIES TAB --}}
                                                    <div class="tab-pane fade show active" id="edit_cats_tab_{{ $cc->id }}">
                                                        <div class="target-picker-container">
                                                            <div class="target-picker-toolbar">
                                                                <div class="target-search-box">
                                                                    <i class="fe-search"></i>
                                                                    <input type="text" class="form-control form-control-sm target-filter-input" placeholder="ক্যাটাগরি সার্চ করুন...">
                                                                </div>
                                                                <div class="d-flex gap-1">
                                                                    <button type="button" class="btn btn-xs btn-outline-primary select-all-btn">সব সিলেক্ট</button>
                                                                    <button type="button" class="btn btn-xs btn-outline-secondary deselect-all-btn">সব ক্লিয়ার</button>
                                                                </div>
                                                            </div>
                                                            <div class="target-selected-chips-bar" data-type="cat">
                                                                @forelse($matchedCats as $mc)
                                                                <span class="target-chip" data-id="{{ $mc->id }}">
                                                                    @if($mc->image)
                                                                        <img src="{{ asset($mc->image) }}" alt="{{ $mc->name }}" onerror="this.src='{{ asset('public/uploads/category/default.png') }}';">
                                                                    @else
                                                                        <span class="chip-placeholder">{{ strtoupper(substr($mc->name, 0, 1)) }}</span>
                                                                    @endif
                                                                    <span>{{ $mc->name }}</span>
                                                                    <span class="chip-remove" onclick="removeTargetChip(this, '{{ $mc->id }}')">&times;</span>
                                                                </span>
                                                                @empty
                                                                <small class="text-muted fst-italic no-chip-msg">কোনো ক্যাটাগরি সিলেক্ট করা হয়নি</small>
                                                                @endforelse
                                                            </div>
                                                            <div class="target-card-grid">
                                                                @foreach($categories as $cat)
                                                                @php $isSelected = in_array($cat->id, $ccCatIds); @endphp
                                                                <div class="target-item-card {{ $isSelected ? 'selected' : '' }}" data-type="cat" data-id="{{ $cat->id }}" data-name="{{ strtolower($cat->name) }}">
                                                                    <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" class="d-none target-checkbox" {{ $isSelected ? 'checked' : '' }}>
                                                                    @if($cat->image)
                                                                        <img src="{{ asset($cat->image) }}" class="target-item-thumb" alt="{{ $cat->name }}" onerror="this.src='{{ asset('public/uploads/category/default.png') }}';">
                                                                    @else
                                                                        <span class="target-item-fallback">{{ strtoupper(substr($cat->name, 0, 2)) }}</span>
                                                                    @endif
                                                                    <div class="target-item-info">
                                                                        <div class="target-item-name" title="{{ $cat->name }}">{{ $cat->name }}</div>
                                                                        <div class="target-item-sub">ক্যাটাগরি</div>
                                                                    </div>
                                                                    <div class="target-item-checkbox">
                                                                        <i class="fe-check"></i>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- BRANDS TAB --}}
                                                    <div class="tab-pane fade" id="edit_brands_tab_{{ $cc->id }}">
                                                        <div class="target-picker-container">
                                                            <div class="target-picker-toolbar">
                                                                <div class="target-search-box">
                                                                    <i class="fe-search"></i>
                                                                    <input type="text" class="form-control form-control-sm target-filter-input" placeholder="ব্র্যান্ড সার্চ করুন...">
                                                                </div>
                                                                <div class="d-flex gap-1">
                                                                    <button type="button" class="btn btn-xs btn-outline-success select-all-btn">সব সিলেক্ট</button>
                                                                    <button type="button" class="btn btn-xs btn-outline-secondary deselect-all-btn">সব ক্লিয়ার</button>
                                                                </div>
                                                            </div>
                                                            <div class="target-selected-chips-bar" data-type="brand">
                                                                @forelse($matchedBrands as $mb)
                                                                <span class="target-chip" data-id="{{ $mb->id }}">
                                                                    @if($mb->image)
                                                                        <img src="{{ asset($mb->image) }}" alt="{{ $mb->name }}">
                                                                    @else
                                                                        <span class="chip-placeholder">{{ strtoupper(substr($mb->name, 0, 1)) }}</span>
                                                                    @endif
                                                                    <span>{{ $mb->name }}</span>
                                                                    <span class="chip-remove" onclick="removeTargetChip(this, '{{ $mb->id }}')">&times;</span>
                                                                </span>
                                                                @empty
                                                                <small class="text-muted fst-italic no-chip-msg">কোনো ব্র্যান্ড সিলেক্ট করা হয়নি</small>
                                                                @endforelse
                                                            </div>
                                                            <div class="target-card-grid">
                                                                @foreach($brands as $b)
                                                                @php $isSelected = in_array($b->id, $ccBrandIds); @endphp
                                                                <div class="target-item-card {{ $isSelected ? 'selected' : '' }}" data-type="brand" data-id="{{ $b->id }}" data-name="{{ strtolower($b->name) }}">
                                                                    <input type="checkbox" name="brand_ids[]" value="{{ $b->id }}" class="d-none target-checkbox" {{ $isSelected ? 'checked' : '' }}>
                                                                    @if($b->image)
                                                                        <img src="{{ asset($b->image) }}" class="target-item-thumb" alt="{{ $b->name }}" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                                                        <span class="target-item-fallback" style="display:none;">{{ strtoupper(substr($b->name, 0, 2)) }}</span>
                                                                    @else
                                                                        <span class="target-item-fallback">{{ strtoupper(substr($b->name, 0, 2)) }}</span>
                                                                    @endif
                                                                    <div class="target-item-info">
                                                                        <div class="target-item-name" title="{{ $b->name }}">{{ $b->name }}</div>
                                                                        <div class="target-item-sub">ব্র্যান্ড</div>
                                                                    </div>
                                                                    <div class="target-item-checkbox">
                                                                        <i class="fe-check"></i>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- PRODUCTS TAB --}}
                                                    <div class="tab-pane fade" id="edit_prods_tab_{{ $cc->id }}">
                                                        <div class="target-picker-container">
                                                            <div class="target-picker-toolbar">
                                                                <div class="target-search-box">
                                                                    <i class="fe-search"></i>
                                                                    <input type="text" class="form-control form-control-sm target-filter-input" placeholder="প্রোডাক্ট নাম বা কোড দিয়ে সার্চ করুন...">
                                                                </div>
                                                                <div class="d-flex gap-1">
                                                                    <button type="button" class="btn btn-xs btn-outline-warning select-all-btn">সব সিলেক্ট</button>
                                                                    <button type="button" class="btn btn-xs btn-outline-secondary deselect-all-btn">সব ক্লিয়ার</button>
                                                                </div>
                                                            </div>
                                                            <div class="target-selected-chips-bar" data-type="product">
                                                                @forelse($matchedProds as $mp)
                                                                <span class="target-chip" data-id="{{ $mp->id }}">
                                                                    @if($mp->image?->image)
                                                                        <img src="{{ asset($mp->image->image) }}" alt="{{ $mp->name }}">
                                                                    @else
                                                                        <span class="chip-placeholder">{{ strtoupper(substr($mp->name, 0, 1)) }}</span>
                                                                    @endif
                                                                    <span>{{ \Illuminate\Support\Str::limit($mp->name, 20) }}</span>
                                                                    <span class="chip-remove" onclick="removeTargetChip(this, '{{ $mp->id }}')">&times;</span>
                                                                </span>
                                                                @empty
                                                                <small class="text-muted fst-italic no-chip-msg">কোনো নির্দিষ্ট পণ্য সিলেক্ট করা হয়নি</small>
                                                                @endforelse
                                                            </div>
                                                            <div class="target-card-grid">
                                                                @foreach($products as $p)
                                                                @php $isSelected = in_array($p->id, $ccProdIds); @endphp
                                                                <div class="target-item-card {{ $isSelected ? 'selected' : '' }}" data-type="product" data-id="{{ $p->id }}" data-name="{{ strtolower($p->name . ' ' . $p->product_code) }}">
                                                                    <input type="checkbox" name="product_ids[]" value="{{ $p->id }}" class="d-none target-checkbox" {{ $isSelected ? 'checked' : '' }}>
                                                                    @if($p->image?->image)
                                                                        <img src="{{ asset($p->image->image) }}" class="target-item-thumb" alt="{{ $p->name }}" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                                                        <span class="target-item-fallback" style="display:none;">{{ strtoupper(substr($p->name, 0, 2)) }}</span>
                                                                    @else
                                                                        <span class="target-item-fallback">{{ strtoupper(substr($p->name, 0, 2)) }}</span>
                                                                    @endif
                                                                    <div class="target-item-info">
                                                                        <div class="target-item-name" title="{{ $p->name }}">{{ $p->name }}</div>
                                                                        <div class="target-item-sub">{{ $p->product_code ? 'Code: '.$p->product_code : 'পণ্য' }}</div>
                                                                    </div>
                                                                    <div class="target-item-checkbox">
                                                                        <i class="fe-check"></i>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">বন্ধ করুন</button>
                                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">
                                                <i class="fe-check me-1"></i> আপডেট সংরক্ষণ করুন
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fe-info me-1"></i> এখনো কোনো কাস্টম ডেলিভারি চার্জ তৈরি করা হয়নি। উপরে <strong>নতুন কাস্টম চার্জ যোগ করুন</strong> বাটনে ক্লিক করে তৈরি করতে পারেন।
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL: ADD CUSTOM DELIVERY CHARGE WITH VISUAL TARGET SELECTOR --}}
    <div class="modal fade" id="addCustomChargeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('admin.delivery.settings.custom-charge.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="fe-plus-circle text-danger me-1"></i>নতুন কাস্টম ডেলিভারি চার্জ তৈরি
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        
                        {{-- Top Section: Basic Info --}}
                        <div class="row g-3 mb-4 pb-3 border-bottom">
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-dark">কাস্টম চার্জের নাম <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control fw-semibold" required placeholder="যেমন: এক্সপ্রেস ডেলিভারি, ফার্নিচার, কাচের পণ্য ইত্যাদি">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">ডেলিভারি চার্জের পরিমাণ (৳) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text fw-bold">৳</span>
                                    <input type="number" step="0.01" min="0" name="amount" class="form-control fw-bold text-danger" required placeholder="150.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark">স্ট্যাটাস</label>
                                <select name="status" class="form-select fw-semibold">
                                    <option value="1" selected>সক্রিয় (Active)</option>
                                    <option value="0">নিষ্ক্রিয় (Inactive)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Visual Targeting Section --}}
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="fe-crosshair text-danger me-1"></i>প্রযোজ্য শর্ত নির্বাচন করুন (ক্যাটাগরি / ব্র্যান্ড / পণ্য)
                                    </h6>
                                    <small class="text-muted">পণ্য বা ব্র্যান্ড বা ক্যাটাগরি সার্চ করে ক্লিক করুন। সিলেক্ট করা আইটেমগুলোতে এই চার্জ অগ্রাধিকার পাবে।</small>
                                </div>
                            </div>

                            {{-- Tab Navs --}}
                            <ul class="nav target-nav-pills mb-3" id="addTab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#add_cats_tab" type="button">
                                        <i class="fe-grid"></i> ক্যাটাগরি সমূহ 
                                        <span class="badge bg-primary text-white rounded-pill ms-1 cat-count">0</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#add_brands_tab" type="button">
                                        <i class="fe-award"></i> ব্র্যান্ড সমূহ 
                                        <span class="badge bg-success text-white rounded-pill ms-1 brand-count">0</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#add_prods_tab" type="button">
                                        <i class="fe-box"></i> নির্দিষ্ট প্রোডাক্টসমূহ 
                                        <span class="badge bg-warning text-dark rounded-pill ms-1 prod-count">0</span>
                                    </button>
                                </li>
                            </ul>

                            {{-- Tab Panes --}}
                            <div class="tab-content">
                                
                                {{-- CATEGORIES TAB --}}
                                <div class="tab-pane fade show active" id="add_cats_tab">
                                    <div class="target-picker-container">
                                        <div class="target-picker-toolbar">
                                            <div class="target-search-box">
                                                <i class="fe-search"></i>
                                                <input type="text" class="form-control form-control-sm target-filter-input" placeholder="ক্যাটাগরি সার্চ করুন...">
                                            </div>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-xs btn-outline-primary select-all-btn">সব সিলেক্ট</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary deselect-all-btn">সব ক্লিয়ার</button>
                                            </div>
                                        </div>
                                        <div class="target-selected-chips-bar" data-type="cat">
                                            <small class="text-muted fst-italic no-chip-msg">কোনো ক্যাটাগরি সিলেক্ট করা হয়নি</small>
                                        </div>
                                        <div class="target-card-grid">
                                            @foreach($categories as $cat)
                                            <div class="target-item-card" data-type="cat" data-id="{{ $cat->id }}" data-name="{{ strtolower($cat->name) }}">
                                                <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" class="d-none target-checkbox">
                                                @if($cat->image)
                                                    <img src="{{ asset($cat->image) }}" class="target-item-thumb" alt="{{ $cat->name }}" onerror="this.src='{{ asset('public/uploads/category/default.png') }}';">
                                                @else
                                                    <span class="target-item-fallback">{{ strtoupper(substr($cat->name, 0, 2)) }}</span>
                                                @endif
                                                <div class="target-item-info">
                                                    <div class="target-item-name" title="{{ $cat->name }}">{{ $cat->name }}</div>
                                                    <div class="target-item-sub">ক্যাটাগরি</div>
                                                </div>
                                                <div class="target-item-checkbox">
                                                    <i class="fe-check"></i>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- BRANDS TAB --}}
                                <div class="tab-pane fade" id="add_brands_tab">
                                    <div class="target-picker-container">
                                        <div class="target-picker-toolbar">
                                            <div class="target-search-box">
                                                <i class="fe-search"></i>
                                                <input type="text" class="form-control form-control-sm target-filter-input" placeholder="ব্র্যান্ড সার্চ করুন...">
                                            </div>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-xs btn-outline-success select-all-btn">সব সিলেক্ট</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary deselect-all-btn">সব ক্লিয়ার</button>
                                            </div>
                                        </div>
                                        <div class="target-selected-chips-bar" data-type="brand">
                                            <small class="text-muted fst-italic no-chip-msg">কোনো ব্র্যান্ড সিলেক্ট করা হয়নি</small>
                                        </div>
                                        <div class="target-card-grid">
                                            @foreach($brands as $b)
                                            <div class="target-item-card" data-type="brand" data-id="{{ $b->id }}" data-name="{{ strtolower($b->name) }}">
                                                <input type="checkbox" name="brand_ids[]" value="{{ $b->id }}" class="d-none target-checkbox">
                                                @if($b->image)
                                                    <img src="{{ asset($b->image) }}" class="target-item-thumb" alt="{{ $b->name }}" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                                    <span class="target-item-fallback" style="display:none;">{{ strtoupper(substr($b->name, 0, 2)) }}</span>
                                                @else
                                                    <span class="target-item-fallback">{{ strtoupper(substr($b->name, 0, 2)) }}</span>
                                                @endif
                                                <div class="target-item-info">
                                                    <div class="target-item-name" title="{{ $b->name }}">{{ $b->name }}</div>
                                                    <div class="target-item-sub">ব্র্যান্ড</div>
                                                </div>
                                                <div class="target-item-checkbox">
                                                    <i class="fe-check"></i>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- PRODUCTS TAB --}}
                                <div class="tab-pane fade" id="add_prods_tab">
                                    <div class="target-picker-container">
                                        <div class="target-picker-toolbar">
                                            <div class="target-search-box">
                                                <i class="fe-search"></i>
                                                <input type="text" class="form-control form-control-sm target-filter-input" placeholder="প্রোডাক্ট নাম বা কোড দিয়ে সার্চ করুন...">
                                            </div>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-xs btn-outline-warning select-all-btn">সব সিলেক্ট</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary deselect-all-btn">সব ক্লিয়ার</button>
                                            </div>
                                        </div>
                                        <div class="target-selected-chips-bar" data-type="product">
                                            <small class="text-muted fst-italic no-chip-msg">কোনো নির্দিষ্ট পণ্য সিলেক্ট করা হয়নি</small>
                                        </div>
                                        <div class="target-card-grid">
                                            @foreach($products as $p)
                                            <div class="target-item-card" data-type="product" data-id="{{ $p->id }}" data-name="{{ strtolower($p->name . ' ' . $p->product_code) }}">
                                                <input type="checkbox" name="product_ids[]" value="{{ $p->id }}" class="d-none target-checkbox">
                                                @if($p->image?->image)
                                                    <img src="{{ asset($p->image->image) }}" class="target-item-thumb" alt="{{ $p->name }}" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                                    <span class="target-item-fallback" style="display:none;">{{ strtoupper(substr($p->name, 0, 2)) }}</span>
                                                @else
                                                    <span class="target-item-fallback">{{ strtoupper(substr($p->name, 0, 2)) }}</span>
                                                @endif
                                                <div class="target-item-info">
                                                    <div class="target-item-name" title="{{ $p->name }}">{{ $p->name }}</div>
                                                    <div class="target-item-sub">{{ $p->product_code ? 'Code: '.$p->product_code : 'পণ্য' }}</div>
                                                </div>
                                                <div class="target-item-checkbox">
                                                    <i class="fe-check"></i>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">বাতিল</button>
                        <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold shadow-sm">
                            <i class="fe-plus-circle me-1"></i> কাস্টম চার্জ সংরক্ষণ করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
function selectDeliveryMode(mode) {
    $('#radio_' + mode).prop('checked', true);

    $('.delivery-mode-option').removeClass('active');
    $('#mode_card_' + (mode === 'free_delivery' ? 'free' : (mode === 'flat_rate' ? 'flat' : (mode === 'weight_based' ? 'weight' : 'area')))).addClass('active');

    if (mode === 'flat_rate') {
        $('#sub_body_flat_rate').slideDown(200);
        $('#sub_body_weight_based, #sub_body_area_based').slideUp(200);
    } else if (mode === 'weight_based') {
        $('#sub_body_weight_based').slideDown(200);
        $('#sub_body_flat_rate, #sub_body_area_based').slideUp(200);
    } else if (mode === 'area_based') {
        $('#sub_body_area_based').slideDown(200);
        $('#sub_body_flat_rate, #sub_body_weight_based').slideUp(200);
    } else {
        $('#sub_body_flat_rate, #sub_body_weight_based, #sub_body_area_based').slideUp(200);
    }
}

// Global function to remove chip on click
function removeTargetChip(element, id) {
    var $chip = $(element).closest('.target-chip');
    var $container = $chip.closest('.target-picker-container');
    var $card = $container.find('.target-item-card[data-id="' + id + '"]');
    
    if ($card.length) {
        $card.removeClass('selected');
        $card.find('.target-checkbox').prop('checked', false);
    }
    $chip.remove();
    updateTabCountAndChips($container);
}

function updateTabCountAndChips($container) {
    var $modal = $container.closest('.modal');
    var type = $container.find('.target-selected-chips-bar').data('type');
    
    var selectedCards = $container.find('.target-item-card.selected');
    var count = selectedCards.length;

    // Update Nav Tab Count Badge
    if (type === 'cat') {
        $modal.find('.cat-count').text(count);
    } else if (type === 'brand') {
        $modal.find('.brand-count').text(count);
    } else if (type === 'product') {
        $modal.find('.prod-count').text(count);
    }

    // Update Chips Bar
    var $chipsBar = $container.find('.target-selected-chips-bar');
    $chipsBar.empty();

    if (count === 0) {
        var msg = type === 'cat' ? 'কোনো ক্যাটাগরি সিলেক্ট করা হয়নি' : (type === 'brand' ? 'কোনো ব্র্যান্ড সিলেক্ট করা হয়নি' : 'কোনো নির্দিষ্ট পণ্য সিলেক্ট করা হয়নি');
        $chipsBar.append('<small class="text-muted fst-italic no-chip-msg">' + msg + '</small>');
    } else {
        selectedCards.each(function() {
            var $c = $(this);
            var id = $c.data('id');
            var name = $c.find('.target-item-name').text();
            var $thumb = $c.find('.target-item-thumb');
            var $fallback = $c.find('.target-item-fallback');
            
            var chipImgHtml = '';
            if ($thumb.length && $thumb.is(':visible')) {
                chipImgHtml = '<img src="' + $thumb.attr('src') + '" alt="' + name + '">';
            } else if ($fallback.length) {
                chipImgHtml = '<span class="chip-placeholder">' + $fallback.text().substring(0, 1) + '</span>';
            }

            var chipHtml = '<span class="target-chip" data-id="' + id + '">' +
                chipImgHtml +
                '<span>' + (name.length > 20 ? name.substring(0, 20) + '...' : name) + '</span>' +
                '<span class="chip-remove" onclick="removeTargetChip(this, \'' + id + '\')">&times;</span>' +
                '</span>';
            
            $chipsBar.append(chipHtml);
        });
    }
}

$(document).ready(function() {
    // Card Click Toggle
    $(document).on('click', '.target-item-card', function(e) {
        if ($(e.target).is('input[type="checkbox"]')) return;
        
        var $card = $(this);
        var $checkbox = $card.find('.target-checkbox');
        var isChecked = !$checkbox.prop('checked');
        
        $checkbox.prop('checked', isChecked);
        $card.toggleClass('selected', isChecked);
        
        var $container = $card.closest('.target-picker-container');
        updateTabCountAndChips($container);
    });

    // Instant Filter/Search per Tab
    $(document).on('keyup', '.target-filter-input', function() {
        var query = $(this).val().toLowerCase().trim();
        var $grid = $(this).closest('.target-picker-container').find('.target-card-grid');
        
        $grid.find('.target-item-card').each(function() {
            var name = $(this).data('name') || '';
            if (!query || name.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Select All visible
    $(document).on('click', '.select-all-btn', function() {
        var $container = $(this).closest('.target-picker-container');
        $container.find('.target-item-card:visible').each(function() {
            $(this).addClass('selected');
            $(this).find('.target-checkbox').prop('checked', true);
        });
        updateTabCountAndChips($container);
    });

    // Deselect All
    $(document).on('click', '.deselect-all-btn', function() {
        var $container = $(this).closest('.target-picker-container');
        $container.find('.target-item-card').each(function() {
            $(this).removeClass('selected');
            $(this).find('.target-checkbox').prop('checked', false);
        });
        updateTabCountAndChips($container);
    });

    // District Quick Search Filter
    $('#districtFilterInput').on('keyup', function() {
        var query = $(this).val().toLowerCase().trim();
        $('.district-row').each(function() {
            var name = $(this).data('name') || '';
            if (!query || name.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        if (query) {
            $('.division-accordion-item .collapse').addClass('show');
        }
    });

    // Single District Instant Save via AJAX
    $('.btn-save-district').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var distId = $btn.data('id');
        var val = $('#dist_input_' + distId).val();

        $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i>');

        $.ajax({
            url: '{{ route("admin.delivery.settings.district-rate") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                district_id: distId,
                charge: val
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fe-check text-success"></i>');
                if (typeof toastr !== 'undefined') {
                    toastr.success(res.message || 'সংরক্ষিত হয়েছে');
                }
                setTimeout(function() {
                    $btn.html('<i class="fe-save"></i>');
                }, 2000);
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fe-save text-danger"></i>');
                if (typeof toastr !== 'undefined') {
                    toastr.error('আপডেট করতে সমস্যা হয়েছে');
                }
            }
        });
    });
});
</script>
@endsectione data-placeholder="প্রোডাক্ট নির্বাচন করুন...">
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">
                                            {{ $p->name }} {{ $p->product_code ? '('.$p->product_code.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">সিলেক্ট করা নির্দিষ্ট পণ্যগুলোতে এই ডেলিভারি চার্জ কার্যকর হবে।</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">স্ট্যাটাস</label>
                                <select name="status" class="form-select">
                                    <option value="1" selected>সক্রিয় (Active)</option>
                                    <option value="0">নিষ্ক্রিয় (Inactive)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">বাতিল</button>
                        <button type="submit" class="btn btn-danger btn-sm">সংরক্ষণ করুন</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
function selectDeliveryMode(mode) {
    // Check the radio input
    $('#radio_' + mode).prop('checked', true);

    // Update active class on cards
    $('.delivery-mode-option').removeClass('active');
    $('#mode_card_' + (mode === 'free_delivery' ? 'free' : (mode === 'flat_rate' ? 'flat' : (mode === 'weight_based' ? 'weight' : 'area')))).addClass('active');

    // Slide toggle sub bodies
    if (mode === 'flat_rate') {
        $('#sub_body_flat_rate').slideDown(200);
        $('#sub_body_weight_based, #sub_body_area_based').slideUp(200);
    } else if (mode === 'weight_based') {
        $('#sub_body_weight_based').slideDown(200);
        $('#sub_body_flat_rate, #sub_body_area_based').slideUp(200);
    } else if (mode === 'area_based') {
        $('#sub_body_area_based').slideDown(200);
        $('#sub_body_flat_rate, #sub_body_weight_based').slideUp(200);
    } else {
        // free_delivery
        $('#sub_body_flat_rate, #sub_body_weight_based, #sub_body_area_based').slideUp(200);
    }
}

$(document).ready(function() {
    // Initialize Select2 in Modals when opened
    $('.modal').on('shown.bs.modal', function () {
        $(this).find('.select2-modal').select2({
            dropdownParent: $(this),
            width: '100%',
            allowClear: true
        });
    });

    // District Quick Search Filter
    $('#districtFilterInput').on('keyup', function() {
        var query = $(this).val().toLowerCase().trim();
        $('.district-row').each(function() {
            var name = $(this).data('name') || '';
            if (!query || name.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        if (query) {
            // Auto expand accordions if searching
            $('.division-accordion-item .collapse').addClass('show');
        }
    });

    // Single District Instant Save via AJAX
    $('.btn-save-district').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var distId = $btn.data('id');
        var val = $('#dist_input_' + distId).val();

        $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i>');

        $.ajax({
            url: '{{ route("admin.delivery.settings.district-rate") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                district_id: distId,
                charge: val
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fe-check text-success"></i>');
                if (typeof toastr !== 'undefined') {
                    toastr.success(res.message || 'সংরক্ষিত হয়েছে');
                }
                setTimeout(function() {
                    $btn.html('<i class="fe-save"></i>');
                }, 2000);
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fe-save text-danger"></i>');
                if (typeof toastr !== 'undefined') {
                    toastr.error('আপডেট করতে সমস্যা হয়েছে');
                }
            }
        });
    });
});
</script>
@endsection
