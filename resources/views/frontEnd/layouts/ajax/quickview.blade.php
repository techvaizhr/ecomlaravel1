@php
    $productcolors = collect();
    $productsizes  = collect();

    if ($data->variantPrices && $data->variantPrices->count() > 0) {
        $productcolors = $data->variantPrices->pluck('color')->unique('id')->filter();
        $productsizes  = $data->variantPrices->pluck('size')->unique('id')->filter();
    }
    if ($productcolors->isEmpty() && $data->colors && $data->colors->count() > 0) {
        $productcolors = $data->colors;
    }
    if ($productsizes->isEmpty() && $data->sizes && $data->sizes->count() > 0) {
        $productsizes = $data->sizes;
    }

    $galleryImages = collect();
    if ($data->image) {
        $galleryImages->push($data->image);
    }
    if ($data->images) {
        foreach ($data->images as $img) {
            if (!$galleryImages->contains('id', $img->id)) {
                $galleryImages->push($img);
            }
        }
    }

    $variantList = ($data->variantPrices ?? collect())->map(function($vp) {
        return [
            'id'       => $vp->id,
            'color_id' => $vp->color_id,
            'size_id'  => $vp->size_id,
            'price'    => (float) $vp->price,
            'stock'    => (int) ($vp->stock ?? 0),
        ];
    });

    $productImagesJson = $galleryImages->map(function($img) {
        return [
            'src'      => asset($img->image),
            'color_id' => $img->color_id ?? null,
        ];
    });
@endphp

<div class="quick-modal-backdrop">
    <div class="quick-variant-modal-dialog">
        <button type="button" class="quick-modal-close-btn" aria-label="Close">&times;</button>
        
        <div class="quick-modal-container">
            {{-- LEFT: IMAGE GALLERY --}}
            <div class="quick-modal-media">
                <div class="quick-modal-main-image-wrap">
                    <img id="quickModalMainImg" 
                         src="{{ asset($data->image->image ?? 'public/uploads/default.webp') }}" 
                         alt="{{ $data->name }}" />
                    @if($data->old_price && $data->old_price > $data->new_price)
                        @php
                            $discountPercent = round((($data->old_price - $data->new_price) / $data->old_price) * 100);
                        @endphp
                        <span class="quick-modal-discount-badge">-{{ $discountPercent }}%</span>
                    @endif
                </div>

                @if($galleryImages->count() > 1)
                <div class="quick-modal-thumbs">
                    @foreach($galleryImages->take(5) as $idx => $gImg)
                    <div class="quick-thumb-item {{ $idx === 0 ? 'active' : '' }}" 
                         data-img-src="{{ asset($gImg->image) }}"
                         data-color-id="{{ $gImg->color_id ?? '' }}">
                        <img src="{{ asset($gImg->image) }}" alt="thumb" />
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- RIGHT: PRODUCT DETAILS & VARIANT FORM --}}
            <div class="quick-modal-details">
                <div class="quick-modal-header">
                    @if($data->category)
                        <span class="quick-modal-category">{{ $data->category->name }}</span>
                    @endif
                    <h3 class="quick-modal-title">
                        <a href="{{ route('product', $data->slug) }}" title="{{ $data->name }}">{{ $data->name }}</a>
                    </h3>
                </div>

                <div class="quick-modal-pricing">
                    <span class="quick-current-price" id="quickModalCurrentPrice">৳ {{ number_format($data->new_price, 0) }}</span>
                    @if($data->old_price && $data->old_price > $data->new_price)
                        <del class="quick-old-price" id="quickModalOldPrice">৳ {{ number_format($data->old_price, 0) }}</del>
                    @endif
                    <span class="quick-stock-status {{ ($data->stock ?? 1) > 0 ? 'in-stock' : 'out-of-stock' }}">
                        <i class="fa fa-circle"></i> {{ ($data->stock ?? 1) > 0 ? 'ইন স্টক' : 'স্টক আউট' }}
                    </span>
                </div>

                {{-- VARIANT SELECTION FORM --}}
                <form id="quickVariantForm" action="{{ route('cart.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}" />

                    {{-- COLOR VARIANTS --}}
                    @if($productcolors->count() > 0)
                    <div class="quick-variant-group">
                        <div class="quick-variant-label">
                            কালার: <span id="quickColorSelectedName" class="quick-variant-selected-val">নির্বাচন করুন</span>
                        </div>
                        <div class="quick-color-options selector">
                            @foreach($productcolors as $color)
                            <div class="selector-item">
                                <input type="radio"
                                       id="quick_c_{{ $color->id }}"
                                       value="{{ $color->id }}"
                                       name="product_color"
                                       data-color-name="{{ $color->getDisplayName() ?? $color->colorName ?? $color->name }}"
                                       class="selector-item_radio quick-color-radio" />
                                <label for="quick_c_{{ $color->id }}"
                                       style="background-color: {{ $color->color ?? '#ddd' }}"
                                       class="selector-item_label quick-color-swatch"
                                       title="{{ $color->getDisplayName() ?? $color->colorName ?? $color->name }}">
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- SIZE VARIANTS --}}
                    @if($productsizes->count() > 0)
                    <div class="quick-variant-group">
                        <div class="quick-variant-label">
                            সাইজ / ভ্যারিয়েন্ট: <span id="quickSizeSelectedName" class="quick-variant-selected-val">নির্বাচন করুন</span>
                        </div>
                        <div class="quick-size-options selector">
                            @foreach($productsizes as $size)
                            <div class="selector-item">
                                <input type="radio"
                                       id="quick_s_{{ $size->id }}"
                                       value="{{ $size->id }}"
                                       name="product_size"
                                       data-size-name="{{ $size->sizeName ?? $size->name }}"
                                       class="selector-item_radio quick-size-radio" />
                                <label for="quick_s_{{ $size->id }}" class="selector-item_label quick-size-pill">
                                    {{ $size->sizeName ?? $size->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- QUANTITY SELECTOR --}}
                    <div class="quick-qty-row">
                        <span class="quick-qty-label">পরিমাণ:</span>
                        <div class="quick-qty-box">
                            <button type="button" class="quick-qty-btn quick-qty-minus">-</button>
                            <input type="number" name="qty" id="quickModalQty" value="1" min="1" step="1" readonly />
                            <button type="button" class="quick-qty-btn quick-qty-plus">+</button>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS: EQUAL 50/50 WIDTH --}}
                    <div class="quick-modal-actions">
                        <button type="button" 
                                class="btn quick-btn quick-btn-cart quick_modal_btn_submit" 
                                data-action="cart">
                            <i class="fa-solid fa-cart-shopping me-1"></i> কার্টে যোগ করুন
                        </button>
                        <button type="button" 
                                class="btn quick-btn quick-btn-order quick_modal_btn_submit" 
                                data-action="order">
                            <i class="fa-solid fa-bolt me-1"></i> অর্ডার করুন
                        </button>
                    </div>
                </form>

                <div class="quick-modal-footer-link">
                    <a href="{{ route('product', $data->slug) }}" class="quick-details-link">
                        সম্পূর্ণ বিবরণ দেখুন <i class="fa-solid fa-arrow-right-long ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var variants = @json($variantList);
    var productImages = @json($productImagesJson);
    var baseNewPrice = parseFloat("{{ $data->new_price }}") || 0;
    var $modal = $('#custom-modal');
    var defaultAction = "{{ $defaultAction ?? 'cart' }}";

    // Close Modal handler
    $('.quick-modal-close-btn, #page-overlay').off('click.quickClose').on('click.quickClose', function() {
        $('#custom-modal').fadeOut(200);
        $('#page-overlay').fadeOut(200);
    });

    // Thumbnail click to swap main image
    $('.quick-thumb-item').on('click', function() {
        $('.quick-thumb-item').removeClass('active');
        $(this).addClass('active');
        var src = $(this).data('img-src');
        if (src) {
            $('#quickModalMainImg').attr('src', src);
        }
    });

    // Update Price and image on Variant change
    function updateVariantState() {
        var colorId = $('input.quick-color-radio:checked').val() || null;
        var sizeId  = $('input.quick-size-radio:checked').val() || null;

        var colorName = $('input.quick-color-radio:checked').data('color-name') || '';
        var sizeName  = $('input.quick-size-radio:checked').data('size-name') || '';

        if (colorName) $('#quickColorSelectedName').text(colorName).addClass('text-primary');
        if (sizeName)  $('#quickSizeSelectedName').text(sizeName).addClass('text-primary');

        var matchedPrice = baseNewPrice;

        if (variants && variants.length > 0) {
            var match = null;
            if (colorId && sizeId) {
                match = variants.find(function(v) {
                    return String(v.color_id) === String(colorId) && String(v.size_id) === String(sizeId);
                });
            }
            if (!match && colorId) {
                match = variants.find(function(v) {
                    return String(v.color_id) === String(colorId) && !v.size_id;
                });
            }
            if (!match && sizeId) {
                match = variants.find(function(v) {
                    return String(v.size_id) === String(sizeId) && !v.color_id;
                });
            }

            if (match && typeof match.price !== 'undefined' && match.price > 0) {
                matchedPrice = parseFloat(match.price);
            }
        }

        $('#quickModalCurrentPrice').text('৳ ' + matchedPrice.toLocaleString('en-US'));

        // Match image by color
        if (colorId && productImages && productImages.length > 0) {
            var matchedImg = productImages.find(function(img) {
                return img.color_id && String(img.color_id) === String(colorId);
            });
            if (matchedImg && matchedImg.src) {
                $('#quickModalMainImg').attr('src', matchedImg.src);
                $('.quick-thumb-item').removeClass('active');
                $('.quick-thumb-item[data-color-id="' + colorId + '"]').addClass('active');
            }
        }
    }

    $('.quick-color-radio, .quick-size-radio').on('change', updateVariantState);

    // Quantity Plus/Minus
    $('.quick-qty-minus').on('click', function() {
        var $q = $('#quickModalQty');
        var val = parseInt($q.val(), 10) || 1;
        if (val > 1) {
            $q.val(val - 1).trigger('change');
        }
    });
    $('.quick-qty-plus').on('click', function() {
        var $q = $('#quickModalQty');
        var val = parseInt($q.val(), 10) || 1;
        $q.val(val + 1).trigger('change');
    });

    // Form submission validation & AJAX
    $('.quick_modal_btn_submit').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var action = $btn.data('action') || 'cart';
        var $form = $('#quickVariantForm');

        // Variant validation
        var hasColors = $form.find('input.quick-color-radio').length > 0;
        var hasSizes  = $form.find('input.quick-size-radio').length > 0;

        if (hasColors && !$form.find('input.quick-color-radio:checked').val()) {
            toastr.error('অনুগ্রহ করে একটি কালার সিলেক্ট করুন', 'ভ্যারিয়েন্ট নির্বাচন');
            return false;
        }

        if (hasSizes && !$form.find('input.quick-size-radio:checked').val()) {
            toastr.warning('অনুগ্রহ করে একটি সাইজ সিলেক্ট করুন', 'ভ্যারিয়েন্ট নির্বাচন');
            return false;
        }

        var postData = $form.serialize();
        if (action === 'order') {
            postData += '&order_now=1';
        }

        $btn.prop('disabled', true).addClass('loading');

        $.ajax({
            type: "POST",
            url: $form.attr('action'),
            data: postData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            dataType: "json",
            success: function(data) {
                $btn.prop('disabled', false).removeClass('loading');
                if (data && data.success) {
                    if (action === 'order') {
                        window.location.href = '{{ route('customer.checkout') }}';
                        return;
                    }

                    toastr.success('পণ্যটি সফলভাবে কার্টে যোগ হয়েছে', 'সফল');
                    if (typeof cart_count === 'function') cart_count();
                    if (typeof mobile_cart === 'function') mobile_cart();
                    if (typeof sidebarCartRefresh === 'function') sidebarCartRefresh();

                    // Close modal smoothly
                    $('#custom-modal').fadeOut(200);
                    $('#page-overlay').fadeOut(200);

                    // Trigger fly-to-cart
                    if (typeof runFlyToCart === 'function') {
                        runFlyToCart($('#quickModalMainImg'), function() {
                            if (typeof openSidebarCart === 'function') openSidebarCart();
                        });
                    } else if (typeof openSidebarCart === 'function') {
                        openSidebarCart();
                    }
                } else {
                    toastr.error((data && data.message) ? data.message : 'যোগ করতে সমস্যা হয়েছে');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).removeClass('loading');
                var msg = 'কার্টে যোগ করা যায়নি';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg, 'ত্রুটি');
            }
        });
    });

})();
</script>