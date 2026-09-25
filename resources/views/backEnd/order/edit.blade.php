@extends('backEnd.layouts.master')
@php
    $editDistricts = collect($districts ?? []);
    $editUpazilas = collect($upazilas ?? []);
    $shipLoc = $shippinginfo;
    $admEdDiv = (int) ($shipLoc->division_id ?? 0);
    $admEdDist = (int) ($shipLoc->district_id ?? 0);
    $admEdUp = (int) ($shipLoc->upazila_id ?? 0);

    $posPay = $order->payment;
    $posPayStatus = optional($posPay)->payment_status ?? ($order->payment_status ?? 'pending');
    $statusName = optional($order->status)->name ?? 'N/A';
@endphp
@section('title', 'অর্ডার এডিট #' . $order->invoice_id)

@section('css')
<style>
    body { background: #f1f5f9; }
    .order-edit-shell { padding: 12px 0 30px; }

    .order-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        margin-bottom: 20px;
        overflow: visible;
    }
    .order-card-header {
        padding: 14px 20px;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        border-bottom: 1px solid #e2e8f0;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .order-card-header h6 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .order-card-header h6 i {
        color: #4f46e5;
    }
    .order-card-body {
        padding: 20px;
    }

    /* PRODUCT LIVE SEARCH BAR & DROPDOWN */
    .pos-search-container {
        position: relative;
        margin-bottom: 16px;
    }
    .pos-search-input-wrap {
        position: relative;
    }
    .pos-search-input-wrap .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 15px;
        pointer-events: none;
    }
    .pos-search-input {
        height: 46px;
        padding-left: 42px;
        padding-right: 40px;
        font-size: 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        transition: all 0.2s ease;
        background: #f8fafc;
    }
    .pos-search-input:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
    }
    .pos-search-clear {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        cursor: pointer;
        display: none;
        padding: 4px;
    }
    .pos-search-clear:hover {
        color: #ef4444;
    }

    /* SEARCH RESULTS DROPDOWN */
    .pos-search-results-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.16);
        max-height: 380px;
        overflow-y: auto;
        z-index: 1050;
        display: none;
    }
    .pos-search-item {
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .pos-search-item:last-child {
        border-bottom: none;
    }
    .pos-search-item:hover, .pos-search-item.active {
        background: #f0f4ff;
    }
    .pos-search-item-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-grow: 1;
        min-width: 0;
    }
    .pos-search-thumb {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .pos-search-info {
        min-width: 0;
    }
    .pos-search-name {
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pos-search-meta {
        font-size: 12px;
        color: #64748b;
        display: flex;
        gap: 8px;
        margin-top: 2px;
    }
    .pos-search-stock {
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }
    .stock-in { background: #dcfce7; color: #166534; }
    .stock-out { background: #fee2e2; color: #991b1b; }
    .pos-search-item-right {
        text-align: right;
        flex-shrink: 0;
    }
    .pos-search-price {
        font-size: 14px;
        font-weight: 700;
        color: #4f46e5;
    }

    /* CART TABLE */
    .pos-cart-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 12.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border-bottom: 2px solid #e2e8f0;
        padding: 10px 12px;
    }
    .pos-cart-table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .cart-empty-state {
        padding: 45px 20px;
        text-align: center;
        color: #94a3b8;
    }

    /* FORM STYLES */
    .form-section-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6366f1;
        margin-bottom: 14px;
        padding-bottom: 6px;
        border-bottom: 1.5px solid #e0e7ff;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pos-summary-table td {
        padding: 8px 10px;
        font-size: 13.5px;
    }
    .btn-submit-order {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        padding: 12px 24px;
        border-radius: 10px;
        width: 100%;
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35);
        transition: all 0.2s ease;
    }
    .btn-submit-order:hover {
        opacity: 0.95;
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(79, 70, 229, 0.45);
        color: #fff;
    }
</style>
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid order-edit-shell">

    {{-- TOP BAR --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="mb-0 fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i>অর্ডার এডিট</h4>
                <span class="badge bg-primary px-3 py-1 rounded-pill fs-6">#{{ $order->invoice_id }}</span>
                <span class="badge bg-soft-info text-info border px-2 py-1 rounded-pill">{{ $statusName }}</span>
            </div>
            <span class="text-muted small">তারিখ: {{ $order->created_at->format('d M, Y h:i A') }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.orders', 'pending') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> অর্ডার তালিকা
            </a>
            <a href="{{ route('admin.order.invoice', $order->invoice_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" target="_blank">
                <i class="fas fa-print me-1"></i> ইনভয়েস
            </a>
        </div>
    </div>

    {{-- ================= 1. PRODUCTS & ORDER ITEMS SECTION (FULL WIDTH) ================= --}}
    <div class="order-card">
        <div class="order-card-header">
            <h6><i class="fas fa-box-open"></i> অর্ডারকৃত পণ্যের তালিকা ও নতুন পণ্য যোগ</h6>
            <span class="badge bg-light text-dark border px-3 py-1">মোট আইটেম: {{ Cart::instance('pos_shopping')->count() }}</span>
        </div>
        <div class="order-card-body">

            {{-- LIVE PRODUCT SEARCH BAR --}}
            <div class="pos-search-container">
                <div class="pos-search-input-wrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text"
                           id="pos_product_search_input"
                           class="form-control pos-search-input"
                           placeholder="নতুন পণ্য খুঁজুন ও যোগ করুন (নাম অথবা SKU লিখুন)..."
                           autocomplete="off">
                    <i class="fas fa-times pos-search-clear" id="pos_search_clear_btn" title="ক্লিয়ার করুন"></i>
                </div>

                {{-- AUTOCOMPLETE DROPDOWN RESULTS --}}
                <div class="pos-search-results-dropdown" id="pos_search_dropdown">
                    <div id="pos_search_results_list"></div>
                </div>
            </div>

            {{-- ORDER ITEMS CART TABLE --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle pos-cart-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 55px;" class="text-center">ছবি</th>
                            <th>পণ্যের নাম ও ভ্যারিয়েন্ট</th>
                            <th style="width: 120px;" class="text-center">মূল্য (Price)</th>
                            <th style="width: 120px;" class="text-center">পরিমাণ (Qty)</th>
                            <th style="width: 110px;" class="text-center">ছাড় (Discount)</th>
                            <th style="width: 110px;" class="text-end">মোট মূল্য</th>
                            <th style="width: 50px;" class="text-center">একশন</th>
                        </tr>
                    </thead>
                    <tbody id="cartTable">
                        @if(Cart::instance('pos_shopping')->count() > 0)
                            @include('backEnd.order.cart_table_rows_edit')
                        @else
                            <tr id="cart_empty_row">
                                <td colspan="7">
                                    <div class="cart-empty-state">
                                        <i class="fas fa-shopping-cart d-block fs-1"></i>
                                        <strong>কোনো পণ্য অবশিষ্ট নেই!</strong>
                                        <div class="small text-muted mt-1">উপরের সার্চ বার থেকে পণ্য খুঁজুন এবং ক্লিক করে যোগ করুন।</div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- ================= 2. CUSTOMER & DELIVERY + ORDER SUMMARY (2 COLUMNS) ================= --}}
    <form action="{{route('admin.order.update')}}" method="POST" class="pos_form" data-parsley-validate="" enctype="multipart/form-data" id="pos_order_form" novalidate>
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <input type="hidden" name="coupon_code" value="{{ Session::get('pos_coupon_code', '') }}">

        <div class="row g-3">
            {{-- LEFT: CUSTOMER & DELIVERY INFO --}}
            <div class="col-lg-7">
                <div class="order-card h-100">
                    <div class="order-card-header">
                        <h6><i class="fas fa-user-check"></i> কাস্টমার ও ডেলিভারি তথ্য</h6>
                    </div>
                    <div class="order-card-body">

                        <div class="form-section-title">
                            <i class="fas fa-address-card"></i> গ্রাহক তথ্য
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">কাস্টমারের নাম <span class="text-danger">*</span></label>
                                <input type="text"
                                       id="name"
                                       class="form-control form-control-sm @error('name') is-invalid @enderror"
                                       placeholder="কাস্টমারের পূর্ণ নাম"
                                       name="name"
                                       value="{{ old('name', $shippinginfo->name ?? optional($order->customer)->name) }}"
                                       required>
                                @error('name')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">মোবাইল নম্বর <span class="text-danger">*</span></label>
                                <input type="number"
                                       id="phone"
                                       class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                       placeholder="01XXXXXXXXX"
                                       name="phone"
                                       value="{{ old('phone', $shippinginfo->phone ?? optional($order->customer)->phone) }}"
                                       required>
                                @error('phone')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark">সম্পূর্ণ ঠিকানা / ডেলিভারির স্থান (ঐচ্ছিক)</label>
                                <input type="text"
                                       id="address"
                                       class="form-control form-control-sm @error('address') is-invalid @enderror"
                                       placeholder="কোথায় ডেলিভারি দিবেন (বাসা/হোল্ডিং, রোড, এলাকা ইত্যাদি বিস্তারিত ঠিকানা)"
                                       name="address"
                                       value="{{ old('address', $shippinginfo->address ?? '') }}">
                                @error('address')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>

                        <div class="form-section-title">
                            <i class="fas fa-map-marker-alt"></i> ডেলিভারি লোকেশন ও এরিয়া
                        </div>

                        <div class="mb-3">
                            @include('frontEnd.layouts.partials.delivery_location_modal', [
                                'prefix' => 'pos_edit',
                                'divisions' => $divisions,
                                'selectedDivisionId' => old('division_id', $admEdDiv),
                                'selectedDistrictId' => old('district_id', $admEdDist),
                                'selectedUpazilaId'  => old('upazila_id', $admEdUp)
                            ])
                            @error('division_id')<span class="text-danger small d-block"><strong>{{ $message }}</strong></span>@enderror
                            @error('district_id')<span class="text-danger small d-block"><strong>{{ $message }}</strong></span>@enderror
                            @error('upazila_id')<span class="text-danger small d-block"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-dark">অর্ডার নোট</label>
                            <textarea name="note" class="form-control form-control-sm" rows="2" placeholder="অর্ডারের বিশেষ নির্দেশনা...">{{ old('note', $order->note ?? $order->order_note ?? '') }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            {{-- RIGHT: SUMMARY & UPDATE --}}
            <div class="col-lg-5">
                <div class="order-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="order-card-header">
                            <h6><i class="fas fa-file-invoice-dollar"></i> অর্ডার হিসাব ও আপডেট</h6>
                        </div>
                        <div class="order-card-body">

                            {{-- SUMMARY TABLE --}}
                            <table class="table table-borderless pos-summary-table mb-3" id="cart_details">
                                @include('backEnd.order.cart_details_edit')
                            </table>

                        </div>
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div class="p-3 bg-light border-top rounded-bottom">
                        <button type="submit" class="btn btn-submit-order d-flex align-items-center justify-content-center gap-2" id="pos_submit_btn">
                            <i class="fas fa-sync-alt fs-5"></i>
                            <span>অর্ডার আপডেট করুন</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $(".select2").select2();
    });

    // -------- CART CONTENT & DETAILS AJAX RELOADERS ----------
    function reloadCartViews() {
        $.ajax({
            type: "GET",
            url: "{{route('admin.order.cart_content')}}?layout=edit",
            dataType: "html",
            success: function (cartHtml) {
                if ($.trim(cartHtml) === "") {
                    $("#cartTable").html('<tr id="cart_empty_row"><td colspan="7"><div class="cart-empty-state"><i class="fas fa-shopping-cart d-block fs-1"></i><strong>কোনো পণ্য অবশিষ্ট নেই!</strong><div class="small text-muted mt-1">উপরের সার্চ বার থেকে পণ্য খুঁজুন এবং ক্লিক করে যোগ করুন।</div></div></td></tr>');
                } else {
                    $("#cartTable").html(cartHtml);
                }
            },
        });

        $.ajax({
            type: "GET",
            url: "{{route('admin.order.cart_details')}}?layout=edit&order_id={{ $order->id }}",
            dataType: "html",
            success: function (detailsHtml) {
                $("#cart_details").html(detailsHtml);
            },
        });
    }

    // -------- LIVE PRODUCT SEARCH DROPDOWN ----------
    var searchTimer = null;
    var $searchInput = $("#pos_product_search_input");
    var $searchDropdown = $("#pos_search_dropdown");
    var $searchResultsList = $("#pos_search_results_list");
    var $searchClearBtn = $("#pos_search_clear_btn");

    $searchInput.on("input focus", function () {
        var keyword = $.trim($(this).val());
        if (keyword.length > 0) {
            $searchClearBtn.show();
        } else {
            $searchClearBtn.hide();
        }

        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            if (keyword.length === 0) {
                $searchDropdown.hide();
                $searchResultsList.empty();
                return;
            }

            $searchResultsList.html('<div class="p-3 text-center text-muted"><i class="fas fa-spinner fa-spin me-1"></i> পণ্য খোঁজা হচ্ছে...</div>');
            $searchDropdown.show();

            $.ajax({
                type: "GET",
                url: "{{ route('admin.order.product_search') }}",
                data: { keyword: keyword },
                dataType: "json",
                success: function (products) {
                    if (!products || products.length === 0) {
                        $searchResultsList.html('<div class="p-3 text-center text-muted"><i class="fas fa-info-circle me-1"></i> কোনো পণ্য পাওয়া যায়নি</div>');
                        return;
                    }

                    var html = "";
                    products.forEach(function (p) {
                        var stockBadge = p.stock > 0
                            ? '<span class="pos-search-stock stock-in"><i class="fas fa-check-circle me-1"></i>স্টক: ' + p.stock + '</span>'
                            : '<span class="pos-search-stock stock-out"><i class="fas fa-times-circle me-1"></i>স্টক শেষ</span>';

                        var codeText = p.product_code ? '<span class="badge bg-light text-secondary border">SKU: ' + p.product_code + '</span>' : '';

                        html += '<div class="pos-search-item pos-select-product" data-id="' + p.id + '">';
                        html += '  <div class="pos-search-item-left">';
                        html += '    <img src="' + p.image + '" class="pos-search-thumb" alt="">';
                        html += '    <div class="pos-search-info">';
                        html += '      <div class="pos-search-name">' + p.name + '</div>';
                        html += '      <div class="pos-search-meta">' + codeText + stockBadge + '</div>';
                        html += '    </div>';
                        html += '  </div>';
                        html += '  <div class="pos-search-item-right">';
                        html += '    <div class="pos-search-price">৳' + parseFloat(p.price).toFixed(2) + '</div>';
                        html += '    <button type="button" class="btn btn-sm btn-primary rounded-pill py-0 px-2 mt-1" style="font-size: 11px;">+ যোগ করুন</button>';
                        html += '  </div>';
                        html += '</div>';
                    });

                    $searchResultsList.html(html);
                },
                error: function () {
                    $searchResultsList.html('<div class="p-3 text-center text-danger">অনুসন্ধানে ত্রুটি হয়েছে</div>');
                }
            });
        }, 220);
    });

    $searchClearBtn.on("click", function () {
        $searchInput.val("").focus();
        $searchClearBtn.hide();
        $searchDropdown.hide();
        $searchResultsList.empty();
    });

    $(document).on("click", function (e) {
        if (!$(e.target).closest(".pos-search-container").length) {
            $searchDropdown.hide();
        }
    });

    // -------- PRODUCT CLICK -> ADD TO CART ----------
    $(document).on("click", ".pos-select-product", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: { id: id },
                url: "{{route('admin.order.cart_add')}}",
                dataType: "json",
                success: function () {
                    reloadCartViews();
                    if (typeof toastr !== 'undefined') {
                        toastr.success('পণ্য কার্টে যুক্ত হয়েছে।');
                    }
                    $searchInput.val("");
                    $searchClearBtn.hide();
                    $searchDropdown.hide();
                },
            });
        }
    });

    // -------- UNIT PRICE / DISCOUNT LIVE UPDATE ----------
    var priceDiscountTimer = null;
    $(document).on("input change", ".cart-price-input, .cart-discount-input", function () {
        var $row = $(this).closest("tr");
        var rowId = $row.data("row-id") || $(this).data("id");
        var price = parseFloat($row.find(".cart-price-input").val()) || 0;
        var discount = parseFloat($row.find(".cart-discount-input").val()) || 0;

        $row.find(".line-discount-hidden").val(discount);

        clearTimeout(priceDiscountTimer);
        priceDiscountTimer = setTimeout(function () {
            $.ajax({
                type: "POST",
                url: "{{ route('admin.order.cart.price_discount_update') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: rowId,
                    price: price,
                    discount: discount
                },
                dataType: "json",
                success: function () {
                    reloadCartViews();
                }
            });
        }, 400);
    });

    // -------- CART QTY + / - ----------
    $(document).on("click", ".cart_increment", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        var qty = $(this).val();
        if (id) {
            $.ajax({
                cache: false,
                data: { id: id, qty: qty },
                type: "GET",
                url: "{{route('admin.order.cart_increment')}}",
                dataType: "json",
                success: function () {
                    reloadCartViews();
                },
            });
        }
    });

    $(document).on("click", ".cart_decrement", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        var qty = $(this).val();
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: { id: id, qty: qty },
                url: "{{route('admin.order.cart_decrement')}}",
                dataType: "json",
                success: function () {
                    reloadCartViews();
                },
            });
        }
    });

    // -------- CART REMOVE ----------
    $(document).on("click", ".cart_remove", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: { id: id },
                url: "{{route('admin.order.cart_remove')}}",
                dataType: "json",
                success: function () {
                    reloadCartViews();
                    if (typeof toastr !== 'undefined') {
                        toastr.info('পণ্যটি সরানো হয়েছে।');
                    }
                },
            });
        }
    });

    // -------- SIZE / COLOR VARIANT UPDATE ----------
    function updateCartVariant(rowId, productId, sizeId, colorId) {
        var $row = $('tr[data-row-id="'+rowId+'"]');
        if (!$row.length) $row = $('.cart-size-selector[data-id="'+rowId+'"]').closest('tr');
        var $sizeSelect = $row.find('.cart-size-selector');
        var $colorSelect = $row.find('.cart-color-selector');
        var sId = sizeId !== undefined ? sizeId : ($sizeSelect.length ? $sizeSelect.val() : '');
        var cId = colorId !== undefined ? colorId : ($colorSelect.length ? $colorSelect.val() : '');
        var pid = productId || $row.data('product-id') || '';

        $.ajax({
            cache: false,
            type: "GET",
            data: { id: rowId, product_id: pid, size_id: sId || '', color_id: cId || '' },
            url: "{{ route('admin.order.cart.update') }}",
            dataType: "json",
            success: function () {
                reloadCartViews();
            },
        });
    }

    $(document).on("change", ".cart-size-selector", function () {
        var rowId = $(this).data("id");
        var productId = $(this).data("product-id");
        var sizeId = $(this).val();
        updateCartVariant(rowId, productId, sizeId, undefined);
    });

    $(document).on("change", ".cart-color-selector", function () {
        var rowId = $(this).data("id");
        var productId = $(this).data("product-id");
        var colorId = $(this).val();
        updateCartVariant(rowId, productId, undefined, colorId);
    });

    // -------- DELIVERY LOCATION SYNC & SHIPPING ----------
    // Sync shipping charge when location modal selects district
    document.addEventListener('deliveryLocationSelected', function (e) {
        if (e.detail && e.detail.prefix === 'pos_edit') {
            var distId = e.detail.district ? e.detail.district.id : null;
            if (distId) {
                $.ajax({
                    type: "GET",
                    data: { id: distId },
                    url: "{{ route('admin.order.cart_shipping') }}",
                    dataType: "json",
                    complete: function () {
                        reloadCartViews();
                    }
                });
            }
        }
    });

    $(document).on("change", "#pos_edit_district_id", function () {
        var id = $(this).val();
        if (id) {
            $.ajax({
                type: "GET",
                data: { id: id },
                url: "{{ route('admin.order.cart_shipping') }}",
                dataType: "json",
                complete: function () {
                    reloadCartViews();
                }
            });
        }
    });

    // -------- FORM VALIDATION & SUBMISSION ----------
    function showFieldError($el, message, title) {
        if (typeof toastr !== 'undefined') {
            toastr.error(message, title || 'তথ্য আবশ্যক');
        } else {
            alert(message);
        }
        $el.addClass('is-invalid');
        $el.focus();
    }

    function clearFieldError($el) {
        $el.removeClass('is-invalid');
    }

    var posFormSubmitting = false;
    $("#pos_order_form").on("submit", function (e) {
        if (posFormSubmitting) return;
        e.preventDefault();
        var form = this;

        // ১. কার্ট চেক
        if ($("#cartTable tr").not("#cart_empty_row").length === 0) {
            if (typeof toastr !== 'undefined') {
                toastr.error('আপনার কার্ট খালি! অনুগ্রহ করে উপরের সার্চ বার থেকে পণ্য যোগ করুন।', 'কার্ট খালি');
            } else {
                alert('আপনার কার্ট খালি! অনুগ্রহ করে উপরের সার্চ বার থেকে পণ্য যোগ করুন।');
            }
            $searchInput.focus();
            return false;
        }

        // ২. কাস্টমার ও ডেলিভারি তথ্য ফিল্ড ভ্যালিডেশন
        var $name = $("#name");
        var $phone = $("#phone");
        var nameVal = $.trim($name.val());
        var phoneVal = $.trim($phone.val());

        if (!nameVal) {
            showFieldError($name, 'কাস্টমারের নাম প্রদান করুন।');
            return false;
        } else {
            clearFieldError($name);
        }

        if (!phoneVal) {
            showFieldError($phone, 'কাস্টমারের মোবাইল নম্বর প্রদান করুন।');
            return false;
        } else if (phoneVal.length < 11) {
            showFieldError($phone, 'সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন।', 'ভুল মোবাইল নম্বর');
            return false;
        } else {
            clearFieldError($phone);
        }

        // ৩. ডেলিভারি এরিয়া (বিভাগ > জেলা > থানা) ভ্যালিডেশন
        var divVal = $("#pos_edit_division_id").val();
        var distVal = $("#pos_edit_district_id").val();
        var upzVal = $("#pos_edit_upazila_id").val();

        if (!divVal || !distVal || !upzVal) {
            if (typeof toastr !== 'undefined') {
                toastr.error('অনুগ্রহ করে কাস্টমারের ডেলিভারি এরিয়া (বিভাগ > জেলা > থানা) নির্বাচন করুন', 'এরিয়া নির্বাচন');
            } else {
                alert('অনুগ্রহ করে কাস্টমারের ডেলিভারি এরিয়া (বিভাগ > জেলা > থানা) নির্বাচন করুন');
            }
            $('#pos_edit_delivery_area_trigger').addClass('is-invalid');
            $('#pos_edit_delivery_area_trigger').trigger('click');
            return false;
        }

        // ৪. সাবমিট
        posFormSubmitting = true;
        form.submit();
    });

    $(document).on("input change", "#name, #phone, #address", function () {
        if ($.trim($(this).val())) {
            $(this).removeClass("is-invalid");
        }
    });

</script>
@endsection
