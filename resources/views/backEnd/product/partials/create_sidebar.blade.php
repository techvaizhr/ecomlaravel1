<div class="card pf-sidebar-panel mb-4">
    <div class="card-body">
        
        {{-- 1. PRICING & INVENTORY --}}
        <div class="pf-side-block">
            <div class="pf-side-head">
                <div class="pf-side-head-left">
                    <i class="fe-dollar-sign"></i>
                    <span>Pricing & Stock</span>
                </div>
                <span class="badge bg-soft-primary text-primary px-2 py-1 rounded-pill" style="font-size:10px;">Currency: ৳</span>
            </div>
            
            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label">Purchase Price <small class="text-muted">(Cost)</small></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted">৳</span>
                        <input type="number" step="0.01" name="purchase_price" id="pro_purchase_price" class="form-control" placeholder="0.00" value="{{ old('purchase_price') }}">
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label">Old Price <small class="text-muted">(Regular)</small></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted">৳</span>
                        <input type="number" step="0.01" name="old_price" id="pro_old_price" class="form-control" placeholder="0.00" value="{{ old('old_price') }}">
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label">New Price <small class="text-danger fw-bold">*</small></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-primary fw-bold">৳</span>
                        <input type="number" step="0.01" name="new_price" id="pro_new_price" class="form-control fw-bold border-primary" placeholder="0.00" value="{{ old('new_price') }}">
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label">Reseller Price</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted">৳</span>
                        <input type="number" step="0.01" name="reseller_price" class="form-control" placeholder="0.00" value="{{ old('reseller_price') }}" title="Special price for resellers">
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label">Stock Qty</label>
                    <input type="number" name="stock" class="form-control form-control-sm" placeholder="e.g. 100" value="{{ old('stock') }}">
                </div>
                <div class="col-6">
                    <label class="form-label">Unit</label>
                    <input type="text" name="pro_unit" class="form-control form-control-sm" placeholder="pcs / kg / box" value="{{ old('pro_unit', 'pcs') }}">
                </div>
            </div>

            {{-- Live Profit / Discount Indicators --}}
            <div id="price_metrics_area" class="d-flex flex-wrap gap-2 mt-2" style="display:none !important;">
                <span id="discount_pill" class="price-metric-badge discount-badge" style="display:none;"></span>
                <span id="profit_pill" class="price-metric-badge profit-pos" style="display:none;"></span>
            </div>
        </div>

        {{-- 2. MEDIA & GALLERY --}}
        <div class="pf-side-block">
            <div class="pf-side-head">
                <div class="pf-side-head-left">
                    <i class="fe-image"></i>
                    <span>Product Gallery</span>
                </div>
                <span class="text-danger fw-bold" style="font-size:12px;">* Required</span>
            </div>

            {{-- Remote Imported Images Container --}}
            <div id="remote_images_preview_area" class="mb-3 d-none">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-soft-info text-info fw-bold" style="font-size:11px;">
                        <i class="fe-download-cloud me-1"></i> Imported Images (<span id="remote_images_count">0</span>)
                    </span>
                    <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none" id="btn_clear_remote_images" style="font-size:11px;">
                        <i class="fe-x"></i> Clear All
                    </button>
                </div>
                <div id="remote_images_grid" class="d-flex flex-wrap gap-2 p-2 bg-light rounded border"></div>
                <small class="text-muted d-block mt-1" style="font-size:10.5px;">
                    সংরক্ষণ করার সময় এই ছবিগুলো স্বয়ংক্রিয়ভাবে ডাউনলোড ও অপ্টিমাইজ হয়ে গ্যালারিতে সেভ হবে।
                </small>
            </div>

            {{-- Local Image Upload Zone --}}
            <div class="gallery-upload-zone mb-2">
                <input type="file" id="local_gallery_file_input" multiple accept="image/*" class="d-none">
                <button type="button" class="btn btn-outline-primary w-100 py-2.5 rounded-3 d-flex align-items-center justify-content-center gap-2 border-2" id="btn_pick_gallery_files" style="border-style:dashed;">
                    <i class="fe-image fs-5"></i>
                    <span class="fw-semibold">ছবি আপলোড করুন (একাধিক নির্বাচনযোগ্য)</span>
                </button>
            </div>

            {{-- Local Uploaded Images Preview Grid with Drag & Drop --}}
            <div id="local_images_preview_area" class="mb-2 d-none">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="text-muted small fw-semibold" style="font-size:11px;">
                        গ্যালারি ছবি (<span id="local_images_count">0</span>) - টেনে সাজান বা প্রধান ছবি বাছুন
                    </span>
                    <button type="button" class="btn btn-xs btn-link text-danger p-0 text-decoration-none" id="btn_clear_local_images" style="font-size:11px;">
                        <i class="fe-x"></i> সব মুছুন
                    </button>
                </div>
                <div id="local_images_grid" class="d-flex flex-wrap gap-2 p-2 bg-light rounded border" style="min-height:90px;"></div>
            </div>

            {{-- Hidden Actual File Input Synchronized by JavaScript --}}
            <input type="file" name="image[]" id="final_gallery_file_input" multiple class="d-none">

            <small class="text-muted d-block mt-1" style="font-size:11px;">
                <i class="fa fa-info-circle text-primary me-1"></i> ১ম ছবিটি স্বয়ংক্রিয়ভাবে <strong>Main Image</strong> হিসেবে থাকবে। মাউস দিয়ে ড্র্যাগ করে অথবা "⭐ Set Main" বাটনে ক্লিক করে পছন্দমতো প্রধান ছবি নির্ধারণ করুন।
            </small>
        </div>

        {{-- 3. PRODUCT VIDEO (Max 5MB for Upload) --}}
        <div class="pf-side-block">
            <div class="pf-side-head">
                <div class="pf-side-head-left">
                    <i class="fe-video"></i>
                    <span>Product Video</span>
                </div>
                <span class="pf-video-size-badge" id="video_limit_badge">
                    <i class="fa fa-shield"></i> Max 5 MB
                </span>
            </div>

            <div class="pf-video-card">
                <div class="pf-video-source-pill">
                    <input type="radio" name="pro_video_source" id="vs_yt_c" value="youtube" checked>
                    <label for="vs_yt_c">
                        <i class="fa fa-youtube-play text-danger"></i> YouTube
                    </label>

                    <input type="radio" name="pro_video_source" id="vs_up_c" value="upload">
                    <label for="vs_up_c">
                        <i class="fa fa-cloud-upload text-primary"></i> Direct Upload
                    </label>
                </div>

                {{-- YouTube Option --}}
                <div id="yt_section_c">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-danger"><i class="fa fa-youtube-play"></i></span>
                        <input type="text" name="pro_video" id="pro_video_c" class="form-control form-control-sm" placeholder="YouTube URL / ID (e.g. dQw4w9WgXcQ)">
                    </div>
                    <div id="yt_preview_c" class="mt-2" style="display:none;">
                        <iframe id="yt_iframe_c" width="100%" height="130" src="" frameborder="0" allowfullscreen style="border-radius:10px;"></iframe>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size:11px;">ইউটিউব ভিডিও লিঙ্ক অথবা ভিডিও আইডি পেস্ট করুন।</small>
                </div>

                {{-- Direct Video Upload Option (Max 5MB) --}}
                <div id="up_section_c" style="display:none;">
                    <div class="position-relative">
                        <input type="file" name="pro_video_file" id="pro_video_file_c" class="form-control form-control-sm" accept="video/mp4,video/webm,video/ogg,video/quicktime">
                    </div>

                    {{-- Dynamic Size Status & Preview --}}
                    <div id="up_preview_c" class="mt-2" style="display:none;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge bg-soft-success text-success" id="video_file_size_text" style="font-size:11px;">0 MB / 5 MB</span>
                            <button type="button" class="btn btn-xs btn-link text-danger p-0 text-decoration-none" id="clear_video_btn">
                                <i class="fe-trash-2"></i> রিমুভ
                            </button>
                        </div>
                        <video id="up_video_c" width="100%" height="130" controls style="border-radius:10px;background:#000;"></video>
                    </div>

                    {{-- Error Alert when > 5MB --}}
                    <div id="video_size_error_c" class="video-error-alert">
                        <i class="fa fa-exclamation-triangle me-1"></i>
                        <span id="video_error_msg">ভিডিও সাইজ ৫ MB এর বেশি হতে পারবে না!</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <small class="text-muted" style="font-size:11px;">MP4, WebM, OGG</small>
                        <small class="text-danger fw-bold" style="font-size:11px;">সর্বোচ্চ ৫ মেগাবাইট (5MB)</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. ORGANIZATION & STATUS --}}
        <div class="pf-side-block">
            <div class="pf-side-head">
                <div class="pf-side-head-left">
                    <i class="fe-settings"></i>
                    <span>Settings & Status</span>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label">Product Type</label>
                    <select class="form-select form-select-sm" id="product_type" name="product_type">
                        <option value="physical" selected>📦 Physical</option>
                        <option value="digital">💾 Digital File</option>
                    </select>
                </div>
                <div id="advance_area" class="col-6">
                    <label class="form-label">Advance (৳)</label>
                    <input type="number" name="advance_amount" class="form-control form-control-sm" placeholder="0" value="{{ old('advance_amount') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Brand</label>
                    <select class="form-control select2" name="brand_id">
                        <option value="">No Brand (Generic)</option>
                        @foreach($brands as $value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Digital File Section --}}
            <div id="digital_area" style="display:none;" class="p-3 border rounded-3 mb-3 bg-light">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fe-download text-primary"></i>
                    <strong style="font-size:12px;">Digital Download Settings</strong>
                </div>
                <label class="form-label">Digital File <small class="text-muted">(ZIP, PDF, Software)</small></label>
                <input type="file" class="form-control form-control-sm mb-2" name="digital_file">
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label"><small>Download Limit</small></label>
                        <input type="number" class="form-control form-control-sm" name="download_limit" value="5">
                    </div>
                    <div class="col-6">
                        <label class="form-label"><small>Expire Days</small></label>
                        <input type="number" class="form-control form-control-sm" name="download_expire_days" value="7">
                    </div>
                </div>
            </div>

            {{-- Product Weight Section --}}
            <div class="p-2 border rounded-3 mb-3 bg-light">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <label class="form-label mb-0 fw-bold" style="font-size:11.5px;">
                        <i class="fe-package text-primary me-1"></i>প্রোডাক্টের ওজন (Weight - KG)
                    </label>
                </div>
                <div class="input-group input-group-sm">
                    <input type="number" step="0.01" min="0" name="weight" class="form-control" placeholder="0.5" value="{{ old('weight', '0.00') }}">
                    <span class="input-group-text">KG</span>
                </div>
                <small class="text-muted" style="font-size:10.5px; display:block; margin-top:2px;">
                    * ডেলিভারি চার্জ সেটিংস থেকে ওজনভিত্তিক মোড চালু থাকলে এটি হিসাব হবে।
                </small>
            </div>

            {{-- Status Toggles Grid --}}
            <div class="row text-center g-2">
                <div class="col-4">
                    <div class="p-2 border rounded-3 bg-light h-100 d-flex flex-column align-items-center justify-content-between">
                        <span class="form-label mb-1 text-uppercase fw-bold" style="font-size:11px;">Status</span>
                        <label class="switch mb-0">
                            <input type="checkbox" value="1" name="status" checked>
                            <span class="slider slider-success"></span>
                        </label>
                        <small class="text-muted mt-1" style="font-size:10px;">Active</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 border rounded-3 bg-light h-100 d-flex flex-column align-items-center justify-content-between">
                        <span class="form-label mb-1 text-uppercase fw-bold text-danger" style="font-size:11px;">Hot Deal</span>
                        <label class="switch mb-0">
                            <input type="checkbox" value="1" name="topsale">
                            <span class="slider slider-danger"></span>
                        </label>
                        <small class="text-muted mt-1" style="font-size:10px;">Top Sale</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 border rounded-3 bg-light h-100 d-flex flex-column align-items-center justify-content-between">
                        <span class="form-label mb-1 text-uppercase fw-bold text-warning" style="font-size:11px;">Flash</span>
                        <label class="switch mb-0">
                            <input type="checkbox" value="1" name="flashsale">
                            <span class="slider slider-warning"></span>
                        </label>
                        <small class="text-muted mt-1" style="font-size:10px;">Flash Sale</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. PUBLISH ACTION --}}
        <div class="pf-side-foot">
            <button type="submit" class="btn btn-publish" id="submit_product_btn">
                <i class="fe-check-circle fs-5"></i> Publish Product
            </button>
        </div>

    </div>
</div>
