@php
    $presetKey = $presetKey ?? 'date_preset';
    $startKey  = $startKey ?? 'start_date';
    $endKey    = $endKey ?? 'end_date';
    $rangeKey  = $rangeKey ?? 'date_range';

    $selectedPreset = request($presetKey, '');
    $selectedStart  = request($startKey, '');
    $selectedEnd    = request($endKey, '');
    $selectedRange  = request($rangeKey, '');

    // Compute active display label
    $presetLabels = [
        'today'        => 'Today',
        'yesterday'    => 'Yesterday',
        'this_week'    => 'This Week',
        'last_week'    => 'Last Week',
        'last_30_days' => 'Last 30 Days',
        'this_month'   => 'This Month',
        'last_month'   => 'Last Month',
        'this_year'    => 'This Year',
        'last_year'    => 'Last Year',
    ];

    $displayLabel = 'All Time / Select Date';
    if (!empty($selectedPreset) && isset($presetLabels[$selectedPreset])) {
        $displayLabel = $presetLabels[$selectedPreset];
    } elseif (!empty($selectedStart) && !empty($selectedEnd)) {
        $displayLabel = \Carbon\Carbon::parse($selectedStart)->format('d M, Y') . ' - ' . \Carbon\Carbon::parse($selectedEnd)->format('d M, Y');
    } elseif (!empty($selectedStart)) {
        $displayLabel = 'From ' . \Carbon\Carbon::parse($selectedStart)->format('d M, Y');
    } elseif (!empty($selectedEnd)) {
        $displayLabel = 'Until ' . \Carbon\Carbon::parse($selectedEnd)->format('d M, Y');
    } elseif (!empty($selectedRange)) {
        $displayLabel = $selectedRange;
    }

    $uniqueId = 'sdf_' . uniqid();
@endphp

<div class="smart-date-filter-wrapper position-relative" id="{{ $uniqueId }}_wrapper">
    {{-- Hidden Form Fields --}}
    <input type="hidden" name="{{ $presetKey }}" id="{{ $uniqueId }}_preset" value="{{ $selectedPreset }}">
    <input type="hidden" name="{{ $startKey }}" id="{{ $uniqueId }}_start" value="{{ $selectedStart }}">
    <input type="hidden" name="{{ $endKey }}" id="{{ $uniqueId }}_end" value="{{ $selectedEnd }}">

    {{-- Trigger Input Button --}}
    <div class="input-group">
        <button type="button" class="btn btn-white border bg-white text-start d-flex align-items-center justify-content-between w-100 smart-date-btn px-3 py-2" id="{{ $uniqueId }}_btn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="border-radius: 8px; font-size: 0.88rem; color: #334155; height: 38px;">
            <span class="d-flex align-items-center text-truncate me-1">
                <i class="far fa-calendar-alt text-primary me-2 flex-shrink-0"></i>
                <span class="fw-semibold text-truncate" id="{{ $uniqueId }}_label">{{ $displayLabel }}</span>
            </span>
            <i class="fas fa-chevron-down text-muted small ms-1 flex-shrink-0" style="font-size: 0.7rem;"></i>
        </button>

        {{-- Dropdown Menu --}}
        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 smart-date-dropdown" id="{{ $uniqueId }}_dropdown" style="min-width: 310px; border-radius: 12px; z-index: 1060;">
            <div class="p-3 border-bottom bg-light bg-opacity-50">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark small"><i class="far fa-calendar-check text-primary me-1"></i> Filter by Date Range</span>
                    @if(!empty($selectedPreset) || !empty($selectedStart) || !empty($selectedEnd) || !empty($selectedRange))
                    <button type="button" class="btn btn-link text-danger p-0 text-decoration-none small" onclick="{{ $uniqueId }}_clearDate(event)">
                        <i class="fas fa-times-circle me-1"></i> Clear
                    </button>
                    @endif
                </div>
            </div>

            {{-- Quick Presets List --}}
            <div class="p-2">
                <div class="row g-1">
                    @foreach($presetLabels as $pKey => $pName)
                    <div class="col-6">
                        <button type="button" class="dropdown-item rounded-2 py-1 px-2 small fw-medium {{ $selectedPreset === $pKey ? 'active bg-primary text-white' : '' }}" onclick="{{ $uniqueId }}_setPreset('{{ $pKey }}', '{{ $pName }}')">
                            {{ $pName }}
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Custom Calendar Box --}}
            <div class="p-3 border-top bg-light bg-opacity-25">
                <div class="small fw-bold text-muted mb-2 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Custom Date Range</div>
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm bg-white border" id="{{ $uniqueId }}_flatpickr" placeholder="Click to select custom range..." value="{{ (!empty($selectedStart) && !empty($selectedEnd)) ? $selectedStart.' to '.$selectedEnd : '' }}" readonly style="font-size: 0.82rem; cursor: pointer;">
                    <span class="input-group-text bg-white border-start-0 text-muted"><i class="far fa-calendar"></i></span>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-2">
                    <button type="button" class="btn btn-xs btn-light border small px-3" onclick="{{ $uniqueId }}_closeDropdown()">Cancel</button>
                    <button type="button" class="btn btn-xs btn-primary small px-3" onclick="{{ $uniqueId }}_applyCustom(event)">Apply Range</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var wrapper = document.getElementById('{{ $uniqueId }}_wrapper');
    var form = wrapper ? wrapper.closest('form') : null;

    window['{{ $uniqueId }}_setPreset'] = function(presetKey, presetLabel) {
        document.getElementById('{{ $uniqueId }}_preset').value = presetKey;
        document.getElementById('{{ $uniqueId }}_start').value = '';
        document.getElementById('{{ $uniqueId }}_end').value = '';
        document.getElementById('{{ $uniqueId }}_label').innerText = presetLabel;
        
        // Auto-close dropdown
        var dropdownEl = document.getElementById('{{ $uniqueId }}_btn');
        if (dropdownEl && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            var bsDropdown = bootstrap.Dropdown.getInstance(dropdownEl);
            if (bsDropdown) bsDropdown.hide();
        }

        // Auto submit parent form
        if (form) {
            form.submit();
        }
    };

    window['{{ $uniqueId }}_clearDate'] = function(e) {
        if (e) e.stopPropagation();
        document.getElementById('{{ $uniqueId }}_preset').value = '';
        document.getElementById('{{ $uniqueId }}_start').value = '';
        document.getElementById('{{ $uniqueId }}_end').value = '';
        document.getElementById('{{ $uniqueId }}_label').innerText = 'All Time / Select Date';
        if (form) form.submit();
    };

    window['{{ $uniqueId }}_closeDropdown'] = function() {
        var dropdownEl = document.getElementById('{{ $uniqueId }}_btn');
        if (dropdownEl && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            var bsDropdown = bootstrap.Dropdown.getInstance(dropdownEl);
            if (bsDropdown) bsDropdown.hide();
        }
    };

    window['{{ $uniqueId }}_applyCustom'] = function(e) {
        if (e) e.preventDefault();
        var fpInput = document.getElementById('{{ $uniqueId }}_flatpickr');
        var val = fpInput ? fpInput.value : '';
        if (val) {
            var parts = val.split(' to ');
            if (parts.length >= 2) {
                document.getElementById('{{ $uniqueId }}_start').value = parts[0].trim();
                document.getElementById('{{ $uniqueId }}_end').value = parts[1].trim();
            } else if (parts.length === 1) {
                document.getElementById('{{ $uniqueId }}_start').value = parts[0].trim();
                document.getElementById('{{ $uniqueId }}_end').value = parts[0].trim();
            }
            document.getElementById('{{ $uniqueId }}_preset').value = '';
            document.getElementById('{{ $uniqueId }}_label').innerText = val;
            if (form) form.submit();
        }
    };

    // Initialize flatpickr on the custom range input
    document.addEventListener('DOMContentLoaded', function() {
        var fpEl = document.getElementById('{{ $uniqueId }}_flatpickr');
        if (fpEl && typeof flatpickr !== 'undefined') {
            flatpickr(fpEl, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd M, Y',
                allowInput: false
            });
        }
    });
})();
</script>
