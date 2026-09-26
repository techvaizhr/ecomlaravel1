<!-- ========================================================= -->
<!-- Universal Partial Settlement Modal (8 -> 9 / 10 / 11)     -->
<!-- ========================================================= -->
<div class="modal fade oi-modal" id="partialSettlementModal" tabindex="-1" aria-labelledby="partialSettlementModalLabel" aria-hidden="true" data-bs-backdrop="static" style="z-index: 1065 !important;">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="z-index: 1066 !important; max-width: 820px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            
            {{-- Clean Modal Header --}}
            <div class="modal-header py-3 px-4 text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e293b, #0f172a); border-bottom: 1px solid #334155;">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-3 bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                        <i class="fas fa-boxes fs-6"></i>
                    </div>
                    <div>
                        <h6 class="modal-title m-0 text-white fw-bold" id="partialSettlementModalLabel" style="font-size: 15px; letter-spacing: -0.2px;">
                            আংশিক ডেলিভারি সেটেলমেন্ট (Partial Settlement)
                        </h6>
                        <small class="text-white-50" style="font-size: 11px;">গ্রাহকের গ্রহণকৃত পণ্য ও প্রাপ্ত টাকার সঠিক হিসাব আপডেট করুন</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="partialSettlementForm" method="POST" action="{{ route('admin.order.settle_partial') }}">
                @csrf
                <input type="hidden" name="order_id" id="ps_order_id" value="">

                <div class="modal-body p-3 p-md-4" style="background: #f8fafc; max-height: 72vh; overflow-y: auto;">
                    
                    {{-- 1. Order Quick Details Pill Bar --}}
                    <div class="p-3 bg-white rounded-3 border shadow-sm mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block" style="font-size: 11px;">ইনভয়েস</span>
                                <span class="badge bg-primary text-white fw-bold px-2 py-1" id="ps_invoice_badge" style="font-size: 12px;">#0000</span>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block" style="font-size: 11px;">গ্রাহক</span>
                                <strong id="ps_customer_name" class="text-dark text-truncate d-block" style="font-size: 13px;">—</strong>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block" style="font-size: 11px;">মোবাইল নম্বর</span>
                                <span id="ps_customer_phone" class="fw-semibold text-secondary" style="font-size: 12.5px;">—</span>
                            </div>
                            <div class="col-sm-3 col-6 text-sm-end">
                                <span class="text-muted small d-block" style="font-size: 11px;">মূল অর্ডার বিল</span>
                                <span class="fw-bold text-success fs-6">৳<span id="ps_original_amount">0</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Clean 3-Option Segmented Cards --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-2 d-flex align-items-center gap-1.5" style="font-size: 13px;">
                            <i class="fas fa-hand-pointer text-primary"></i> 
                            <span>পার্শিয়াল ডেলিভারির ধরন সিলেক্ট করুন:</span>
                        </label>

                        <div class="row g-2.5">
                            {{-- Option 10: Item Received (Recommended Default) --}}
                            <div class="col-md-4">
                                <label class="ps-clean-card h-100 w-100 p-3 rounded-3 border d-flex flex-column justify-content-between" for="ps_opt_10">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-primary text-white px-2 py-1" style="font-size: 11px;">অপশন ১০</span>
                                            <input class="form-check-input mt-0" type="radio" name="target_status" id="ps_opt_10" value="10" checked style="cursor: pointer;">
                                        </div>
                                        <div class="fw-bold text-dark mb-1" style="font-size: 13px;">আইটেম ভিত্তিক গ্রহণ</div>
                                        <p class="text-muted mb-0" style="font-size: 11.5px; line-height: 1.35;">নির্দিষ্ট কিছু পণ্য নিয়েছেন এবং বাকি পণ্য কুরিয়ারে ফেরত এসেছে।</p>
                                    </div>
                                    <div class="mt-2.5 pt-2 border-top text-primary fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-sync-alt me-1"></i> ফেরত পণ্য স্টকে ব্যাক হবে
                                    </div>
                                </label>
                            </div>

                            {{-- Option 9: Full Received --}}
                            <div class="col-md-4">
                                <label class="ps-clean-card h-100 w-100 p-3 rounded-3 border d-flex flex-column justify-content-between" for="ps_opt_9">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-success text-white px-2 py-1" style="font-size: 11px;">অপশন ৯</span>
                                            <input class="form-check-input mt-0" type="radio" name="target_status" id="ps_opt_9" value="9" style="cursor: pointer;">
                                        </div>
                                        <div class="fw-bold text-dark mb-1" style="font-size: 13px;">সব পণ্য গ্রহণ (টাকা কম)</div>
                                        <p class="text-muted mb-0" style="font-size: 11.5px; line-height: 1.35;">কাস্টমার সব পণ্য নিয়েছেন, কিন্তু টাকা কম দিয়ে ডেলিভারি সম্পন্ন হয়েছে।</p>
                                    </div>
                                    <div class="mt-2.5 pt-2 border-top text-success fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-check-double me-1"></i> সব পণ্যের স্টক কাটা থাকবে
                                    </div>
                                </label>
                            </div>

                            {{-- Option 11: Delivery Charge Only --}}
                            <div class="col-md-4">
                                <label class="ps-clean-card h-100 w-100 p-3 rounded-3 border d-flex flex-column justify-content-between" for="ps_opt_11">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-danger text-white px-2 py-1" style="font-size: 11px;">অপশন ১১</span>
                                            <input class="form-check-input mt-0" type="radio" name="target_status" id="ps_opt_11" value="11" style="cursor: pointer;">
                                        </div>
                                        <div class="fw-bold text-dark mb-1" style="font-size: 13px;">শুধু ডেলিভারি চার্জ আদায়</div>
                                        <p class="text-muted mb-0" style="font-size: 11.5px; line-height: 1.35;">পণ্য নেয়নি, শুধুমাত্র ডেলিভারি চার্জ আদায় হয়েছে।</p>
                                    </div>
                                    <div class="mt-2.5 pt-2 border-top text-danger fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-undo-alt me-1"></i> সব পণ্য স্টকে ব্যাক হবে
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Dynamic Section for Option 10 (Item Received) --}}
                    <div id="ps_section_10" class="ps-dynamic-section p-3 bg-white rounded-3 border shadow-sm mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2.5 pb-2 border-bottom">
                            <h6 class="fw-bold text-dark m-0" style="font-size: 13.5px;">
                                <i class="fas fa-list-ol text-primary me-1"></i> ডেলিভারিকৃত পণ্যের সংখ্যা উল্লেখ করুন:
                            </h6>
                            <span class="badge bg-light text-secondary border" style="font-size: 11px;">বাকিগুলো অটো স্টকে ফেরত যাবে</span>
                        </div>

                        <div class="table-responsive mb-2">
                            <table class="table table-sm align-middle mb-0" style="font-size: 12.5px;">
                                <thead style="background: #f1f5f9;">
                                    <tr>
                                        <th class="ps-2 py-2 text-secondary" style="border-top-left-radius: 6px; border-bottom-left-radius: 6px;">পণ্য</th>
                                        <th class="text-center py-2 text-secondary" style="width: 85px;">মূল্য</th>
                                        <th class="text-center py-2 text-secondary" style="width: 70px;">অর্ডার</th>
                                        <th class="text-center py-2 text-secondary" style="width: 130px;">ডেলিভারি সংখ্যা</th>
                                        <th class="text-center py-2 text-secondary" style="width: 100px;">ফেরত অবস্থা</th>
                                        <th class="text-end pe-2 py-2 text-secondary" style="width: 95px; border-top-right-radius: 6px; border-bottom-right-radius: 6px;">মোট</th>
                                    </tr>
                                </thead>
                                <tbody id="ps_items_tbody">
                                    {{-- Loaded via JS --}}
                                </tbody>
                            </table>
                        </div>

                        <div class="p-2.5 rounded-2 bg-light border d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input cursor-pointer" type="checkbox" id="ps_include_shipping" name="include_shipping" value="1" checked>
                                <label class="form-check-label fw-semibold text-dark small cursor-pointer" for="ps_include_shipping">
                                    ডেলিভারি চার্জ যোগ করুন (৳<span id="ps_ship_charge_label">0</span>)
                                </label>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted small">হিসাবকৃত মোট আদায়:</span>
                                <span class="badge bg-success fs-6 fw-bold px-2.5 py-1">৳<span id="ps_calculated_total_10">0</span></span>
                                <input type="hidden" name="collected_amount_10" id="ps_collected_amount_10" value="0">
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Section for Option 9 (Full Received) --}}
                    <div id="ps_section_9" class="ps-dynamic-section p-3 bg-white rounded-3 border shadow-sm mb-3" style="display: none;">
                        <h6 class="fw-bold text-success mb-2.5" style="font-size: 13.5px;">
                            <i class="fas fa-money-bill-wave me-1"></i> কাস্টমার থেকে প্রাপ্ত টাকার পরিমাণ লিখুন:
                        </h6>
                        <div class="row g-3 align-items-center">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">প্রাপ্ত মোট টাকা (৳): <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light fw-bold text-success">৳</span>
                                    <input type="number" step="0.01" min="0" name="collected_amount_9" id="ps_collected_amount_9" class="form-control form-control-sm fw-bold text-success fs-6">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2.5 rounded bg-light border text-muted small" style="font-size: 12px;">
                                    অর্ডার বিল: <strong>৳<span class="ps-calc-orig">0</span></strong> | ছাড়/ঘাটতি: <strong class="text-danger" id="ps_shortage_9">৳0</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Section for Option 11 (Delivery Charge Only) --}}
                    <div id="ps_section_11" class="ps-dynamic-section p-3 bg-white rounded-3 border shadow-sm mb-3" style="display: none;">
                        <h6 class="fw-bold text-danger mb-2.5" style="font-size: 13.5px;">
                            <i class="fas fa-truck-loading me-1"></i> শুধুমাত্র ডেলিভারি চার্জ আদায়
                        </h6>
                        <div class="row g-3 align-items-center">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">প্রাপ্ত ডেলিভারি চার্জ (৳): <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light fw-bold text-danger">৳</span>
                                    <input type="number" step="0.01" min="0" name="delivery_charge_paid_11" id="ps_delivery_charge_paid_11" class="form-control form-control-sm fw-bold text-danger fs-6">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="alert alert-warning py-2 px-3 mb-0 small" style="font-size: 11.5px;">
                                    <i class="fas fa-info-circle me-1"></i> সব পণ্যের সংখ্যা স্বয়ংক্রিয়ভাবে আপনার স্টকে ফেরত (Restore) যুক্ত হবে।
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Settlement Note --}}
                    <div class="p-2.5 bg-white rounded-3 border shadow-sm">
                        <label for="ps_note" class="form-label small fw-semibold text-muted mb-1 d-flex align-items-center gap-1">
                            <i class="far fa-comment-dots text-secondary"></i> 
                            <span>সেটেলমেন্ট মন্তব্য / নোট (ঐচ্ছিক):</span>
                        </label>
                        <input type="text" class="form-control form-control-sm" name="note" id="ps_note" placeholder="যেমন: কাস্টমার ১টি জামা নিয়েছেন, বাকি ১টি রিটার্ন করেছেন...">
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer py-2.5 px-4 bg-white border-top d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal" style="font-size: 13px; font-weight: 600;">
                        বাতিল
                    </button>
                    <button type="submit" id="ps_submit_btn" class="btn btn-success px-4 py-1.5 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 13.5px; font-weight: 700; border-radius: 8px;">
                        <i class="fas fa-check-circle"></i> 
                        <span>সেটেলমেন্ট সম্পন্ন করুন</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
#partialSettlementModal {
    z-index: 1065 !important;
}
#partialSettlementModal .modal-dialog {
    z-index: 1066 !important;
}
.ps-clean-card {
    cursor: pointer;
    background: #ffffff;
    transition: all 0.2s ease-in-out;
    border-color: #e2e8f0 !important;
}
.ps-clean-card:hover {
    border-color: #cbd5e1 !important;
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
}
.ps-clean-card.active {
    border-color: #3b82f6 !important;
    background: #eff6ff !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}
.ps-qty-btn {
    width: 26px;
    height: 26px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    border-radius: 4px;
}
</style>
