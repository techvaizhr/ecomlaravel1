<!-- ======================================================== -->
<!-- MODAL: IMPORT PRODUCT FROM URL (DARAZ, ALIBABA, ETC.)     -->
<!-- VENDOR PORTAL VERSION                                     -->
<!-- ======================================================== -->
<div class="modal fade" id="vendorImportProductModal" tabindex="-1" aria-labelledby="vendorImportProductModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            
            {{-- Modal Header --}}
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); padding: 20px 26px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px);">
                        <i class="fa fa-cloud-download-alt text-white fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="vendorImportProductModalLabel">
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
                            <i class="fa fa-link text-primary me-1"></i> Product Webpage URL:
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-globe"></i></span>
                            <input type="url" id="v_import_url_input" class="form-control form-control-lg border-start-0 border-end-0" placeholder="https://www.daraz.com.bd/products/... বা https://www.alibaba.com/product-detail/..." style="font-size: 14px;">
                            <button class="btn btn-outline-secondary px-3" type="button" id="v_btn_paste_url" title="Paste from clipboard">
                                <i class="fa fa-paste me-1"></i> Paste
                            </button>
                            <button class="btn btn-primary px-4 fw-bold" type="button" id="v_btn_fetch_product" style="background: linear-gradient(135deg, #4e73df, #224abe); border: none;">
                                <span class="spinner-border spinner-border-sm me-1 d-none" id="v_fetch_spinner" role="status"></span>
                                <span id="v_fetch_btn_text"><i class="fa fa-bolt me-1"></i> Fetch Info</span>
                            </button>
                        </div>
                        
                        {{-- Supported Platforms Badges --}}
                        <div class="d-flex align-items-center flex-wrap gap-2 mt-2 pt-1">
                            <span class="text-muted small fw-semibold me-1" style="font-size: 11px;">Supported:</span>
                            <span class="badge rounded-pill" style="background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; font-size:11px;">
                                <i class="fa fa-check-circle me-1"></i> Daraz (Bangladesh)
                            </span>
                            <span class="badge rounded-pill" style="background:#fff7ed; color:#ea580c; border:1px solid #fed7aa; font-size:11px;">
                                <i class="fa fa-check-circle me-1"></i> Alibaba
                            </span>
                            <span class="badge rounded-pill" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca; font-size:11px;">
                                <i class="fa fa-check-circle me-1"></i> AliExpress
                            </span>
                            <span class="badge rounded-pill" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; font-size:11px;">
                                <i class="fa fa-check-circle me-1"></i> Amazon
                            </span>
                            <span class="badge rounded-pill" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; font-size:11px;">
                                <i class="fa fa-check-circle me-1"></i> eBay
                            </span>
                            <span class="badge rounded-pill" style="background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; font-size:11px;">
                                <i class="fa fa-globe me-1"></i> Any E-Commerce Site
                            </span>
                        </div>
                    </div>
                </div>

                {{-- LOADING / STATUS STATE --}}
                <div id="v_import_loading_state" class="text-center py-5 d-none">
                    <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                    <h5 class="fw-bold text-dark mt-3 mb-1">প্রোডাক্ট তথ্য সংগ্রহ করা হচ্ছে...</h5>
                    <p class="text-muted small">পেজের টাইটেল, ছবি, মূল্য ও ডেসক্রিপশন প্রসেসিং চলছে, কয়েক সেকেন্ড অপেক্ষা করুন।</p>
                </div>

                {{-- STEP 2: VERIFICATION & REVIEW FORM (Initially Hidden) --}}
                <div id="v_import_review_area" class="d-none">
                    
                    {{-- Source Platform Bar --}}
                    <div class="alert alert-info border-0 rounded-3 py-2 px-3 mb-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white rounded-pill px-2.5 py-1" id="v_imp_platform_badge">Source: Unknown</span>
                            <small class="text-muted text-truncate" id="v_imp_source_url_display" style="max-width: 500px;"></small>
                        </div>
                        <span class="text-success fw-bold small"><i class="fa fa-check-circle me-1"></i> তথ্য সংগ্রহ সম্পন্ন — অনুগ্রহ করে যাচাই করুন</span>
                    </div>

                    <form id="vendorImportQuickStoreForm">
                        @csrf
                        <input type="hidden" name="source_platform" id="v_imp_hidden_platform" value="">
                        <input type="hidden" name="source_url" id="v_imp_hidden_url" value="">
                        <input type="hidden" name="brand" id="v_imp_hidden_brand" value="">
                        <input type="hidden" name="sku" id="v_imp_hidden_sku" value="">

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
                                            <input type="text" name="name" id="v_imp_name" class="form-control fw-semibold" placeholder="Product Title" required>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">
                                                    Main Category <span class="text-danger">*</span>
                                                </label>
                                                <select name="category_id" id="v_imp_category_id" class="form-select form-select-sm" required>
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
                                                <select name="subcategory_id" id="v_imp_subcategory_id" class="form-select form-select-sm">
                                                    <option value="">-- Choose Subcategory --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Brand</label>
                                                <select name="brand_id" id="v_imp_brand_id" class="form-select form-select-sm">
                                                    <option value="">-- No Brand (Generic) --</option>
                                                    @if(isset($brands))
                                                        @foreach($brands as $b)
                                                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card 2: Pricing & Stock --}}
                                <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 13px;">
                                            <i class="fa fa-dollar-sign text-success me-1"></i> Pricing & Inventory (৳ BDT)
                                        </h6>
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <label class="form-label small text-muted mb-1">New / Sale Price <span class="text-danger">*</span></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light text-primary fw-bold">৳</span>
                                                    <input type="number" step="0.01" name="new_price" id="v_imp_new_price" class="form-control fw-bold border-primary" placeholder="0.00" required>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small text-muted mb-1">Old / MRP Price</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light text-muted">৳</span>
                                                    <input type="number" step="0.01" name="old_price" id="v_imp_old_price" class="form-control" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small text-muted mb-1">Purchase / Cost</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light text-muted">৳</span>
                                                    <input type="number" step="0.01" name="purchase_price" id="v_imp_purchase_price" class="form-control" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <label class="form-label small text-muted mb-1">Stock Quantity</label>
                                                <input type="number" name="stock" id="v_imp_stock" class="form-control form-control-sm" value="100">
                                            </div>
                                            <div class="col-6 mt-2">
                                                <label class="form-label small text-muted mb-1">Unit</label>
                                                <input type="text" name="pro_unit" id="v_imp_unit" class="form-control form-control-sm" value="pcs">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card 3: Description --}}
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 13px;">
                                            <i class="fa fa-file-alt text-primary me-1"></i> Product Description <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="description" id="v_imp_description" class="form-control" rows="6" placeholder="Product details..." required></textarea>
                                    </div>
                                </div>

                            </div>

                            {{-- RIGHT COLUMN: Gallery Images & SEO --}}
                            <div class="col-lg-5">
                                
                                {{-- Card 4: Images Gallery Selection --}}
                                <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <h6 class="fw-bold text-dark mb-0" style="font-size: 13px;">
                                                <i class="fa fa-images text-primary me-1"></i> Extracted Images (<span id="v_imp_images_count">0</span>)
                                            </h6>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 11px;" id="v_imp_btn_select_all">Select All</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 11px;" id="v_imp_btn_deselect_all">Clear</button>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 p-1.5 mb-2 rounded bg-light border text-muted" style="font-size: 11px;">
                                            <i class="fa fa-arrows-alt text-primary"></i>
                                            <span>ছবি ড্র্যাগ করে সাজান অথবা <strong>"⭐ Set Main"</strong> বাটনে ক্লিক করে প্রধান ছবি নির্ধারণ করুন।</span>
                                        </div>

                                        <div id="v_imp_images_grid" class="d-flex flex-wrap gap-2" style="max-height: 250px; overflow-y: auto; padding: 4px;">
                                            {{-- Dynamic draggable images loaded here --}}
                                        </div>
                                    </div>
                                </div>

                                {{-- Card 5: SEO Preview & Meta --}}
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-dark mb-2" style="font-size: 13px;">
                                            <i class="fa fa-search text-info me-1"></i> SEO Meta Preview
                                        </h6>
                                        <div class="mb-2">
                                            <label class="form-label small text-muted mb-1">Meta Title</label>
                                            <input type="text" name="meta_title" id="v_imp_meta_title" class="form-control form-control-sm">
                                        </div>
                                        <div>
                                            <label class="form-label small text-muted mb-1">Meta Description</label>
                                            <textarea name="meta_description" id="v_imp_meta_description" class="form-control form-control-sm" rows="2"></textarea>
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
                                <button type="button" class="btn btn-outline-primary rounded-pill px-3 fw-bold" id="v_btn_open_full_edit">
                                    <i class="fa fa-external-link-alt me-1"></i> Open Full Edit Page
                                </button>
                                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" id="v_btn_publish_product">
                                    <span class="spinner-border spinner-border-sm me-1 d-none" id="v_save_spinner" role="status"></span>
                                    <i class="fa fa-check-circle me-1"></i> Save Product
                                </button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const importModalEl = document.getElementById('vendorImportProductModal');
    if (!importModalEl) return;

    const urlInput = document.getElementById('v_import_url_input');
    const fetchBtn = document.getElementById('v_btn_fetch_product');
    const fetchSpinner = document.getElementById('v_fetch_spinner');
    const fetchBtnText = document.getElementById('v_fetch_btn_text');
    const pasteBtn = document.getElementById('v_btn_paste_url');
    
    const loadingState = document.getElementById('v_import_loading_state');
    const reviewArea = document.getElementById('v_import_review_area');
    
    const imagesGrid = document.getElementById('v_imp_images_grid');
    const imagesCount = document.getElementById('v_imp_images_count');
    
    let lastFetchedData = null;

    // Paste from clipboard
    if (pasteBtn) {
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
    }

    // Enter key trigger
    if (urlInput) {
        urlInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                fetchBtn.click();
            }
        });
    }

    // Fetch Product by URL Handler
    if (fetchBtn) {
        fetchBtn.addEventListener('click', function () {
            const url = urlInput.value.trim();
            if (!url) {
                toastr.error('একটি সঠিক প্রোডাক্ট লিঙ্ক (URL) দিন।');
                urlInput.focus();
                return;
            }

            fetchSpinner.classList.remove('d-none');
            fetchBtn.disabled = true;
            loadingState.classList.remove('d-none');
            reviewArea.classList.add('d-none');

            $.ajax({
                type: "POST",
                url: "{{ route('vendor.products.import_url_fetch') }}",
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
                        populateVendorModalForm(res.data);
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
    }

    // Drag and Drop State for Extracted Images
    let draggedCard = null;

    function handleCardDragStart(e) {
        draggedCard = this;
        this.style.opacity = '0.4';
        this.style.cursor = 'grabbing';
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', this.getAttribute('data-url'));
    }

    function handleCardDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        if (this !== draggedCard) {
            this.style.transform = 'scale(1.05)';
            this.style.borderColor = '#4e73df';
        }
    }

    function handleCardDragLeave() {
        this.style.transform = 'scale(1)';
        this.style.borderColor = '#e2e8f0';
    }

    function handleCardDrop(e) {
        e.preventDefault();
        this.style.transform = 'scale(1)';
        this.style.borderColor = '#e2e8f0';

        if (draggedCard && this !== draggedCard) {
            const allCards = Array.from(imagesGrid.querySelectorAll('.v-imp-img-card'));
            const draggedIdx = allCards.indexOf(draggedCard);
            const targetIdx = allCards.indexOf(this);

            if (draggedIdx < targetIdx) {
                this.after(draggedCard);
            } else {
                this.before(draggedCard);
            }
            updateImpImageBadges();
        }
    }

    function handleCardDragEnd() {
        this.style.opacity = '1';
        this.style.cursor = 'grab';
        document.querySelectorAll('#v_imp_images_grid .v-imp-img-card').forEach(card => {
            card.style.transform = 'scale(1)';
            card.style.borderColor = '#e2e8f0';
        });
        updateImpImageBadges();
    }

    function updateImpImageBadges() {
        const cards = imagesGrid.querySelectorAll('.v-imp-img-card');
        cards.forEach((card, idx) => {
            const footer = card.querySelector('.card-footer-action');
            if (!footer) return;

            if (idx === 0) {
                card.style.borderColor = '#4e73df';
                card.style.boxShadow = '0 0 0 2px rgba(78,115,223,0.25)';
                footer.innerHTML = `<span class="badge bg-primary w-100 py-1" style="font-size:9px; border-radius:3px;"><i class="fa fa-star me-0.5"></i> Main</span>`;
            } else {
                card.style.borderColor = '#e2e8f0';
                card.style.boxShadow = 'none';
                footer.innerHTML = `<button type="button" class="btn btn-xs btn-light w-100 py-0 border shadow-sm btn-make-v-imp-main" style="font-size:8.5px; font-weight:600; color:#1e293b; border-radius:3px;" title="প্রধান ছবি নির্বাচন করুন"><i class="fa fa-star text-warning me-0.5"></i> Set Main</button>`;
            }
        });
    }

    // Set Main Image Click Handler
    $(document).on('click', '.btn-make-v-imp-main', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const card = $(this).closest('.v-imp-img-card')[0];
        if (card && imagesGrid) {
            imagesGrid.prepend(card);
            updateImpImageBadges();
            toastr.info('প্রধান ছবি পরিবর্তন করা হয়েছে!');
        }
    });

    // Populate Modal Form with Scraped Data
    function populateVendorModalForm(data) {
        document.getElementById('v_imp_platform_badge').textContent = 'Source: ' + (data.source_platform || 'Web');
        document.getElementById('v_imp_source_url_display').textContent = data.source_url || '';
        document.getElementById('v_imp_hidden_platform').value = data.source_platform || '';
        document.getElementById('v_imp_hidden_url').value = data.source_url || '';
        document.getElementById('v_imp_hidden_brand').value = data.brand || '';
        document.getElementById('v_imp_hidden_sku').value = data.sku || '';

        document.getElementById('v_imp_name').value = data.name || '';
        document.getElementById('v_imp_new_price').value = data.new_price || 0;
        document.getElementById('v_imp_old_price').value = data.old_price || 0;
        document.getElementById('v_imp_purchase_price').value = data.purchase_price || 0;
        document.getElementById('v_imp_stock').value = data.stock || 100;
        document.getElementById('v_imp_unit').value = data.pro_unit || 'pcs';

        document.getElementById('v_imp_description').value = data.description || '';
        document.getElementById('v_imp_meta_title').value = data.meta_title || data.name || '';
        document.getElementById('v_imp_meta_description').value = data.meta_description || '';

        // Category auto-select
        const catSelect = document.getElementById('v_imp_category_id');
        if (data.suggested_category_id && catSelect.querySelector(`option[value="${data.suggested_category_id}"]`)) {
            catSelect.value = data.suggested_category_id;
            loadSubcategoriesFor(data.suggested_category_id);
        } else if (catSelect.options.length > 1) {
            catSelect.selectedIndex = 1;
            loadSubcategoriesFor(catSelect.value);
        }

        // Render Extracted Images Grid with Drag & Drop
        imagesGrid.innerHTML = '';
        const images = data.images || [];
        imagesCount.textContent = images.length;

        if (images.length === 0) {
            imagesGrid.innerHTML = '<span class="text-muted small p-2">কোনো ছবি পাওয়া যায়নি।</span>';
        } else {
            images.forEach((imgUrl) => {
                const col = document.createElement('div');
                col.className = 'position-relative border rounded p-1 v-imp-img-card';
                col.setAttribute('draggable', 'true');
                col.setAttribute('data-url', imgUrl);
                col.style.cssText = 'width: 84px; height: 84px; background:#fff; cursor: grab; user-select: none; transition: transform 0.15s, box-shadow 0.15s; border-radius: 8px;';
                col.innerHTML = `
                    <img src="${imgUrl}" alt="Product image" style="width:100%; height:100%; object-fit:cover; border-radius:6px; pointer-events: none;" loading="lazy">
                    <span class="position-absolute bg-dark bg-opacity-75 text-white rounded-circle d-flex align-items-center justify-content-center" style="top:4px; left:4px; width:18px; height:18px; font-size:10px; cursor:grab;" title="Drag to reorder"><i class="fa fa-arrows-alt"></i></span>
                    <input type="checkbox" name="selected_images[]" value="${imgUrl}" checked class="form-check-input position-absolute" style="top:4px; right:4px; margin:0; cursor:pointer; width:17px; height:17px; z-index:3;">
                    <div class="card-footer-action position-absolute" style="bottom:3px; left:3px; right:3px; z-index:3;"></div>
                `;

                col.addEventListener('dragstart', handleCardDragStart);
                col.addEventListener('dragover', handleCardDragOver);
                col.addEventListener('dragleave', handleCardDragLeave);
                col.addEventListener('drop', handleCardDrop);
                col.addEventListener('dragend', handleCardDragEnd);

                imagesGrid.appendChild(col);
            });

            updateImpImageBadges();
        }
    }

    // Category Change -> Load Subcategories
    $('#v_imp_category_id').on('change', function () {
        loadSubcategoriesFor($(this).val());
    });

    function loadSubcategoriesFor(catId) {
        const $subSelect = $('#v_imp_subcategory_id');
        $subSelect.empty().append('<option value="">-- Choose Subcategory --</option>');
        if (!catId) return;

        $.ajax({
            type: "GET",
            url: "{{ route('vendor.ajax.subcategory') }}?category_id=" + catId,
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
    if (document.getElementById('v_imp_btn_select_all')) {
        document.getElementById('v_imp_btn_select_all').addEventListener('click', function () {
            document.querySelectorAll('#v_imp_images_grid input[type="checkbox"]').forEach(cb => cb.checked = true);
        });
    }
    if (document.getElementById('v_imp_btn_deselect_all')) {
        document.getElementById('v_imp_btn_deselect_all').addEventListener('click', function () {
            document.querySelectorAll('#v_imp_images_grid input[type="checkbox"]').forEach(cb => cb.checked = false);
        });
    }

    // Form Submit: Quick Store
    const vForm = document.getElementById('vendorImportQuickStoreForm');
    if (vForm) {
        vForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('v_btn_publish_product');
            const saveSpinner = document.getElementById('v_save_spinner');
            submitBtn.disabled = true;
            saveSpinner.classList.remove('d-none');

            const formData = $(this).serialize();

            $.ajax({
                type: "POST",
                url: "{{ route('vendor.products.import_url_quick_store') }}",
                data: formData,
                success: function (res) {
                    submitBtn.disabled = false;
                    saveSpinner.classList.add('d-none');

                    if (res.status === 'success') {
                        toastr.success(res.message);
                        bootstrap.Modal.getInstance(importModalEl).hide();
                        setTimeout(() => {
                            window.location.href = "{{ route('vendor.products.index') }}";
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
    }

    // Open Full Edit Page Button
    const vOpenFullBtn = document.getElementById('v_btn_open_full_edit');
    if (vOpenFullBtn) {
        vOpenFullBtn.addEventListener('click', function () {
            if (!lastFetchedData) return;

            const selectedImgs = [];
            document.querySelectorAll('#v_imp_images_grid .v-imp-img-card').forEach(card => {
                const cb = card.querySelector('input[type="checkbox"]');
                if (cb && cb.checked) {
                    selectedImgs.push(cb.value);
                }
            });

            const transferPayload = {
                name: document.getElementById('v_imp_name').value,
                category_id: document.getElementById('v_imp_category_id').value,
                subcategory_id: document.getElementById('v_imp_subcategory_id').value,
                brand_id: document.getElementById('v_imp_brand_id') ? document.getElementById('v_imp_brand_id').value : '',
                new_price: document.getElementById('v_imp_new_price').value,
                old_price: document.getElementById('v_imp_old_price').value,
                purchase_price: document.getElementById('v_imp_purchase_price').value,
                stock: document.getElementById('v_imp_stock').value,
                pro_unit: document.getElementById('v_imp_unit').value,
                description: document.getElementById('v_imp_description').value,
                meta_title: document.getElementById('v_imp_meta_title').value,
                meta_description: document.getElementById('v_imp_meta_description').value,
                images: selectedImgs.length > 0 ? selectedImgs : (lastFetchedData.images || [])
            };

            sessionStorage.setItem('vendor_imported_product_prefill', JSON.stringify(transferPayload));
            window.location.href = "{{ route('vendor.products.create') }}?prefill=1";
        });
    }
});
</script>
