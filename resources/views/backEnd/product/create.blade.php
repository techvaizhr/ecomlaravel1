@extends('backEnd.layouts.master')
@section('title','Create New Product')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
@include('backEnd.product.partials.product_form_styles')
@endsection

@section('content')
<div class="container-fluid product-form-page">
    
    {{-- Page Header --}}
    <div class="pf-page-header">
        <div>
            <h4>
                <span class="header-icon-badge"><i class="fe-package"></i></span>
                নতুন প্রোডাক্ট যোগ করুন
            </h4>
            <p class="pf-sub mb-0">প্রোডাক্টের সাধারণ তথ্য, ভ্যারিয়েন্ট, হোলসেল টায়ার, এসইও এবং মিডিয়া ফাইল যুক্ত করে সহজে পাবলিশ করুন।</p>
        </div>
        <div class="pf-header-actions d-flex align-items-center gap-2">
            <button type="button" class="btn btn-info rounded-pill px-3 py-1.5 fw-bold text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#importProductModal">
                <i class="fe-download-cloud me-1"></i> Import from URL
            </button>
            <a href="{{ route('products.index') }}" class="pf-btn-manage">
                <i class="fe-arrow-left"></i> প্রোডাক্ট তালিকা
            </a>
        </div>
    </div>

    {{-- Fast URL Import Bar --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: linear-gradient(135deg, #0284c7 0%, #2563eb 50%, #4f46e5 100%);">
        <div class="card-body p-3 p-md-4 text-white">
            <div class="row align-items-center g-3">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-white bg-opacity-25" style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="fe-zap text-white fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-white" style="font-size: 15px;">Auto-Fill from URL</h6>
                            <small class="text-white-50" style="font-size: 12px;">Daraz, Alibaba, AliExpress, Amazon, eBay ইত্যাদি</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="input-group">
                        <input type="url" id="page_quick_import_url" class="form-control border-0" placeholder="প্রোডাক্ট লিঙ্ক পেস্ট করুন (যেমন: https://www.daraz.com.bd/... বা Alibaba)" style="border-radius: 8px 0 0 8px; font-size: 13.5px;">
                        <button class="btn btn-light px-3 fw-bold text-dark border-0" type="button" id="btn_page_paste_url">
                            <i class="fe-clipboard me-1"></i> Paste
                        </button>
                        <button class="btn btn-warning px-4 fw-bold text-dark border-0" type="button" id="btn_page_quick_fetch" style="border-radius: 0 8px 8px 0;">
                            <span class="spinner-border spinner-border-sm me-1 d-none" id="page_fetch_spinner"></span>
                            <span id="page_fetch_btn_text"><i class="fe-download-cloud me-1"></i> Fetch & Fill Form</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{route('products.store')}}" method="POST" id="productForm" data-parsley-validate="" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- Left Main Column --}}
            <div class="col-lg-8">
                
                {{-- 1. Basic Information Card --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="section-title">
                            <div class="section-title-left">
                                <i class="fe-info"></i>
                                <span>Basic Information</span>
                            </div>
                            <span class="badge bg-soft-primary text-primary rounded-pill px-2.5 py-1">ধাপ ১ : সাধারণ তথ্য</span>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">
                                Product Name <span class="req">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" name="name" id="product_name_input" value="{{ old('name') }}" placeholder="e.g. Premium Cotton Casual Shirt for Men" required />
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Main Category <span class="req">*</span></label>
                                <select class="form-control select2 @error('category_id') is-invalid @enderror" name="category_id" id="category_id" required>
                                    <option value="">Select Main Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{$category->name}}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Sub Category</label>
                                <select class="form-control select2" name="subcategory_id" id="subcategory_id">
                                    <option value="">Choose Sub Category</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Child Category</label>
                                <select class="form-control select2" name="childcategory_id" id="childcategory_id">
                                    <option value="">Choose Child Category</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="pf-desc-label-row">
                                <label class="form-label mb-0">Full Description <span class="req">*</span></label>
                                @include('backEnd.product.partials.ai_description_button')
                            </div>
                            <textarea name="description" class="summernote" required>{{ old('description') }}</textarea>
                            @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Internal Short Note <small class="text-muted">(Optional)</small></label>
                            <textarea name="note" rows="2" class="form-control" placeholder="অভ্যন্তরীণ বা স্টাফদের জন্য কোনো বিশেষ নোট...">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 2. Wholesale Pricing Card --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-soft-success p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                    <i class="fe-tag text-success fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold" style="font-size:14px;">Wholesale / পাইকারি বিক্রি</h6>
                                    <small class="text-muted">পরিমাণ অনুযায়ী পাইকারি বা হোলসেল মূল্যের স্তর তৈরি করুন</small>
                                </div>
                            </div>
                            <label class="switch mb-0">
                                <input type="checkbox" value="1" name="is_wholesale" id="is_wholesale">
                                <span class="slider slider-success"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div id="wholesale_area" style="display:none;" class="card mb-4">
                    <div class="card-body">
                        <div class="section-title">
                            <div class="section-title-left">
                                <i class="fe-dollar-sign text-success"></i>
                                <span>Wholesale Pricing Tiers</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-wholesale-tier rounded-pill px-3 shadow-sm">
                                <i class="fa fa-plus me-1"></i> Add Tier
                            </button>
                        </div>
                        
                        <div id="wholesale-wrapper">
                            <div class="variant-card">
                                <div class="row align-items-end g-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Min Quantity</label>
                                        <input type="number" name="wholesale_price[0][min_quantity]" class="form-control" placeholder="e.g. 10">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Max Quantity <small class="text-muted">(Opt)</small></label>
                                        <input type="number" name="wholesale_price[0][max_quantity]" class="form-control" placeholder="e.g. 50">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Tier Price (৳)</label>
                                        <input type="number" step="0.01" name="wholesale_price[0][wholesale_price]" class="form-control" placeholder="0.00">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Stock Qty</label>
                                        <input type="number" name="wholesale_price[0][stock]" class="form-control" placeholder="0">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-success add-wholesale-tier w-100 rounded-3">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Product Variants Card --}}
                <div class="card mb-4" id="variant_section">
                    <div class="card-body">
                        <div class="section-title">
                            <div class="section-title-left">
                                <i class="fe-layers"></i>
                                <span>Product Variants (Size & Color)</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary add-variant rounded-pill px-3 shadow-sm" style="background:var(--pf-primary);border-color:var(--pf-primary);">
                                <i class="fa fa-plus me-1"></i> Add New Variant
                            </button>
                        </div>
                        
                        <div id="variant-wrapper">
                            <div class="variant-card variant-item">
                                <div class="row align-items-end g-2">
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Color <small class="text-muted">(Optional)</small></label>
                                        <select name="variant_price[0][color_id]" class="form-control select2 variant-color-select">
                                            <option value="">Select Color</option>
                                            @foreach($colors as $color)
                                                <option value="{{ $color->id }}">{{ $color->colorName ?? $color->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Size <small class="text-muted">(Optional)</small></label>
                                        <select name="variant_price[0][size_id][]" class="form-control select2 variant-size-select" multiple>
                                            @foreach($sizes as $size)
                                                <option value="{{ $size->id }}">{{ $size->sizeName ?? $size->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">Price (৳)</label>
                                        <input type="number" step="0.01" name="variant_price[0][price]" class="form-control" placeholder="0.00">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">Stock</label>
                                        <input type="number" name="variant_price[0][stock]" class="form-control" placeholder="0">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <button type="button" class="btn btn-outline-danger btn-remove-row d-none w-100 rounded-3">
                                            <i class="fe-trash-2"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="row align-items-center mt-2 pt-2 border-top g-2">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center gap-2">
                                            <label class="form-label mb-0" style="min-width:90px;font-size:12px;">Variant Image:</label>
                                            <input type="file" name="variant_image[0][image]" class="form-control form-control-sm variant-img-input" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="variant-img-preview" style="display:none;">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="" alt="Preview" class="rounded border" style="width:40px;height:40px;object-fit:cover;">
                                                <button type="button" class="btn btn-xs btn-outline-danger variant-img-clear" title="Remove image">
                                                    <i class="fe-x"></i> Clear
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fa fa-info-circle text-primary me-1"></i> কালার ও সাইজ নির্বাচন করে ভ্যারিয়েন্ট তৈরি করুন। একাধিক সাইজ সিলেক্ট করলে স্বয়ংক্রিয়ভাবে আলাদা ভ্যারিয়েন্ট সাজিয়ে সেভ হবে।
                        </small>
                    </div>
                </div>

                {{-- 4. SEO Configuration Card --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="section-title">
                            <div class="section-title-left">
                                <i class="fe-search text-info"></i>
                                <span>Search Engine Optimization (SEO)</span>
                            </div>
                            <span class="badge bg-soft-info text-info rounded-pill px-2.5 py-1">Google SERP Preview</span>
                        </div>

                        {{-- Google SERP Snippet Preview Box --}}
                        <div class="serp-preview-card">
                            <div class="serp-url">
                                <i class="fa fa-globe text-muted"></i>
                                <span>{{ url('/') }} › product › <span id="serp_slug_preview">your-product-slug</span></span>
                            </div>
                            <div class="serp-title" id="serp_title_preview">Your Product Title Preview - Buy Online in Bangladesh</div>
                            <div class="serp-desc" id="serp_desc_preview">Product description snippet will appear here on Google search results...</div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" id="meta_title_input" class="form-control" placeholder="SEO optimized title">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control" placeholder="keyword1, keyword2, keyword3">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description" id="meta_desc_input" class="form-control" rows="2" placeholder="Brief search snippet description (max 160 characters)..."></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Meta Social Image <small class="text-muted">(OG Share Image)</small></label>
                                <input type="file" name="meta_image" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Sidebar Column --}}
            <div class="col-lg-4 pf-sidebar-col">
                @include('backEnd.product.partials.create_sidebar')
            </div>
        </div>
    </form>
</div>

@include('backEnd.product.partials.import_modal')
@endsection 

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/summernote/summernote-lite.min.js"></script>

<script>
    $(document).ready(function () {
        $('.select2').select2({ width: '100%' });
        $(".summernote").summernote({
            height: 220,
            placeholder: "প্রোডাক্টের বিস্তারিত বর্ণনা লিখুন...",
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });

        // Image Increment
        $(".btn-increment").click(function () {
            var html = $(".clone").html();
            $(".increment-wrapper").append(html);
        });
        $("body").on("click", ".btn-remove-image", function () {
            $(this).parents(".control-group").remove();
        });

        // Product Type Toggle
        $('#product_type').change(function(){
            let type = $(this).val();
            if(type === 'digital'){
                $('#digital_area').slideDown();
                $('#advance_area').slideUp();
                $('#variant_section').slideUp();
            } else {
                $('#digital_area').slideUp();
                $('#advance_area').slideDown();
                $('#variant_section').slideDown();
            }
        });

        // Initialize Select2 with multiple for size
        $('.variant-size-select').select2({
            multiple: true,
            width: '100%'
        });
        
        $('.variant-color-select').select2({
            width: '100%'
        });

        // Dynamic Variant Add/Remove
        let variantIndex = 1;
        $(".add-variant").click(function () {
            let wrapper = $("#variant-wrapper");
            let firstRow = wrapper.find('.variant-item').first().clone();
            
            firstRow.find('.select2-container').remove();
            firstRow.find('input').val('');
            firstRow.find('select').each(function(){
                let oldName = $(this).attr('name');
                if (oldName) {
                    if (oldName.includes('[size_id][]')) {
                        $(this).attr('name', 'variant_price[' + variantIndex + '][size_id][]');
                    } else if (oldName.includes('variant_image')) {
                        $(this).attr('name', 'variant_image[' + variantIndex + '][image]');
                    } else {
                        $(this).attr('name', oldName.replace(/\[\d+\]/, '[' + variantIndex + ']'));
                    }
                }
                if ($(this).attr('type') !== 'file') $(this).val(null).trigger('change');
                else {
                    $(this).val('');
                    $(this).siblings(".variant-img-preview").hide().find("img").attr("src", "");
                }
            });

            firstRow.find('.btn-remove-row').removeClass('d-none');
            wrapper.append(firstRow);
            
            setTimeout(() => {
                firstRow.find('.variant-size-select').select2({
                    multiple: true,
                    width: '100%',
                    dropdownParent: $('#variant-wrapper')
                });
                firstRow.find('.variant-color-select').select2({
                    width: '100%',
                    dropdownParent: $('#variant-wrapper')
                });
            }, 100);
            
            variantIndex++;
        });

        $("body").on("click", ".btn-remove-row", function () {
            $(this).parents(".variant-item").remove();
        });

        // Variant Image Preview & Clear
        $("body").on("change", ".variant-img-input", function() {
            var $input = $(this);
            var $row = $input.closest(".variant-item");
            var $preview = $row.find(".variant-img-preview");
            var $img = $preview.find("img");
            var file = this.files[0];
            if (file && file.type.startsWith("image/")) {
                var reader = new FileReader();
                reader.onload = function(e) { $img.attr("src", e.target.result); $preview.show(); };
                reader.readAsDataURL(file);
            } else { $preview.hide(); $img.attr("src", ""); }
        });
        $("body").on("click", ".variant-img-clear", function() {
            var $row = $(this).closest(".variant-item");
            $row.find(".variant-img-input").val("");
            $row.find(".variant-img-preview").hide().find("img").attr("src", "");
        });

        // Handle form submission - expand multiple sizes into separate entries
        $('#productForm').on('submit', function(e) {
            let variantData = [];
            let variantIdx = 0;
            let rowIndex = 0;
            
            $('#variant-wrapper .variant-item').each(function() {
                let $row = $(this);
                let colorId = $row.find('.variant-color-select').val() || null;
                let selectedSizes = $row.find('.variant-size-select').val() || [];
                let price = $row.find('input[name*="[price]"]').val() || 0;
                let stock = $row.find('input[name*="[stock]"]').val() || 0;
                
                if (!colorId && selectedSizes.length === 0) return;
                
                if (selectedSizes.length > 0) {
                    selectedSizes.forEach(function(sizeId) {
                        variantData.push({ index: variantIdx++, color_id: colorId, size_id: sizeId, price: price, stock: stock, image_row: rowIndex });
                    });
                } else {
                    variantData.push({ index: variantIdx++, color_id: colorId, size_id: null, price: price, stock: stock, image_row: rowIndex });
                }
                rowIndex++;
            });
            
            $(this).find('input[name*="variant_price"]:not([type="file"]), select[name*="variant_price"]').remove();
            
            variantData.forEach(function(v) {
                $('<input>').attr({ type: 'hidden', name: 'variant_price[' + v.index + '][color_id]', value: v.color_id }).appendTo($('#productForm'));
                $('<input>').attr({ type: 'hidden', name: 'variant_price[' + v.index + '][size_id]', value: v.size_id || '' }).appendTo($('#productForm'));
                $('<input>').attr({ type: 'hidden', name: 'variant_price[' + v.index + '][price]', value: v.price }).appendTo($('#productForm'));
                $('<input>').attr({ type: 'hidden', name: 'variant_price[' + v.index + '][stock]', value: v.stock }).appendTo($('#productForm'));
                $('<input>').attr({ type: 'hidden', name: 'variant_price[' + v.index + '][image_row]', value: v.image_row }).appendTo($('#productForm'));
            });
        });

        // Wholesale toggle
        $("#is_wholesale").on("change", function () {
            if ($(this).is(':checked')) {
                $("#wholesale_area").slideDown();
            } else {
                $("#wholesale_area").slideUp();
            }
        });

        // Wholesale pricing tiers
        let wholesaleIndex = 1;
        $("body").on("click", ".add-wholesale-tier", function () {
            let wrapper = $("#wholesale-wrapper");
            let firstRow = wrapper.find(".variant-card").first().clone();
            
            firstRow.find('input').each(function(){
                let oldName = $(this).attr('name');
                if (oldName) {
                    $(this).attr('name', oldName.replace(/\[\d+\]/, '[' + wholesaleIndex + ']'));
                }
                $(this).val('');
            });

            firstRow.find('.add-wholesale-tier').removeClass('btn-success add-wholesale-tier').addClass('btn-outline-danger btn-remove-wholesale').html('<i class="fa fa-trash"></i>');
            wrapper.append(firstRow);
            wholesaleIndex++;
        });

        $("body").on("click", ".btn-remove-wholesale", function () {
            $(this).parents(".variant-card").remove();
        });

        // AJAX Categories
        $("#category_id").on("change", function () {
            var id = $(this).val();
            if (id) {
                $.get("{{url('ajax-product-subcategory')}}?category_id=" + id, function(res){
                    $("#subcategory_id").empty().append('<option value="">Choose Sub Category</option>');
                    $.each(res, function(key, value){
                        $("#subcategory_id").append('<option value="'+key+'">'+value+'</option>');
                    });
                });
            } else {
                $("#subcategory_id").empty().append('<option value="">Choose Sub Category</option>');
                $("#childcategory_id").empty().append('<option value="">Choose Child Category</option>');
            }
        });

        $("#subcategory_id").on("change", function () {
            var id = $(this).val();
            if (id) {
                $.get("{{url('ajax-product-childcategory')}}?subcategory_id=" + id, function(res){
                    $("#childcategory_id").empty().append('<option value="">Choose Child Category</option>');
                    $.each(res, function(key, value){
                        $("#childcategory_id").append('<option value="'+key+'">'+value+'</option>');
                    });
                });
            } else {
                $("#childcategory_id").empty().append('<option value="">Choose Child Category</option>');
            }
        });

        // Live Profit & Discount % Calculator
        function calculateMetrics() {
            var purchase = parseFloat($('#pro_purchase_price').val()) || 0;
            var oldP = parseFloat($('#pro_old_price').val()) || 0;
            var newP = parseFloat($('#pro_new_price').val()) || 0;
            var $area = $('#price_metrics_area');
            var $disc = $('#discount_pill');
            var $prof = $('#profit_pill');
            var hasAny = false;

            // Discount calculation
            if (oldP > 0 && newP > 0 && oldP > newP) {
                var discPct = Math.round(((oldP - newP) / oldP) * 100);
                $disc.html('<i class="fe-tag"></i> ' + discPct + '% Discount').show();
                hasAny = true;
            } else {
                $disc.hide();
            }

            // Profit calculation
            if (newP > 0 && purchase > 0) {
                var profit = newP - purchase;
                var profitPct = Math.round((profit / purchase) * 100);
                if (profit >= 0) {
                    $prof.html('<i class="fe-trending-up"></i> Profit: ৳' + profit.toFixed(2) + ' (' + profitPct + '%)').removeClass('bg-danger text-white').show();
                } else {
                    $prof.html('<i class="fe-trending-down"></i> Loss: ৳' + Math.abs(profit).toFixed(2)).addClass('bg-danger text-white').show();
                }
                hasAny = true;
            } else {
                $prof.hide();
            }

            if (hasAny) {
                $area.removeAttr('style').css('display', 'flex !important');
            } else {
                $area.attr('style', 'display:none !important;');
            }
        }

        $('#pro_purchase_price, #pro_old_price, #pro_new_price').on('input change', calculateMetrics);

        // SEO SERP Live Preview
        $('#product_name_input').on('input', function() {
            var name = $(this).val().trim();
            if (!$('#meta_title_input').val()) {
                $('#serp_title_preview').text(name || 'Your Product Title Preview');
            }
            var slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
            $('#serp_slug_preview').text(slug || 'your-product-slug');
        });

        $('#meta_title_input').on('input', function() {
            var val = $(this).val().trim();
            $('#serp_title_preview').text(val || $('#product_name_input').val() || 'Your Product Title Preview');
        });

        $('#meta_desc_input').on('input', function() {
            var val = $(this).val().trim();
            $('#serp_desc_preview').text(val || 'Product description snippet will appear here on Google search results...');
        });
    });

    // ===== VIDEO LOGIC & 5MB FILE SIZE ENFORCEMENT =====
    (function () {
        var radios   = document.querySelectorAll('input[name="pro_video_source"]');
        var ytSec    = document.getElementById('yt_section_c');
        var upSec    = document.getElementById('up_section_c');
        var limitBadge = document.getElementById('video_limit_badge');

        function switchVideo(val) {
            if (val === 'upload') {
                ytSec.style.display = 'none';
                upSec.style.display = '';
            } else {
                ytSec.style.display = '';
                upSec.style.display = 'none';
            }
        }

        radios.forEach(function (r) {
            r.addEventListener('change', function () { switchVideo(this.value); });
        });

        // YouTube live preview
        var ytInput = document.getElementById('pro_video_c');
        if (ytInput) {
            ytInput.addEventListener('input', function () {
                var val = this.value.trim();
                var id  = extractYtId(val);
                var box = document.getElementById('yt_preview_c');
                var fr  = document.getElementById('yt_iframe_c');
                if (id) {
                    fr.src = 'https://www.youtube.com/embed/' + id;
                    box.style.display = '';
                } else {
                    fr.src = '';
                    box.style.display = 'none';
                }
            });
        }

        // Direct Video Upload: 5MB MAX SIZE ENFORCEMENT
        var upInput = document.getElementById('pro_video_file_c');
        var errBox  = document.getElementById('video_size_error_c');
        var errMsg  = document.getElementById('video_error_msg');
        var sizeTxt = document.getElementById('video_file_size_text');
        var box     = document.getElementById('up_preview_c');
        var vid     = document.getElementById('up_video_c');
        var clearBtn = document.getElementById('clear_video_btn');

        var MAX_VIDEO_SIZE_BYTES = 5 * 1024 * 1024; // 5 MB = 5,242,880 Bytes

        if (upInput) {
            upInput.addEventListener('change', function () {
                var file = this.files[0];
                if (!file) {
                    box.style.display = 'none';
                    errBox.style.display = 'none';
                    vid.src = '';
                    return;
                }

                // Check 5 MB Limit
                if (file.size > MAX_VIDEO_SIZE_BYTES) {
                    var currentMb = (file.size / (1024 * 1024)).toFixed(2);
                    errMsg.innerHTML = 'ভিডিও সাইজ সর্বোচ্চ <strong>৫ MB</strong> হতে পারবে। আপনার ভিডিওর সাইজ <strong>' + currentMb + ' MB</strong>!';
                    errBox.style.display = 'block';
                    
                    // Reset input and preview
                    this.value = '';
                    box.style.display = 'none';
                    vid.src = '';

                    if (limitBadge) {
                        limitBadge.className = 'pf-video-size-badge';
                        limitBadge.innerHTML = '<i class="fa fa-times-circle"></i> ' + currentMb + ' MB (Exceeded)';
                    }
                    return;
                }

                // Valid <= 5MB Video File
                errBox.style.display = 'none';
                var fileMb = (file.size / (1024 * 1024)).toFixed(2);
                sizeTxt.innerText = fileMb + ' MB / 5 MB';
                vid.src = URL.createObjectURL(file);
                box.style.display = '';

                if (limitBadge) {
                    limitBadge.className = 'pf-video-size-badge valid';
                    limitBadge.innerHTML = '<i class="fa fa-check-circle"></i> ' + fileMb + ' MB (Valid)';
                }
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                if (upInput) upInput.value = '';
                if (box) box.style.display = 'none';
                if (vid) vid.src = '';
                if (errBox) errBox.style.display = 'none';
                if (limitBadge) {
                    limitBadge.className = 'pf-video-size-badge';
                    limitBadge.innerHTML = '<i class="fa fa-shield"></i> Max 5 MB';
                }
            });
        }

        function extractYtId(input) {
            if (!input) return null;
            if (/^[a-zA-Z0-9_-]{11}$/.test(input)) return input;
            var m = input.match(/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
            return m ? m[1] : null;
        }
    })();
</script>

<script>
    // ========================================================
    // IMPORT FROM URL / PREFILL LOGIC (DARAZ, ALIBABA, ETC.)
    // ========================================================
    function applyPrefillData(data) {
        if (!data) return;

        // Title
        if (data.name) {
            $('#product_name_input').val(data.name).trigger('input');
        }

        // Pricing & Stock
        if (data.new_price !== undefined && data.new_price !== null) {
            $('#pro_new_price').val(data.new_price).trigger('input');
        }
        if (data.old_price !== undefined && data.old_price !== null) {
            $('#pro_old_price').val(data.old_price).trigger('input');
        }
        if (data.purchase_price !== undefined && data.purchase_price !== null) {
            $('#pro_purchase_price').val(data.purchase_price).trigger('input');
        }
        if (data.stock !== undefined && data.stock !== null) {
            $('input[name="stock"]').val(data.stock);
        }
        if (data.pro_unit) {
            $('input[name="pro_unit"]').val(data.pro_unit);
        }

        // Description (Summernote)
        if (data.description) {
            try {
                $('textarea.summernote').summernote('code', data.description);
            } catch (e) {
                $('textarea.summernote').val(data.description);
            }
        }

        // SEO Fields
        if (data.meta_title || data.name) {
            $('#meta_title_input').val(data.meta_title || data.name).trigger('input');
        }
        if (data.meta_description) {
            $('#meta_desc_input').val(data.meta_description).trigger('input');
        }

        // Category & Subcategory
        const catId = data.suggested_category_id || data.category_id;
        if (catId && $('#category_id option[value="' + catId + '"]').length > 0) {
            $('#category_id').val(catId).trigger('change');
            if (data.subcategory_id) {
                setTimeout(function () {
                    $('#subcategory_id').val(data.subcategory_id).trigger('change');
                }, 600);
            }
        }

        // Remote Images
        const images = data.images || [];
        if (images.length > 0) {
            renderRemoteImages(images);
        }
    }

    function renderRemoteImages(images) {
        const $area = $('#remote_images_preview_area');
        const $grid = $('#remote_images_grid');
        const $count = $('#remote_images_count');

        $grid.empty();
        $count.text(images.length);

        images.forEach(function (imgUrl, idx) {
            const isMain = (idx === 0);
            const itemHtml = `
                <div class="position-relative border rounded p-1 bg-white remote-img-card" style="width: 72px; height: 72px;">
                    <img src="${imgUrl}" alt="Preview" style="width:100%; height:100%; object-fit:cover; border-radius:4px;" loading="lazy">
                    <input type="hidden" name="imported_remote_images[]" value="${imgUrl}">
                    <button type="button" class="btn btn-xs btn-danger position-absolute btn-remove-remote-img" style="top:-6px; right:-6px; border-radius:50%; width:18px; height:18px; padding:0; display:flex; align-items:center; justify-content:center;" title="Remove">
                        <i class="fe-x" style="font-size:10px;"></i>
                    </button>
                    ${isMain ? '<span class="badge bg-primary position-absolute" style="bottom:2px; left:2px; font-size:8.5px; padding:1px 3px;">Main</span>' : ''}
                </div>
            `;
            $grid.append(itemHtml);
        });

        $area.removeClass('d-none');
        $('.gallery-file-input').removeAttr('required');
    }

    // Remove single remote image
    $(document).on('click', '.btn-remove-remote-img', function () {
        $(this).closest('.remote-img-card').remove();
        const count = $('#remote_images_grid .remote-img-card').length;
        $('#remote_images_count').text(count);
        if (count === 0) {
            $('#remote_images_preview_area').addClass('d-none');
            $('.gallery-file-input').first().attr('required', 'required');
        }
    });

    // Clear all remote images
    $('#btn_clear_remote_images').on('click', function () {
        $('#remote_images_grid').empty();
        $('#remote_images_preview_area').addClass('d-none');
        $('.gallery-file-input').first().attr('required', 'required');
    });

    // Fast URL Paste Button
    $('#btn_page_paste_url').on('click', async function () {
        try {
            const text = await navigator.clipboard.readText();
            if (text) {
                $('#page_quick_import_url').val(text.trim());
            }
        } catch (err) {
            toastr.info('ক্লিপবোর্ড থেকে সরাসরি লিঙ্ক পেতে অনুমতি প্রয়োজন অথবা বক্সে পেস্ট করুন।');
        }
    });

    // Fast URL Fetch & Auto-Fill Button
    $('#btn_page_quick_fetch').on('click', function () {
        const url = $('#page_quick_import_url').val().trim();
        if (!url) {
            toastr.warning('অনুগ্রহ করে একটি সঠিক প্রোডাক্ট লিঙ্ক প্রদান করুন!');
            $('#page_quick_import_url').focus();
            return;
        }

        const $btn = $(this);
        const $spinner = $('#page_fetch_spinner');
        const $text = $('#page_fetch_btn_text');

        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $text.html('তথ্য সংগ্রহ হচ্ছে...');

        $.ajax({
            type: 'POST',
            url: "{{ route('products.import_url_fetch') }}",
            data: {
                _token: "{{ csrf_token() }}",
                url: url
            },
            success: function (res) {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $text.html('<i class="fe-download-cloud me-1"></i> Fetch & Fill Form');

                if (res.status === 'success' && res.data) {
                    applyPrefillData(res.data);
                    toastr.success('✅ প্রোডাক্টের তথ্য সফলভাবে ফর্মে সেট হয়েছে! অনুগ্রহ করে যাচাই করুন।');
                } else {
                    toastr.error(res.message || 'প্রোডাক্ট তথ্য পাওয়া যায়নি।');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $text.html('<i class="fe-download-cloud me-1"></i> Fetch & Fill Form');
                let msg = 'প্রোডাক্ট তথ্য সংগ্রহে সমস্যা হয়েছে।';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
            }
        });
    });

    // Check if prefill data exists from Import Modal redirect
    $(document).ready(function () {
        try {
            const stored = sessionStorage.getItem('imported_product_prefill');
            if (stored) {
                const prefillData = JSON.parse(stored);
                sessionStorage.removeItem('imported_product_prefill');
                setTimeout(function () {
                    applyPrefillData(prefillData);
                    toastr.success('✅ ইমপোর্ট করা প্রোডাক্টের তথ্য সফলভাবে ফর্মে বসে গেছে!');
                }, 400);
            }
        } catch (e) {
            console.error('Prefill read error:', e);
        }
    });
</script>
@include('backEnd.product.partials.ai_description_script')
@endsection
