<!-- ========================================================= -->
<!-- Universal Partial Settlement Modal (8 -> 9 / 10 / 11)     -->
<!-- ========================================================= -->
<div class="modal fade oi-modal" id="partialSettlementModal" tabindex="-1" aria-labelledby="partialSettlementModalLabel" aria-hidden="true" data-bs-backdrop="static" style="z-index: 1065 !important;">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="z-index: 1066 !important; max-width: 800px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            
            {{-- Header --}}
            <div class="modal-header py-2.5 px-3.5 text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e293b, #0f172a); border-bottom: 1px solid #334155;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-boxes me-1"></i> সেটেলমেন্ট</span>
                    <h6 class="modal-title m-0 text-white fw-bold" id="partialSettlementModalLabel" style="font-size: 14.5px;">
                        আংশিক ডেলিভারি সেটেলমেন্ট
                    </h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="partialSettlementForm" method="POST" action="{{ route('admin.order.settle_partial') }}">
                @csrf
                <input type="hidden" name="order_id" id="ps_order_id" value="">

                <div class="modal-body p-3" style="background: #f8fafc; max-height: 72vh; overflow-y: auto;">
                    
                    {{-- 1. Order Info Bar --}}
                    <div class="p-2.5 bg-white rounded-3 border shadow-sm mb-2.5">
                        <div class="row g-2 align-items-center text-secondary" style="font-size: 12px;">
                            <div class="col-sm-3 col-6">
                                <span>ইনভয়েস:</span>
                                <span class="badge bg-primary-subtle text-primary fw-bold" id="ps_invoice_badge">#0000</span>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span>গ্রাহক:</span>
                                <strong id="ps_customer_name" class="text-dark text-truncate d-inline-block align-bottom" style="max-width: 120px;">—</strong>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span>ফোন:</span>
                                <span id="ps_customer_phone" class="fw-semibold text-dark">—</span>
                            </div>
                            <div class="col-sm-3 col-6 text-sm-end">
                                <span>মূল বিল:</span>
                                <strong class="text-success fs-6">৳<span id="ps_original_amount">0</span></strong>
                            </div>
                        </div>
                    </div>

                    {{-- 2. 3 Status Tabs in Serial: 9 -> 10 -> 11 --}}
                    <div class="mb-2.5">
                        <div class="row g-2">
                            {{-- Tab 9: Partial (Full Received) --}}
                            <div class="col-4">
                                <label class="ps-clean-card h-100 w-100 p-2 rounded-3 border text-start d-block cursor-pointer" for="ps_opt_9">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 10.5px;">৯. Full Received</span>
                                        <input class="form-check-input mt-0" type="radio" name="target_status" id="ps_opt_9" value="9">
                                    </div>
                                    <div class="fw-bold text-dark" style="font-size: 12px;">সব পণ্য গ্রহণ</div>
                                    <small class="text-muted d-block" style="font-size: 10.5px; line-height: 1.2;">টাকা কম/ছাড়</small>
                                </label>
                            </div>

                            {{-- Tab 10: Partial (Item Received) --}}
                            <div class="col-4">
                                <label class="ps-clean-card h-100 w-100 p-2 rounded-3 border text-start d-block cursor-pointer active" for="ps_opt_10">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold" style="font-size: 10.5px;">১০. Item Received</span>
                                        <input class="form-check-input mt-0" type="radio" name="target_status" id="ps_opt_10" value="10" checked>
                                    </div>
                                    <div class="fw-bold text-dark" style="font-size: 12px;">আইটেম ভিত্তিক গ্রহণ</div>
                                    <small class="text-muted d-block" style="font-size: 10.5px; line-height: 1.2;">কিছু গ্রহণ ও কিছু ফেরত</small>
                                </label>
                            </div>

                            {{-- Tab 11: Partial (Delivery Charge Only) --}}
                            <div class="col-4">
                                <label class="ps-clean-card h-100 w-100 p-2 rounded-3 border text-start d-block cursor-pointer" for="ps_opt_11">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size: 10.5px;">১১. Charge Only</span>
                                        <input class="form-check-input mt-0" type="radio" name="target_status" id="ps_opt_11" value="11">
                                    </div>
                                    <div class="fw-bold text-dark" style="font-size: 12px;">শুধু চার্জ আদায়</div>
                                    <small class="text-muted d-block" style="font-size: 10.5px; line-height: 1.2;">কোনো পণ্য নেয়নি</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Section for Option 9 (Full Received) --}}
                    <div id="ps_section_9" class="ps-dynamic-section p-2.5 bg-white rounded-3 border shadow-sm mb-2.5" style="display: none;">
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-dark mb-1">প্রাপ্ত মোট টাকা (৳): <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light fw-bold text-success">৳</span>
                                    <input type="number" step="0.01" min="0" name="collected_amount_9" id="ps_collected_amount_9" class="form-control form-control-sm fw-bold text-success fs-6">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 rounded bg-light border text-muted small" style="font-size: 11.5px;">
                                    মূল বিল: <strong>৳<span class="ps-calc-orig">0</span></strong> | ছাড়: <strong class="text-danger" id="ps_shortage_9">৳0</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Section for Option 10 (Item Received) --}}
                    <div id="ps_section_10" class="ps-dynamic-section p-2.5 bg-white rounded-3 border shadow-sm mb-2.5">
                        <div class="table-responsive mb-2">
                            <table class="table table-sm align-middle mb-0" style="font-size: 12px;">
                                <thead style="background: #f1f5f9;">
                                    <tr>
                                        <th class="ps-2 py-1.5 text-secondary">পণ্য</th>
                                        <th class="text-center py-1.5 text-secondary" style="width: 75px;">মূল্য</th>
                                        <th class="text-center py-1.5 text-secondary" style="width: 60px;">অর্ডার</th>
                                        <th class="text-center py-1.5 text-secondary" style="width: 115px;">ডেলিভারি সংখ্যা</th>
                                        <th class="text-center py-1.5 text-secondary" style="width: 90px;">ফেরত</th>
                                        <th class="text-end pe-2 py-1.5 text-secondary" style="width: 85px;">মোট</th>
                                    </tr>
                                </thead>
                                <tbody id="ps_items_tbody">
                                    {{-- Loaded via JS --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Delivery Charge Switch + Editable Collected Amount --}}
                        <div class="p-2 rounded-2 bg-light border row g-2 align-items-center">
                            <div class="col-sm-5">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input cursor-pointer" type="checkbox" id="ps_include_shipping" name="include_shipping" value="1" checked>
                                    <label class="form-check-label fw-semibold text-dark small cursor-pointer" for="ps_include_shipping" style="font-size: 11.5px;">
                                        ডেলিভারি চার্জ (৳<span id="ps_ship_charge_label">0</span>)
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-7 d-flex align-items-center justify-content-sm-end gap-1.5">
                                <span class="text-muted small text-nowrap" style="font-size: 11.5px;">প্রাপ্ত টাকা (৳):</span>
                                <input type="number" step="0.01" min="0" name="collected_amount_10" id="ps_collected_amount_10" class="form-control form-control-sm fw-bold text-success text-end" style="max-width: 110px; font-size: 13px;" value="0">
                                <button type="button" class="btn btn-outline-primary btn-sm px-1.5 py-0.5" id="ps_sync_calc_10" title="হিসাব অনুযায়ী রিসেট করুন" style="font-size: 11px;">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Section for Option 11 (Delivery Charge Only) --}}
                    <div id="ps_section_11" class="ps-dynamic-section p-2.5 bg-white rounded-3 border shadow-sm mb-2.5" style="display: none;">
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-dark mb-1">প্রাপ্ত ডেলিভারি চার্জ (৳): <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light fw-bold text-danger">৳</span>
                                    <input type="number" step="0.01" min="0" name="delivery_charge_paid_11" id="ps_delivery_charge_paid_11" class="form-control form-control-sm fw-bold text-danger fs-6">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="alert alert-warning py-1.5 px-2 mb-0 small" style="font-size: 11px;">
                                    <i class="fas fa-info-circle me-1"></i> সব পণ্য আপনার স্টকে ফেরত (Restore) যুক্ত হবে।
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Note --}}
                    <div class="p-2 bg-white rounded-3 border shadow-sm">
                        <input type="text" class="form-control form-control-sm" name="note" id="ps_note" placeholder="মন্তব্য / নোট (ঐচ্ছিক)..." style="font-size: 12px;">
                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer py-2 px-3 bg-white border-top d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">
                        বাতিল
                    </button>
                    <button type="submit" id="ps_submit_btn" class="btn btn-sm btn-success px-3.5 py-1.5 fw-bold shadow-sm d-flex align-items-center gap-1.5">
                        <i class="fas fa-check-circle"></i> 
                        <span>সেটেলমেন্ট কনফার্ম করুন</span>
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
    transition: all 0.15s ease-in-out;
    border-color: #e2e8f0 !important;
}
.ps-clean-card:hover {
    border-color: #cbd5e1 !important;
    background: #f8fafc;
}
.ps-clean-card.active {
    border-color: #3b82f6 !important;
    background: #eff6ff !important;
    box-shadow: 0 0 0 1.5px rgba(59, 130, 246, 0.25);
}
.ps-qty-btn {
    width: 24px;
    height: 24px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    border-radius: 4px;
}
</style>
