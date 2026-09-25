@extends('backEnd.layouts.master')
@section('title', 'ডেলিভারি চার্জ সেটিংস')

@section('css')
<style>
    .delivery-settings-page {
        padding: 10px 0 30px;
    }
    .del-mode-card {
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.25s ease;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .del-mode-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.12);
        transform: translateY(-2px);
    }
    .del-mode-card.active {
        border-color: #2563eb;
        background: #f0f7ff;
        box-shadow: 0 8px 20px -3px rgba(37, 99, 235, 0.18);
    }
    .del-mode-radio {
        position: absolute;
        top: 14px;
        right: 14px;
        accent-color: #2563eb;
        width: 18px;
        height: 18px;
    }
    .del-mode-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 12px;
    }
    .del-mode-icon.area { background: #e0f2fe; color: #0284c7; }
    .del-mode-icon.flat { background: #fef3c7; color: #d97706; }
    .del-mode-icon.free { background: #dcfce7; color: #16a34a; }
    .del-mode-icon.weight { background: #f3e8ff; color: #9333ea; }

    .del-mode-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }
    .del-mode-desc {
        font-size: 12.5px;
        color: #64748b;
        line-height: 1.4;
        margin-bottom: 0;
    }

    .config-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .config-header {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .config-header h5 {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .config-body {
        padding: 20px;
    }

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
</style>
@endsection

@section('content')
<div class="container-fluid delivery-settings-page">
    
    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1 text-dark fw-bold">
                        <i class="fe-truck text-primary me-2"></i>ডেলিভারি চার্জ সেটিংস ও ম্যানেজমেন্ট
                    </h4>
                    <p class="text-muted mb-0 small">
                        সারা দেশের জন্য এরিয়াভিত্তিক (বিভাগ ও জেলা ওভাররাইড), ফিক্সড ফ্ল্যাট রেট, ফ্রি ডেলিভারি কিংবা ওজনভিত্তিক ডেলিভারি চার্জ নির্ধারণ করুন।
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.delivery.divisions.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fe-map-pin me-1"></i> ডেলিভারি লোকেশন
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Form for Active Delivery System Mode --}}
    <form action="{{ route('admin.delivery.settings.update') }}" method="POST">
        @csrf

        {{-- 1. Mode Selection Card Grid --}}
        <div class="config-card">
            <div class="config-header">
                <h5>
                    <i class="fe-settings text-primary"></i>
                    <span>১. সক্রিয় ডেলিভারি চার্জ সিস্টেম নির্বাচন করুন (Select Active Mode)</span>
                </h5>
                <span class="badge bg-primary rounded-pill px-3 py-1.5">সিস্টেম মোড</span>
            </div>
            <div class="config-body">
                <div class="row g-3">
                    
                    {{-- Mode 1: Area Based --}}
                    <div class="col-md-3">
                        <label class="del-mode-card {{ $setting->active_method === 'area_based' ? 'active' : '' }}" for="mode_area_based">
                            <input type="radio" name="active_method" id="mode_area_based" value="area_based" class="del-mode-radio" {{ $setting->active_method === 'area_based' ? 'checked' : '' }}>
                            <div>
                                <div class="del-mode-icon area">
                                    <i class="fe-map-pin"></i>
                                </div>
                                <div class="del-mode-title">এরিয়া ভিত্তিক (Area Based)</div>
                                <p class="del-mode-desc">বিভাগ ও জেলা অনুযায়ী আলাদা নির্দিষ্ট ডেলিভারি চার্জ স্বয়ংক্রিয়ভাবে কার্যকর হবে।</p>
                            </div>
                        </label>
                    </div>

                    {{-- Mode 2: Flat Rate --}}
                    <div class="col-md-3">
                        <label class="del-mode-card {{ $setting->active_method === 'flat_rate' ? 'active' : '' }}" for="mode_flat_rate">
                            <input type="radio" name="active_method" id="mode_flat_rate" value="flat_rate" class="del-mode-radio" {{ $setting->active_method === 'flat_rate' ? 'checked' : '' }}>
                            <div>
                                <div class="del-mode-icon flat">
                                    <i class="fe-layers"></i>
                                </div>
                                <div class="del-mode-title">ফ্ল্যাট রেট (Flat Rate)</div>
                                <p class="del-mode-desc">সারা দেশের সকল জেলার অর্ডারে একটি ফিক্সড নির্দিষ্ট চার্জ প্রযোজ্য হবে।</p>
                            </div>
                        </label>
                    </div>

                    {{-- Mode 3: Free Delivery --}}
                    <div class="col-md-3">
                        <label class="del-mode-card {{ $setting->active_method === 'free_delivery' ? 'active' : '' }}" for="mode_free_delivery">
                            <input type="radio" name="active_method" id="mode_free_delivery" value="free_delivery" class="del-mode-radio" {{ $setting->active_method === 'free_delivery' ? 'checked' : '' }}>
                            <div>
                                <div class="del-mode-icon free">
                                    <i class="fe-gift"></i>
                                </div>
                                <div class="del-mode-title">ফ্রি ডেলিভারি (Free Delivery)</div>
                                <p class="del-mode-desc">সারা দেশে সকল অর্ডারে সম্পূর্ণ ফ্রি ডেলিভারি (৳০) কার্যকর হবে।</p>
                            </div>
                        </label>
                    </div>

                    {{-- Mode 4: Weight Based --}}
                    <div class="col-md-3">
                        <label class="del-mode-card {{ $setting->active_method === 'weight_based' ? 'active' : '' }}" for="mode_weight_based">
                            <input type="radio" name="active_method" id="mode_weight_based" value="weight_based" class="del-mode-radio" {{ $setting->active_method === 'weight_based' ? 'checked' : '' }}>
                            <div>
                                <div class="del-mode-icon weight">
                                    <i class="fe-shield"></i>
                                </div>
                                <div class="del-mode-title">ওজন ভিত্তিক (Weight Based)</div>
                                <p class="del-mode-desc">কার্টের মোট ওজনের (কেজি) ওপর ভিত্তি করে ডেলিভারি চার্জ স্বয়ংক্রিয়ভাবে গণনা হবে।</p>
                            </div>
                        </label>
                    </div>

                </div>
            </div>
        </div>

        {{-- 2. Global Rate Parameters Card --}}
        <div class="config-card">
            <div class="config-header">
                <h5>
                    <i class="fe-sliders text-primary"></i>
                    <span>২. রেট প্যারামিটার কনফিগারেশন (Rate Parameters)</span>
                </h5>
            </div>
            <div class="config-body">
                <div class="row g-3">
                    
                    {{-- Flat Rate Input --}}
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <label class="form-label fw-bold text-dark">
                                <i class="fe-layers text-warning me-1"></i>ফ্ল্যাট রেট চার্জ (Flat Rate)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" step="0.01" min="0" name="flat_rate_amount" class="form-control fw-bold" value="{{ old('flat_rate_amount', $setting->flat_rate_amount) }}">
                            </div>
                            <small class="text-muted d-block mt-1">ফ্ল্যাট রেট একটিভ থাকলে সারা দেশে এই চার্জ প্রযোজ্য হবে।</small>
                        </div>
                    </div>

                    {{-- Default Area Fallback Charge --}}
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <label class="form-label fw-bold text-dark">
                                <i class="fe-map-pin text-info me-1"></i>ডিফল্ট এরিয়া চার্জ (Default Rate)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" step="0.01" min="0" name="default_area_charge" class="form-control fw-bold" value="{{ old('default_area_charge', $setting->default_area_charge ?? 100.00) }}">
                            </div>
                            <small class="text-muted d-block mt-1">বিভাগ বা জেলায় আলাদা চার্জ উল্লেখ না থাকলে এটি কার্যকর হবে।</small>
                        </div>
                    </div>

                    {{-- Weight Based Inputs --}}
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <label class="form-label fw-bold text-dark">
                                <i class="fe-shield text-purple me-1"></i>ওজনভিত্তিক ডেলিভারি প্যারামিটার (Weight Rules)
                            </label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <small class="fw-semibold">বেস ওজন (Base KG)</small>
                                    <div class="input-group input-group-sm mt-1">
                                        <input type="number" step="0.1" min="0.1" name="weight_base_kg" class="form-control fw-bold" value="{{ old('weight_base_kg', $setting->weight_base_kg) }}">
                                        <span class="input-group-text">KG</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <small class="fw-semibold">বেস ওজনের চার্জ</small>
                                    <div class="input-group input-group-sm mt-1">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" step="0.01" min="0" name="weight_base_cost" class="form-control fw-bold" value="{{ old('weight_base_cost', $setting->weight_base_cost) }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <small class="fw-semibold">অতিরিক্ত প্রতি কেজি</small>
                                    <div class="input-group input-group-sm mt-1">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" step="0.01" min="0" name="weight_extra_per_kg" class="form-control fw-bold" value="{{ old('weight_extra_per_kg', $setting->weight_extra_per_kg) }}">
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">১ম {{ $setting->weight_base_kg }} কেজির জন্য ৳{{ $setting->weight_base_cost }}, এরপর প্রতি অতিরিক্ত কেজির জন্য ৳{{ $setting->weight_extra_per_kg }} যোগ হবে।</small>
                        </div>
                    </div>

                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fe-check-circle me-1"></i> সেটিংস সংরক্ষণ করুন (Save Settings)
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- 3. Area Based Division & District Rates Management --}}
    <div class="config-card">
        <div class="config-header">
            <div>
                <h5>
                    <i class="fe-map text-primary"></i>
                    <span>৩. এরিয়া ভিত্তিক রেট কনফিগারেশন (Division & District Overrides)</span>
                </h5>
                <small class="text-muted">
                    বিভাগে যে রেট দেবেন, ওই বিভাগের সকল জেলায় সেটি স্বয়ংক্রিয়ভাবে প্রযোজ্য হবে। কোনো নির্দিষ্ট জেলায় ভিন্ন চার্জ চাইলে জেলার ঘরে লিখে দিন (যেমন: ঢাকা বিভাগ ১০০, কিন্তু গাজীপুর ৫০)।
                </small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="districtFilterInput" class="form-control form-control-sm rounded-pill" placeholder="জেলা সার্চ করুন..." style="width: 200px;">
            </div>
        </div>
        <div class="config-body p-0">
            
            <form action="{{ route('admin.delivery.settings.area-rates') }}" method="POST" id="areaRatesForm">
                @csrf
                <div class="p-3">
                    @foreach($divisions as $div)
                    <div class="division-accordion-item" data-div-id="{{ $div->id }}">
                        <div class="division-accordion-header" data-bs-toggle="collapse" data-bs-target="#collapse_div_{{ $div->id }}">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-soft-primary text-primary fs-6 px-2.5 py-1">
                                    <i class="fe-map-pin me-1"></i>{{ $div->name }} বিভাগ
                                </span>
                                <span class="text-muted small">({{ $div->districts->count() }}টি জেলা)</span>
                            </div>
                            <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation();">
                                <span class="fw-semibold small text-dark">বিভাগীয় ডিফল্ট চার্জ:</span>
                                <div class="input-group input-group-sm" style="width: 140px;">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0" name="divisions[{{ $div->id }}][charge]" class="form-control charge-input" value="{{ (float) ($div->delivery_charge ?? 0) }}" placeholder="0.00">
                                </div>
                                <button type="button" class="btn btn-sm btn-light border ms-2" data-bs-toggle="collapse" data-bs-target="#collapse_div_{{ $div->id }}">
                                    <i class="fe-chevron-down"></i>
                                </button>
                            </div>
                        </div>

                        <div class="collapse show" id="collapse_div_{{ $div->id }}">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered district-table mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>জেলার নাম (District)</th>
                                            <th>বিভাগ (Division)</th>
                                            <th style="width: 240px;">নির্দিষ্ট জেলা ওভাররাইড চার্জ (District Override)</th>
                                            <th style="width: 140px;">কার্যকর রেট (Active)</th>
                                            <th style="width: 90px;" class="text-center">অ্যাকশন</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($div->districts as $idx => $dist)
                                        @php
                                            $distCharge = (float) ($dist->delivery_charge ?? 0);
                                            $divCharge  = (float) ($div->delivery_charge ?? 0);
                                            $effectiveCharge = $distCharge > 0 ? $distCharge : ($divCharge > 0 ? $divCharge : (float) ($setting->default_area_charge ?? 100.00));
                                        @endphp
                                        <tr class="district-row" data-name="{{ strtolower($dist->name) }}">
                                            <td>{{ $idx + 1 }}</td>
                                            <td>
                                                <strong>{{ $dist->name }}</strong>
                                            </td>
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

                    <div class="text-end mt-3 p-2 bg-light rounded-3 d-flex align-items-center justify-content-between">
                        <span class="text-muted small">সকল বিভাগের ও জেলার পরিবর্তন একসাথে সেভ করতে ডানপাশের বাটনে ক্লিক করুন।</span>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                            <i class="fe-check me-1"></i> সকল এরিয়া রেট সংরক্ষণ করুন (Save All Area Rates)
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Mode card radio toggle
    $('.del-mode-card').on('click', function() {
        var radio = $(this).find('input[type="radio"]');
        if (radio.length) {
            radio.prop('checked', true);
            $('.del-mode-card').removeClass('active');
            $(this).addClass('active');
        }
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
