@foreach($cartinfo as $key=>$value)
@php
    $lineDiscountVal = (float) ($value->options->product_discount ?? 0);
    $unitPrice = (float) $value->price;
    $qty = (int) $value->qty;
    $lineSubtotal = max(0, ($unitPrice - $lineDiscountVal) * $qty);

    $product = \App\Models\Product::find($value->id);
    $sizesList = collect();
    $colorsList = collect();
    if ($product) {
        $sizeIds = \App\Models\ProductVariantPrice::where('product_id', $product->id)->whereNotNull('size_id')->pluck('size_id')->unique()->filter();
        $colorIds = \App\Models\ProductVariantPrice::where('product_id', $product->id)->whereNotNull('color_id')->pluck('color_id')->unique()->filter();
        if ($sizeIds->isNotEmpty()) {
            $sizesList = \App\Models\Size::whereIn('id', $sizeIds)->get();
        }
        if ($colorIds->isNotEmpty()) {
            $colorsList = \App\Models\Color::whereIn('id', $colorIds)->get();
        }
        if ($sizesList->isEmpty() && $colorsList->isEmpty()) {
            $sizesList = $product->sizes ?? collect();
            $colorsList = $product->colors ?? collect();
        }
    }
    $hasSizes = $sizesList->isNotEmpty();
    $hasColors = $colorsList->isNotEmpty();
    $currentSizeId = $value->options->size_id ?? '';
    $currentColorId = $value->options->color_id ?? '';
@endphp
<tr data-row-id="{{ $value->rowId }}" data-product-id="{{ $value->id }}">
  <td style="width: 55px;" class="align-middle text-center">
    <img src="{{ asset($value->options->image ?? 'public/no-image.png') }}" class="rounded" style="width: 44px; height: 44px; object-fit: cover; border: 1px solid #e2e8f0;">
  </td>
  <td class="align-middle">
      <div class="fw-bold text-dark mb-1" style="font-size: 13.5px;">{{ $value->name }}</div>
      @if($hasSizes || $hasColors)
      <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
          @if($hasColors)
          <div class="d-flex align-items-center gap-1">
              <span class="text-muted small" style="font-size: 11px;">Color:</span>
              <select class="form-select form-select-sm cart-color-selector py-0 px-1" data-id="{{ $value->rowId }}" data-product-id="{{ $value->id }}" style="min-width: 85px; font-size: 12px; height: 26px;">
                  <option value="">Select</option>
                  @foreach($colorsList as $c)
                  <option value="{{ $c->id }}" {{ (string)$currentColorId === (string)$c->id ? 'selected' : '' }}>{{ $c->colorName ?? $c->color_name ?? $c->name ?? 'N/A' }}</option>
                  @endforeach
              </select>
          </div>
          @endif
          @if($hasSizes)
          <div class="d-flex align-items-center gap-1">
              <span class="text-muted small" style="font-size: 11px;">Size:</span>
              <select class="form-select form-select-sm cart-size-selector py-0 px-1" data-id="{{ $value->rowId }}" data-product-id="{{ $value->id }}" style="min-width: 85px; font-size: 12px; height: 26px;">
                  <option value="">Select</option>
                  @foreach($sizesList as $s)
                  <option value="{{ $s->id }}" {{ (string)$currentSizeId === (string)$s->id ? 'selected' : '' }}>{{ $s->sizeName ?? $s->size_name ?? $s->name ?? 'N/A' }}</option>
                  @endforeach
              </select>
          </div>
          @endif
      </div>
      @endif
  </td>
  <td class="align-middle text-center" style="width: 120px;">
    <div class="input-group input-group-sm" style="width: 110px; margin: 0 auto;">
        <span class="input-group-text py-0 px-1 text-muted" style="font-size: 12px;">৳</span>
        <input type="number" 
               class="form-control form-control-sm text-end cart-price-input" 
               value="{{ $unitPrice }}" 
               data-id="{{ $value->rowId }}" 
               min="0" 
               step="1"
               title="ইউনিট বিক্রয় মূল্য"
               style="font-weight: 600;">
    </div>
  </td>
  <td class="align-middle text-center" style="width: 120px;">
    <div class="d-inline-flex align-items-center border rounded bg-light" style="border-color: #cbd5e1 !important;">
        <button type="button" class="btn btn-sm btn-link text-dark p-0 px-2 text-decoration-none cart_decrement" value="{{ $value->qty }}" data-id="{{ $value->rowId }}" style="font-weight: bold; font-size: 14px;">−</button>
        <input type="text" value="{{ $value->qty }}" readonly class="text-center bg-transparent border-0 fw-bold" style="width: 34px; font-size: 13px;" />
        <button type="button" class="btn btn-sm btn-link text-dark p-0 px-2 text-decoration-none cart_increment" value="{{ $value->qty }}" data-id="{{ $value->rowId }}" style="font-weight: bold; font-size: 14px;">+</button>
    </div>
  </td>
  <td class="align-middle text-center" style="width: 110px;">
    <div class="input-group input-group-sm" style="width: 100px; margin: 0 auto;">
        <span class="input-group-text py-0 px-1 text-muted" style="font-size: 12px;">৳</span>
        <input type="number" 
               class="form-control form-control-sm text-end cart-discount-input" 
               value="{{ $lineDiscountVal }}" 
               data-id="{{ $value->rowId }}" 
               min="0" 
               step="1"
               placeholder="0"
               title="প্রতি পণ্যে ছাড়"
               style="color: #dc2626; font-weight: 600;">
        <input type="hidden" name="line_discount[{{ $value->rowId }}]" value="{{ $lineDiscountVal }}" class="line-discount-hidden">
    </div>
  </td>
  <td class="align-middle text-end fw-bold text-dark" style="width: 110px; font-size: 14px;">
    ৳{{ number_format($lineSubtotal, 2) }}
  </td>
  <td class="align-middle text-center" style="width: 50px;">
    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1 cart_remove" data-id="{{ $value->rowId }}" title="রিমুভ করুন" style="width: 28px; height: 28px; line-height: 1;">
        <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
    </button>
  </td>
</tr>
@endforeach
