{{-- Universal Compact Courier Booking Modal --}}
<div class="modal fade oi-modal" id="universalCourierModal" tabindex="-1" aria-labelledby="universalCourierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px; margin: 1.25rem auto;">
        <div class="modal-content ucm-compact-modal">
            {{-- Header --}}
            <div class="modal-header ucm-modal-head">
                <div class="d-flex align-items-center gap-2">
                    <div class="ucm-head-icon">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="universalCourierModalLabel">Courier Booking</h6>
                        <small class="text-white-50" id="ucm_order_summary_text">পার্সেল বুকিং নিশ্চিত করুন</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="universalCourierForm" method="POST" action="{{ route('admin.order.book_courier') }}">
                @csrf
                <input type="hidden" name="order_ids" id="ucm_order_ids" value="">
                <input type="hidden" name="courier_type" id="ucm_selected_courier" value="">

                <div class="modal-body p-3 p-sm-4">

                    {{-- Selected Order Summary Bar --}}
                    <div class="ucm-summary-pill mb-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="fas fa-receipt text-primary"></i>
                                <span class="fw-bold text-dark" id="ucm_preview_invoices">-</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-muted small">COD:</span>
                                <span class="fw-bold text-success" id="ucm_preview_cod">৳0</span>
                            </div>
                        </div>
                    </div>

                    @php
                        $isCarrybeeActive = isset($carrybee_info) ? ($carrybee_info->status == 1) : (\App\Models\Courierapi::where(['status' => 1, 'type' => 'carrybee'])->exists());
                        $isSteadfastActive = isset($steadfast) ? ($steadfast->status == 1) : (\App\Models\Courierapi::where(['status' => 1, 'type' => 'steadfast'])->exists());
                        $isPathaoActive = isset($pathao_info) ? ($pathao_info->status == 1) : (\App\Models\Courierapi::where(['status' => 1, 'type' => 'pathao'])->exists());
                        $isRedxActive = isset($redx_info) ? ($redx_info->status == 1) : (\App\Models\Courierapi::where(['status' => 1, 'type' => 'redx'])->exists());

                        $courierList = [
                            'carrybee'  => ['name' => 'Carrybee', 'active' => $isCarrybeeActive, 'logo' => asset('public/frontEnd/images/carrybee.svg'), 'fallback' => asset('public/uploads/default/carrybee.svg'), 'badge' => 'Express'],
                            'steadfast' => ['name' => 'Steadfast', 'active' => $isSteadfastActive, 'logo' => asset('public/frontEnd/images/stade.svg'), 'fallback' => '', 'badge' => 'Fast COD'],
                            'pathao'    => ['name' => 'Pathao', 'active' => $isPathaoActive, 'logo' => 'https://merchant.pathao.com/assets/logo_pathao_courier.a3ef9b7c.svg', 'fallback' => '', 'badge' => 'Hermes API'],
                            'redx'      => ['name' => 'RedX', 'active' => $isRedxActive, 'logo' => 'https://redx.com.bd/images/logo.png', 'fallback' => '', 'badge' => 'Doorstep'],
                        ];

                        $activeCount = 0;
                        foreach($courierList as $c) {
                            if ($c['active']) $activeCount++;
                        }
                    @endphp

                    {{-- 1. Courier List Cards --}}
                    <div class="mb-3">
                        <label class="ucm-section-label">
                            <span>Select Courier Gateway</span>
                            @if($activeCount > 1)
                                <small class="text-muted fw-normal">({{ $activeCount }} টি সক্রিয়)</small>
                            @endif
                        </label>

                        <div class="ucm-courier-list-group" id="ucm_courier_cards">
                            @foreach($courierList as $cKey => $cData)
                                @if($cData['active'])
                                    <div class="ucm-list-card" data-courier="{{ $cKey }}">
                                        <div class="d-flex align-items-center gap-2.5 flex-grow-1">
                                            <div class="ucm-list-logo-wrap">
                                                <img src="{{ $cData['logo'] }}" alt="{{ $cData['name'] }}"
                                                     @if(!empty($cData['fallback'])) onerror="this.src='{{ $cData['fallback'] }}'" @endif>
                                            </div>
                                            <div>
                                                <div class="ucm-list-title">{{ $cData['name'] }}</div>
                                                <div class="ucm-list-sub">{{ $cData['badge'] }}</div>
                                            </div>
                                        </div>
                                        <div class="ucm-radio-indicator">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. Store & Parcel Options Form --}}
                    <div class="ucm-options-card">
                        {{-- Pickup Store Selector --}}
                        <div class="mb-2.5">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="ucm-field-label mb-0"><i class="fas fa-store text-primary me-1"></i> Pickup Store</label>
                                <span class="ucm-store-hint" id="ucm_store_count_hint"></span>
                            </div>
                            <select name="store_id" id="ucm_store_select" class="form-select form-select-sm ucm-custom-select" required>
                                <option value="">স্টোর লোড হচ্ছে...</option>
                            </select>
                        </div>

                        {{-- Carrybee Specific Options --}}
                        <div class="ucm-courier-fields d-none" id="ucm_carrybee_fields">
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="ucm-field-label">ডেলিভারি ধরন</label>
                                    <select name="delivery_type" class="form-select form-select-sm">
                                        <option value="1" selected>Normal Delivery</option>
                                        <option value="2">Express Delivery</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="ucm-field-label">পার্সেল ধরন</label>
                                    <select name="product_type" class="form-select form-select-sm">
                                        <option value="1" selected>Parcel</option>
                                        <option value="2">Book</option>
                                        <option value="3">Document</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Pathao Specific Options --}}
                        <div class="ucm-courier-fields d-none" id="ucm_pathao_fields">
                            <div class="row g-2 mb-2">
                                <div class="col-4">
                                    <label class="ucm-field-label">City</label>
                                    <select name="pathaocity" id="ucm_pathao_city" class="form-select form-select-sm">
                                        <option value="">সিটি...</option>
                                        @if(isset($pathaocities['data']['data']))
                                            @foreach($pathaocities['data']['data'] as $city)
                                                <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="ucm-field-label">Zone</label>
                                    <select name="pathaozone" id="ucm_pathao_zone" class="form-select form-select-sm">
                                        <option value="">জোন...</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="ucm-field-label">Area</label>
                                    <select name="pathaoarea" id="ucm_pathao_area" class="form-select form-select-sm">
                                        <option value="">এরিয়া...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Weight & COD Amount --}}
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="ucm-field-label">পার্সেল ওজন (Grams)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="item_weight" class="form-control" value="200" min="50" max="25000" placeholder="200" required />
                                    <span class="input-group-text bg-light text-muted small">g</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="ucm-field-label">ক্যাশ কালেকশন (COD ৳)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted">৳</span>
                                    <input type="number" name="collectable_amount" id="ucm_cod_input" class="form-control" placeholder="অর্ডারের দাম" />
                                </div>
                            </div>
                        </div>

                        {{-- Special Instruction / Note --}}
                        <div>
                            <label class="ucm-field-label">স্পেশাল নোট</label>
                            <input type="text" name="special_instruction" id="ucm_note_input" class="form-control form-control-sm" value="পণ্য চেক করে রিসিভ করুন" placeholder="ডেলিভারি নোট..." />
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer ucm-modal-footer">
                    <button type="button" class="btn btn-sm btn-light px-3 border text-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold ucm-submit-btn" id="ucm_submit_btn">
                        <i class="fas fa-paper-plane me-1.5"></i> Confirm Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Compact Courier Booking Modal Styles */
.ucm-compact-modal {
    border-radius: 14px;
    border: none;
    box-shadow: 0 16px 36px rgba(15, 23, 42, 0.22);
    overflow: hidden;
    font-family: system-ui, -apple-system, sans-serif;
}
.ucm-modal-head {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 12px 18px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.ucm-head-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255,255,255,0.12);
    color: #f59e0b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}
.ucm-summary-pill {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 12.5px;
}
.ucm-section-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    margin-bottom: 6px;
}
.ucm-courier-list-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.ucm-list-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: all 0.18s ease;
}
.ucm-list-card:hover {
    border-color: #93c5fd;
    background: #f8fafc;
    transform: translateY(-1px);
}
.ucm-list-card.active {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.12);
}
.ucm-list-logo-wrap {
    width: 40px;
    height: 30px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3px 5px;
}
.ucm-list-logo-wrap img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.ucm-list-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
}
.ucm-list-sub {
    font-size: 10.5px;
    color: #64748b;
    line-height: 1.2;
    margin-top: 1px;
}
.ucm-radio-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1.5px solid #cbd5e1;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: transparent;
    transition: all 0.15s ease;
}
.ucm-list-card.active .ucm-radio-indicator {
    background: #2563eb;
    border-color: #2563eb;
    color: #fff;
}
.ucm-options-card {
    background: #fafbfc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px;
}
.ucm-field-label {
    font-size: 11.5px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 4px;
    display: block;
}
.ucm-store-hint {
    font-size: 11px;
    color: #64748b;
}
.ucm-custom-select {
    font-weight: 500;
    font-size: 12.5px;
    border-color: #cbd5e1;
}
.ucm-modal-footer {
    padding: 10px 18px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ucm-submit-btn {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border: none;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.28);
}
.ucm-submit-btn:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
}
@media (max-width: 575.98px) {
    .ucm-compact-modal {
        margin: 0.5rem;
    }
}
</style>

<script>
(function(){
    // Global helper to load stores
    window.ucmLoadStores = function(courierType) {
        var $select = $('#ucm_store_select');
        $select.empty().append('<option value="">স্টোর লোড হচ্ছে...</option>');
        $('#ucm_store_count_hint').text('');

        $.ajax({
            url: "{{ url('admin/courierapi/stores') }}/" + courierType,
            type: "GET",
            dataType: "json",
            success: function(res) {
                $select.empty();
                if (res.success && res.stores && res.stores.length > 0) {
                    var defaultStore = res.default_store;
                    $('#ucm_store_count_hint').text('(' + res.stores.length + ' টি স্টোর)');

                    res.stores.forEach(function(st, idx) {
                        var isSel = (defaultStore && (st.store_id == defaultStore || st.id == defaultStore)) || (!defaultStore && st.is_default) || (idx === 0 && !defaultStore);
                        var optText = st.store_name + (st.is_default ? ' ⭐ (Default)' : '') + (st.address ? ' - ' + st.address.substring(0, 30) : '');
                        var val = st.store_id || st.id;
                        $select.append(new Option(optText, val, isSel, isSel));
                    });
                } else {
                    $select.append(new Option('Default Merchant Profile Store (Primary)', 'default_profile', true, true));
                    $('#ucm_store_count_hint').text('(Primary)');
                }
            },
            error: function() {
                $select.empty();
                $select.append(new Option('Default Merchant Profile Store', 'default_profile', true, true));
            }
        });
    };

    // Courier selection card click
    $(document).on('click', '.ucm-list-card', function(){
        $('.ucm-list-card').removeClass('active');
        $(this).addClass('active');

        var courier = $(this).data('courier');
        $('#ucm_selected_courier').val(courier);

        // Toggle courier specific fields
        $('.ucm-courier-fields').addClass('d-none');
        $('#ucm_' + courier + '_fields').removeClass('d-none');

        // Load stores
        window.ucmLoadStores(courier);
    });

    // Open Modal from Bulk Action or Single buttons
    $(document).on('click', '.open-universal-courier-btn, .single-courier-btn', function(e){
        e.preventDefault();
        var $btn = $(this);
        var orderIds = [];
        var invoiceText = '';
        var totalCod = 0;

        var singleOrderId = $btn.data('order-id');
        var preferredCourier = $btn.data('courier');

        if (singleOrderId) {
            orderIds = [singleOrderId];
            invoiceText = '#' + ($btn.data('invoice') || singleOrderId);
            totalCod = Number($btn.data('amount') || 0);
            $('#ucm_cod_input').val(totalCod > 0 ? totalCod : '');
        } else {
            // Bulk from checkboxes
            $('.checkbox:checked').each(function(){
                orderIds.push($(this).val());
            });

            if (orderIds.length === 0) {
                if (typeof toastr !== 'undefined') toastr.warning('Please select at least one order');
                return;
            }

            invoiceText = orderIds.length + ' Orders Selected';
            $('#ucm_cod_input').val('');
        }

        $('#ucm_order_ids').val(orderIds.join(','));
        $('#ucm_preview_invoices').text(invoiceText);
        $('#ucm_order_summary_text').text(orderIds.length + ' Order(s) to dispatch');
        $('#ucm_preview_cod').text(totalCod > 0 ? '৳' + totalCod.toLocaleString() : (orderIds.length + ' Orders'));

        // Pick preferred or first active courier card
        var $targetCard = null;
        if (preferredCourier) {
            $targetCard = $('.ucm-list-card[data-courier="' + preferredCourier + '"]');
        }
        if (!$targetCard || $targetCard.length === 0) {
            $targetCard = $('.ucm-list-card').first();
        }

        if ($targetCard && $targetCard.length > 0) {
            $targetCard.trigger('click');
        } else {
            window.ucmLoadStores('carrybee');
        }

        var modalEl = document.getElementById('universalCourierModal');
        if (modalEl) {
            var myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            myModal.show();
        }
    });

    // Pathao City -> Zone
    $(document).on('change', '#ucm_pathao_city', function(){
        var cityId = $(this).val();
        var $zone = $('#ucm_pathao_zone');
        $zone.empty().append('<option value="">Loading...</option>');
        $('#ucm_pathao_area').empty().append('<option value="">Select Zone first</option>');

        if (cityId) {
            $.get("{{ route('pathaocity') }}", { city_id: cityId }, function(data){
                $zone.empty().append('<option value="">Select Zone...</option>');
                if (data && data.data && data.data.data) {
                    data.data.data.forEach(function(z){
                        $zone.append(new Option(z.zone_name, z.zone_id));
                    });
                }
            });
        }
    });

    // Pathao Zone -> Area
    $(document).on('change', '#ucm_pathao_zone', function(){
        var zoneId = $(this).val();
        var $area = $('#ucm_pathao_area');
        $area.empty().append('<option value="">Loading...</option>');

        if (zoneId) {
            $.get("{{ route('pathaozone') }}", { zone_id: zoneId }, function(data){
                $area.empty().append('<option value="">Select Area...</option>');
                if (data && data.data && data.data.data) {
                    data.data.data.forEach(function(a){
                        $area.append(new Option(a.area_name, a.area_id));
                    });
                }
            });
        }
    });

    // Universal Courier Form Submit
    $('#universalCourierForm').on('submit', function(e){
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#ucm_submit_btn');
        var originalBtnHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Booking...');

        $.ajax({
            url: $form.attr('action'),
            type: "POST",
            data: $form.serialize(),
            dataType: "json",
            success: function(res) {
                $btn.prop('disabled', false).html(originalBtnHtml);
                if (res.status === 'success') {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.message || 'Courier booking successful!', 'Success');
                    }
                    var modalEl = document.getElementById('universalCourierModal');
                    if (modalEl) {
                        var myModal = bootstrap.Modal.getInstance(modalEl);
                        if (myModal) myModal.hide();
                    }
                    setTimeout(function(){
                        location.reload();
                    }, 800);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(res.message || 'Booking failed', 'Error');
                    }
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalBtnHtml);
                var msg = xhr.responseJSON?.message || 'Error occurred while booking courier';
                if (typeof toastr !== 'undefined') {
                    toastr.error(msg, 'Error');
                } else {
                    alert(msg);
                }
            }
        });
    });
})();
</script>
