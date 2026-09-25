@php
    $prefix = $prefix ?? 'checkout';
    $selectedDivisionId = $selectedDivisionId ?? old('division_id', Auth::guard('customer')->user()->division_id ?? null);
    $selectedDistrictId = $selectedDistrictId ?? old('district_id', Auth::guard('customer')->user()->district_id ?? null);
    $selectedUpazilaId  = $selectedUpazilaId ?? old('upazila_id', Auth::guard('customer')->user()->upazila_id ?? null);
    $deliveryDivisions  = $divisions ?? \App\Models\DeliveryDivision::active()->ordered()->get();
@endphp

{{-- ======================================================================
     3-IN-1 DELIVERY LOCATION PICKER (DIVISION > DISTRICT > THANA)
     ====================================================================== --}}
<div class="delivery-area-field-wrapper mb-3" id="{{ $prefix }}_delivery_area_wrapper">
    <label class="form-label-custom fw-semibold mb-1" for="{{ $prefix }}_delivery_area_trigger">
        ডেলিভারি এরিয়া (বিভাগ, জেলা ও থানা) <span class="text-danger">*</span>
    </label>
    <div class="delivery-area-picker-trigger" id="{{ $prefix }}_delivery_area_trigger" role="button" tabindex="0" title="ক্লিক করে বিভাগ, জেলা ও থানা সিলেক্ট করুন">
        <div class="delivery-area-trigger-content">
            <div class="delivery-area-text-wrap">
                <span class="delivery-area-pin-icon"><i class="fas fa-map-marker-alt"></i></span>
                <span class="delivery-area-label" id="{{ $prefix }}_delivery_area_label">
                    ডেলিভারি এরিয়া নির্বাচন করুন (বিভাগ > জেলা > থানা)
                </span>
            </div>
            <div class="delivery-area-action-wrap">
                <span class="delivery-area-badge d-none" id="{{ $prefix }}_delivery_area_badge">
                    <i class="fas fa-check-circle text-success me-1"></i>পরিবর্তন <i class="fas fa-pencil-alt ms-1 small"></i>
                </span>
                <span class="delivery-area-chevron" id="{{ $prefix }}_delivery_area_chevron">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </div>
        </div>
    </div>

    {{-- Hidden form inputs submitted with checkout / campaign form --}}
    <input type="hidden" name="division_id" id="{{ $prefix }}_division_id" value="{{ $selectedDivisionId }}">
    <input type="hidden" name="district_id" id="{{ $prefix }}_district_id" value="{{ $selectedDistrictId }}">
    <input type="hidden" name="upazila_id" id="{{ $prefix }}_upazila_id" value="{{ $selectedUpazilaId }}">
</div>

{{-- MODAL CONTAINER --}}
<div id="{{ $prefix }}_delivery_location_modal" class="delivery-location-modal-container" style="display:none;" aria-hidden="true" role="dialog">
    <div class="delivery-location-backdrop" id="{{ $prefix }}_modal_backdrop"></div>
    <div class="delivery-location-dialog">
        {{-- Header --}}
        <div class="delivery-location-header">
            <div>
                <h5 class="delivery-location-title mb-0">
                    <i class="fas fa-map-marked-alt text-primary me-2"></i>ডেলিভারি এরিয়া নির্বাচন করুন
                </h5>
                <p class="delivery-location-subtitle mb-0">সহজ ৩টি ধাপে আপনার এলাকা নির্ধারণ করুন</p>
            </div>
            <button type="button" class="delivery-location-close-btn" id="{{ $prefix }}_modal_close_btn" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Stepper Progress Bar --}}
        <div class="delivery-location-stepper">
            <div class="delivery-step-item active" data-step="1" id="{{ $prefix }}_step_nav_1">
                <span class="step-num">১</span>
                <span class="step-label" id="{{ $prefix }}_step_label_1">বিভাগ</span>
            </div>
            <div class="step-arrow"><i class="fas fa-chevron-right"></i></div>
            <div class="delivery-step-item disabled" data-step="2" id="{{ $prefix }}_step_nav_2">
                <span class="step-num">২</span>
                <span class="step-label" id="{{ $prefix }}_step_label_2">জেলা</span>
            </div>
            <div class="step-arrow"><i class="fas fa-chevron-right"></i></div>
            <div class="delivery-step-item disabled" data-step="3" id="{{ $prefix }}_step_nav_3">
                <span class="step-num">৩</span>
                <span class="step-label" id="{{ $prefix }}_step_label_3">থানা / উপজেলা</span>
            </div>
        </div>

        {{-- Search Input --}}
        <div class="delivery-location-search-wrap">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="delivery-location-search-input" id="{{ $prefix }}_location_search" placeholder="সার্চ করুন (যেমন: ঢাকা, মিরপুর, কুমিল্লা)..." autocomplete="off">
            <button type="button" class="delivery-search-clear d-none" id="{{ $prefix }}_search_clear"><i class="fas fa-times-circle"></i></button>
        </div>

        {{-- Selected Breadcrumb pill --}}
        <div class="delivery-current-path-bar d-none" id="{{ $prefix }}_current_path_bar">
            <span class="path-title">নির্বাচিত:</span>
            <span class="path-content" id="{{ $prefix }}_current_path_text"></span>
            <button type="button" class="path-back-btn" id="{{ $prefix }}_step_back_btn">
                <i class="fas fa-arrow-left me-1"></i>আগের ধাপ
            </button>
        </div>

        {{-- Content Body with 3 Steps --}}
        <div class="delivery-location-body">
            {{-- Loading spinner --}}
            <div class="delivery-location-loading d-none" id="{{ $prefix }}_location_loading">
                <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                <span class="ms-2">তথ্য লোড হচ্ছে...</span>
            </div>

            {{-- STEP 1: DIVISIONS --}}
            <div class="delivery-step-pane active" id="{{ $prefix }}_pane_step_1">
                <div class="delivery-pane-heading">বিভাগ বেছে নিন:</div>
                <div class="delivery-grid-list" id="{{ $prefix }}_divisions_list">
                    @foreach($deliveryDivisions as $div)
                    <div class="delivery-option-item" data-id="{{ $div->id }}" data-name="{{ $div->name }}">
                        <div class="option-icon"><i class="fas fa-landmark"></i></div>
                        <div class="option-name">{{ $div->name }}</div>
                        <div class="option-arrow"><i class="fas fa-chevron-right"></i></div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- STEP 2: DISTRICTS --}}
            <div class="delivery-step-pane" id="{{ $prefix }}_pane_step_2" style="display:none;">
                <div class="delivery-pane-heading">জেলা বেছে নিন:</div>
                <div class="delivery-grid-list" id="{{ $prefix }}_districts_list"></div>
            </div>

            {{-- STEP 3: UPAZILAS / THANAS --}}
            <div class="delivery-step-pane" id="{{ $prefix }}_pane_step_3" style="display:none;">
                <div class="delivery-pane-heading">থানা বা উপজেলা বেছে নিন:</div>
                <div class="delivery-grid-list" id="{{ $prefix }}_upazilas_list"></div>
            </div>

            {{-- No results message --}}
            <div class="delivery-no-results d-none" id="{{ $prefix }}_no_results">
                <i class="fas fa-search me-1"></i> কোনো ফলাফল পাওয়া যায়নি
            </div>
        </div>
    </div>
</div>

{{-- STYLES --}}
<style>
/* Trigger box */
.delivery-area-picker-trigger {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    padding: 11px 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}
.delivery-area-picker-trigger:hover,
.delivery-area-picker-trigger:focus {
    border-color: #0f3460;
    box-shadow: 0 0 0 3px rgba(15, 52, 96, 0.1);
    outline: none;
}
.delivery-area-picker-trigger.is-invalid {
    border-color: #dc2626 !important;
    animation: triggerShake 0.4s ease;
}
@keyframes triggerShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
}
.delivery-area-trigger-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.delivery-area-text-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    flex: 1;
}
.delivery-area-pin-icon {
    color: #e94560;
    font-size: 17px;
    flex-shrink: 0;
}
.delivery-area-label {
    font-size: 14.5px;
    font-weight: 500;
    color: #475569;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.delivery-area-picker-trigger.has-value .delivery-area-label {
    color: #0f172a !important;
    font-weight: 600;
}
.delivery-area-action-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.delivery-area-badge {
    background: #ecfdf5;
    color: #059669;
    font-size: 12px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
    border: 1px solid #a7f3d0;
}
.delivery-area-badge .badge-change {
    color: #0f3460;
    text-decoration: underline;
}
.delivery-area-chevron {
    color: #94a3b8;
    font-size: 13px;
}

/* Modal Dialog */
.delivery-location-modal-container {
    position: fixed;
    inset: 0;
    z-index: 100050;
    display: flex;
    align-items: center;
    justify-content: center;
}
.delivery-location-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.delivery-location-dialog {
    position: relative;
    background: #ffffff;
    width: 94%;
    max-width: 540px;
    max-height: 85vh;
    border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 2;
    animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.94); }
    to { opacity: 1; transform: scale(1); }
}

/* Header */
.delivery-location-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    background: #f8fafc;
}
.delivery-location-title {
    font-size: 16.5px;
    font-weight: 700;
    color: #0f172a;
}
.delivery-location-subtitle {
    font-size: 12px;
    color: #64748b;
    margin-top: 2px;
}
.delivery-location-close-btn {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.delivery-location-close-btn:hover {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fca5a5;
}

/* Stepper */
.delivery-location-stepper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
}
.delivery-step-item {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 6px;
    transition: all 0.2s;
}
.delivery-step-item .step-num {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11.5px;
    font-weight: 700;
    background: #e2e8f0;
    color: #64748b;
}
.delivery-step-item .step-label {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
}
.delivery-step-item.active {
    background: #eff6ff;
}
.delivery-step-item.active .step-num {
    background: #0f3460;
    color: #ffffff;
}
.delivery-step-item.active .step-label {
    color: #0f3460;
    font-weight: 700;
}
.delivery-step-item.completed .step-num {
    background: #10b981;
    color: #ffffff;
}
.delivery-step-item.completed .step-label {
    color: #10b981;
}
.delivery-step-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.step-arrow {
    color: #cbd5e1;
    font-size: 11px;
}

/* Search bar */
.delivery-location-search-wrap {
    position: relative;
    padding: 10px 18px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.delivery-location-search-wrap .search-icon {
    position: absolute;
    left: 30px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 13.5px;
}
.delivery-location-search-input {
    width: 100%;
    height: 38px;
    padding: 0 35px 0 34px;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13.5px;
    color: #0f172a;
    background: #ffffff;
    transition: border-color 0.2s;
}
.delivery-location-search-input:focus {
    outline: none;
    border-color: #0f3460;
    box-shadow: 0 0 0 3px rgba(15, 52, 96, 0.1);
}
.delivery-search-clear {
    position: absolute;
    right: 28px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 14px;
    cursor: pointer;
}

/* Breadcrumb bar */
.delivery-current-path-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 18px;
    background: #f0fdf4;
    border-bottom: 1px solid #bbf7d0;
    font-size: 12.5px;
}
.delivery-current-path-bar .path-title {
    color: #166534;
    font-weight: 600;
    margin-right: 4px;
}
.delivery-current-path-bar .path-content {
    color: #15803d;
    font-weight: 700;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
}
.path-back-btn {
    background: #ffffff;
    border: 1px solid #86efac;
    color: #15803d;
    font-size: 11.5px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 4px;
    cursor: pointer;
    flex-shrink: 0;
    margin-left: 8px;
}
.path-back-btn:hover {
    background: #dcfce7;
}

/* Body / Lists */
.delivery-location-body {
    padding: 14px 18px;
    overflow-y: auto;
    flex: 1;
    min-height: 260px;
    max-height: 52vh;
}
.delivery-pane-heading {
    font-size: 13px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 10px;
}
.delivery-grid-list {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}
.delivery-option-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    border: 1.5px solid #e2e8f0;
    background: #ffffff;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
}
.delivery-option-item:hover {
    border-color: #0f3460;
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}
.delivery-option-item .option-icon {
    color: #0f3460;
    font-size: 13px;
    margin-right: 8px;
    opacity: 0.8;
}
.delivery-option-item .option-name {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    flex: 1;
    text-align: left;
}
.delivery-option-item .option-charge {
    font-size: 11px;
    color: #059669;
    background: #ecfdf5;
    padding: 1px 6px;
    border-radius: 4px;
    margin-right: 6px;
    font-weight: 600;
}
.delivery-option-item .option-arrow {
    color: #94a3b8;
    font-size: 11px;
}
.delivery-option-item.selected {
    border-color: #0f3460;
    background: #eff6ff;
}
.delivery-option-item.selected .option-name {
    color: #0f3460;
    font-weight: 700;
}

/* Loading & No results */
.delivery-location-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px 0;
    color: #0f3460;
    font-size: 14px;
    font-weight: 600;
}
.delivery-no-results {
    text-align: center;
    padding: 30px 0;
    color: #94a3b8;
    font-size: 14px;
}

/* Mobile responsive (Bottom Sheet) */
@media (max-width: 768px) {
    .delivery-location-dialog {
        width: 100%;
        max-width: 100%;
        max-height: 88vh;
        border-radius: 20px 20px 0 0;
        position: fixed;
        bottom: 0;
        animation: modalSlideUp 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes modalSlideUp {
        from { transform: translateY(100%); }
        to { transform: translateY(0); }
    }
    .delivery-grid-list {
        grid-template-columns: 1fr;
    }
    .delivery-location-body {
        max-height: 58vh;
    }
}
</style>

{{-- SCRIPT COMPONENT --}}
<script>
(function ($) {
    $(function () {
        var prefix = '{{ $prefix }}';
        var $trigger = $('#' + prefix + '_delivery_area_trigger');
        var $modal = $('#' + prefix + '_delivery_location_modal');
        var $backdrop = $('#' + prefix + '_modal_backdrop');
        var $closeBtn = $('#' + prefix + '_modal_close_btn');
        var $searchInput = $('#' + prefix + '_location_search');
        var $searchClear = $('#' + prefix + '_search_clear');
        var $noResults = $('#' + prefix + '_no_results');
        var $loading = $('#' + prefix + '_location_loading');

        var $inputDiv = $('#' + prefix + '_division_id');
        var $inputDist = $('#' + prefix + '_district_id');
        var $inputUpa = $('#' + prefix + '_upazila_id');
        var $displayLabel = $('#' + prefix + '_delivery_area_label');
        var $badge = $('#' + prefix + '_delivery_area_badge');
        var $chevron = $('#' + prefix + '_delivery_area_chevron');

        var $stepNav1 = $('#' + prefix + '_step_nav_1');
        var $stepNav2 = $('#' + prefix + '_step_nav_2');
        var $stepNav3 = $('#' + prefix + '_step_nav_3');

        var $paneStep1 = $('#' + prefix + '_pane_step_1');
        var $paneStep2 = $('#' + prefix + '_pane_step_2');
        var $paneStep3 = $('#' + prefix + '_pane_step_3');

        var $districtsList = $('#' + prefix + '_districts_list');
        var $upazilasList = $('#' + prefix + '_upazilas_list');

        var $pathBar = $('#' + prefix + '_current_path_bar');
        var $pathText = $('#' + prefix + '_current_path_text');
        var $stepBackBtn = $('#' + prefix + '_step_back_btn');

        var currentStep = 1;
        var selectedDiv = { id: null, name: '' };
        var selectedDist = { id: null, name: '', charge: 0 };
        var selectedUpa = { id: null, name: '' };

        var districtCache = {};
        var upazilaCache = {};

        // Initial setup from old input or user profile if present
        var initDivId = $inputDiv.val();
        var initDistId = $inputDist.val();
        var initUpaId = $inputUpa.val();

        function openModal() {
            $modal.show();
            $('body').addClass('modal-open').css('overflow', 'hidden');
            $trigger.removeClass('is-invalid');
            if (currentStep === 1) {
                setTimeout(function () { $searchInput.focus(); }, 150);
            }
        }

        function closeModal() {
            $modal.hide();
            $('body').removeClass('modal-open').css('overflow', '');
            $searchInput.val('');
            $searchClear.addClass('d-none');
            filterItems('');
        }

        $trigger.on('click', openModal);
        $backdrop.on('click', closeModal);
        $closeBtn.on('click', closeModal);

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $modal.is(':visible')) {
                closeModal();
            }
        });

        function updateStepUI(step) {
            currentStep = step;
            $searchInput.val('');
            $searchClear.addClass('d-none');
            $noResults.addClass('d-none');

            // Nav stepper update
            $stepNav1.removeClass('active completed disabled');
            $stepNav2.removeClass('active completed disabled');
            $stepNav3.removeClass('active completed disabled');

            if (step === 1) {
                $stepNav1.addClass('active');
                $stepNav2.addClass('disabled');
                $stepNav3.addClass('disabled');
                $pathBar.addClass('d-none');
            } else if (step === 2) {
                $stepNav1.addClass('completed');
                $stepNav2.addClass('active');
                $stepNav3.addClass('disabled');
                $pathBar.removeClass('d-none');
                $pathText.text(selectedDiv.name);
            } else if (step === 3) {
                $stepNav1.addClass('completed');
                $stepNav2.addClass('completed');
                $stepNav3.addClass('active');
                $pathBar.removeClass('d-none');
                $pathText.text(selectedDiv.name + ' > ' + selectedDist.name);
            }

            // Panes update
            $paneStep1.hide();
            $paneStep2.hide();
            $paneStep3.hide();

            if (step === 1) $paneStep1.show();
            else if (step === 2) $paneStep2.show();
            else if (step === 3) $paneStep3.show();
        }

        // Stepper nav clicks
        $stepNav1.on('click', function () {
            if (!$stepNav1.hasClass('disabled')) updateStepUI(1);
        });
        $stepNav2.on('click', function () {
            if (!$stepNav2.hasClass('disabled') && selectedDiv.id) updateStepUI(2);
        });
        $stepNav3.on('click', function () {
            if (!$stepNav3.hasClass('disabled') && selectedDist.id) updateStepUI(3);
        });

        $stepBackBtn.on('click', function () {
            if (currentStep === 3) updateStepUI(2);
            else if (currentStep === 2) updateStepUI(1);
        });

        // 1. Division selection
        $(document).on('click', '#' + prefix + '_divisions_list .delivery-option-item', function () {
            var divId = $(this).data('id');
            var divName = $(this).data('name');
            selectedDiv = { id: divId, name: divName };
            $('#' + prefix + '_step_label_1').text(divName);

            $('#' + prefix + '_divisions_list .delivery-option-item').removeClass('selected');
            $(this).addClass('selected');

            loadDistricts(divId);
        });

        // 2. Load Districts
        function loadDistricts(divId) {
            updateStepUI(2);
            if (districtCache[divId]) {
                renderDistricts(districtCache[divId]);
                return;
            }

            $loading.removeClass('d-none');
            $districtsList.empty();

            $.get('{{ url("/ajax/delivery/districts") }}/' + divId, function (res) {
                $loading.addClass('d-none');
                var items = (res && res.data) ? res.data : [];
                districtCache[divId] = items;
                renderDistricts(items);
            }).fail(function () {
                $loading.addClass('d-none');
                $districtsList.html('<div class="text-danger py-3 text-center">জেলা লোড করতে ব্যর্থ হয়েছে। আবার চেষ্টা করুন।</div>');
            });
        }

        function renderDistricts(items) {
            if (!items.length) {
                $districtsList.html('<div class="text-muted py-3 text-center">এই বিভাগে কোনো জেলা পাওয়া যায়নি</div>');
                return;
            }
            var html = '';
            items.forEach(function (dist) {
                var chargeBadge = (dist.delivery_charge > 0) ? '<span class="option-charge">৳' + dist.delivery_charge + '</span>' : '';
                html += '<div class="delivery-option-item" data-id="' + dist.id + '" data-name="' + dist.name + '" data-charge="' + (dist.delivery_charge || 0) + '">'
                      + '<div class="option-icon"><i class="fas fa-city"></i></div>'
                      + '<div class="option-name">' + dist.name + '</div>'
                      + chargeBadge
                      + '<div class="option-arrow"><i class="fas fa-chevron-right"></i></div>'
                      + '</div>';
            });
            $districtsList.html(html);
        }

        // District click
        $(document).on('click', '#' + prefix + '_districts_list .delivery-option-item', function () {
            var distId = $(this).data('id');
            var distName = $(this).data('name');
            var distCharge = $(this).data('charge');
            selectedDist = { id: distId, name: distName, charge: distCharge };
            $('#' + prefix + '_step_label_2').text(distName);

            $('#' + prefix + '_districts_list .delivery-option-item').removeClass('selected');
            $(this).addClass('selected');

            loadUpazilas(distId);
        });

        // 3. Load Upazilas
        function loadUpazilas(distId) {
            updateStepUI(3);
            if (upazilaCache[distId]) {
                renderUpazilas(upazilaCache[distId]);
                return;
            }

            $loading.removeClass('d-none');
            $upazilasList.empty();

            $.get('{{ url("/ajax/delivery/upazilas") }}/' + distId, function (res) {
                $loading.addClass('d-none');
                var items = (res && res.data) ? res.data : [];
                upazilaCache[distId] = items;
                renderUpazilas(items);
            }).fail(function () {
                $loading.addClass('d-none');
                $upazilasList.html('<div class="text-danger py-3 text-center">উপজেলা লোড করতে ব্যর্থ হয়েছে। আবার চেষ্টা করুন।</div>');
            });
        }

        function renderUpazilas(items) {
            if (!items.length) {
                $upazilasList.html('<div class="text-muted py-3 text-center">এই জেলায় কোনো উপজেলা পাওয়া যায়নি</div>');
                return;
            }
            var html = '';
            items.forEach(function (upa) {
                html += '<div class="delivery-option-item" data-id="' + upa.id + '" data-name="' + upa.name + '">'
                      + '<div class="option-icon"><i class="fas fa-map-pin"></i></div>'
                      + '<div class="option-name">' + upa.name + '</div>'
                      + '<div class="option-arrow"><i class="fas fa-check text-success"></i></div>'
                      + '</div>';
            });
            $upazilasList.html(html);
        }

        // Upazila click -> COMPLETE SELECTION!
        $(document).on('click', '#' + prefix + '_upazilas_list .delivery-option-item', function () {
            var upaId = $(this).data('id');
            var upaName = $(this).data('name');
            selectedUpa = { id: upaId, name: upaName };
            $('#' + prefix + '_step_label_3').text(upaName);

            applyCompletedSelection();
        });

        function applyCompletedSelection() {
            // Set form values
            $inputDiv.val(selectedDiv.id);
            $inputDist.val(selectedDist.id);
            $inputUpa.val(selectedUpa.id);

            // Display string: বিভাগ > জেলা > থানা
            var displayPath = selectedDiv.name + ' > ' + selectedDist.name + ' > ' + selectedUpa.name;
            $displayLabel.text(displayPath);
            $trigger.addClass('has-value').removeClass('is-invalid');
            $badge.removeClass('d-none');
            $chevron.addClass('d-none');

            // Sync with delivery area charge select (Inside Dhaka vs Outside Dhaka)
            syncAreaChargeWithDistrict(selectedDist.name);

            closeModal();

            // Trigger incomplete order save if function exists
            if (typeof saveIncompleteOrder === 'function') {
                saveIncompleteOrder();
            }
        }

        function syncAreaChargeWithDistrict(districtName) {
            var $areaSelect = $('#checkout_area, #area');
            if (!$areaSelect.length) return;

            var distLower = (districtName || '').toLowerCase().trim();
            var isDhaka = distLower.indexOf('ঢাকা') !== -1 || distLower.indexOf('dhaka') !== -1;

            var matchedVal = null;
            $areaSelect.find('option').each(function () {
                var txt = ($(this).text() || '').toLowerCase();
                var val = $(this).val();
                if (!val) return;

                if (isDhaka) {
                    if (txt.indexOf('ঢাকা') !== -1 && (txt.indexOf('ভিতরে') !== -1 || txt.indexOf('inside') !== -1 || txt.indexOf('সিটি') !== -1)) {
                        matchedVal = val;
                        return false;
                    }
                } else {
                    if (txt.indexOf('বাইরে') !== -1 || txt.indexOf('outside') !== -1 || (txt.indexOf('ঢাকা') === -1 && txt.indexOf('সমগ্র') !== -1)) {
                        matchedVal = val;
                        return false;
                    }
                }
            });

            if (matchedVal) {
                $areaSelect.val(matchedVal).trigger('change');
            }
        }

        // Search Filter
        function filterItems(query) {
            var q = (query || '').toLowerCase().trim();
            var $currentPane = null;
            if (currentStep === 1) $currentPane = $paneStep1;
            else if (currentStep === 2) $currentPane = $paneStep2;
            else if (currentStep === 3) $currentPane = $paneStep3;

            if (!$currentPane) return;

            var $items = $currentPane.find('.delivery-option-item');
            var matchedCount = 0;

            if (!q) {
                $items.show();
                $noResults.addClass('d-none');
                return;
            }

            $items.each(function () {
                var text = ($(this).data('name') || '').toString().toLowerCase();
                if (text.indexOf(q) !== -1) {
                    $(this).show();
                    matchedCount++;
                } else {
                    $(this).hide();
                }
            });

            if (matchedCount === 0) {
                $noResults.removeClass('d-none');
            } else {
                $noResults.addClass('d-none');
            }
        }

        $searchInput.on('input', function () {
            var val = $(this).val();
            $searchClear.toggleClass('d-none', !val);
            filterItems(val);
        });

        $searchClear.on('click', function () {
            $searchInput.val('').focus();
            $(this).addClass('d-none');
            filterItems('');
        });

        // Initialize label if existing values exist on load
        if (initDivId && initDistId) {
            $.get('{{ url("/ajax/delivery/districts") }}/' + initDivId, function (dRes) {
                var dItems = (dRes && dRes.data) ? dRes.data : [];
                districtCache[initDivId] = dItems;
                var foundDist = dItems.find(function(d) { return String(d.id) === String(initDistId); });
                var distName = foundDist ? foundDist.name : '';
                var divName = $('#' + prefix + '_divisions_list .delivery-option-item[data-id="' + initDivId + '"]').data('name') || '';

                if (initUpaId) {
                    $.get('{{ url("/ajax/delivery/upazilas") }}/' + initDistId, function (uRes) {
                        var uItems = (uRes && uRes.data) ? uRes.data : [];
                        upazilaCache[initDistId] = uItems;
                        var foundUpa = uItems.find(function(u) { return String(u.id) === String(initUpaId); });
                        var upaName = foundUpa ? foundUpa.name : '';
                        if (divName && distName && upaName) {
                            selectedDiv = { id: initDivId, name: divName };
                            selectedDist = { id: initDistId, name: distName };
                            selectedUpa = { id: initUpaId, name: upaName };
                            $displayLabel.text(divName + ' > ' + distName + ' > ' + upaName);
                            $trigger.addClass('has-value');
                            $badge.removeClass('d-none');
                            $chevron.addClass('d-none');
                        }
                    });
                } else if (divName && distName) {
                    $displayLabel.text(divName + ' > ' + distName);
                    $trigger.addClass('has-value');
                    $badge.removeClass('d-none');
                    $chevron.addClass('d-none');
                }
            });
        }
    });
})(jQuery);
</script>
