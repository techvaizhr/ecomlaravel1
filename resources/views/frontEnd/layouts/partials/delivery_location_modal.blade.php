@php
    $prefix = $prefix ?? 'checkout';
    $selectedDivisionId = $selectedDivisionId ?? old('division_id', Auth::guard('customer')->user()->division_id ?? null);
    $selectedDistrictId = $selectedDistrictId ?? old('district_id', Auth::guard('customer')->user()->district_id ?? null);
    $selectedUpazilaId  = $selectedUpazilaId ?? old('upazila_id', Auth::guard('customer')->user()->upazila_id ?? null);
    $deliveryDivisions  = $divisions ?? \App\Models\DeliveryDivision::active()->ordered()->get();

    // Server-side lookup of initial location label for instantaneous display
    $initLabel = null;
    if ($selectedDivisionId && $selectedDistrictId && $selectedUpazilaId) {
        $initLabel = \App\Support\DeliveryLocation::shippingLabel($selectedDivisionId, $selectedDistrictId, $selectedUpazilaId);
    } elseif ($selectedDivisionId && $selectedDistrictId) {
        $sDiv = \App\Models\DeliveryDivision::find($selectedDivisionId);
        $sDist = \App\Models\DeliveryDistrict::find($selectedDistrictId);
        if ($sDiv && $sDist) {
            $initLabel = $sDiv->name . ' > ' . $sDist->name;
        }
    }
@endphp

{{-- ======================================================================
     3-IN-1 DELIVERY LOCATION PICKER (DIVISION > DISTRICT > THANA)
     ====================================================================== --}}
<div class="delivery-area-field-wrapper modern-outline-group mb-3" id="{{ $prefix }}_delivery_area_wrapper">
    <label class="modern-outline-label" for="{{ $prefix }}_delivery_area_trigger">
        {{ $fieldLabel ?? 'ডেলিভারি এরিয়া' }} <span class="text-danger">*</span>
    </label>
    <div class="delivery-area-picker-trigger modern-outline-input {{ $initLabel ? 'has-value' : '' }}" id="{{ $prefix }}_delivery_area_trigger" role="button" tabindex="0" title="ক্লিক করে বিভাগ, জেলা ও থানা সিলেক্ট করুন">
        <div class="delivery-area-trigger-content">
            <div class="delivery-area-text-wrap">
                <span class="delivery-area-pin-icon"><i class="fas fa-map-marker-alt"></i></span>
                <span class="delivery-area-label {{ $initLabel ? '' : 'is-placeholder' }}" id="{{ $prefix }}_delivery_area_label">
                    {{ $initLabel ?: 'নির্বাচন করতে ক্লিক করুন (বিভাগ > জেলা > থানা)' }}
                </span>
            </div>
            <div class="delivery-area-action-wrap">
                <span class="delivery-area-badge {{ $initLabel ? '' : 'd-none' }}" id="{{ $prefix }}_delivery_area_badge">
                    <i class="fas fa-check-circle text-success me-1"></i>পরিবর্তন <i class="fas fa-pencil-alt ms-1 small"></i>
                </span>
                <span class="delivery-area-chevron {{ $initLabel ? 'd-none' : '' }}" id="{{ $prefix }}_delivery_area_chevron">
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
/* Outlined / Notched Border Label Form Styling */
.modern-outline-group {
    position: relative;
    margin-top: 14px;
    margin-bottom: 16px;
}
.modern-outline-group .modern-outline-label {
    position: absolute;
    top: -9px;
    left: 12px;
    background: #ffffff;
    padding: 0 6px;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    z-index: 2;
    pointer-events: none;
    line-height: 1.2;
    border-radius: 2px;
    margin-bottom: 0;
    white-space: nowrap;
}

/* Trigger box */
.delivery-area-picker-trigger {
    background: #ffffff;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    padding: 12px 14px;
    min-height: 48px;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
    box-sizing: border-box;
    display: flex;
    align-items: center;
}
.delivery-area-picker-trigger:hover,
.delivery-area-picker-trigger:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
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
    width: 100%;
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
    font-size: 16px;
    flex-shrink: 0;
}
.delivery-area-label {
    font-size: 14px;
    font-weight: 500;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.delivery-area-label.is-placeholder {
    color: #9ca3af !important;
    opacity: 0.55 !important;
    font-size: 13.5px !important;
    font-weight: 400 !important;
}
.delivery-area-picker-trigger.has-value .delivery-area-label {
    color: #111827 !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    opacity: 1 !important;
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

{{-- SCRIPT COMPONENT (100% Standalone Vanilla JS - No jQuery dependency) --}}
<script>
(function () {
    function initDeliveryLocationPicker() {
        var prefix = '{{ $prefix }}';
        var trigger = document.getElementById(prefix + '_delivery_area_trigger');
        var modal = document.getElementById(prefix + '_delivery_location_modal');
        var backdrop = document.getElementById(prefix + '_modal_backdrop');
        var closeBtn = document.getElementById(prefix + '_modal_close_btn');
        var searchInput = document.getElementById(prefix + '_location_search');
        var searchClear = document.getElementById(prefix + '_search_clear');
        var noResults = document.getElementById(prefix + '_no_results');
        var loading = document.getElementById(prefix + '_location_loading');

        var inputDiv = document.getElementById(prefix + '_division_id');
        var inputDist = document.getElementById(prefix + '_district_id');
        var inputUpa = document.getElementById(prefix + '_upazila_id');
        var displayLabel = document.getElementById(prefix + '_delivery_area_label');
        var badge = document.getElementById(prefix + '_delivery_area_badge');
        var chevron = document.getElementById(prefix + '_delivery_area_chevron');

        var stepNav1 = document.getElementById(prefix + '_step_nav_1');
        var stepNav2 = document.getElementById(prefix + '_step_nav_2');
        var stepNav3 = document.getElementById(prefix + '_step_nav_3');

        var stepLabel1 = document.getElementById(prefix + '_step_label_1');
        var stepLabel2 = document.getElementById(prefix + '_step_label_2');
        var stepLabel3 = document.getElementById(prefix + '_step_label_3');

        var paneStep1 = document.getElementById(prefix + '_pane_step_1');
        var paneStep2 = document.getElementById(prefix + '_pane_step_2');
        var paneStep3 = document.getElementById(prefix + '_pane_step_3');

        var divisionsList = document.getElementById(prefix + '_divisions_list');
        var districtsList = document.getElementById(prefix + '_districts_list');
        var upazilasList = document.getElementById(prefix + '_upazilas_list');

        var pathBar = document.getElementById(prefix + '_current_path_bar');
        var pathText = document.getElementById(prefix + '_current_path_text');
        var stepBackBtn = document.getElementById(prefix + '_step_back_btn');

        if (!trigger || !modal) return;

        var currentStep = 1;
        var selectedDiv = { id: null, name: '' };
        var selectedDist = { id: null, name: '', charge: 0 };
        var selectedUpa = { id: null, name: '' };

        var districtCache = {};
        var upazilaCache = {};

        var initDivId = inputDiv ? inputDiv.value : '';
        var initDistId = inputDist ? inputDist.value : '';
        var initUpaId = inputUpa ? inputUpa.value : '';

        function openModal() {
            modal.style.display = 'flex';
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';
            trigger.classList.remove('is-invalid');
            // Desktop-only auto-focus — mobile-এ keyboard auto-open হবে না
            if (searchInput && window.innerWidth >= 768) {
                setTimeout(function () { searchInput.focus(); }, 120);
            }
        }

        function closeModal() {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            if (searchInput) searchInput.value = '';
            if (searchClear) searchClear.classList.add('d-none');
            filterItems('');
        }

        trigger.addEventListener('click', openModal);
        if (backdrop) backdrop.addEventListener('click', closeModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.style.display !== 'none') {
                closeModal();
            }
        });

        function updateStepUI(step) {
            currentStep = step;
            if (searchInput) {
                searchInput.value = '';
                searchInput.placeholder = step === 1 ? 'বিভাগ সার্চ করুন...' : (step === 2 ? 'জেলা সার্চ করুন...' : 'থানা / উপজেলা সার্চ করুন...');
            }
            if (searchClear) searchClear.classList.add('d-none');
            if (noResults) noResults.classList.add('d-none');

            [stepNav1, stepNav2, stepNav3].forEach(function (el) {
                if (el) el.classList.remove('active', 'completed', 'disabled');
            });

            if (step === 1) {
                if (stepNav1) stepNav1.classList.add('active');
                if (stepNav2) stepNav2.classList.add('disabled');
                if (stepNav3) stepNav3.classList.add('disabled');
                if (pathBar) pathBar.classList.add('d-none');
            } else if (step === 2) {
                if (stepNav1) stepNav1.classList.add('completed');
                if (stepNav2) stepNav2.classList.add('active');
                if (stepNav3) stepNav3.classList.add('disabled');
                if (pathBar) pathBar.classList.remove('d-none');
                if (pathText) pathText.textContent = selectedDiv.name;
            } else if (step === 3) {
                if (stepNav1) stepNav1.classList.add('completed');
                if (stepNav2) stepNav2.classList.add('completed');
                if (stepNav3) stepNav3.classList.add('active');
                if (pathBar) pathBar.classList.remove('d-none');
                if (pathText) pathText.textContent = selectedDiv.name + ' > ' + selectedDist.name;
            }

            if (paneStep1) paneStep1.style.display = (step === 1) ? 'block' : 'none';
            if (paneStep2) paneStep2.style.display = (step === 2) ? 'block' : 'none';
            if (paneStep3) paneStep3.style.display = (step === 3) ? 'block' : 'none';

            var currentList = (step === 1) ? divisionsList : ((step === 2) ? districtsList : upazilasList);
            if (currentList) currentList.scrollTop = 0;
        }

        if (stepNav1) stepNav1.addEventListener('click', function () {
            if (!stepNav1.classList.contains('disabled')) updateStepUI(1);
        });
        if (stepNav2) stepNav2.addEventListener('click', function () {
            if (!stepNav2.classList.contains('disabled') && selectedDiv.id) updateStepUI(2);
        });
        if (stepNav3) stepNav3.addEventListener('click', function () {
            if (!stepNav3.classList.contains('disabled') && selectedDist.id) updateStepUI(3);
        });

        if (stepBackBtn) stepBackBtn.addEventListener('click', function () {
            if (currentStep === 3) updateStepUI(2);
            else if (currentStep === 2) updateStepUI(1);
        });

        // 1. Division Click
        if (divisionsList) {
            divisionsList.addEventListener('click', function (e) {
                var item = e.target.closest('.delivery-option-item');
                if (!item) return;

                var divId = item.getAttribute('data-id');
                var divName = item.getAttribute('data-name');
                selectedDiv = { id: divId, name: divName };
                if (stepLabel1) stepLabel1.textContent = divName;

                divisionsList.querySelectorAll('.delivery-option-item').forEach(function (el) {
                    el.classList.remove('selected');
                });
                item.classList.add('selected');

                loadDistricts(divId);
            });
        }

        // 2. Load Districts
        function loadDistricts(divId) {
            updateStepUI(2);
            if (districtCache[divId]) {
                renderDistricts(districtCache[divId]);
                return;
            }

            if (loading) loading.classList.remove('d-none');
            if (districtsList) districtsList.innerHTML = '';

            fetch('{{ url("/ajax/delivery/districts") }}/' + divId, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (res) { return res.json(); })
            .then(function (res) {
                if (loading) loading.classList.add('d-none');
                var items = (res && res.data) ? res.data : [];
                districtCache[divId] = items;
                renderDistricts(items);
            })
            .catch(function () {
                if (loading) loading.classList.add('d-none');
                if (districtsList) districtsList.innerHTML = '<div class="text-danger py-3 text-center">জেলা লোড করতে ব্যর্থ হয়েছে। আবার চেষ্টা করুন।</div>';
            });
        }

        function renderDistricts(items) {
            if (!districtsList) return;
            if (!items.length) {
                districtsList.innerHTML = '<div class="text-muted py-3 text-center">এই বিভাগে কোনো জেলা পাওয়া যায়নি</div>';
                return;
            }
            var html = '';
            items.forEach(function (dist) {
                var chargeVal = parseFloat(dist.delivery_charge) || 0;
                var chargeBadge = (chargeVal > 0) ? '<span class="option-charge">৳' + Math.round(chargeVal) + '</span>' : '';
                html += '<div class="delivery-option-item" data-id="' + dist.id + '" data-name="' + dist.name + '" data-charge="' + chargeVal + '">'
                      + '<div class="option-icon"><i class="fas fa-city"></i></div>'
                      + '<div class="option-name">' + dist.name + '</div>'
                      + chargeBadge
                      + '<div class="option-arrow"><i class="fas fa-chevron-right"></i></div>'
                      + '</div>';
            });
            districtsList.innerHTML = html;
        }

        // District Click
        if (districtsList) {
            districtsList.addEventListener('click', function (e) {
                var item = e.target.closest('.delivery-option-item');
                if (!item) return;

                var distId = item.getAttribute('data-id');
                var distName = item.getAttribute('data-name');
                var distCharge = parseFloat(item.getAttribute('data-charge')) || 0;
                selectedDist = { id: distId, name: distName, charge: distCharge };
                if (stepLabel2) stepLabel2.textContent = distName;

                districtsList.querySelectorAll('.delivery-option-item').forEach(function (el) {
                    el.classList.remove('selected');
                });
                item.classList.add('selected');

                loadUpazilas(distId);
            });
        }

        // 3. Load Upazilas
        function loadUpazilas(distId) {
            updateStepUI(3);
            if (upazilaCache[distId]) {
                renderUpazilas(upazilaCache[distId]);
                return;
            }

            if (loading) loading.classList.remove('d-none');
            if (upazilasList) upazilasList.innerHTML = '';

            fetch('{{ url("/ajax/delivery/upazilas") }}/' + distId, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (res) { return res.json(); })
            .then(function (res) {
                if (loading) loading.classList.add('d-none');
                var items = (res && res.data) ? res.data : [];
                upazilaCache[distId] = items;
                renderUpazilas(items);
            })
            .catch(function () {
                if (loading) loading.classList.add('d-none');
                if (upazilasList) upazilasList.innerHTML = '<div class="text-danger py-3 text-center">উপজেলা লোড করতে ব্যর্থ হয়েছে। আবার চেষ্টা করুন।</div>';
            });
        }

        function renderUpazilas(items) {
            if (!upazilasList) return;
            if (!items.length) {
                upazilasList.innerHTML = '<div class="text-muted py-3 text-center">এই জেলায় কোনো উপজেলা পাওয়া যায়নি</div>';
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
            upazilasList.innerHTML = html;
        }

        // Upazila Click -> Complete Selection!
        if (upazilasList) {
            upazilasList.addEventListener('click', function (e) {
                var item = e.target.closest('.delivery-option-item');
                if (!item) return;

                var upaId = item.getAttribute('data-id');
                var upaName = item.getAttribute('data-name');
                selectedUpa = { id: upaId, name: upaName };
                if (stepLabel3) stepLabel3.textContent = upaName;

                applyCompletedSelection();
            });
        }

        function applyCompletedSelection() {
            if (inputDiv) {
                inputDiv.value = selectedDiv.id;
                inputDiv.dispatchEvent(new Event('change', { bubbles: true }));
                if (window.jQuery) window.jQuery(inputDiv).trigger('change');
            }
            if (inputDist) {
                inputDist.value = selectedDist.id;
                inputDist.dispatchEvent(new Event('change', { bubbles: true }));
                if (window.jQuery) window.jQuery(inputDist).trigger('change');
            }
            if (inputUpa) {
                inputUpa.value = selectedUpa.id;
                inputUpa.dispatchEvent(new Event('change', { bubbles: true }));
                if (window.jQuery) window.jQuery(inputUpa).trigger('change');
            }

            var displayPath = selectedDiv.name + ' > ' + selectedDist.name + ' > ' + selectedUpa.name;
            if (displayLabel) {
                displayLabel.textContent = displayPath;
                displayLabel.classList.remove('is-placeholder');
            }
            if (trigger) {
                trigger.classList.add('has-value');
                trigger.classList.remove('is-invalid');
            }
            if (badge) badge.classList.remove('d-none');
            if (chevron) chevron.classList.add('d-none');

            // Dispatch global custom event for external listeners (e.g. admin pos or custom checkout)
            try {
                document.dispatchEvent(new CustomEvent('deliveryLocationSelected', {
                    detail: {
                        prefix: prefix,
                        division: selectedDiv,
                        district: selectedDist,
                        upazila: selectedUpa
                    }
                }));
            } catch(e) {}

            // Synchronize delivery area charge select (Inside Dhaka vs Outside Dhaka)
            syncAreaChargeWithDistrict(selectedDist.name);

            closeModal();

            if (typeof window.saveIncompleteOrder === 'function') {
                window.saveIncompleteOrder();
            }
        }

        function syncAreaChargeWithDistrict(districtName) {
            var areaSelect = document.getElementById('checkout_area') || document.getElementById('area');
            if (!areaSelect) return;

            var distLower = (districtName || '').toLowerCase().trim();
            var isDhaka = distLower.indexOf('ঢাকা') !== -1 || distLower.indexOf('dhaka') !== -1;

            var matchedVal = null;
            for (var i = 0; i < areaSelect.options.length; i++) {
                var opt = areaSelect.options[i];
                var txt = (opt.text || '').toLowerCase();
                var val = opt.value;
                if (!val) continue;

                if (isDhaka) {
                    if (txt.indexOf('ঢাকা') !== -1 && (txt.indexOf('ভিতরে') !== -1 || txt.indexOf('inside') !== -1 || txt.indexOf('সিটি') !== -1)) {
                        matchedVal = val;
                        break;
                    }
                } else {
                    if (txt.indexOf('বাইরে') !== -1 || txt.indexOf('outside') !== -1 || (txt.indexOf('ঢাকা') === -1 && txt.indexOf('সমগ্র') !== -1)) {
                        matchedVal = val;
                        break;
                    }
                }
            }

            if (matchedVal) {
                areaSelect.value = matchedVal;
                var evt = new Event('change', { bubbles: true });
                areaSelect.dispatchEvent(evt);
                if (window.jQuery) {
                    window.jQuery(areaSelect).trigger('change');
                }
            }
        }

        // Search Filter
        function filterItems(query) {
            var q = (query || '').toLowerCase().trim();
            var currentPane = null;
            if (currentStep === 1) currentPane = paneStep1;
            else if (currentStep === 2) currentPane = paneStep2;
            else if (currentStep === 3) currentPane = paneStep3;

            if (!currentPane) return;

            var items = currentPane.querySelectorAll('.delivery-option-item');
            var matchedCount = 0;

            if (!q) {
                items.forEach(function (el) { el.style.display = 'flex'; });
                if (noResults) noResults.classList.add('d-none');
                return;
            }

            items.forEach(function (el) {
                var text = (el.getAttribute('data-name') || '').toLowerCase();
                if (text.indexOf(q) !== -1) {
                    el.style.display = 'flex';
                    matchedCount++;
                } else {
                    el.style.display = 'none';
                }
            });

            if (noResults) {
                if (matchedCount === 0) noResults.classList.remove('d-none');
                else noResults.classList.add('d-none');
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                var val = searchInput.value;
                if (searchClear) searchClear.classList.toggle('d-none', !val);
                filterItems(val);
            });
        }

        if (searchClear) {
            searchClear.addEventListener('click', function () {
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
                searchClear.classList.add('d-none');
                filterItems('');
            });
        }

        // Initial fetch if values are already set
        if (initDivId && initDistId) {
            fetch('{{ url("/ajax/delivery/districts") }}/' + initDivId, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(res){ return res.json(); })
            .then(function(dRes){
                var dItems = (dRes && dRes.data) ? dRes.data : [];
                districtCache[initDivId] = dItems;
                var foundDist = dItems.find(function(d) { return String(d.id) === String(initDistId); });
                var distName = foundDist ? foundDist.name : '';
                var divItem = divisionsList ? divisionsList.querySelector('.delivery-option-item[data-id="' + initDivId + '"]') : null;
                var divName = divItem ? divItem.getAttribute('data-name') : '';

                if (initUpaId) {
                    fetch('{{ url("/ajax/delivery/upazilas") }}/' + initDistId, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(function(res){ return res.json(); })
                    .then(function(uRes){
                        var uItems = (uRes && uRes.data) ? uRes.data : [];
                        upazilaCache[initDistId] = uItems;
                        var foundUpa = uItems.find(function(u) { return String(u.id) === String(initUpaId); });
                        var upaName = foundUpa ? foundUpa.name : '';
                        if (divName && distName && upaName) {
                            selectedDiv = { id: initDivId, name: divName };
                            selectedDist = { id: initDistId, name: distName };
                            selectedUpa = { id: initUpaId, name: upaName };
                            if (displayLabel) displayLabel.textContent = divName + ' > ' + distName + ' > ' + upaName;
                            if (trigger) trigger.classList.add('has-value');
                            if (badge) badge.classList.remove('d-none');
                            if (chevron) chevron.classList.add('d-none');
                        }
                    });
                } else if (divName && distName) {
                    if (displayLabel) displayLabel.textContent = divName + ' > ' + distName;
                    if (trigger) trigger.classList.add('has-value');
                    if (badge) badge.classList.remove('d-none');
                    if (chevron) chevron.classList.add('d-none');
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDeliveryLocationPicker);
    } else {
        initDeliveryLocationPicker();
    }
})();
</script>
