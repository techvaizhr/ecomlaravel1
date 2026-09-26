<!-- ========================================================= -->
<!-- Universal Partial Settlement Modal (8 -> 9 / 10 / 11)     -->
<!-- ========================================================= -->
<div class="modal fade" id="partialSettlementModal" tabindex="-1" aria-labelledby="partialSettlementModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-gradient py-2.5 px-3 text-white" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <h5 class="modal-title m-0 fw-bold d-flex align-items-center text-white" id="partialSettlementModalLabel" style="font-size: 15px;">
                    <i class="fas fa-boxes me-2"></i> আংশিক অর্ডার সেটেলমেন্ট (Partial Settlement)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="partialSettlementForm" method="POST" action="{{ route('admin.order.settle_partial') }}">
                @csrf
                <input type="hidden" name="order_id" id="ps_order_id" value="">

                <div class="modal-body p-3 bg-light" style="max-height: 75vh; overflow-y: auto;">
                    {{-- Order Quick Summary Bar --}}
                    <div class="card border-0 shadow-sm rounded-3 mb-3" style="background: #fff;">
                        <div class="card-body p-2.5">
                            <div class="row g-2 align-items-center text-muted" style="font-size: 12.5px;">
                                <div class="col-sm-3 col-6">
                                    <div class="fw-semibold text-dark">ইনভয়েস:</div>
                                    <span class="badge bg-primary-subtle text-primary fw-bold" id="ps_invoice_badge">#0000</span>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <div class="fw-semibold text-dark">গ্রাহক:</div>
                                    <span id="ps_customer_name" class="text-truncate d-block">—</span>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <div class="fw-semibold text-dark">ফোন:</div>
                                    <span id="ps_customer_phone">—</span>
                                </div>
                                <div class="col-sm-3 col-6 text-sm-end">
                                    <div class="fw-semibold text-dark">অর্ডারের মোট মূল্য:</div>
                                    <span class="fw-bold text-success fs-6">৳<span id="ps_original_amount">0</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Partial Decision Type Selector (3 Cards) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-2" style="font-size: 13px;">
                            <i class="fas fa-hand-pointer text-warning me-1"></i> কোন ধরনের আংশিক ডেলিভারি হয়েছে তা নির্বাচন করুন: <span class="text-danger">*</span>
                        </label>

                        <div class="row g-2">
                            {{-- Option 9: Full Received --}}
                            <div class="col-md-4">
                                <label class="ps-option-card h-100 w-100 p-2.5 border rounded-3 d-flex flex-column justify-content-between cursor-pointer" for="ps_opt_9" style="cursor: pointer; background: #fff; transition: all 0.2s;">
                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <input class="form-check-input me-2 mt-0" type="radio" name="target_status" id="ps_opt_9" value="9">
                                            <span class="badge bg-success" style="font-size: 10.5px;">৯ - Full Received</span>
                                        </div>
                                        <div class="fw-bold text-dark" style="font-size: 12.5px;">সব আইটেম গ্রহণ (টাকা আংশিক)</div>
                                        <div class="text-muted small mt-1" style="font-size: 11px;">কাস্টমার সব পণ্য নিয়েছেন, কিন্তু টাকা কম/ছাড় দিয়ে ডেলিভারি সম্পন্ন হয়েছে।</div>
                                    </div>
                                    <div class="mt-2 text-success fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-check-double me-1"></i> সব পণ্যের স্টক কাটা থাকবে
                                    </div>
                                </label>
                            </div>

                            {{-- Option 10: Item Received --}}
                            <div class="col-md-4">
                                <label class="ps-option-card h-100 w-100 p-2.5 border rounded-3 d-flex flex-column justify-content-between cursor-pointer" for="ps_opt_10" style="cursor: pointer; background: #fff; transition: all 0.2s;">
                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <input class="form-check-input me-2 mt-0" type="radio" name="target_status" id="ps_opt_10" value="10" checked>
                                            <span class="badge bg-warning text-dark" style="font-size: 10.5px;">১০ - Item Received</span>
                                        </div>
                                        <div class="fw-bold text-dark" style="font-size: 12.5px;">কিছু আইটেম গ্রহণ ও কিছু ফেরত</div>
                                        <div class="text-muted small mt-1" style="font-size: 11px;">কাস্টমার নির্দিষ্ট কিছু পণ্য নিয়েছেন এবং বাকিগুলো কুরিয়ারে ফেরত পাঠিয়েছেন।</div>
                                    </div>
                                    <div class="mt-2 text-warning text-dark fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-sync-alt me-1"></i> ফেরত পণ্যের স্টক ইনভেন্টরিতে ব্যাক হবে
                                    </div>
                                </label>
                            </div>

                            {{-- Option 11: Delivery Charge Only --}}
                            <div class="col-md-4">
                                <label class="ps-option-card h-100 w-100 p-2.5 border rounded-3 d-flex flex-column justify-content-between cursor-pointer" for="ps_opt_11" style="cursor: pointer; background: #fff; transition: all 0.2s;">
                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <input class="form-check-input me-2 mt-0" type="radio" name="target_status" id="ps_opt_11" value="11">
                                            <span class="badge bg-danger" style="font-size: 10.5px;">১১ - Charge Only</span>
                                        </div>
                                        <div class="fw-bold text-dark" style="font-size: 12.5px;">শুধু ডেলিভারি চার্জ আদায়</div>
                                        <div class="text-muted small mt-1" style="font-size: 11px;">কাস্টমার কোনো পণ্য নেননি, শুধুমাত্র ডেলিভারি চার্জ বাবদ টাকা পাওয়া গেছে।</div>
                                    </div>
                                    <div class="mt-2 text-danger fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-undo-alt me-1"></i> সব পণ্যের স্টক ইনভেন্টরিতে ব্যাক হবে
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 1: Details for Option 9 (Full Received) --}}
                    <div id="ps_section_9" class="ps-dynamic-section card border-0 shadow-sm p-3 mb-3 bg-white" style="display: none;">
                        <h6 class="fw-bold text-success mb-2" style="font-size: 13px;"><i class="fas fa-money-bill-wave me-1"></i> মোট প্রাপ্ত বা আদায়কৃত টাকার পরিমাণ</h6>
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">কাস্টমার থেকে প্রাপ্ত মোট টাকা (৳):</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light fw-bold">৳</span>
                                    <input type="number" step="0.01" min="0" name="collected_amount_9" id="ps_collected_amount_9" class="form-control form-control-sm fw-bold text-success">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 rounded bg-light border text-muted small" style="font-size: 11.5px;">
                                    অর্ডার মূল্য: ৳<span class="ps-calc-orig">0</span> | ছাড়/ঘাটতি: <strong class="text-danger" id="ps_shortage_9">৳0</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: Details for Option 10 (Item Received) --}}
                    <div id="ps_section_10" class="ps-dynamic-section card border-0 shadow-sm p-3 mb-3 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-primary mb-0" style="font-size: 13px;">
                                <i class="fas fa-list-check me-1"></i> কোন পণ্য কতটি ডেলিভারি হয়েছে তা সিলেক্ট করুন:
                            </h6>
                            <span class="badge bg-light text-muted border" style="font-size: 11px;">বাকিগুলো অটোমেটিক স্টকে ফেরত যাবে</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-2" style="font-size: 12px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>পণ্য</th>
                                        <th class="text-center" style="width: 70px;">মূল্য</th>
                                        <th class="text-center" style="width: 60px;">অর্ডার</th>
                                        <th class="text-center" style="width: 100px;">ডেলিভারি সংখ্যা</th>
                                        <th class="text-center" style="width: 90px;">ফেরত সংখ্যা</th>
                                        <th class="text-end" style="width: 90px;">লাইন মোট</th>
                                    </tr>
                                </thead>
                                <tbody id="ps_items_tbody">
                                    {{-- Rendered via JS --}}
                                </tbody>
                            </table>
                        </div>

                        <div class="row g-2 align-items-center mt-1">
                            <div class="col-sm-6">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="ps_include_shipping" name="include_shipping" value="1" checked>
                                    <label class="form-check-label small fw-semibold" for="ps_include_shipping">
                                        ডেলিভারি চার্জ যোগ করুন (৳<span id="ps_ship_charge_label">0</span>)
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <div class="small fw-semibold text-muted">
                                    হিসাবকৃত মোট আদায়: <span class="fs-6 fw-bold text-success">৳<span id="ps_calculated_total_10">0</span></span>
                                </div>
                                <input type="hidden" name="collected_amount_10" id="ps_collected_amount_10" value="0">
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: Details for Option 11 (Delivery Charge Only) --}}
                    <div id="ps_section_11" class="ps-dynamic-section card border-0 shadow-sm p-3 mb-3 bg-white" style="display: none;">
                        <h6 class="fw-bold text-danger mb-2" style="font-size: 13px;"><i class="fas fa-truck-loading me-1"></i> শুধুমাত্র ডেলিভারি চার্জ আদায়</h6>
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">প্রাপ্ত ডেলিভারি চার্জের পরিমাণ (৳):</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light fw-bold">৳</span>
                                    <input type="number" step="0.01" min="0" name="delivery_charge_paid_11" id="ps_delivery_charge_paid_11" class="form-control form-control-sm fw-bold text-danger">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-warning py-1.5 px-2.5 mb-0 small" style="font-size: 11px;">
                                    <i class="fas fa-info-circle me-1"></i> এই অর্ডারের সব পণ্যের সংখ্যা সম্পূর্ণভাবে আপনার স্টকে ফেরত (Restore) যুক্ত হবে।
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Note Field --}}
                    <div class="card border-0 shadow-sm p-2.5 bg-white">
                        <label for="ps_note" class="form-label small fw-semibold text-muted mb-1">
                            <i class="far fa-comment-dots me-1"></i> সেটেলমেন্ট নোট / কুরিয়ার মন্তব্য (ঐচ্ছিক):
                        </label>
                        <textarea class="form-control form-control-sm" name="note" id="ps_note" rows="2" placeholder="যেমন: কাস্টমার ১টি টিশার্ট নিয়েছেন, শাড়ি রিটার্ন করেছেন এবং ৮৫০ টাকা দিয়েছেন..."></textarea>
                    </div>
                </div>

                <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">বাতিল করুন</button>
                    <button type="submit" id="ps_submit_btn" class="btn btn-sm btn-success fw-bold px-3">
                        <i class="fas fa-check-circle me-1"></i> কনফার্ম ও সেটেল করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.ps-option-card:hover {
    border-color: #f59e0b !important;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15);
}
.ps-option-card.active {
    border-color: #d97706 !important;
    background: #fffbeb !important;
    box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
}
</style>
