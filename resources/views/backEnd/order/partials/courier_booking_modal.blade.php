{{-- Universal Courier Booking Modal --}}
<div class="modal fade oi-modal" id="universalCourierModal" tabindex="-1" aria-labelledby="universalCourierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header py-3 px-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
                <div>
                    <h5 class="modal-title d-flex align-items-center gap-2 text-white fw-bold mb-0" id="universalCourierModalLabel">
                        <i class="fas fa-shipping-fast text-warning"></i> কুরিয়ার পার্সেল বুকিং
                    </h5>
                    <small class="text-white-50" id="ucm_order_summary_text">অর্ডার নির্বাচন করুন</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="universalCourierForm" method="POST" action="{{ route('admin.order.book_courier') }}">
                @csrf
                <input type="hidden" name="order_ids" id="ucm_order_ids" value="">
                <input type="hidden" name="courier_type" id="ucm_selected_courier" value="carrybee">

                <div class="modal-body p-4">

                    {{-- Selected Orders Preview Card --}}
                    <div class="p-3 mb-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <span class="text-muted small fw-bold text-uppercase">নির্বাচিত অর্ডার:</span>
                                <span class="fw-bold text-primary ms-1" id="ucm_preview_invoices">-</span>
                            </div>
                            <div>
                                <span class="text-muted small fw-bold text-uppercase">মোট COD পরিমাণ:</span>
                                <span class="fw-bold text-success fs-6 ms-1" id="ucm_preview_cod">৳0</span>
                            </div>
                        </div>
                    </div>

                    {{-- Courier Selection Grid --}}
                    <label class="form-label text-muted small fw-bold text-uppercase mb-2">১. কুরিয়ার সার্ভিস নির্বাচন করুন</label>

                    @php
                        $isCarrybeeActive = isset($carrybee_info) ? ($carrybee_info->status == 1) : (\App\Models\Courierapi::where(['status' => 1, 'type' => 'carrybee'])->exists());
                        $isSteadfastActive = isset($steadfast) ? ($steadfast->status == 1) : (\App\Models\Courierapi::where(['status' => 1, 'type' => 'steadfast'])->exists());
                        $isPathaoActive = isset($pathao_info) ? ($pathao_info->status == 1) : (\App\Models\Courierapi::where(['status' => 1, 'type' => 'pathao'])->exists());
                        $isRedxActive = isset($redx_info) ? ($redx_info->status == 1) : (\App\Models\Courierapi::where(['status' => 1, 'type' => 'redx'])->exists());
                    @endphp

                    <div class="row g-2 mb-4" id="ucm_courier_cards">
                        {{-- Carrybee --}}
                        <div class="col-6 col-md-3">
                            <div class="ucm-courier-card {{ $isCarrybeeActive ? 'active' : 'disabled' }}" data-courier="carrybee">
                                <div class="ucm-logo-wrap">
                                    <img src="{{ asset('public/frontEnd/images/carrybee.svg') }}" alt="Carrybee"
                                         onerror="this.src='{{ asset('public/uploads/default/carrybee.svg') }}'">
                                </div>
                                <div class="ucm-courier-name">Carrybee</div>
                                <span class="ucm-status-badge {{ $isCarrybeeActive ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $isCarrybeeActive ? 'চালু আছে' : 'নিষ্ক্রিয়' }}
                                </span>
                            </div>
                        </div>

                        {{-- Steadfast --}}
                        <div class="col-6 col-md-3">
                            <div class="ucm-courier-card {{ $isSteadfastActive ? (!$isCarrybeeActive ? 'active' : '') : 'disabled' }}" data-courier="steadfast">
                                <div class="ucm-logo-wrap">
                                    <img src="{{ asset('public/frontEnd/images/stade.svg') }}" alt="Steadfast">
                                </div>
                                <div class="ucm-courier-name">Steadfast</div>
                                <span class="ucm-status-badge {{ $isSteadfastActive ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $isSteadfastActive ? 'চালু আছে' : 'নিষ্ক্রিয়' }}
                                </span>
                            </div>
                        </div>

                        {{-- Pathao --}}
                        <div class="col-6 col-md-3">
                            <div class="ucm-courier-card {{ $isPathaoActive ? '' : 'disabled' }}" data-courier="pathao">
                                <div class="ucm-logo-wrap">
                                    <img src="https://merchant.pathao.com/assets/logo_pathao_courier.a3ef9b7c.svg" alt="Pathao">
                                </div>
                                <div class="ucm-courier-name">Pathao</div>
                                <span class="ucm-status-badge {{ $isPathaoActive ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $isPathaoActive ? 'চালু আছে' : 'নিষ্ক্রিয়' }}
                                </span>
                            </div>
                        </div>

                        {{-- RedX --}}
                        <div class="col-6 col-md-3">
                            <div class="ucm-courier-card {{ $isRedxActive ? '' : 'disabled' }}" data-courier="redx">
                                <div class="ucm-logo-wrap">
                                    <img src="https://redx.com.bd/images/logo.png" alt="RedX"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ctext x=%2250%22 y=%2255%22 font-size=%2236%22 text-anchor=%22middle%22 fill=%22%23f59e0b%22%3ERX%3C/text%3E%3C/svg%3E'">
                                </div>
                                <div class="ucm-courier-name">RedX</div>
                                <span class="ucm-status-badge {{ $isRedxActive ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $isRedxActive ? 'চালু আছে' : 'নিষ্ক্রিয়' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Settings Accordion per Courier --}}
                    <label class="form-label text-muted small fw-bold text-uppercase mb-2">২. পিকআপ স্টোর ও পার্সেল তথ্য</label>

                    <div class="p-3 rounded-3" style="background: #ffffff; border: 1.5px solid #cbd5e1;">

                        {{-- Pickup Store Selector --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-store text-primary me-1"></i> পিকআপ স্টোর (Pickup Store) <span class="text-danger">*</span></span>
                                <small class="text-muted fw-normal" id="ucm_store_count_hint"></small>
                            </label>
                            <select name="store_id" id="ucm_store_select" class="form-select" required>
                                <option value="">স্টোর লোড হচ্ছে...</option>
                            </select>
                            <small class="text-muted mt-1 d-block" style="font-size: 11px;">
                                * সেটিংস থেকে যে স্টোরটি "ডিফল্ট" করা আছে তা স্বয়ংক্রিয়ভাবে নির্বাচিত থাকবে।
                            </small>
                        </div>

                        {{-- Carrybee Specific Options --}}
                        <div class="ucm-courier-fields" id="ucm_carrybee_fields">
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">ডেলিভারি টাইপ</label>
                                    <select name="delivery_type" class="form-select form-select-sm">
                                        <option value="1" selected>নরমাল ডেলিভারি (Normal)</option>
                                        <option value="2">এক্সপ্রেস ডেলিভারি (Express)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">প্রোডাক্ট টাইপ</label>
                                    <select name="product_type" class="form-select form-select-sm">
                                        <option value="1" selected>পার্সেল (Parcel)</option>
                                        <option value="2">বই (Book)</option>
                                        <option value="3">ডকুমেন্ট (Document)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_closed_box" value="1" id="cb_closed_box">
                                <label class="form-check-label small" for="cb_closed_box">ক্লোজড বক্স ডেলিভারি (Closed Box)</label>
                            </div>
                        </div>

                        {{-- Pathao Specific Options --}}
                        <div class="ucm-courier-fields d-none" id="ucm_pathao_fields">
                            <div class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">শহর (City)</label>
                                    <select name="pathaocity" id="ucm_pathao_city" class="form-select form-select-sm">
                                        <option value="">সিলেক্ট সিটি...</option>
                                        @if(isset($pathaocities['data']['data']))
                                            @foreach($pathaocities['data']['data'] as $city)
                                                <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">জোন (Zone)</label>
                                    <select name="pathaozone" id="ucm_pathao_zone" class="form-select form-select-sm">
                                        <option value="">আগে সিটি সিলেক্ট করুন</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">এরিয়া (Area)</label>
                                    <select name="pathaoarea" id="ucm_pathao_area" class="form-select form-select-sm">
                                        <option value="">আগে জোন সিলেক্ট করুন</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- RedX Specific Options --}}
                        <div class="ucm-courier-fields d-none" id="ucm_redx_fields">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">ডেলিভারি এরিয়া (RedX Area)</label>
                                <select name="delivery_area_id" id="ucm_redx_area" class="form-select form-select-sm">
                                    <option value="1">Default / All Bangladesh</option>
                                    @if(isset($redxAreas) && is_array($redxAreas))
                                        @foreach(array_slice($redxAreas, 0, 100) as $area)
                                            <option value="{{ $area['id'] ?? '' }}">{{ $area['name'] ?? ($area['post_code'] ?? '') }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        {{-- Steadfast Specific info --}}
                        <div class="ucm-courier-fields d-none" id="ucm_steadfast_fields">
                            <div class="alert alert-info py-2 px-3 small mb-3">
                                <i class="fas fa-info-circle me-1"></i> Steadfast API এর মাধ্যমে ইনভয়েস, কাস্টমার নাম, ফোন ও ঠিকানাসহ বুকিং পাঠানো হবে।
                            </div>
                        </div>

                        {{-- Common Fields: Weight & Collectable COD --}}
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold">পার্সেল ওজন (গ্রাম / Grams)</label>
                                <input type="number" name="item_weight" class="form-control form-control-sm" value="200" min="50" max="25000" placeholder="200" />
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold">ক্যাশ কালেকশন (COD ৳)</label>
                                <input type="number" name="collectable_amount" id="ucm_cod_input" class="form-control form-control-sm" placeholder="অর্ডারের পরিমাণ" />
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold">বিশেষ নির্দেশনা / স্পেশাল নোট</label>
                            <input type="text" name="special_instruction" id="ucm_note_input" class="form-control form-control-sm" value="পণ্য চেক করে রিসিভ করুন" placeholder="ডেলিভারি সংক্রান্ত নির্দেশনা" />
                        </div>

                    </div>

                </div>

                <div class="modal-footer py-2.5 px-4 bg-light d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="ucm_submit_btn">
                        <i class="fas fa-check-circle me-1"></i> বুকিং সম্পন্ন করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .ucm-courier-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 8px;
        text-align: center;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    .ucm-courier-card:hover:not(.disabled) {
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }
    .ucm-courier-card.active {
        border-color: #3b82f6;
        background: #eff6ff;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
    .ucm-courier-card.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f8fafc;
    }
    .ucm-logo-wrap {
        width: 44px;
        height: 38px;
        margin: 0 auto 6px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ucm-logo-wrap img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .ucm-courier-name {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2px;
    }
    .ucm-status-badge {
        font-size: 9.5px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 999px;
        display: inline-block;
    }
    .ucm-status-badge.badge-active {
        background: #dcfce7;
        color: #15803d;
    }
    .ucm-status-badge.badge-inactive {
        background: #f1f5f9;
        color: #94a3b8;
    }
</style>

<script>
(function(){
    // Helper to load stores for a courier
    window.ucmLoadStores = function(courierType) {
        var $select = $('#ucm_store_select');
        $select.html('<option value="">স্টোর লোড হচ্ছে...</option>');
        $('#ucm_store_count_hint').text('');

        $.ajax({
            url: "{{ url('admin/courierapi/stores') }}/" + courierType,
            type: "GET",
            dataType: "json",
            success: function(res) {
                $select.empty();
                if (res.success && res.stores && res.stores.length > 0) {
                    var defaultStore = res.default_store;
                    $('#ucm_store_count_hint').text('(' + res.stores.length + ' টি স্টোর উপলব্ধ)');

                    res.stores.forEach(function(st, idx) {
                        var isSel = (defaultStore && (st.store_id == defaultStore || st.id == defaultStore)) || (!defaultStore && st.is_default) || (idx === 0 && !defaultStore);
                        var optText = st.store_name + (st.is_default ? ' ⭐ (ডিফল্ট)' : '') + (st.address ? ' - ' + st.address.substring(0, 30) : '');
                        var val = st.store_id || st.id;
                        $select.append(new Option(optText, val, isSel, isSel));
                    });
                } else {
                    $select.append(new Option('ডিফল্ট মার্চেন্ট প্রোফাইল স্টোর (Primary)', 'default_profile', true, true));
                    $('#ucm_store_count_hint').text('(কুরিয়ার প্রোফাইল স্টোর)');
                }
            },
            error: function() {
                $select.empty();
                $select.append(new Option('ডিফল্ট মার্চেন্ট প্রোফাইল স্টোর', 'default_profile', true, true));
            }
        });
    };

    // Courier selection card click
    $(document).on('click', '.ucm-courier-card:not(.disabled)', function(){
        $('.ucm-courier-card').removeClass('active');
        $(this).addClass('active');

        var courier = $(this).data('courier');
        $('#ucm_selected_courier').val(courier);

        // Toggle field visibility
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
                if (typeof toastr !== 'undefined') toastr.warning('অনুগ্রহ করে অন্তত একটি অর্ডার সিলেক্ট করুন');
                return;
            }

            invoiceText = orderIds.length + ' টি অর্ডার নির্বাচিত';
            $('#ucm_cod_input').val('');
        }

        $('#ucm_order_ids').val(orderIds.join(','));
        $('#ucm_preview_invoices').text(invoiceText);
        $('#ucm_order_summary_text').text('মোট ' + orderIds.length + ' টি অর্ডার কুরিয়ারে পাঠানো হবে');
        $('#ucm_preview_cod').text(totalCod > 0 ? '৳' + totalCod.toLocaleString() : (orderIds.length + ' টি অর্ডার'));

        // Pick preferred or first active courier
        var $targetCard = null;
        if (preferredCourier) {
            $targetCard = $('.ucm-courier-card[data-courier="' + preferredCourier + '"]:not(.disabled)');
        }
        if (!$targetCard || $targetCard.length === 0) {
            $targetCard = $('.ucm-courier-card:not(.disabled)').first();
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
        $zone.empty().append('<option value="">জোন লোড হচ্ছে...</option>');
        $('#ucm_pathao_area').empty().append('<option value="">আগে জোন সিলেক্ট করুন</option>');

        if (cityId) {
            $.get("{{ route('pathaocity') }}", { city_id: cityId }, function(data){
                $zone.empty().append('<option value="">সিলেক্ট জোন...</option>');
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
        $area.empty().append('<option value="">এরিয়া লোড হচ্ছে...</option>');

        if (zoneId) {
            $.get("{{ route('pathaozone') }}", { zone_id: zoneId }, function(data){
                $area.empty().append('<option value="">সিলেক্ট এরিয়া...</option>');
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

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> কুরিয়ার বুকিং হচ্ছে...');

        $.ajax({
            url: $form.attr('action'),
            type: "POST",
            data: $form.serialize(),
            dataType: "json",
            success: function(res) {
                $btn.prop('disabled', false).html(originalBtnHtml);
                if (res.status === 'success') {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.message || 'কুরিয়ার বুকিং সফল হয়েছে!', 'বুকিং সফল');
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
                        toastr.error(res.message || 'বুকিং ব্যর্থ হয়েছে', 'ত্রুটি');
                    }
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalBtnHtml);
                var msg = xhr.responseJSON?.message || 'কুরিয়ার বুকিং করার সময় ত্রুটি হয়েছে';
                if (typeof toastr !== 'undefined') {
                    toastr.error(msg, 'ত্রুটি');
                } else {
                    alert(msg);
                }
            }
        });
    });
})();
</script>
