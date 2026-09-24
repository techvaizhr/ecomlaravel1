@extends('backEnd.layouts.master')
@section('title','Product Edit')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />

@include('backEnd.product.partials.product_form_styles')
@endsection

@section('content')
<div class="container-fluid product-form-page">
    <div class="pf-page-header">
        <div>
            <h4>প্রোডাক্ট সম্পাদনা</h4>
            <p class="pf-sub mb-0">{{ $edit_data->name }} — সকল অপশন ও ফিচার আগের মতোই থাকবে, শুধু UI আপডেট।</p>
        </div>
        <div class="pf-header-actions">
            <a href="{{ request('return_url') ?? (empty($edit_data->vendor_id) ? route('inhouse.products.index') : route('products.index')) }}" class="pf-btn-manage"><i class="fe-list"></i> প্রোডাক্ট তালিকা</a>
        </div>
    </div>
    <form action="{{route('products.update')}}" method="POST" data-parsley-validate="" enctype="multipart/form-data" name="editForm">
        @csrf
        <input type="hidden" value="{{$edit_data->id}}" name="id" />
        <input type="hidden" name="return_url" value="{{ request('return_url') ?? old('return_url') ?? '' }}" />

        <div class="row">
            <div class="col-lg-8">
                
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="section-title"><i class="fe-info me-1"></i> Basic Information</div>

                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{$edit_data->name }}" id="name" required />
                            @error('name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label">Categories *</label>
                                <select class="form-control form-select select2 @error('category_id') is-invalid @enderror"
                                        name="category_id" id="category_id" required>
                                    <optgroup>
                                        <option value="">Select..</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}" @if($edit_data->category_id==$category->id) selected @endif>
                                                {{$category->name}}
                                            </option>
                                            @foreach ($category->childrenCategories as $childCategory)
                                                <option value="{{$childCategory->id}}" @if($edit_data->category_id==$childCategory->id) selected @endif>
                                                    - {{$childCategory->name}}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('category_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="subcategory_id" class="form-label">SubCategories</label>
                                <select class="form-control form-select select2 @error('subcategory_id') is-invalid @enderror"
                                        id="subcategory_id" name="subcategory_id">
                                    <optgroup>
                                        <option value="">Select..</option>
                                        @foreach($subcategory as $value)
                                            <option value="{{$value->id}}" @if($edit_data->subcategory_id==$value->id) selected @endif>
                                                {{$value->subcategoryName}}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('subcategory_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="childcategory_id" class="form-label">Child Categories</label>
                                <select class="form-control form-select select2 @error('childcategory_id') is-invalid @enderror"
                                        id="childcategory_id" name="childcategory_id">
                                    <optgroup>
                                        <option value="">Select..</option>
                                        @foreach($childcategory as $value)
                                            <option value="{{$value->id}}" @if($edit_data->childcategory_id==$value->id) selected @endif>
                                                {{$value->childcategoryName}}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('childcategory_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="pf-desc-label-row">
                                <label for="description" class="form-label mb-0">Description</label>
                                @include('backEnd.product.partials.ai_description_button')
                            </div>
                            <textarea name="description" rows="6"
                                      class="summernote form-control @error('description') is-invalid @enderror">
                                {{$edit_data->description}}
                            </textarea>
                            @error('description')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="note" class="form-label">Note</label>
                            <textarea name="note" rows="2"
                                      class="form-control @error('note') is-invalid @enderror">{{$edit_data->note}}</textarea>
                            @error('note')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="d-block form-label">Wholesale Product</label>
                            <label class="switch">
                                <input type="checkbox" value="1" name="is_wholesale" id="is_wholesale" {{ old('is_wholesale', $edit_data->is_wholesale ?? 0) ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- WHOLESALE PRICING TIERS --}}
                <div id="wholesale_area" style="{{ old('is_wholesale', $edit_data->is_wholesale ?? 0) ? 'display:block;' : 'display:none;' }}" class="card mb-4">
                    <div class="card-body">
                        <div class="section-title d-flex justify-content-between align-items-center">
                            <span><i class="fe-dollar-sign me-1"></i> Wholesale Pricing Tiers</span>
                            <button type="button" class="btn btn-sm btn-success add-wholesale-tier rounded-pill px-3"><i class="fa fa-plus me-1"></i> Add New Tier</button>
                        </div>
                        
                        <div id="wholesale-wrapper">
                            @if($wholesalePrices && $wholesalePrices->count() > 0)
                                @foreach($wholesalePrices as $key => $tier)
                                    <div class="variant-card">
                                        <div class="row align-items-end">
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Min Quantity</label>
                                                <input type="number" name="wholesale_price[{{ $key }}][min_quantity]" class="form-control" 
                                                       value="{{ old('wholesale_price.'.$key.'.min_quantity', $tier->min_quantity) }}">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Max Quantity</label>
                                                <input type="number" name="wholesale_price[{{ $key }}][max_quantity]" class="form-control" 
                                                       value="{{ old('wholesale_price.'.$key.'.max_quantity', $tier->max_quantity) }}" placeholder="Optional">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <label class="form-label">Wholesale Price</label>
                                                <input type="number" step="0.01" name="wholesale_price[{{ $key }}][wholesale_price]" class="form-control" 
                                                       value="{{ old('wholesale_price.'.$key.'.wholesale_price', $tier->wholesale_price) }}">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <label class="form-label">Stock Qty</label>
                                                <input type="number" name="wholesale_price[{{ $key }}][stock]" class="form-control" 
                                                       value="{{ old('wholesale_price.'.$key.'.stock', $tier->stock ?? 0) }}" placeholder="0">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                @if($loop->first)
                                                    <button type="button" class="btn btn-success add-wholesale-tier w-100"><i class="fa fa-plus"></i></button>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-remove-wholesale w-100"><i class="fa fa-trash"></i></button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="variant-card">
                                    <div class="row align-items-end">
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Min Quantity</label>
                                            <input type="number" name="wholesale_price[0][min_quantity]" class="form-control" placeholder="e.g. 10">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Max Quantity</label>
                                            <input type="number" name="wholesale_price[0][max_quantity]" class="form-control" placeholder="e.g. 50 (optional)">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Wholesale Price</label>
                                            <input type="number" step="0.01" name="wholesale_price[0][wholesale_price]" class="form-control" placeholder="0.00">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Stock Qty</label>
                                            <input type="number" name="wholesale_price[0][stock]" class="form-control" placeholder="0">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <button type="button" class="btn btn-success add-wholesale-tier w-100"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- VARIANT PRICE CARD --}}
                <div class="card mb-4" id="variant_section">
                    <div class="card-body">
                        <div class="section-title d-flex justify-content-between align-items-center">
                            <span><i class="fe-layers me-1"></i> Product Variants (Color & Size)</span>
                            <button type="button" class="btn btn-sm btn-success add-variant rounded-pill px-3"><i class="fa fa-plus me-1"></i> Add New Variant</button>
                        </div>

                        <div id="variant-wrapper">
                            @php
                                // Group variants by color_id, then by size_id
                                // First, group by color_id (including null colors)
                                $groupedByColor = $edit_data->variantPrices->groupBy(function($variant) {
                                    return $variant->color_id ?? 'no_color';
                                });
                                $variantIndex = 0;
                            @endphp
                            
                            @forelse($groupedByColor as $colorId => $variantsForColor)
                                @php
                                    // Get all size IDs for this color group
                                    $sizeIds = $variantsForColor->pluck('size_id')->filter()->toArray();
                                    $firstVariant = $variantsForColor->first();
                                @endphp
                                <div class="variant-card variant-item">
                                    <div class="row align-items-end">
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Color</label>
                                            <select name="variant_price[{{ $variantIndex }}][color_id]" class="form-control select2 variant-color-select">
                                                <option value="">Select Color (Optional)</option>
                                                @foreach($totalcolors as $color)
                                                    <option value="{{ $color->id }}" {{ $colorId == $color->id ? 'selected' : '' }}>
                                                        {{ $color->colorName ?? $color->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Size</label>
                                            <select name="variant_price[{{ $variantIndex }}][size_id][]" class="form-control select2 variant-size-select" multiple>
                                                @foreach($totalsizes as $size)
                                                    <option value="{{ $size->id }}" {{ in_array($size->id, $sizeIds) ? 'selected' : '' }}>
                                                        {{ $size->sizeName ?? $size->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Price</label>
                                            <input type="number" step="0.01" name="variant_price[{{ $variantIndex }}][price]"
                                                   value="{{ $firstVariant->price }}" class="form-control" placeholder="Enter Price">
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Stock</label>
                                            <input type="number" name="variant_price[{{ $variantIndex }}][stock]"
                                                   value="{{ $firstVariant->stock }}" class="form-control" placeholder="0">
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Variant Image</label>
                                            @php
                                                $variantColorId = ($colorId === 'no_color') ? null : $colorId;
                                                $variantImages = $edit_data->images->filter(function($img) use ($variantColorId, $sizeIds) {
                                                    $colorMatch = ($img->color_id == $variantColorId) || (empty($img->color_id) && empty($variantColorId));
                                                    $sizeMatch = empty($sizeIds) ? empty($img->size_id) : in_array($img->size_id, $sizeIds);
                                                    return $colorMatch && $sizeMatch;
                                                })->unique('image');
                                            @endphp
                                            @if($variantImages->isNotEmpty())
                                                <div class="variant-existing-imgs d-flex flex-wrap gap-1 mb-2">
                                                    @foreach($variantImages as $vImg)
                                                        <div class="position-relative">
                                                            <img src="{{ asset($vImg->image) }}" class="rounded border" style="width:50px;height:50px;object-fit:cover;" alt="">
                                                            <a href="{{ route('products.image.destroy', ['id' => $vImg->id]) }}" class="btn btn-xs btn-danger position-absolute top-0 end-0 rounded-circle" style="padding:0 4px;top:-4px;right:-4px;" onclick="return confirm('Delete this image?')"><i class="mdi mdi-close"></i></a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="variant-img-upload">
                                                <input type="file" name="variant_image[{{ $variantIndex }}][image]" class="form-control form-control-sm variant-img-input" accept="image/*">
                                                <div class="variant-img-preview mt-1" style="display:none;">
                                                    <img src="" alt="Preview" class="rounded border" style="max-width:60px;max-height:60px;object-fit:cover;">
                                                    <button type="button" class="btn btn-sm btn-danger variant-img-clear ms-1" title="Remove"><i class="fe-x"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-1 mb-2 d-flex justify-content-end">
                                            @if($loop->first)
                                                <button type="button" class="btn btn-success add-variant" style="margin-top:5px;">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-danger remove-variant" style="margin-top:5px;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <small class="text-muted">
                                                <i class="fa fa-info-circle"></i> 
                                                আপনি শুধু Color, শুধু Size, অথবা Color + Size উভয় add করতে পারবেন
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                @php $variantIndex++; @endphp
                            @empty
                                <div class="variant-card variant-item">
                                    <div class="row align-items-end">
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Color <small class="text-muted">(Optional)</small></label>
                                            <select name="variant_price[0][color_id]" class="form-control select2 variant-color-select">
                                                <option value="">Select Color (Optional)</option>
                                                @foreach($totalcolors as $color)
                                                    <option value="{{ $color->id }}">{{ $color->colorName ?? $color->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Size <small class="text-muted">(Optional)</small></label>
                                            <select name="variant_price[0][size_id][]" class="form-control select2 variant-size-select" multiple>
                                                @foreach($totalsizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->sizeName ?? $size->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Price <small class="text-muted">(Optional)</small></label>
                                            <input type="number" step="0.01" name="variant_price[0][price]"
                                                   class="form-control" placeholder="Enter Price">
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Stock</label>
                                            <input type="number" name="variant_price[0][stock]" class="form-control" placeholder="0">
                                        </div>

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Variant Image</label>
                                            <div class="variant-img-upload">
                                                <input type="file" name="variant_image[0][image]" class="form-control form-control-sm variant-img-input" accept="image/*">
                                                <div class="variant-img-preview mt-1" style="display:none;">
                                                    <img src="" alt="Preview" class="rounded border" style="max-width:60px;max-height:60px;object-fit:cover;">
                                                    <button type="button" class="btn btn-sm btn-danger variant-img-clear ms-1" title="Remove"><i class="fe-x"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-1 mb-2 d-flex justify-content-end">
                                            <button type="button" class="btn btn-success add-variant" style="margin-top:5px;">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <small class="text-muted">
                                                <i class="fa fa-info-circle"></i> 
                                                আপনি শুধু Color, শুধু Size, অথবা Color + Size উভয় add করতে পারবেন
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- SEO CONFIG CARD --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="section-title"><i class="fe-search me-1"></i> SEO Configuration</div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" id="meta_title" class="form-control"
                                       value="{{ $edit_data->meta_title ?? $edit_data->name }}"
                                       placeholder="Enter meta title">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"
                                       value="{{ $edit_data->meta_keywords ?? '' }}"
                                       placeholder="meta1, meta2, meta3">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" class="form-control" rows="3"
                                          placeholder="Enter short SEO description...">{{ $edit_data->meta_description ?? \Illuminate\Support\Str::limit(strip_tags($edit_data->description), 160) }}</textarea>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="meta_image" class="form-label">Meta Image (og:image)</label>
                                <input type="file" name="meta_image" id="meta_image" class="form-control">

                                @if(!empty($edit_data->meta_image))
                                    <div class="mt-2">
                                        <img src="{{ asset($edit_data->meta_image) }}" alt="Meta Image"
                                             class="border rounded" width="120">
                                    </div>
                                @endif
                                <small class="text-muted d-block mt-1">Recommended size: 1200x630px</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 pf-sidebar-col">

                @include('backEnd.product.partials.edit_sidebar')
            </div>
            </div>
    </form>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs//summernote/summernote-lite.min.js"></script>

<script>
    $(".summernote").summernote({
        placeholder: "Enter Your Text Here",
    });
</script>

<script>
    $(document).ready(function () {
        // Gallery image add
        $(".increment-wrapper .btn-increment").click(function () {
            var html = $(".clone").html();
            $(".increment-wrapper").append(html);
        });
        $("body").on("click", ".btn-remove-image", function () {
            $(this).parents(".control-group").remove();
        });

        $(".select2").select2();
    });
</script>

<script>
    // Category to subcategory & childcategory
    $("#category_id").on("change", function () {
        var ajaxId = $(this).val();
        if (ajaxId) {
            $.ajax({
                type: "GET",
                url: "{{url('ajax-product-subcategory')}}?category_id=" + ajaxId,
                success: function (res) {
                    if (res) {
                        $("#subcategory_id").empty();
                        $("#subcategory_id").append('<option value="0">Choose...</option>');
                        $.each(res, function (key, value) {
                            $("#subcategory_id").append('<option value="' + key + '">' + value + "</option>");
                        });
                    } else {
                        $("#subcategory_id").empty();
                    }
                },
            });
        } else {
            $("#subcategory_id").empty();
        }
    });

    $("#subcategory_id").on("change", function () {
        var ajaxId = $(this).val();
        if (ajaxId) {
            $.ajax({
                type: "GET",
                url: "{{url('ajax-product-childcategory')}}?subcategory_id=" + ajaxId,
                success: function (res) {
                    if (res) {
                        $("#childcategory_id").empty();
                        $("#childcategory_id").append('<option value="0">Choose...</option>');
                        $.each(res, function (key, value) {
                            $("#childcategory_id").append('<option value="' + key + '">' + value + "</option>");
                        });
                    } else {
                        $("#childcategory_id").empty();
                    }
                },
            });
        } else {
            $("#childcategory_id").empty();
        }
    });

    // Set selected values on load
    document.forms["editForm"].elements["category_id"].value = "{{$edit_data->category_id}}";
    document.forms["editForm"].elements["subcategory_id"].value = "{{$edit_data->subcategory_id}}";
    document.forms["editForm"].elements["childcategory_id"].value = "{{$edit_data->childcategory_id}}";
</script>

{{-- Variant add/remove with Multiple Size Select --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    let variantIndex = {{ $edit_data->variantPrices->count() ?? 1 }};
    
    // Initialize Select2 with multiple for size
    $('.variant-size-select').select2({
        multiple: true,
        width: '100%'
    });
    
    $('.variant-color-select').select2({
        width: '100%'
    });

    // Add new variant row
    document.body.addEventListener('click', function (e) {
        const target = e.target.closest('.add-variant, .remove-variant');
        if (!target) return;

        if (target.classList.contains('add-variant')) {
            const wrapper = document.getElementById('variant-wrapper');
            const firstRow = wrapper.querySelector('.variant-item');
            if (!firstRow) return;

            const newRow = $(firstRow.cloneNode(true));
            newRow.find('.select2-container').remove();

            newRow.find('.variant-existing-imgs').remove();
            newRow.find('input, select').each(function () {
                const oldName = $(this).attr('name');
                if (oldName) {
                    if (oldName.includes('[size_id][]')) {
                        $(this).attr('name', 'variant_price[' + variantIndex + '][size_id][]');
                    } else if (oldName.includes('variant_image')) {
                        $(this).attr('name', 'variant_image[' + variantIndex + '][image]');
                    } else {
                        $(this).attr('name', oldName.replace(/\[\d+\]/, '[' + variantIndex + ']'));
                    }
                }
                if ($(this).attr('type') === 'file') {
                    $(this).val('');
                    $(this).siblings('.variant-img-preview').hide().find('img').attr('src', '');
                } else if ($(this).is('input')) $(this).val('');
                else if ($(this).is('select')) $(this).val(null).trigger('change');
            });

            newRow.find('.add-variant')
                .removeClass('btn-success add-variant')
                .addClass('btn-danger remove-variant')
                .html('<i class="fa fa-trash"></i>');

            newRow.appendTo(wrapper);

            // Reinitialize Select2 for new row
            setTimeout(() => {
                newRow.find('.variant-size-select').select2({
                    multiple: true,
                    width: '100%',
                    dropdownParent: $('#variant-wrapper')
                });
                newRow.find('.variant-color-select').select2({
                    width: '100%',
                    dropdownParent: $('#variant-wrapper')
                });
            }, 100);

            variantIndex++;
        }

        if (target.classList.contains('remove-variant')) {
            target.closest('.variant-item').remove();
        }
    });

    // Variant Image Preview & Clear
    $(document).on('change', '.variant-img-input', function() {
        var $input = $(this);
        var $preview = $input.siblings('.variant-img-preview');
        var $img = $preview.find('img');
        var file = this.files[0];
        if (file && file.type.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) { $img.attr('src', e.target.result); $preview.show(); };
            reader.readAsDataURL(file);
        } else { $preview.hide(); $img.attr('src', ''); }
    });
    $(document).on('click', '.variant-img-clear', function() {
        var $preview = $(this).closest('.variant-img-preview');
        $preview.siblings('.variant-img-input').val('');
        $preview.find('img').attr('src', '');
        $preview.hide();
    });
    
    // Handle form submission - expand multiple sizes into separate entries
    $('form[name="editForm"]').on('submit', function(e) {
        let formData = new FormData(this);
        let variantData = [];
        let variantIndex = 0;
        let rowIndex = 0;
        
            $('#variant-wrapper .variant-item').each(function() {
                let $row = $(this);
                let colorId = $row.find('.variant-color-select').val() || null;
                let selectedSizes = $row.find('.variant-size-select').val() || [];
                let price = $row.find('input[name*="[price]"]').val() || 0;
                let stock = $row.find('input[name*="[stock]"]').val() || 0;
                
                // Validate: At least color or size must be selected
                if (!colorId && selectedSizes.length === 0) {
                    // Skip if neither color nor size is selected
                    return;
                }
                
                // If sizes are selected, create separate entry for each size
                if (selectedSizes.length > 0) {
                    selectedSizes.forEach(function(sizeId) {
                        variantData.push({ index: variantIndex++, color_id: colorId, size_id: sizeId, price: price, stock: stock, image_row: rowIndex });
                    });
                } else {
                    variantData.push({ index: variantIndex++, color_id: colorId, size_id: null, price: price, stock: stock, image_row: rowIndex });
                }
                rowIndex++;
            });
        
        $(this).find('input[name*="variant_price"]:not([type="file"]), select[name*="variant_price"]').remove();
        
        // Add new hidden inputs for each variant
        variantData.forEach(function(variant) {
            $('<input>').attr({
                type: 'hidden',
                name: 'variant_price[' + variant.index + '][color_id]',
                value: variant.color_id
            }).appendTo($('form[name="editForm"]'));
            
            $('<input>').attr({
                type: 'hidden',
                name: 'variant_price[' + variant.index + '][size_id]',
                value: variant.size_id
            }).appendTo($('form[name="editForm"]'));
            
            $('<input>').attr({
                type: 'hidden',
                name: 'variant_price[' + variant.index + '][price]',
                value: variant.price
            }).appendTo($('form[name="editForm"]'));
            
            $('<input>').attr({
                type: 'hidden',
                name: 'variant_price[' + variant.index + '][stock]',
                value: variant.stock
            }).appendTo($('form[name="editForm"]'));
            
            $('<input>').attr({
                type: 'hidden',
                name: 'variant_price[' + variant.index + '][image_row]',
                value: variant.image_row
            }).appendTo($('form[name="editForm"]'));
        });
    });
});
</script>

{{-- Product type toggle --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    function toggleFields() {
        let type = document.getElementById('product_type').value;
        if (type === 'digital') {
            document.getElementById('digital_area').style.display = 'block';
            document.getElementById('advance_area').style.display = 'none';
        } else {
            document.getElementById('digital_area').style.display = 'none';
            document.getElementById('advance_area').style.display = 'block';
        }
    }

    document.getElementById('product_type').addEventListener('change', toggleFields);
    
    // Wholesale toggle
    document.getElementById('is_wholesale').addEventListener('change', function() {
        var wholesaleArea = document.getElementById('wholesale_area');
        if (this.checked) {
            wholesaleArea.style.display = 'block';
            wholesaleArea.querySelectorAll('input').forEach(function(input) {
                input.setAttribute('required', 'required');
            });
        } else {
            wholesaleArea.style.display = 'none';
            wholesaleArea.querySelectorAll('input').forEach(function(input) {
                input.removeAttribute('required');
            });
        }
    });
    toggleFields(); // initial
    // Wholesale pricing tiers
    let wholesaleIndex = {{ ($wholesalePrices && $wholesalePrices->count() > 0) ? $wholesalePrices->count() : 1 }};
    $('.add-wholesale-tier').on('click', function() {
        let wrapper = $('#wholesale-wrapper');
        let firstRow = wrapper.find('.variant-card').first().clone();
        
        firstRow.find('input').each(function(){
            let oldName = $(this).attr('name');
            $(this).attr('name', oldName.replace(/\[\d+\]/, '[' + wholesaleIndex + ']'));
            $(this).val('');
        });

        firstRow.find('.btn-remove-wholesale').removeClass('d-none');
        wrapper.append(firstRow);
        wholesaleIndex++;
    });
    
    $("body").on("click", ".btn-remove-wholesale", function () {
        $(this).parents(".variant-card").remove();
    });

    // Variant Image Add/Remove
    let variantImgIndex = 1;
    $(".add-variant-image").click(function () {
        let wrapper = $("#variant-image-wrapper");
        let firstRow = wrapper.find(".variant-image-row").first().clone();
        firstRow.find('.select2-container').remove();
        firstRow.find('input[type="file"]').val('');
        firstRow.find('select').each(function(){
            let name = $(this).attr('name');
            if (name) $(this).attr('name', name.replace(/\[\d+\]/, '[' + variantImgIndex + ']'));
            $(this).val(null);
        });
        firstRow.find('input[type="file"]').each(function(){
            let name = $(this).attr('name');
            if (name) $(this).attr('name', name.replace(/\[\d+\]/, '[' + variantImgIndex + ']'));
        });
        firstRow.find('.btn-remove-variant-img').show();
        wrapper.append(firstRow);
        firstRow.find('.variant-img-color, .variant-img-size').select2({ width: '100%' });
        variantImgIndex++;
    });
    $("body").on("click", ".btn-remove-variant-img", function () {
        $(this).closest(".variant-image-row").remove();
    });
});

// ===== VIDEO SOURCE SWITCHER (Edit) =====
(function () {
    var radios = document.querySelectorAll('input[name="pro_video_source"]');
    var ytSec  = document.getElementById('yt_section_e');
    var upSec  = document.getElementById('up_section_e');

    function switchVideo(val) {
        if (val === 'upload') {
            if (ytSec) ytSec.style.display = 'none';
            if (upSec) upSec.style.display = '';
        } else {
            if (ytSec) ytSec.style.display = '';
            if (upSec) upSec.style.display = 'none';
        }
    }

    radios.forEach(function (r) {
        r.addEventListener('change', function () { switchVideo(this.value); });
    });

    // YouTube live preview
    var ytInput = document.getElementById('pro_video_e');
    if (ytInput) {
        ytInput.addEventListener('input', function () {
            var val = this.value.trim();
            var id  = extractYtId(val);
            var box = document.getElementById('yt_preview_e');
            var fr  = document.getElementById('yt_iframe_e');
            if (id && box && fr) {
                fr.src = 'https://www.youtube.com/embed/' + id;
                box.style.display = '';
            } else if (box && fr) {
                fr.src = '';
                box.style.display = 'none';
            }
        });
    }

    // Upload local preview & 5MB Size Validation
    var upInput = document.getElementById('pro_video_file_e');
    var errBox  = document.getElementById('video_size_error_e');
    var errMsg  = document.getElementById('video_error_msg_e');
    var sizeTxt = document.getElementById('video_file_size_text_e');
    var box     = document.getElementById('up_preview_e');
    var vid     = document.getElementById('up_video_e');
    var clearBtn = document.getElementById('clear_video_btn_e');
    var limitBadge = document.getElementById('video_limit_badge_e');

    var MAX_VIDEO_SIZE_BYTES = 5 * 1024 * 1024; // 5 MB

    if (upInput) {
        upInput.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) {
                if (box) box.style.display = 'none';
                if (errBox) errBox.style.display = 'none';
                if (vid) vid.src = '';
                return;
            }

            // Check 5 MB Limit
            if (file.size > MAX_VIDEO_SIZE_BYTES) {
                var currentMb = (file.size / (1024 * 1024)).toFixed(2);
                if (errMsg) errMsg.innerHTML = 'ভিডিও সাইজ সর্বোচ্চ <strong>৫ MB</strong> হতে পারবে। আপনার ভিডিওর সাইজ <strong>' + currentMb + ' MB</strong>!';
                if (errBox) errBox.style.display = 'block';
                
                this.value = '';
                if (box) box.style.display = 'none';
                if (vid) vid.src = '';

                if (limitBadge) {
                    limitBadge.className = 'pf-video-size-badge';
                    limitBadge.innerHTML = '<i class="fa fa-times-circle"></i> ' + currentMb + ' MB (Exceeded)';
                }
                return;
            }

            // Valid <= 5MB Video File
            if (errBox) errBox.style.display = 'none';
            var fileMb = (file.size / (1024 * 1024)).toFixed(2);
            if (sizeTxt) sizeTxt.innerText = fileMb + ' MB / 5 MB';
            if (vid) vid.src = URL.createObjectURL(file);
            if (box) box.style.display = '';

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

{{-- Gallery Images Drag & Drop and Main Image Selection (Existing & New) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------
    // 1. Existing Gallery Images Drag & Drop & Set Main
    // ----------------------------------------------------
    let draggedExistingCard = null;

    function initExistingDragAndDrop() {
        const cards = document.querySelectorAll('#existing_images_grid .existing-img-card');
        cards.forEach(card => {
            card.removeEventListener('dragstart', handleExistingDragStart);
            card.removeEventListener('dragover', handleExistingDragOver);
            card.removeEventListener('dragleave', handleExistingDragLeave);
            card.removeEventListener('drop', handleExistingDrop);
            card.removeEventListener('dragend', handleExistingDragEnd);

            card.addEventListener('dragstart', handleExistingDragStart);
            card.addEventListener('dragover', handleExistingDragOver);
            card.addEventListener('dragleave', handleExistingDragLeave);
            card.addEventListener('drop', handleExistingDrop);
            card.addEventListener('dragend', handleExistingDragEnd);
        });
    }

    function handleExistingDragStart(e) {
        draggedExistingCard = this;
        this.style.opacity = '0.4';
        this.style.cursor = 'grabbing';
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', this.getAttribute('data-id'));
    }

    function handleExistingDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        if (this !== draggedExistingCard) {
            this.style.transform = 'scale(1.05)';
            this.style.borderColor = '#2563eb';
        }
    }

    function handleExistingDragLeave() {
        this.style.transform = 'scale(1)';
        this.style.borderColor = '#e2e8f0';
    }

    function handleExistingDrop(e) {
        e.preventDefault();
        this.style.transform = 'scale(1)';
        this.style.borderColor = '#e2e8f0';

        const grid = document.getElementById('existing_images_grid');
        if (draggedExistingCard && this !== draggedExistingCard && grid) {
            const allCards = Array.from(grid.querySelectorAll('.existing-img-card'));
            const draggedIdx = allCards.indexOf(draggedExistingCard);
            const targetIdx = allCards.indexOf(this);

            if (draggedIdx < targetIdx) {
                this.after(draggedExistingCard);
            } else {
                this.before(draggedExistingCard);
            }
            updateExistingImageBadges();
        }
    }

    function handleExistingDragEnd() {
        this.style.opacity = '1';
        this.style.cursor = 'grab';
        document.querySelectorAll('#existing_images_grid .existing-img-card').forEach(card => {
            card.style.transform = 'scale(1)';
            card.style.borderColor = '#e2e8f0';
        });
        updateExistingImageBadges();
    }

    function updateExistingImageBadges() {
        const cards = document.querySelectorAll('#existing_images_grid .existing-img-card');
        cards.forEach((card, idx) => {
            const footer = card.querySelector('.existing-card-footer');
            if (!footer) return;

            if (idx === 0) {
                card.style.borderColor = '#2563eb';
                card.style.boxShadow = '0 0 0 2px rgba(37,99,235,0.25)';
                footer.innerHTML = `<span class="badge bg-primary w-100 py-0.5" style="font-size:8.5px; border-radius:3px;"><i class="fe-star me-0.5"></i> Main</span>`;
            } else {
                card.style.borderColor = '#e2e8f0';
                card.style.boxShadow = 'none';
                footer.innerHTML = `<button type="button" class="btn btn-xs btn-light w-100 py-0 border shadow-sm btn-set-existing-main" style="font-size:8px; font-weight:600; color:#1e293b; border-radius:3px;" title="প্রধান ছবি নির্ধারণ করুন"><i class="fe-star text-warning me-0.5"></i> Set Main</button>`;
            }
        });
    }

    // Click "⭐ Set Main" on an existing image
    $(document).on('click', '.btn-set-existing-main', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const card = $(this).closest('.existing-img-card')[0];
        const grid = document.getElementById('existing_images_grid');
        if (card && grid) {
            grid.prepend(card);
            updateExistingImageBadges();
            toastr.info('প্রধান ছবি নির্বাচন করা হয়েছে!');
        }
    });

    initExistingDragAndDrop();
    updateExistingImageBadges();

    // ----------------------------------------------------
    // 2. New Gallery Images Upload with Drag & Drop & Sync
    // ----------------------------------------------------
    let newGalleryFilesEdit = [];

    $('#btn_pick_gallery_files_edit').on('click', function () {
        $('#local_gallery_file_input_edit').click();
    });

    $('#local_gallery_file_input_edit').on('change', function () {
        const files = Array.from(this.files);
        if (files.length === 0) return;

        files.forEach(file => {
            if (file.type.startsWith('image/')) {
                newGalleryFilesEdit.push(file);
            }
        });

        this.value = '';
        syncNewGalleryFilesEdit();
        renderNewGalleryGridEdit();
    });

    function syncNewGalleryFilesEdit() {
        try {
            const dt = new DataTransfer();
            newGalleryFilesEdit.forEach(file => dt.items.add(file));
            const finalInput = document.getElementById('final_gallery_file_input_edit');
            if (finalInput) {
                finalInput.files = dt.files;
            }
        } catch (err) {
            console.error('DataTransfer sync error:', err);
        }
    }

    function renderNewGalleryGridEdit() {
        const $area = $('#new_images_preview_area_edit');
        const $grid = $('#new_images_grid_edit');
        const $count = $('#new_images_count_edit');

        $grid.empty();
        $count.text(newGalleryFilesEdit.length);

        if (newGalleryFilesEdit.length === 0) {
            $area.addClass('d-none');
            return;
        }

        $area.removeClass('d-none');

        newGalleryFilesEdit.forEach((file, idx) => {
            const url = URL.createObjectURL(file);
            const cardHtml = `
                <div class="position-relative border rounded p-1 bg-white new-edit-img-card" draggable="true" data-file-idx="${idx}" style="width: 80px; height: 80px; cursor: grab; user-select: none; transition: transform 0.15s, box-shadow 0.15s; border-radius: 8px;">
                    <img src="${url}" alt="Preview" style="width:100%; height:100%; object-fit:cover; border-radius:5px; pointer-events: none;">
                    <span class="position-absolute bg-dark bg-opacity-75 text-white rounded-circle d-flex align-items-center justify-content-center" style="top:3px; left:3px; width:17px; height:17px; font-size:9.5px; cursor:grab;" title="Drag to reorder"><i class="fe-move"></i></span>
                    <button type="button" class="btn btn-xs btn-danger position-absolute btn-remove-new-edit-img" style="top:-5px; right:-5px; border-radius:50%; width:18px; height:18px; padding:0; display:flex; align-items:center; justify-content:center; z-index:3;" title="Remove image">
                        <i class="fe-x" style="font-size:10px;"></i>
                    </button>
                    <div class="new-edit-card-footer position-absolute" style="bottom:3px; left:3px; right:3px; z-index:3;">
                        <span class="badge bg-secondary w-100 py-0.5" style="font-size:8px; border-radius:3px;">New</span>
                    </div>
                </div>
            `;
            $grid.append(cardHtml);
        });

        initNewEditDragAndDrop();
    }

    let draggedNewEditCard = null;

    function initNewEditDragAndDrop() {
        const cards = document.querySelectorAll('#new_images_grid_edit .new-edit-img-card');
        cards.forEach(card => {
            card.removeEventListener('dragstart', handleNewEditDragStart);
            card.removeEventListener('dragover', handleNewEditDragOver);
            card.removeEventListener('dragleave', handleNewEditDragLeave);
            card.removeEventListener('drop', handleNewEditDrop);
            card.removeEventListener('dragend', handleNewEditDragEnd);

            card.addEventListener('dragstart', handleNewEditDragStart);
            card.addEventListener('dragover', handleNewEditDragOver);
            card.addEventListener('dragleave', handleNewEditDragLeave);
            card.addEventListener('drop', handleNewEditDrop);
            card.addEventListener('dragend', handleNewEditDragEnd);
        });
    }

    function handleNewEditDragStart(e) {
        draggedNewEditCard = this;
        this.style.opacity = '0.4';
        this.style.cursor = 'grabbing';
        e.dataTransfer.effectAllowed = 'move';
    }

    function handleNewEditDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        if (this !== draggedNewEditCard) {
            this.style.transform = 'scale(1.05)';
            this.style.borderColor = '#2563eb';
        }
    }

    function handleNewEditDragLeave() {
        this.style.transform = 'scale(1)';
        this.style.borderColor = '#e2e8f0';
    }

    function handleNewEditDrop(e) {
        e.preventDefault();
        this.style.transform = 'scale(1)';
        this.style.borderColor = '#e2e8f0';

        const grid = document.getElementById('new_images_grid_edit');
        if (draggedNewEditCard && this !== draggedNewEditCard && grid) {
            const allCards = Array.from(grid.querySelectorAll('.new-edit-img-card'));
            const draggedIdx = allCards.indexOf(draggedNewEditCard);
            const targetIdx = allCards.indexOf(this);

            const movedItem = newGalleryFilesEdit.splice(draggedIdx, 1)[0];
            newGalleryFilesEdit.splice(targetIdx, 0, movedItem);

            syncNewGalleryFilesEdit();
            renderNewGalleryGridEdit();
        }
    }

    function handleNewEditDragEnd() {
        this.style.opacity = '1';
        this.style.cursor = 'grab';
        document.querySelectorAll('#new_images_grid_edit .new-edit-img-card').forEach(card => {
            card.style.transform = 'scale(1)';
            card.style.borderColor = '#e2e8f0';
        });
    }

    // Remove single new image
    $(document).on('click', '.btn-remove-new-edit-img', function (e) {
        e.preventDefault();
        const card = $(this).closest('.new-edit-img-card')[0];
        const grid = document.getElementById('new_images_grid_edit');
        if (card && grid) {
            const allCards = Array.from(grid.querySelectorAll('.new-edit-img-card'));
            const cardIdx = allCards.indexOf(card);
            if (cardIdx !== -1) {
                newGalleryFilesEdit.splice(cardIdx, 1);
                syncNewGalleryFilesEdit();
                renderNewGalleryGridEdit();
            }
        }
    });

    // Clear all new images in edit
    $('#btn_clear_new_images_edit').on('click', function () {
        newGalleryFilesEdit = [];
        syncNewGalleryFilesEdit();
        renderNewGalleryGridEdit();
    });
});
@include('backEnd.product.partials.ai_description_script')
@endsection
