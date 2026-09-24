<!-- ======================================================== -->
<!-- MODAL: IMPORT PRODUCT FROM URL (DARAZ, ALIBABA, ETC.)     -->
<!-- ======================================================== -->
<div class="modal fade" id="importProductModal" tabindex="-1" aria-labelledby="importProductModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            
            {{-- Modal Header --}}
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #0284c7 0%, #2563eb 50%, #4f46e5 100%); padding: 20px 26px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px);">
                        <i class="fe-download-cloud fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="importProductModalLabel">
                            Import Product from URL
                        </h5>
                        <small class="text-white-50" style="font-size: 12.5px;">
                            Daraz, Alibaba, AliExpress, Amazon, eBay অথবা যেকোনো ই-কমার্স লিঙ্ক থেকে প্রোডাক্ট ইমপোর্ট করুন
                        </small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4" style="background: #f8fafc;">
                
                {{-- STEP 1: URL INPUT CARD --}}
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 14px; background: #fff;">
                    <div class="card-body p-3">
                        <label class="form-label fw-bold text-dark mb-2" style="font-size: 13px;">
                            <i class="fe-link text-primary me-1"></i> Product Webpage URL:
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fe-globe"></i></span>
                            <input type="url" id="import_url_input" class="form-control form-control-lg border-start-0 border-end-0" placeholder="https://www.daraz.com.bd/products/... বা https://www.alibaba.com/product-detail/..." style="font-size: 14px;">
                            <button class="btn btn-outline-secondary px-3" type="button" id="btn_paste_url" title="Paste from clipboard">
                                <i class="fe-clipboard me-1"></i> Paste
                            </button>
                            <button class="btn btn-primary px-4 fw-bold" type="button" id="btn_fetch_product" style="background: linear-gradient(135deg, #2563eb, #4f46e5); border: none;">
                                <span class="spinner-border spinner-border-sm me-1 d-none" id="fetch_spinner" role="status"></span>
                                <span id="fetch_btn_text"><i class="fe-zap me-1"></i> Fetch Info</span>
                            </button>
                        </div>
                        
                        {{-- Supported Platforms Badges --}}
                        <div class="d-flex align-items-center flex-wrap gap-2 mt-2 pt-1">
                            <span class="text-muted small fw-semibold me-1" style="font-size: 11px;">Supported:</span>
                            <span class="badge rounded-pill" style="background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; font-size:11px;">
                                <i class="fe-check-circle me-1"></i> Daraz (Bangladesh)
                            </span>
                            <span class="badge rounded-pill" style="background:#fff7ed; color:#ea580c; border:1px solid #fed7aa; font-size:11px;">
                                <i class="fe-check-circle me-1"></i> Alibaba
                            </span>
                            <span class="badge rounded-pill" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca; font-size:11px;">
                                <i class="fe-check-circle me-1"></i> AliExpress
                            </span>
                            <span class="badge rounded-pill" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; font-size:11px;">
                                <i class="fe-check-circle me-1"></i> Amazon
                            </span>
                            <span class="badge rounded-pill" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; font-size:11px;">
                                <i class="fe-check-circle me-1"></i> eBay
                            </span>
                            <span class="badge rounded-pill" style="background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; font-size:11px;">
                                <i class="fe-globe me-1"></i> Any E-Commerce Site
                            </span>
                        </div>
                    </div>
                </div>

                {{-- LOADING / STATUS STATE --}}
                <div id="import_loading_state" class="text-center py-5 d-none">
                    <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                    <h5 class="fw-bold text-dark mt-3 mb-1">প্রোডাক্ট তথ্য সংগ্রহ করা হচ্ছে...</h5>
                    <p class="text-muted small">পেজের টাইটেল, ছবি, মূল্য ও ডেসক্রিপশন প্রসেসিং চলছে, কয়েক সেকেন্ড অপেক্ষা করুন।</p>
                </div>

                {{-- STEP 2: VERIFICATION & REVIEW FORM (Initially Hidden) --}}
                <div id="import_review_area" class="d-none">
                    
                    {{-- Source Platform Bar --}}
                    <div class="alert alert-info border-0 rounded-3 py-2 px-3 mb-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white rounded-pill px-2.5 py-1" id="imp_platform_badge">Source: Unknown</span>
                            <small class="text-muted text-truncate" id="imp_source_url_display" style="max-width: 500px;"></small>
                        </div>
                        <span class="text-success fw-bold small"><i class="fe-check-circle me-1"></i> তথ্য সংগ্রহ সম্পন্ন — অনুগ্রহ করে যাচাই করুন</span>
                    </div>

                    <form id="importQuickStoreForm">
                        @csrf
                        <input type="hidden" name="source_platform" id="imp_hidden_platform" value="">
                        <input type="hidden" name="source_url" id="imp_hidden_url" value="">
                        <input type="hidden" name="brand" id="imp_hidden_brand" value="">
                        <input type="hidden" name="sku" id="imp_hidden_sku" value="">

                        <div class="row g-3">
                            
                            {{-- LEFT COLUMN: Basic & Pricing --}}
                            <div class="col-lg-7">
                                
                                {{-- Card 1: Title & Category --}}
                                <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark" style="font-size: 13px;">
                                                Product Name / Title <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="name" id="imp_name" class="form-control fw-semibold" placeholder="Product Title" required>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">
                                                    Main Category <span class="text-danger">*</span>
                                                </label>
                                                <select name="category_id" id="imp_category_id" class="form-select form-select-sm" required>
                                                    <option value="">-- Select Category --</option>
                                                    @if(isset($categories))
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Sub Category</label>
                                                <select name="subcategory_id" id="imp_subcategory_id" class="form-select form-select-sm">
                                                    <option value="">-- Choose Subcategory --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card 2: Pricing & Stock --}}
                                <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 13px;">
                                            <i class="fe-dollar-sign text-success me-1"></i> Pricing & Inventory (৳ BDT)
                                        </h6>
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <label class="form-label small text-muted mb-1">New / Sale Price <span class="text-danger">*</span></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light text-primary fw-bold">৳</span>
                                                    <input type="number" step="0.01" name="new_price" id="imp_new_price" class="form-control fw-bold border-primary" placeholder="0.00" required>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small text-muted mb-1">Old / MRP Price</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light text-muted">৳</span>
                                                    <input type="number" step="0.01" name="old_price" id="imp_old_price" class="form-control" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small text-muted mb-1">Purchase / Cost</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light text-muted">৳</span>
                                                    <input type="number" step="0.01" name="purchase_price" id="imp_purchase_price" class="form-control" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <label class="form-label small text-muted mb-1">Stock Quantity</label>
                                                <input type="number" name="stock" id="imp_stock" class="form-control form-control-sm" value="100">
                                            </div>
                                            <div class="col-6 mt-2">
                                                <label class="form-label small text-muted mb-1">Unit</label>
                                                <input type="text" name="pro_unit" id="imp_unit" class="form-control form-control-sm" value="pcs">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card 3: Description --}}
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 13px;">
                                            <i class="fe-file-text text-primary me-1"></i> Product Description <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="description" id="imp_description" class="form-control" rows="6" placeholder="Product details..." required></textarea>
                                    </div>
                                </div>

                            </div>

                            {{-- RIGHT COLUMN: Gallery Images & SEO --}}
                            <div class="col-lg-5">
                                
                                {{-- Card 4: Images Gallery Selection --}}
                                <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="fw-bold text-dark mb-0" style="font-size: 13px;">
                                                <i class="fe-image text-primary me-1"></i> Extracted Images (<span id="imp_images_count">0</span>)
                                            </h6>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 11px;" id="imp_btn_select_all">Select All</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 11px;" id="imp_btn_deselect_all">Clear</button>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mb-2" style="font-size: 11px;">
                                            যেসব ছবি সেভ করতে চান তা টিক দিন। ১ম ছবিটি প্রধান ছবি হিসেবে থাকবে।
                                        </small>

                                        <div id="imp_images_grid" class="d-flex flex-wrap gap-2" style="max-height: 240px; overflow-y: auto; padding: 4px;">
                                            {{-- Dynamic images loaded here --}}
                                        </div>
                                    </div>
                                </div>

                                {{-- Card 5: SEO Preview & Meta --}}
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-dark mb-2" style="font-size: 13px;">
                                            <i class="fe-search text-info me-1"></i> SEO Meta Preview
                                        </h6>
                                        <div class="mb-2">
                                            <label class="form-label small text-muted mb-1">Meta Title</label>
                                            <input type="text" name="meta_title" id="imp_meta_title" class="form-control form-control-sm">
                                        </div>
                                        <div>
                                            <label class="form-label small text-muted mb-1">Meta Description</label>
                                            <textarea name="meta_description" id="imp_meta_description" class="form-control form-control-sm" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- Modal Action Buttons --}}
                        <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top">
                            <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-primary rounded-pill px-3 fw-bold" id="btn_open_full_edit">
                                    <i class="fe-external-link me-1"></i> Open Full Edit Page
                                </button>
                                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" id="btn_publish_product">
                                    <span class="spinner-border spinner-border-sm me-1 d-none" id="save_spinner" role="status"></span>
                                    <i class="fe-check-circle me-1"></i> Verify & Publish Product
                                </button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Hidden Form for Opening Full Create Page with Data Prefill --}}
<form id="openFullCreateForm" action="{{ route('products.create') }}" method="GET" class="d-none">
    <input type="hidden" name="prefill_name" id="pfc_name">
    <input type="hidden" name="prefill_category_id" id="pfc_category_id">
    <input type="hidden" name="prefill_new_price" id="pfc_new_price">
    <input type="hidden" name="prefill_old_price" id="pfc_old_price">
    <input type="hidden" name="prefill_purchase_price" id="pfc_purchase_price">
    <input type="hidden" name="prefill_description" id="pfc_description">
    <input type="hidden" name="prefill_meta_title" id="pfc_meta_title">
    <input type="hidden" name="prefill_meta_description" id="pfc_meta_description">
    <input type="hidden" name="prefill_images" id="pfc_images">
</form>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const importModalEl = document.getElementById('importProductModal');
    if (!importModalEl) return;

    const urlInput = document.getElementById('import_url_input');
    const fetchBtn = document.getElementById('btn_fetch_product');
    const fetchSpinner = document.getElementById('fetch_spinner');
    const fetchBtnText = document.getElementById('fetch_btn_text');
    const pasteBtn = document.getElementById('btn_paste_url');
    
    const loadingState = document.getElementById('import_loading_state');
    const reviewArea = document.getElementById('import_review_area');
    
    const imagesGrid = document.getElementById('imp_images_grid');
    const imagesCount = document.getElementById('imp_images_count');
    
    let lastFetchedData = null;

    // Paste from clipboard helper
    pasteBtn.addEventListener('click', async function () {
        try {
            const text = await navigator.clipboard.readText();
            if (text) {
                urlInput.value = text.trim();
                urlInput.focus();
            }
        } catch (e) {
            toastr.info('Please paste the URL directly into the field (Ctrl+V).');
        }
    });

    // Enter key trigger
    urlInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchBtn.click();
        }
    });

    // Fetch Product Handler
    fetchBtn.addEventListener('click', function () {
        const url = urlInput.value.trim();
        if (!url) {
            toastr.error('একটি সঠিক প্রোডাক্ট লিঙ্ক (URL) দিন।');
            urlInput.focus();
            return;
        }

        // UI state
        fetchSpinner.classList.remove('d-none');
        fetchBtn.disabled = true;
        loadingState.classList.remove('d-none');
        reviewArea.classList.add('d-none');

        $.ajax({
            type: "POST",
            url: "{{ route('products.import_url_fetch') }}",
            data: {
                _token: "{{ csrf_token() }}",
                url: url
            },
            success: function (res) {
                fetchSpinner.classList.add('d-none');
                fetchBtn.disabled = false;
                loadingState.classList.add('d-none');

                if (res.status === 'success' && res.data) {
                    lastFetchedData = res.data;
                    populateModalForm(res.data);
                    reviewArea.classList.remove('d-none');
                    toastr.success('প্রোডাক্ট তথ্য সফলভাবে সংগ্রহ করা হয়েছে!');
                } else {
                    toastr.error(res.message || 'প্রোডাক্ট তথ্য সংগ্রহ করা সম্ভব হয়নি।');
                }
            },
            error: function (xhr) {
                fetchSpinner.classList.add('d-none');
                fetchBtn.disabled = false;
                loadingState.classList.add('d-none');

                let msg = 'প্রোডাক্ট তথ্য সংগ্রহে ত্রুটি ঘটেছে।';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
            }
        });
    });

    // Populate Modal Form with Scraped Data
    function populateModalForm(data) {
        document.getElementById('imp_platform_badge').textContent = 'Source: ' + (data.source_platform || 'Web');
        document.getElementById('imp_source_url_display').textContent = data.source_url || '';
        document.getElementById('imp_hidden_platform').value = data.source_platform || '';
        document.getElementById('imp_hidden_url').value = data.source_url || '';
        document.getElementById('imp_hidden_brand').value = data.brand || '';
        document.getElementById('imp_hidden_sku').value = data.sku || '';

        document.getElementById('imp_name').value = data.name || '';
        document.getElementById('imp_new_price').value = data.new_price || 0;
        document.getElementById('imp_old_price').value = data.old_price || 0;
        document.getElementById('imp_purchase_price').value = data.purchase_price || 0;
        document.getElementById('imp_stock').value = data.stock || 100;
        document.getElementById('imp_unit').value = data.pro_unit || 'pcs';

        document.getElementById('imp_description').value = data.description || '';
        document.getElementById('imp_meta_title').value = data.meta_title || data.name || '';
        document.getElementById('imp_meta_description').value = data.meta_description || '';

        // Category auto-select
        const catSelect = document.getElementById('imp_category_id');
        if (data.suggested_category_id && catSelect.querySelector(`option[value="${data.suggested_category_id}"]`)) {
            catSelect.value = data.suggested_category_id;
            loadSubcategoriesFor(data.suggested_category_id);
        } else if (catSelect.options.length > 1) {
            catSelect.selectedIndex = 1;
            loadSubcategoriesFor(catSelect.value);
        }

        // Images Grid
        imagesGrid.innerHTML = '';
        const images = data.images || [];
        imagesCount.textContent = images.length;

        if (images.length === 0) {
            imagesGrid.innerHTML = '<span class="text-muted small p-2">কোনো ছবি পাওয়া যায়নি। পরবর্তীতে ছবি আপলোড করতে পারবেন।</span>';
        } else {
            images.forEach((imgUrl, idx) => {
                const isMain = (idx === 0);
                const col = document.createElement('div');
                col.className = 'position-relative border rounded p-1 imp-img-card';
                col.style.cssText = 'width: 76px; height: 76px; background:#fff; cursor:pointer;';
                col.innerHTML = `
                    <img src="${imgUrl}" alt="Product image" style="width:100%; height:100%; object-fit:cover; border-radius:4px;" loading="lazy">
                    <input type="checkbox" name="selected_images[]" value="${imgUrl}" checked class="form-check-input position-absolute" style="top:4px; right:4px; margin:0; cursor:pointer; width:17px; height:17px;">
                    ${isMain ? '<span class="badge bg-primary position-absolute" style="bottom:2px; left:2px; font-size:9px; padding:1px 4px;">Main</span>' : ''}
                `;
                imagesGrid.appendChild(col);
            });
        }
    }

    // Category Change -> Load Subcategories
    $('#imp_category_id').on('change', function () {
        loadSubcategoriesFor($(this).val());
    });

    function loadSubcategoriesFor(catId) {
        const $subSelect = $('#imp_subcategory_id');
        $subSelect.empty().append('<option value="">-- Choose Subcategory --</option>');
        if (!catId) return;

        $.ajax({
            type: "GET",
            url: "{{ url('ajax-product-subcategory') }}?category_id=" + catId,
            success: function (res) {
                if (res) {
                    $.each(res, function (key, value) {
                        $subSelect.append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            }
        });
    }

    // Select / Deselect All Images
    document.getElementById('imp_btn_select_all').addEventListener('click', function () {
        document.querySelectorAll('#imp_images_grid input[type="checkbox"]').forEach(cb => cb.checked = true);
    });
    document.getElementById('imp_btn_deselect_all').addEventListener('click', function () {
        document.querySelectorAll('#imp_images_grid input[type="checkbox"]').forEach(cb => cb.checked = false);
    });

    // Form Submit: Quick Store & Publish
    document.getElementById('importQuickStoreForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById('btn_publish_product');
        const saveSpinner = document.getElementById('save_spinner');
        submitBtn.disabled = true;
        saveSpinner.classList.remove('d-none');

        const formData = $(this).serialize();

        $.ajax({
            type: "POST",
            url: "{{ route('products.import_url_quick_store') }}",
            data: formData,
            success: function (res) {
                submitBtn.disabled = false;
                saveSpinner.classList.add('d-none');

                if (res.status === 'success') {
                    toastr.success(res.message);
                    bootstrap.Modal.getInstance(importModalEl).hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    toastr.error(res.message || 'সংরক্ষণ ব্যর্থ হয়েছে।');
                }
            },
            error: function (xhr) {
                submitBtn.disabled = false;
                saveSpinner.classList.add('d-none');

                let msg = 'প্রোডাক্ট সংরক্ষণ করতে সমস্যা হয়েছে।';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
            }
        });
    });

    // Open Full Edit Page Button
    document.getElementById('btn_open_full_edit').addEventListener('click', function () {
        if (!lastFetchedData) return;

        // Gather selected images
        const selectedImgs = [];
        document.querySelectorAll('#imp_images_grid input[type="checkbox"]:checked').forEach(cb => {
            selectedImgs.push(cb.value);
        });

        // Store into sessionStorage for full create page consumption
        const transferPayload = {
            name: document.getElementById('imp_name').value,
            category_id: document.getElementById('imp_category_id').value,
            subcategory_id: document.getElementById('imp_subcategory_id').value,
            new_price: document.getElementById('imp_new_price').value,
            old_price: document.getElementById('imp_old_price').value,
            purchase_price: document.getElementById('imp_purchase_price').value,
            stock: document.getElementById('imp_stock').value,
            pro_unit: document.getElementById('imp_unit').value,
            description: document.getElementById('imp_description').value,
            meta_title: document.getElementById('imp_meta_title').value,
            meta_description: document.getElementById('imp_meta_description').value,
            images: selectedImgs.length > 0 ? selectedImgs : (lastFetchedData.images || [])
        };

        sessionStorage.setItem('imported_product_prefill', JSON.stringify(transferPayload));
        window.location.href = "{{ route('products.create') }}?prefill=1";
    });
});
</script>
