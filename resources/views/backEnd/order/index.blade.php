@extends('backEnd.layouts.master')
@section('title', $order_status->name . ' অর্ডার')

@section('css')
@include('backEnd.order.partials.index_styles')
@endsection

@section('content')
@include('backEnd.order.partials.index_header')
<tbody>
                                @foreach($show_data as $key => $value)
                                    <tr>
                                        {{-- 1. Checkbox + Serial + Quick View + 3-Dot Actions --}}
                                        <td class="text-center align-middle" style="width: 42px; padding-left: 2px; padding-right: 2px;">
                                            <div class="d-flex flex-column align-items-center" style="gap: 2px;">
                                                <input type="checkbox" class="checkbox form-check-input m-0" value="{{ $value->id }}" style="width: 13.5px; height: 13.5px; cursor: pointer;">
                                                <span class="text-muted fw-bold" style="font-size: 10.5px; line-height: 1;">#{{ $loop->iteration }}</span>
                                                <button type="button"
                                                    class="btn btn-xs btn-light p-0 border-0 order-quick-view-btn text-primary rounded-circle"
                                                    data-order-id="{{ $value->id }}"
                                                    title="কুইক ভিউ"
                                                    style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; background: #e0f2fe;">
                                                    <i class="fas fa-eye" style="font-size: 9.5px;"></i>
                                                </button>

                                                {{-- 3-Dot Action Menu (Horizontal '...' format) --}}
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-xs btn-light border-0 p-0 text-muted d-inline-flex align-items-center justify-content-center" 
                                                            type="button" 
                                                            data-bs-toggle="dropdown" 
                                                            aria-expanded="false" 
                                                            style="width: 20px; height: 16px; line-height: 1; border-radius: 4px;" 
                                                            title="অন্যান্য অ্যাকশন">
                                                        <i class="fas fa-ellipsis-h" style="font-size: 11px;"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-start shadow-sm border-0 order-action-dropdown" style="min-width: 175px; z-index: 1050; font-size: 12.5px;">
                                                        <li>
                                                            <a class="dropdown-item order-quick-view-btn d-flex align-items-center" href="javascript:void(0);" data-order-id="{{ $value->id }}">
                                                                <i class="fas fa-eye text-primary me-2"></i> বিস্তারিত ভিউ
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.order.process', ['invoice_id' => $value->invoice_id]) }}">
                                                                <i class="fas fa-tasks text-info me-2"></i> প্রসেস / স্ট্যাটাস
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item single-assign-btn d-flex align-items-center" href="javascript:void(0);" data-order-id="{{ $value->id }}" data-current-user="{{ $value->user_id }}" data-invoice="{{ $value->invoice_id }}">
                                                                <i class="fas fa-user-plus text-primary me-2"></i> ইউজার অ্যাসাইন
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.order.edit', ['invoice_id' => $value->invoice_id]) }}">
                                                                <i class="fas fa-edit text-warning me-2"></i> অর্ডার এডিট
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center" target="_blank" href="{{ route('admin.order.invoice', ['invoice_id' => $value->invoice_id]) }}">
                                                                <i class="fas fa-print text-secondary me-2"></i> ইনভয়েস প্রিন্ট
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center" target="_blank" href="{{ route('admin.order.order_print') }}?order_ids[]={{ $value->id }}&type=label">
                                                                <i class="fas fa-tag text-success me-2"></i> লেবেল প্রিন্ট
                                                            </a>
                                                        </li>
                                                        @php
                                                            $activeCouriers = [];
                                                            if (isset($carrybee_info) && $carrybee_info) $activeCouriers['carrybee'] = 'Carrybee';
                                                            if (isset($steadfast) && $steadfast) $activeCouriers['steadfast'] = 'Steadfast';
                                                            if (isset($pathao_info) && $pathao_info) $activeCouriers['pathao'] = 'Pathao';
                                                            if (isset($redx_info) && $redx_info) $activeCouriers['redx'] = 'RedX';

                                                            $courierBtnText = 'Courier Booking';
                                                            $defaultCourierCode = '';
                                                            if (count($activeCouriers) === 1) {
                                                                $defaultCourierCode = array_key_first($activeCouriers);
                                                                $courierBtnText = reset($activeCouriers) . ' Booking';
                                                            }
                                                        @endphp
                                                        @if(count($activeCouriers) > 0)
                                                        <li>
                                                            <a class="dropdown-item single-courier-btn d-flex align-items-center fw-semibold text-primary" href="javascript:void(0);" data-order-id="{{ $value->id }}" data-invoice="{{ $value->invoice_id }}" data-amount="{{ $value->customer_payable_amount ?: $value->amount }}" data-courier="{{ $defaultCourierCode }}">
                                                                <i class="fas fa-shipping-fast me-2 text-primary"></i> {{ $courierBtnText }}
                                                            </a>
                                                        </li>
                                                        @endif
                                                        <li>
                                                            <form method="post" action="{{ route('admin.order.destroy') }}" class="d-block m-0 p-0">
                                                                @csrf
                                                                <input type="hidden" value="{{ $value->id }}" name="id">
                                                                <button type="submit" class="dropdown-item text-danger delete-confirm d-flex align-items-center">
                                                                    <i class="fas fa-trash-alt me-2"></i> ডিলিট করুন
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 2. Invoice with Copy Icon & Traffic Source Below --}}
                                        <td class="align-middle text-nowrap" style="width: 1%; padding-left: 4px; padding-right: 4px;">
                                            <div class="d-flex align-items-center" style="gap: 3px; line-height: 1.2;">
                                                <a href="{{ route('admin.order.process', ['invoice_id' => $value->invoice_id]) }}" class="oi-invoice-link fw-bold text-primary" style="font-size: 13px;">#{{ $value->invoice_id }}</a>
                                                <button type="button" class="btn btn-xs p-0 border-0 text-secondary copy-invoice-btn d-inline-flex align-items-center justify-content-center" data-invoice="{{ $value->invoice_id }}" style="width: 15px; height: 15px;" title="ইনভয়েস কপি করুন">
                                                    <i class="far fa-copy" style="font-size: 9.5px;"></i>
                                                </button>
                                            </div>
                                            @php
                                                $tsKey = strtolower(trim((string) ($value->traffic_source ?? 'direct')));
                                                $trafficOpts = isset($traffic_source_options) ? $traffic_source_options : [];
                                                $tsLabel = isset($trafficOpts[$tsKey]) ? $trafficOpts[$tsKey] : ucfirst($tsKey ?: 'direct');
                                                $tsBadgeClass = match ($tsKey) {
                                                    'facebook' => 'bg-primary',
                                                    'instagram' => 'bg-danger',
                                                    'google' => 'bg-success',
                                                    'tiktok' => 'bg-dark',
                                                    'youtube' => 'bg-danger',
                                                    'whatsapp' => 'bg-success',
                                                    'bing' => 'bg-info',
                                                    'yahoo' => 'bg-secondary',
                                                    'twitter' => 'bg-info',
                                                    'direct' => 'bg-secondary',
                                                    'other' => 'bg-warning',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <div style="margin-top: 2px; line-height: 1;">
                                                <span class="badge {{ $tsBadgeClass }}" style="font-size: 9.5px; font-weight: 600; padding: 1.5px 5px;">{{ $tsLabel }}</span>
                                            </div>
                                        </td>

                                        {{-- 3. Date & Time --}}
                                        <td class="align-middle text-nowrap" style="width: 1%; padding-left: 4px; padding-right: 4px; font-size: 11.5px; line-height: 1.2;">
                                            <div>{{ date('d-m-Y', strtotime($value->updated_at)) }}</div>
                                            <small class="text-muted" style="font-size: 10.5px;">{{ date('h:i:s A', strtotime($value->updated_at)) }}</small>
                                        </td>

                                        {{-- 4. Customer with Call, WhatsApp, Copy & Full Address --}}
                                        <td class="align-middle" style="padding-left: 6px; padding-right: 6px;">
                                            @php
                                                $custName = $value->shipping ? $value->shipping->name : ($value->customer ? $value->customer->name : 'N/A');
                                                $custPhone = $value->shipping ? $value->shipping->phone : ($value->customer ? $value->customer->phone : '');
                                                $custAddr = $value->shipping ? $value->shipping->full_address : ($value->customer ? $value->customer->address : '');
                                            @endphp
                                            <div class="fw-bold text-dark" style="font-size: 13px; line-height: 1.2;">{{ $custName }}</div>
                                            @if($custPhone)
                                                <div class="d-flex align-items-center" style="gap: 3px; margin-top: 2px; line-height: 1.15;">
                                                    <span class="text-secondary fw-semibold" style="font-size: 11.5px;">{{ $custPhone }}</span>
                                                    <a href="tel:{{ $custPhone }}" class="btn btn-xs p-0 border-0 text-primary d-inline-flex align-items-center justify-content-center" style="width: 15px; height: 15px;" title="কল করুন">
                                                        <i class="fas fa-phone-alt" style="font-size: 9.5px;"></i>
                                                    </a>
                                                    @php
                                                        $cleanPhone = preg_replace('/[^0-9]/', '', $custPhone);
                                                        if (str_starts_with($cleanPhone, '0')) {
                                                            $waPhone = '88' . $cleanPhone;
                                                        } else {
                                                            $waPhone = $cleanPhone;
                                                        }
                                                    @endphp
                                                    <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="btn btn-xs p-0 border-0 text-success d-inline-flex align-items-center justify-content-center" style="width: 15px; height: 15px;" title="হোয়াটসঅ্যাপ মেসেজ">
                                                        <i class="fab fa-whatsapp" style="font-size: 11px;"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-xs p-0 border-0 text-secondary d-inline-flex align-items-center justify-content-center copy-phone-btn" data-phone="{{ $custPhone }}" style="width: 15px; height: 15px;" title="নাম্বার কপি করুন">
                                                        <i class="far fa-copy" style="font-size: 9.5px;"></i>
                                                    </button>
                                                </div>
                                            @endif
                                            @if(!empty($custAddr))
                                                <div class="text-muted" style="font-size: 11px; line-height: 1.25; margin-top: 2px; max-width: 250px;">
                                                    <i class="fas fa-map-marker-alt text-danger me-1" style="font-size: 9px;"></i>{{ $custAddr }}
                                                </div>
                                            @endif
                                        </td>

                                        {{-- 5. Product (Image + 20 char Title + Collapse for multiples + Image Click Zoom) --}}
                                        <td class="align-middle" style="min-width: 170px;">
                                            @php
                                                $details = $value->orderdetails ?? collect([]);
                                                $firstItem = $details->first();
                                                $totalItems = $details->count();
                                                $fallbackImg = asset('public/uploads/default/no-image.png');
                                            @endphp

                                            @if($firstItem)
                                                @php
                                                    $firstImg = ($firstItem->image && $firstItem->image->image) 
                                                        ? asset($firstItem->image->image) 
                                                        : (($firstItem->product && $firstItem->product->image && $firstItem->product->image->image) 
                                                            ? asset($firstItem->product->image->image) 
                                                            : $fallbackImg);
                                                    $firstName = $firstItem->product_name ?? optional($firstItem->product)->name ?? 'পণ্য';
                                                @endphp
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ $firstImg }}" 
                                                         alt="{{ $firstName }}" 
                                                         class="rounded border zoomable-product-img flex-shrink-0" 
                                                         data-full-img="{{ $firstImg }}"
                                                         data-title="{{ $firstName }}"
                                                         onerror="this.src='{{ $fallbackImg }}'"
                                                         style="width: 34px; height: 34px; object-fit: cover; cursor: zoom-in;" 
                                                         title="বড় করে দেখতে ক্লিক করুন">
                                                    <div class="text-truncate" style="max-width: 140px;" title="{{ $firstName }}">
                                                        <div class="fw-semibold text-dark" style="font-size: 12px; line-height: 1.25;">
                                                            {{ Str::limit($firstName, 20) }}
                                                        </div>
                                                        <small class="text-muted" style="font-size: 11px;">পরিমাণ: <span class="fw-bold text-dark">×{{ $firstItem->qty }}</span></small>
                                                    </div>
                                                </div>

                                                @if($totalItems > 1)
                                                    <div class="mt-1">
                                                        <a class="btn btn-xs btn-light border py-0 px-1 text-primary fw-semibold d-inline-flex align-items-center gap-1" 
                                                           data-bs-toggle="collapse" 
                                                           href="#order-products-{{ $value->id }}" 
                                                           role="button" 
                                                           aria-expanded="false" 
                                                           style="font-size: 10px; border-radius: 4px;">
                                                            <i class="fas fa-layer-group" style="font-size: 9px;"></i> +{{ $totalItems - 1 }} আরও
                                                        </a>
                                                    </div>
                                                    <div class="collapse mt-2" id="order-products-{{ $value->id }}">
                                                        <div class="d-flex flex-column gap-2 pt-1 border-top">
                                                            @foreach($details->slice(1) as $extraItem)
                                                                @php
                                                                    $extraImg = ($extraItem->image && $extraItem->image->image) 
                                                                        ? asset($extraItem->image->image) 
                                                                        : (($extraItem->product && $extraItem->product->image && $extraItem->product->image->image) 
                                                                            ? asset($extraItem->product->image->image) 
                                                                            : $fallbackImg);
                                                                    $extraName = $extraItem->product_name ?? optional($extraItem->product)->name ?? 'পণ্য';
                                                                @endphp
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <img src="{{ $extraImg }}" 
                                                                         alt="{{ $extraName }}" 
                                                                         class="rounded border zoomable-product-img flex-shrink-0" 
                                                                         data-full-img="{{ $extraImg }}"
                                                                         data-title="{{ $extraName }}"
                                                                         onerror="this.src='{{ $fallbackImg }}'"
                                                                         style="width: 30px; height: 30px; object-fit: cover; cursor: zoom-in;" 
                                                                         title="বড় করে দেখতে ক্লিক করুন">
                                                                    <div class="text-truncate" style="max-width: 135px;" title="{{ $extraName }}">
                                                                        <div class="fw-semibold text-dark" style="font-size: 11.5px; line-height: 1.25;">
                                                                            {{ Str::limit($extraName, 20) }}
                                                                        </div>
                                                                        <small class="text-muted" style="font-size: 10.5px;">পরিমাণ: <span class="fw-bold text-dark">×{{ $extraItem->qty }}</span></small>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        {{-- 6. Amount --}}
                                        <td class="align-middle text-end text-nowrap" style="width: 1%; padding-left: 6px; padding-right: 4px;">
                                            @php
                                                $payment = \App\Models\Payment::where('order_id', $value->id)->first();
                                                $paid = $payment ? floatval($payment->amount) : 0;
                                                $total = floatval($value->amount);
                                                $showAmount = $total;
                                                if ($paid > 0 && $paid < $total) {
                                                    $showAmount = $total - $paid;
                                                }
                                            @endphp
                                            <span class="oi-amount fw-bold text-dark" style="font-size: 13.5px;">৳{{ number_format($showAmount, 0) }}</span>
                                        </td>

                                        {{-- 6. Status & Courier Info --}}
                                        <td class="align-middle text-center text-nowrap" style="width: 1%; padding-left: 4px; padding-right: 4px;">
                                            @php
                                                $stId = (int) $value->order_status;
                                                $stPillClass = match ($stId) {
                                                    7 => 'bg-success text-white',
                                                    8 => 'bg-warning text-dark border-warning fw-bold',
                                                    9 => 'bg-success text-white',
                                                    10 => 'bg-info text-dark',
                                                    11 => 'bg-secondary text-white',
                                                    12 => 'bg-danger text-white',
                                                    13 => 'bg-danger text-white',
                                                    15 => 'bg-dark text-white',
                                                    default => 'bg-light text-dark border',
                                                };
                                            @endphp
                                            <a href="javascript:void(0);" 
                                               class="quick-change-status-btn text-decoration-none d-inline-block" 
                                               data-order-id="{{ $value->id }}" 
                                               data-current-status="{{ $value->order_status }}" 
                                               data-invoice="{{ $value->invoice_id }}" 
                                               data-status-name="{{ $value->status ? $value->status->name : '—' }}" 
                                               title="স্ট্যাটাস পরিবর্তন করতে ক্লিক করুন">
                                                <span class="oi-status-pill badge {{ $stPillClass }} px-2 py-1" style="font-size: 11px; font-weight: 600; cursor: pointer;">
                                                    {{ $value->status ? $value->status->name : '—' }} <i class="fas fa-caret-down opacity-75" style="font-size: 8.5px; margin-left: 2px;"></i>
                                                </span>
                                            </a>

                                            @if($stId === 8 || $stId === 9 || $stId === 10 || $stId === 11)
                                                <div class="mt-1">
                                                    <button type="button" 
                                                            class="btn btn-xs {{ $stId === 8 ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' }} open-partial-settle-btn py-0 px-1.5 shadow-sm d-inline-flex align-items-center" 
                                                            data-order-id="{{ $value->id }}" 
                                                            style="font-size: 10px; border-radius: 4px;" 
                                                            title="আংশিক ডেলিভারি সেটেলমেন্ট বা হিসাব এডিট করুন">
                                                        <i class="fas fa-boxes me-1 {{ $stId === 8 ? 'text-danger' : 'text-primary' }}"></i> {{ $stId === 8 ? 'সেটেল করুন' : 'সেটেলমেন্ট' }}
                                                    </button>
                                                </div>
                                            @endif

                                            @php
                                                $cTrackingId = $value->courier_tracking_id_clean;
                                                $cTrackUrl   = $value->courier_tracking_url;
                                                $cName       = $value->courier_name_display;
                                            @endphp
                                            @if(!empty($cTrackingId))
                                                <div class="courier-booking-box" style="margin-top: 2px; font-size: 11px; line-height: 1.15;" id="courier-booking-{{ $value->id }}">
                                                    {{-- Courier Name (Clickable Tracking Link) --}}
                                                    <div class="courier-name-wrap">
                                                        @if($cTrackUrl)
                                                            <a href="{{ $cTrackUrl }}" target="_blank" rel="noopener noreferrer" class="fw-bold text-primary text-decoration-none d-inline-flex align-items-center" title="কুরিয়ার পাবলিক ট্র্যাকিং লিংক দেখুন" style="font-size: 10.5px; gap: 3px;">
                                                                <i class="fas fa-truck text-secondary" style="font-size: 9px;"></i> {{ $cName }} <i class="fas fa-external-link-alt text-muted" style="font-size: 7.5px;"></i>
                                                            </a>
                                                        @else
                                                            <span class="fw-bold text-dark" style="font-size: 10.5px;">
                                                                <i class="fas fa-truck text-secondary" style="font-size: 9px;"></i> {{ $cName }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Courier ID with Copy & Recall/Sync --}}
                                                    <div class="d-flex align-items-center justify-content-center" style="gap: 3px; margin-top: 1px;">
                                                        <span class="text-secondary font-monospace" style="font-size: 10px; max-width: 82px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="কুরিয়ার ট্র্যাকিং আইডি: {{ $cTrackingId }}">{{ $cTrackingId }}</span>
                                                        
                                                        <button type="button" class="btn btn-xs p-0 border-0 text-muted copy-courier-id-btn d-inline-flex align-items-center justify-content-center" data-id="{{ $cTrackingId }}" style="width: 14px; height: 14px;" title="কুরিয়ার আইডি কপি করুন">
                                                            <i class="far fa-copy" style="font-size: 9.5px;"></i>
                                                        </button>

                                                        <button type="button" class="btn btn-xs p-0 border-0 text-info sync-courier-status-btn d-inline-flex align-items-center justify-content-center" data-order-id="{{ $value->id }}" data-invoice="{{ $value->invoice_id }}" style="width: 14px; height: 14px;" title="কুরিয়ার লাইভ স্ট্যাটাস চেক ও সিঙ্ক করুন">
                                                            <i class="fas fa-sync-alt" style="font-size: 9.5px;"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- 7. Fraud Check (Tight & Compact) --}}
                                        <td class="align-middle text-center text-nowrap" style="width: 1%; padding-left: 4px; padding-right: 2px;">
                                            @if(is_null($value->fraud_rate))
                                                <a href="javascript:void(0);" 
                                                   class="btn btn-xs fraud-check"
                                                   data-mobile="{{ $value->shipping ? $value->shipping->phone : '' }}"
                                                   style="background:#fb8709; color:#fff; padding: 2.5px 7px; border-radius: 999px; font-size: 10.5px; font-weight: 600; line-height: 1.2;">
                                                    যাচাই
                                                </a>
                                            @else
                                                <a href="javascript:void(0);" 
                                                   class="btn btn-xs fraud-check {{ $value->fraud_rate >= 80 ? 'btn-success' : 'btn-danger' }}"
                                                   data-mobile="{{ $value->shipping ? $value->shipping->phone : '' }}"
                                                   data-id="{{ $value->id }}"
                                                   style="padding: 2.5px 7px; border-radius: 999px; font-size: 10.5px; font-weight: 600; line-height: 1.2;">
                                                    {{ $value->fraud_rate }}% {{ $value->fraud_rate >= 80 ? 'নিরাপদ' : 'ঝুঁকি' }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-2 border-top">
                        <div class="text-muted small">
                            দেখাচ্ছে <span class="fw-bold text-dark">{{ $show_data->firstItem() ?? 0 }}</span> থেকে <span class="fw-bold text-dark">{{ $show_data->lastItem() ?? 0 }}</span> (মোট <span class="fw-bold text-primary">{{ $show_data->total() }}</span> টি অর্ডার)
                        </div>
                        <div class="oi-paginate custom-paginate order-custom-paginate m-0">
                            {{ $show_data->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
        </div>
    </div>
</div>

@include('backEnd.order.partials.order_quick_view_modal')

<div class="modal fade oi-modal" id="asignUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="asignUserModalTitle"><i class="fas fa-user-plus me-1"></i> ইউজার অ্যাসাইন</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.order.assign') }}" id="order_assign">
        <input type="hidden" id="single_assign_order_id" value="">
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label mb-1">ইউজার নির্বাচন করুন</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Select..</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade oi-modal" id="changeStatus" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-flag me-1"></i> স্ট্যাটাস পরিবর্তন</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.order.status') }}" id="order_status_form" novalidate>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Select Status <span class="text-danger">*</span></label>
                <select name="order_status" id="order_status" class="form-control">
                    <option value="">Select Status..</option>
                    @if(isset($orderstatus) && $orderstatus->count() > 0)
                        @foreach($orderstatus as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    @else
                        <option value="">No status available</option>
                    @endif
                </select>
                <small class="text-muted">Select orders first, then choose status</small>
                <div class="invalid-feedback" id="status_error" style="display: none;">Please select a status</div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Update Status</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade oi-modal" id="quickSingleStatusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 360px;">
    <div class="modal-content border-0 shadow">
      <div class="modal-header py-2.5 px-3 bg-primary text-white">
        <h6 class="modal-title m-0 fw-bold text-white" id="quickStatusModalTitle"><i class="fas fa-flag me-1"></i> স্ট্যাটাস পরিবর্তন</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3">
        <input type="hidden" id="quick_status_order_id" value="">
        <div class="text-muted small mb-2 fw-semibold">নতুন স্ট্যাটাস সিলেক্ট করুন:</div>
        <div class="d-flex flex-column gap-1.5" id="quick_status_list">
            @if(isset($orderstatus) && $orderstatus->count() > 0)
                @foreach($orderstatus as $s)
                    <button type="button" class="btn btn-sm btn-outline-primary text-start d-flex align-items-center justify-content-between py-1.5 px-2.5 quick-status-opt-btn" data-status-id="{{ $s->id }}" data-status-name="{{ $s->name }}" style="border-radius: 6px; font-size: 12.5px;">
                        <span><i class="far fa-check-circle me-1.5 opacity-50"></i> {{ $s->name }}</span>
                        <span class="badge bg-light text-dark border current-tag d-none" style="font-size: 10px;">বর্তমান</span>
                    </button>
                @endforeach
            @else
                <div class="text-muted small text-center py-2">কোন স্ট্যাটাস পাওয়া যায়নি</div>
            @endif
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade oi-modal" id="pathao" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-truck me-1"></i> Pathao কুরিয়ার</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.order.pathao') }}" id="order_sendto_pathao" method="POST">
      @csrf
      <input type="hidden" name="order_ids" id="pathao_order_ids" value="">
      <div class="modal-body">
        <div class="form-group">
            <label for="pathaostore" class="form-label">Store</label>
           <select name="pathaostore" id="pathaostore" class="pathaostore form-control" >
             <option value="">Select Store...</option>
             @if(isset($pathaostore['data']['data']))
                 @foreach($pathaostore['data']['data'] as $store)
                     <option value="{{ $store['store_id'] }}">{{ $store['store_name'] }}</option>
                 @endforeach
             @endif
           </select>
        </div>

        <div class="form-group mt-3">
          <label for="pathaocity" class="form-label">City</label>
           <select name="pathaocity" id="pathaocity" class="chosen-select pathaocity form-control" style="width:100%" >
             <option value="">Select City...</option>
             @if(isset($pathaocities['data']['data']))
                 @foreach($pathaocities['data']['data'] as $city)
                     <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}</option>
                 @endforeach
             @endif
           </select>
        </div>

        <div class="form-group mt-3">
          <label class="form-label">Zone</label>
             <select name="pathaozone" id="pathaozone" class="pathaozone chosen-select form-control" style="width:100%"></select>
        </div>

        <div class="form-group mt-3">
          <label class="form-label">Area</label>
             <select name="pathaoarea" id="pathaoarea" class="pathaoarea chosen-select form-control" style="width:100%"></select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade oi-modal" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white" id="noteModalLabel">Note</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="note_order_id">
        <input type="hidden" id="note_type">

        <div class="form-group">
            <label id="note_label">Note</label>
            <textarea id="note_modal_text" class="form-control" rows="5" placeholder="Write note here..."></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="saveNoteBtn">Save</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="fraudCheckModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered modal-fullscreen-md-down">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header" style="background:#10b981; color:#fff;">
                <h5 class="modal-title">
                    <i class="fe-shield"></i> ফ্রড চেকার রিপোর্ট
                </h5>
                <button type="button" class="btn-close btn-light" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="fraudModalBody" style="min-height:250px;">
                <div class="text-center py-5">
                    <div class="spinner-border text-success" style="width:3rem;height:3rem;"></div>
                    <p class="mt-3 fw-bold">ডাটা লোড হচ্ছে...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@include('backEnd.order.partials.partial_settlement_modal')

@endsection

@section('script')
@include('backEnd.order.partials.partial_settlement_js')
<script>
    // Safe number helper
    function toNum(v) {
        if (v === null || v === undefined || v === '') return 0;
        var n = Number(v);
        return isNaN(n) ? 0 : n;
    }

    // buildSummary: Updated to handle New API keys
    function buildSummary(raw) {
        var pathao = raw.pathao || raw.Pathao || raw.pathao_data || raw.pathao || {};
        var redx = raw.redx || raw.RedX || raw.redx_data || raw.redx || {};
        var steadfast = raw.steadfast || raw.Steadfast || raw.steadfast_data || raw.steadfast || {};
        var parceldex = raw.parceldex || raw.ParcelDex || {};
        var paperfly = raw.paperfly || raw.PaperFly || {};

        function getStats(obj) {
            var t = toNum(obj.total_parcel || obj.total || obj.orders || obj.count);
            var s = toNum(obj.success_parcel || obj.success || obj.complete || obj.delivered);
            var c = toNum(obj.cancelled_parcel || obj.cancel || obj.cancelled || obj.failed);
            var r = (obj.success_ratio !== undefined) ? toNum(obj.success_ratio) : (t > 0 ? Math.round((s / t) * 100) : 0);
            return { total: t, success: s, cancel: c, rate: r };
        }

        var p = getStats(pathao);
        var r = getStats(redx);
        var s = getStats(steadfast);
        var pd = getStats(parceldex);
        var pf = getStats(paperfly);

        var total = p.total + r.total + s.total + pd.total + pf.total;
        var success = p.success + r.success + s.success + pd.success + pf.success;
        var cancel = p.cancel + r.cancel + s.cancel + pd.cancel + pf.cancel;

        var rate = 0;
        if (total > 0) rate = Math.round((success / total) * 100);

        return {
            total: total,
            success: success,
            cancel: cancel,
            rate: rate,
            couriers: {
                Pathao: p,
                RedX: r,
                Steadfast: s,
                ParcelDex: pd,
                PaperFly: pf
            }
        };
    }

    // Render HTML for modal from canonical summary (IN BANGLA)
    function loadFraudHtml(data, mobile) {
        if (data.total === 0) {
            return `
            <div class="container-fluid">
                <div class="p-3 mb-3" style="background:#f8f9fa;border-radius:8px;">
                    <h5><i class="fe-phone-call"></i> ${mobile}</h5>
                    <small>সফলতার হার: 0%</small>
                    <span class="badge bg-secondary float-end">কোন তথ্য নেই</span>
                </div>
                <div class="alert alert-light text-center py-3" style="border:1px solid #ddd;">
                    <h5 class="text-muted mb-0">😕 কোনো তথ্য খুঁজে পাওয়া যায়নি</h5>
                    <small>এই কাস্টমারের সম্পর্কে কোনো তথ্য পাওয়া যায়নি। অতিরিক্ত সতর্কতার জন্য নিজের যাচাই করুন।</small>
                </div>
            </div>`;
        }

        var rateText = (data.rate || data.rate === 0) ? (data.rate + '%') : 'N/A';
        
        // Bangla Risk Tags
        var riskTag = '<span class="badge bg-success">নিরাপদ</span>';
        var showWarning = (data.total > 0 && data.rate < 80);
        if (showWarning) { riskTag = '<span class="badge bg-danger">উচ্চ ঝুঁকি</span>'; }

        var courierRows = '';
        Object.entries(data.couriers).forEach(function([name, c]) {
            if(c.total === 0) return;

            var cRateNum = toNum(c.rate);
            var cRate = (c.total === 0) ? 'N/A' : (cRateNum + '%');
            var badgeClass = 'bg-secondary';
            if (c.total === 0) { badgeClass = 'bg-secondary'; }
            else if (cRateNum >= 90) { badgeClass = 'bg-success'; }
            else if (cRateNum >= 70) { badgeClass = 'bg-warning text-dark'; }
            else { badgeClass = 'bg-danger'; }

            courierRows += `
                <tr>
                    <td>${name}</td>
                    <td>${c.total}</td>
                    <td class="text-success">${c.success}</td>
                    <td class="text-danger">${c.cancel}</td>
                    <td><span class="badge ${badgeClass}">${cRate}</span></td>
                </tr>`;
        });

        var warningHtml = '';
        if (showWarning) {
            warningHtml = `<div class="alert alert-danger text-center py-2">⚠️ সতর্কতা: ডেলিভারি হার কম - COD যাচাই করুন অথবা এডভান্স নিন</div>`;
        } else {
            warningHtml = `<div class="text-start mb-3"><small class="text-success">✓ নিরাপদ - কাস্টমারের ডেলিভারি রেকর্ড ভালো।</small></div>`;
        }

        return `
            <div class="container-fluid">
                <div class="p-3 mb-3" style="background:#e8fff3;border-radius:8px;">
                    <h5><i class="fe-phone-call"></i> ${mobile}</h5>
                    <small>সফলতার হার: ${rateText}</small>
                    <span class="float-end">${riskTag}</span>
                </div>
                ${warningHtml}
                <div class="row text-center mb-4">
                    <div class="col-md-3 mb-2">
                        <div class="p-3 text-white" style="background:#6366f1;border-radius:10px;">
                            <h3>${data.total}</h3><span>মোট পার্সেল</span>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="p-3 text-white" style="background:#10b981;border-radius:10px;">
                            <h3>${data.success}</h3><span>ডেলিভারি</span>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="p-3 text-white" style="background:#ef4444;border-radius:10px;">
                            <h3>${data.cancel}</h3><span>বাতিল/রিটার্ন</span>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="p-3 text-white" style="background:#f97316;border-radius:10px;">
                            <h3>${rateText}</h3><span>হার</span>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>কুরিয়ার</th><th>মোট</th><th>সফল</th><th>বাতিল</th><th>হার</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${courierRows}
                    </tbody>
                </table>
            </div>
        `;
    }
</script>

<script>
(function ($) {
    if (!$) return;

$(document).ready(function(){

    // আটকে থাকা modal backdrop / scroll লক সরানো
    $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
    $('.modal-backdrop').remove();
    document.querySelectorAll('.modal.show').forEach(function (m) {
        m.classList.remove('show');
        m.style.display = '';
        m.setAttribute('aria-hidden', 'true');
    });

    // Order Note / Admin Note popup open
    $(document).on('click', '.note-modal-btn', function (e) {
        e.preventDefault();
        let orderId = $(this).data('id');
        let type    = $(this).data('type');
        let note    = $(this).data('note') || '';

        $('#note_order_id').val(orderId);
        $('#note_type').val(type);
        $('#note_modal_text').val(note);

        if (type === 'admin') {
            $('#noteModalLabel').text('Admin Note');
            $('#note_label').text('Admin Note');
        } else {
            $('#noteModalLabel').text('Order Note (Customer)');
            $('#note_label').text('Order Note (Customer)');
        }

        $('#noteModal').modal('show');
    });

    // Save Note (AJAX)
    $('#saveNoteBtn').on('click', function () {
        let orderId = $('#note_order_id').val();
        let type    = $('#note_type').val();
        let note    = $('#note_modal_text').val();

        $.ajax({
            url: "{{ route('admin.order.update_note') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                order_id: orderId,
                note_type: type,
                note: note
            },
            success: function (res) {
                if (res.status === 'success') {
                    toastr.success('Note updated successfully');
                    let selector = '.note-modal-btn[data-id="' + orderId + '"][data-type="' + type + '"]';
                    let $btn = $(selector);
                    $btn.data('note', note);
                    $btn.text(note ? 'View' : 'Add');
                    $('#noteModal').modal('hide');
                } else {
                    toastr.error(res.message || 'Update failed');
                }
            },
            error: function () {
                toastr.error('Something went wrong');
            }
        });
    });

    // ── Bulk Actions Bar Dynamic Visibility ──
    function updateBulkActionVisibility() {
        var count = $('input.checkbox:checked').length;
        var $wrapper = $('#bulkActionsWrapper');
        var $countSpan = $('#selectedOrdersCount');
        if ($countSpan.length) {
            $countSpan.text(count);
        }
        if (count > 0) {
            if ($wrapper.is(':hidden')) {
                $wrapper.stop(true, true).slideDown(200);
            }
        } else {
            if ($wrapper.is(':visible')) {
                $wrapper.stop(true, true).slideUp(150);
            }
        }
    }

    // checkall
    $(document).on('change', '.checkall', function(){
        $(".checkbox").prop('checked', $(this).is(":checked"));
        updateBulkActionVisibility();
    });

    // individual checkbox
    $(document).on('change', '.checkbox', function(){
        if (!$(this).is(':checked')) {
            $('.checkall').prop('checked', false);
        } else {
            var totalBoxes = $('.checkbox').length;
            var checkedBoxes = $('.checkbox:checked').length;
            if (totalBoxes > 0 && totalBoxes === checkedBoxes) {
                $('.checkall').prop('checked', true);
            }
        }
        updateBulkActionVisibility();
    });

    updateBulkActionVisibility();

    // ── অর্ডার কুইক ভিউ মডাল ──
    function oqvShowModal() {
        var el = document.getElementById('orderQuickViewModal');
        if (!el) return;
        $('.modal-backdrop').not('.oqv-temp-backdrop').remove();
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var inst = bootstrap.Modal.getOrCreateInstance(el);
            inst.show();
        } else if ($.fn.modal) {
            $(el).modal('show');
        }
    }

    $('#orderQuickViewModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
        $('.modal-backdrop').remove();
        $('#orderQuickViewBody').html(
            '<div class="oqv-loading"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 mb-0">লোড হচ্ছে...</p></div>'
        );
    });

    function oqvUpdateFraudBadge(rate, isFraud) {
        var $box = $('#oqvFraudStatus');
        if (!$box.length) return;
        if (isFraud) {
            $box.html('<span class="badge bg-danger oqv-fraud-badge">ফ্রড (ঝুঁকি)</span>');
            return;
        }
        if (rate >= 80) {
            $box.html('<span class="badge bg-success oqv-fraud-badge">' + rate + '% নিরাপদ</span>');
        } else {
            $box.html('<span class="badge bg-danger oqv-fraud-badge">' + rate + '% ঝুঁকি</span>');
        }
    }

    $(document).on('click', '.order-quick-view-btn', function () {
        var orderId = $(this).data('order-id');
        if (!orderId) return;
        $('#orderQuickViewBody').html(
            '<div class="oqv-loading"><div class="spinner-border text-primary"></div><p class="mt-3 mb-0">লোড হচ্ছে...</p></div>'
        );
        $('#oqvModalInvoice').text('');
        oqvShowModal();
        $.get('{{ url("admin/order/quick-view") }}/' + orderId, function (res) {
            if (res.status === 'success') {
                $('#orderQuickViewBody').html(res.html);
                $('#oqvModalInvoice').text('#' + res.invoice_id);
            } else {
                $('#orderQuickViewBody').html('<div class="alert alert-danger">ডাটা লোড করা যায়নি</div>');
            }
        }).fail(function () {
            $('#orderQuickViewBody').html('<div class="alert alert-danger">সার্ভার এরর — আবার চেষ্টা করুন</div>');
        });
    });

    $(document).on('click', '.fraud-check-oqv', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var mobile = $(this).data('mobile');
        if (!mobile) { toastr.error('মোবাইল নম্বর নেই'); return; }
        var $btn = $(this);
        var $report = $('#oqvFraudReport');
        $btn.prop('disabled', true);
        $('#oqvFraudStatus').html('<span class="badge bg-secondary">চেক হচ্ছে...</span>');
        $report.html(
            '<div class="text-center py-4">' +
            '<div class="spinner-border text-primary" role="status"></div>' +
            '<p class="mt-2 mb-0 small text-muted">কুরিয়ার ডাটা যাচাই হচ্ছে...</p></div>'
        ).show();
        $.ajax({
            url: "{{ route('admin.fraud.check') }}",
            type: 'POST',
            data: { mobile: mobile, _token: "{{ csrf_token() }}" },
            timeout: 60000,
            success: function (res) {
                $btn.prop('disabled', false);
                if (res && res.status === 'success') {
                    if (res.data && res.data.is_fraud === true) {
                        oqvUpdateFraudBadge(0, true);
                        $report.html(
                            '<div class="alert alert-danger mb-0 text-center">' +
                            '<h6 class="mb-1"><i class="fas fa-exclamation-triangle"></i> ফ্রড ডিটেক্টেড</h6>' +
                            '<p class="mb-0 small">মোবাইল: ' + mobile + '</p></div>'
                        ).show();
                        $('.fraud-check[data-mobile="' + mobile + '"]').not('.fraud-check-oqv')
                            .removeClass('btn-warning btn-success').addClass('btn-danger').text('ফ্রড (ঝুঁকি)');
                        toastr.warning('ফ্রড ডিটেক্টেড');
                        return;
                    }
                    var apiData = (res.data && res.data.data) ? res.data.data : (res.data || {});
                    var summary = buildSummary(apiData);
                    oqvUpdateFraudBadge(summary.rate, false);
                    $report.html(loadFraudHtml(summary, mobile)).show();
                    var allBtns = $('.fraud-check[data-mobile="' + mobile + '"]').not('.fraud-check-oqv');
                    allBtns.removeClass('btn-warning btn-success btn-danger');
                    if (summary.rate >= 80) {
                        allBtns.addClass('btn-success').text(summary.rate + '% নিরাপদ');
                    } else {
                        allBtns.addClass('btn-danger').text(summary.rate + '% ঝুঁকি');
                    }
                    var $mb = $('#orderQuickViewBody');
                    if ($report.length && $mb.length) {
                        $mb.animate({
                            scrollTop: $report.offset().top - $mb.offset().top + $mb.scrollTop() - 16
                        }, 300);
                    }
                    toastr.success('ফ্রড চেক সম্পন্ন');
                } else {
                    $report.html('<div class="alert alert-danger mb-0">' + ((res && res.message) ? res.message : 'ফ্রড চেক ব্যর্থ') + '</div>').show();
                    toastr.error((res && res.message) ? res.message : 'ফ্রড চেক ব্যর্থ');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                var msg = 'ফ্রড চেক ব্যর্থ';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                $report.html('<div class="alert alert-danger mb-0">' + msg + '</div>').show();
                toastr.error(msg);
            }
        });
    });

    $(document).on('click', '.oi-quick-courier', function () {
        var slug = $(this).data('courier');
        var orderId = $(this).data('order-id');
        var $btn = $(this);
        if (!orderId || !slug) return;
        if (!confirm('এই অর্ডার ' + slug.toUpperCase() + ' কুরিয়ারে পাঠাতে চান?')) return;
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.get('{{ url("admin/bulk-courier") }}/' + slug + '?status=5', { order_ids: [orderId] }, function (res) {
            if (res.status === 'success') {
                toastr.success(res.message || 'কুরিয়ারে পাঠানো হয়েছে');
                setTimeout(function () { location.reload(); }, 1200);
            } else {
                toastr.error(res.message || 'ব্যর্থ');
                $btn.prop('disabled', false).html('<i class="fas fa-truck"></i> ' + slug.charAt(0).toUpperCase() + slug.slice(1));
            }
        }).fail(function () {
            toastr.error('কুরিয়ার রিকোয়েস্ট ব্যর্থ');
            $btn.prop('disabled', false);
        });
    });

    $(document).on('click', '.oi-quick-pathao', function () {
        var orderId = $(this).data('order-id');
        $('#pathao_order_ids').val(orderId);
        var qvEl = document.getElementById('orderQuickViewModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(qvEl).hide();
        } else {
            $(qvEl).modal('hide');
        }
        $('#pathao').modal('show');
    });

    // Fraud check → Popup Modal Open
    $(document).on('click', '.fraud-check', function(e){
        e.preventDefault();
        let mobile  = $(this).data('mobile');
        
        if (!mobile) { return toastr.error("No mobile number found"); }

        $("#fraudModalBody").html(`
            <div class="text-center py-5">
                <div class="spinner-border text-success" style="width:3rem;height:3rem;"></div>
                <p class="mt-3 fw-bold">তথ্য যাচাই করা হচ্ছে...</p>
            </div>
        `);

        $("#fraudCheckModal").modal("show");

        $.ajax({
            url: "{{ route('admin.fraud.check') }}",
            type: "POST",
            data: { 
                mobile: mobile,
                // আমরা এখানে order_id পাঠাচ্ছি না, কারণ কন্ট্রোলার মোবাইল নম্বর দিয়ে 
                // সব অর্ডার আপডেট করবে।
                _token: "{{ csrf_token() }}" 
            },
            timeout: 60000, // 60 seconds timeout
            beforeSend: function() {
                // Show loading state
                $("#fraudModalBody").html(`
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">ফ্রড চেক করা হচ্ছে... অনুগ্রহ করে অপেক্ষা করুন।</p>
                    </div>
                `);
            },
            success: function(res) {
                
                if (res && res.status === "success") {
                    let apiData = {};
                    
                    if(res.data && res.data.data) {
                        apiData = res.data.data;
                    } else if (res.data) {
                        apiData = res.data;
                    }

                    // এখন আমরা পেইজে থাকা ওই মোবাইল নাম্বারের *সব বাটন* খুঁজে বের করব
                    let allBtns = $('.fraud-check[data-mobile="'+mobile+'"]');

                    if(res.data && res.data.is_fraud === true) {
                         $("#fraudModalBody").html(`
                            <div class="alert alert-danger text-center p-5">
                                <h3>⚠️ ফ্রড ডিটেক্টেড!</h3>
                                <p>এই নাম্বারটি ফ্রড তালিকায় রয়েছে।</p>
                            </div>
                         `);
                         
                         // সব বাটন লাল করে দেওয়া
                         allBtns.removeClass('btn-warning text-dark btn-success').addClass('btn-danger').text('ফ্রড (ঝুঁকি)');
                         return;
                    }

                    // Build Summary
                    var summary = buildSummary(apiData);
                    $("#fraudModalBody").html(loadFraudHtml(summary, mobile));

                    // ==========================================
                    // INSTANT BUTTON UPDATE LOGIC (ALL BUTTONS)
                    // ==========================================
                    let r = summary.rate;
                    
                    // আগের ক্লাস রিমুভ
                    allBtns.removeClass('btn-warning text-dark btn-success btn-danger');

                    if(r >= 80) {
                        // Safe
                        allBtns.addClass('btn-success');
                        allBtns.text(r + '% নিরাপদ');
                    } else {
                        // Risk
                        allBtns.addClass('btn-danger');
                        allBtns.text(r + '% ঝুঁকি');
                    }

                    toastr.success('স্ট্যাটাস সফলভাবে সেভ হয়েছে!');

                } else {
                    var msg = (res && res.message) ? res.message : 'No data returned';
                    $("#fraudModalBody").html(`<div class="alert alert-danger text-center p-4">${msg}</div>`);
                }
            },

            error: function(xhr, status, error) {
                console.error('Fraud Check AJAX Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseJSON,
                    statusCode: xhr.status
                });
                
                let errorMessage = 'অনুগ্রহ করে আবার চেষ্টা করুন।';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (status === 'timeout') {
                    errorMessage = 'Request timeout! API server response নেওয়া যায়নি। অনুগ্রহ করে আবার চেষ্টা করুন।';
                } else if (status === 'error') {
                    errorMessage = 'Connection error! API server-এ connection করতে পারছে না।';
                } else if (xhr.status === 400) {
                    errorMessage = 'Invalid request! দয়া করে মোবাইল নাম্বার চেক করুন।';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error! দয়া করে admin-কে জানান।';
                } else if (xhr.status === 404) {
                    errorMessage = 'API endpoint not found!';
                }
                
                $("#fraudModalBody").html(`
                    <div class="alert alert-danger text-center p-4">
                        <h5>❌ Error!</h5>
                        <p>${errorMessage}</p>
                        ${xhr.responseJSON && xhr.responseJSON.message ? `<small>${xhr.responseJSON.message}</small>` : ''}
                    </div>
                `);
                
                // Reset button to original state
                let allBtns = $('.fraud-check[data-mobile="'+mobile+'"]');
                allBtns.removeClass('btn-success btn-danger').addClass('btn-warning').text('চেকিং');
                
                toastr.error('Fraud check failed: ' + errorMessage);
            }
        });
    });

    // order assign (Single from 3-dot)
    $(document).on('click', '.single-assign-btn', function (e) {
        e.preventDefault();
        var orderId = $(this).data('order-id');
        var currentUserId = $(this).data('current-user') || '';
        var invoice = $(this).data('invoice') || '';
        $('#single_assign_order_id').val(orderId);
        $('#user_id').val(currentUserId);
        $('#asignUserModalTitle').html('<i class="fas fa-user-plus me-1"></i> ইউজার অ্যাসাইন #' + invoice);
        $('#asignUser').modal('show');
    });

    // Bulk assign trigger from toolbar
    $(document).on('click', '[data-bs-target="#asignUser"]', function () {
        $('#single_assign_order_id').val('');
        $('#user_id').val('');
        $('#asignUserModalTitle').html('<i class="fas fa-user-plus me-1"></i> ইউজার অ্যাসাইন (বাল্ক)');
    });

    // order assign submit
    $(document).on('submit', 'form#order_assign', function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        let user_id = $('#user_id').val();
        let singleOrderId = $('#single_assign_order_id').val();

        var order_ids = [];
        if (singleOrderId) {
            order_ids = [singleOrderId];
        } else {
            var order = $('input.checkbox:checked').map(function(){
              return $(this).val();
            });
            order_ids = order.get();
        }

        if(order_ids.length == 0){
            toastr.error('Please Select An Order First !');
            return;
        }

        if(!user_id){
            toastr.error('Please Select A User First !');
            return;
        }

        var $submitBtn = $(this).find('button[type="submit"]');
        var origText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('অ্যাসাইন হচ্ছে...');

        $.ajax({
           type: 'GET',
           url: url,
           data: { user_id: user_id, order_ids: order_ids },
           success: function(res){
               if(res.status == 'success'){
                   toastr.success(res.message);
                   $('#asignUser').modal('hide');
                   setTimeout(function(){
                       window.location.reload();
                   }, 500);
               } else {
                   toastr.error(res.message || 'Failed something wrong');
                   $submitBtn.prop('disabled', false).text(origText);
               }
           },
           error: function(){
               toastr.error('Something went wrong');
               $submitBtn.prop('disabled', false).text(origText);
           }
        });
    });

    // order status change
    $(document).on('submit', 'form#order_status_form', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        var url = $(this).attr('action');
        let order_status = $('#order_status').val();
        var $statusSelect = $('#order_status');
        var $statusError = $('#status_error');
        
        // Clear any previous validation state
        $statusSelect.removeClass('is-invalid is-valid');
        $statusError.hide();

        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids = order.get();

        // Validate orders selected FIRST
        if(order_ids.length == 0){
            toastr.error('Please Select An Order First !');
            return false;
        }
        
        // Validate status selected - check multiple conditions
        var statusValue = String(order_status || '').trim();
        if(!statusValue || statusValue === '' || statusValue === 'null' || statusValue === 'undefined' || statusValue === '0'){
            $statusSelect.addClass('is-invalid');
            $statusError.text('Please select a status').show();
            toastr.error('Please Select A Status First !');
            // Focus on select field and scroll to it
            $statusSelect.focus();
            $('html, body').animate({
                scrollTop: $statusSelect.offset().top - 100
            }, 300);
            return false;
        }
        
        // Additional check - make sure it's a valid number
        if(isNaN(parseInt(statusValue)) || parseInt(statusValue) <= 0){
            $statusSelect.addClass('is-invalid');
            $statusError.text('Please select a valid status').show();
            toastr.error('Please Select A Valid Status !');
            $statusSelect.focus();
            return false;
        // If partial settlement status is selected:
        if (statusValue == '8' || statusValue == '9' || statusValue == '10' || statusValue == '11') {
            if (order_ids.length === 1) {
                $('#changeStatus').modal('hide');
                window.openPartialSettlementModal(order_ids[0]);
                return false;
            } else {
                toastr.warning('আংশিক ডেলিভারি সেটেলমেন্টের জন্য প্রতিটি অর্ডার আলাদাভাবে সেটেল করতে হবে।');
                return false;
            }
        }

        // Show loading
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var originalHtml = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fe-loader"></i> Updating...');

        $.ajax({
           type: 'GET',
           url: url,
           data: { order_status: order_status, order_ids: order_ids },
           success: function(res){
               if(res.status == 'success'){
                   toastr.success(res.message);
                   $('#changeStatus').modal('hide');
                   setTimeout(function(){
                       window.location.reload();
                   }, 1000);
               } else {
                   toastr.error(res.message || 'Failed something wrong');
                   $submitBtn.prop('disabled', false).html(originalHtml);
               }
           },
           error: function(xhr){
               console.error('Status update error:', xhr);
               var errorMsg = 'Something went wrong';
               
               // Handle Laravel validation errors
               if(xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors){
                   var errors = xhr.responseJSON.errors;
                   if(errors.order_status){
                       $statusSelect.addClass('is-invalid');
                       $statusError.text(errors.order_status[0]).show();
                       errorMsg = errors.order_status[0];
                   } else if(errors.order_ids){
                       errorMsg = errors.order_ids[0];
                   }
               } else if(xhr.responseJSON && xhr.responseJSON.message){
                   errorMsg = xhr.responseJSON.message;
               } else if(xhr.status === 400){
                   errorMsg = 'Bad request. Please check your selection.';
               }
               
               toastr.error(errorMsg);
               $submitBtn.prop('disabled', false).html(originalHtml);
           }
        });
        
        return false;
    });

    // ── Single Order Quick Status Modal Popup ──
    $(document).on('click', '.quick-change-status-btn', function (e) {
        e.preventDefault();
        var orderId = $(this).data('order-id');
        var currentStatus = $(this).data('current-status');
        var invoice = $(this).data('invoice');

        $('#quick_status_order_id').val(orderId);
        $('#quickStatusModalTitle').html('<i class="fas fa-flag me-1"></i> স্ট্যাটাস #' + invoice);

        // Highlight current status button
        $('#quick_status_list .quick-status-opt-btn').each(function () {
            var sId = $(this).data('status-id');
            var sName = $(this).data('status-name');
            $(this).prop('disabled', false).html('<span><i class="far fa-check-circle me-1.5 opacity-50"></i> ' + sName + '</span><span class="badge bg-light text-dark border current-tag d-none" style="font-size: 10px;">বর্তমান</span>');

            if (String(sId) === String(currentStatus)) {
                $(this).addClass('btn-primary text-white').removeClass('btn-outline-primary');
                $(this).find('.current-tag').removeClass('d-none');
            } else {
                $(this).removeClass('btn-primary text-white').addClass('btn-outline-primary');
                $(this).find('.current-tag').addClass('d-none');
            }
        });

        $('#quickSingleStatusModal').modal('show');
    });

    $(document).on('click', '.quick-status-opt-btn', function (e) {
        e.preventDefault();
        var orderId = $('#quick_status_order_id').val();
        var statusId = $(this).data('status-id');
        var statusName = $(this).data('status-name');
        var $btn = $(this);

        if (!orderId || !statusId) return;

        // If target status is 8 (Pending Partial), 9 (Full Received), 10 (Item Received), or 11 (Charge Only):
        if (statusId == 8 || statusId == 9 || statusId == 10 || statusId == 11) {
            $('#quickSingleStatusModal').modal('hide');
            window.openPartialSettlementModal(orderId);
            return;
        }

        $('#quick_status_list .quick-status-opt-btn').prop('disabled', true);
        $btn.html('<i class="fas fa-spinner fa-spin me-1"></i> আপডেট হচ্ছে...');

        $.ajax({
            type: 'GET',
            url: "{{ route('admin.order.status') }}",
            data: {
                order_status: statusId,
                order_ids: [orderId]
            },
            success: function (res) {
                if (res && res.status === 'success') {
                    toastr.success(res.message || 'স্ট্যাটাস আপডেট সফল হয়েছে');
                    $('#quickSingleStatusModal').modal('hide');
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                } else {
                    toastr.error((res && res.message) ? res.message : 'স্ট্যাটাস পরিবর্তন ব্যর্থ');
                    $('#quick_status_list .quick-status-opt-btn').prop('disabled', false);
                    $btn.html('<span><i class="far fa-check-circle me-1.5 opacity-50"></i> ' + statusName + '</span>');
                }
            },
            error: function (xhr) {
                var msg = 'সার্ভার ত্রুটি, অনুগ্রহ করে আবার চেষ্টা করুন';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
                $('#quick_status_list .quick-status-opt-btn').prop('disabled', false);
                $btn.html('<span><i class="far fa-check-circle me-1.5 opacity-50"></i> ' + statusName + '</span>');
            }
        });
    });

    // order delete (bulk)
    $(document).on('click', '.order_delete', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids = order.get();

        if(order_ids.length == 0){
            toastr.error('Please Select An Order First !');
            return;
        }

        $.ajax({
           type: 'GET',
           url: url,
           data: { order_ids: order_ids },
           success: function(res){
               if(res.status == 'success'){
                   toastr.success(res.message);
                   window.location.reload();
               } else {
                   toastr.error(res.message || 'Failed something wrong');
               }
           },
           error: function(){
               toastr.error('Something went wrong');
           }
        });
    });

    // multiple print
    $(document).on('click', '.multi_order_print', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids = order.get();

        if(order_ids.length == 0){
            toastr.error('Please Select Atleast One Order!');
            return;
        }
        $.ajax({
           type: 'GET',
           url: url,
           data: { order_ids: order_ids },
           success: function(res){
               if(res.status == 'success'){
                   var myWindow = window.open("", "_blank");
                   myWindow.document.write(res.view);
               } else {
                   toastr.error(res.message || 'Failed something wrong');
               }
           },
           error: function(){
               toastr.error('Something went wrong');
           }
        });
    });

    // label print
    $(document).on('click', '.multi_label_print', function(e){
        e.preventDefault();
        var order_ids = $('input.checkbox:checked').map(function(){ return $(this).val(); }).get();
        if(order_ids.length == 0){ toastr.error('Please Select Atleast One Order!'); return; }
        $.ajax({
            type: 'GET',
            url: $(this).attr('href'),
            data: { order_ids: order_ids, type: 'label' },
            success: function(res){
                if(res.status == 'success'){
                    var w = window.open("","_blank");
                    w.document.write(res.view);
                } else { toastr.error(res.message || 'Failed'); }
            },
            error: function(){ toastr.error('Something went wrong'); }
        });
    });

    // multiple courier
    $(document).on('click', '.multi_order_courier', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids = order.get();

        if(order_ids.length == 0){
            toastr.error('Please Select An Order First !');
            return;
        }
        
        // Show loading
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fe-loader"></i> Sending...');

        $.ajax({
           type: 'GET',
           url: url,
           data: { order_ids: order_ids },
           success: function(res){
               console.log('Courier Response:', res); // Debug log
               
               if(res.status == 'success'){
                    if(res.success && res.success.length > 0){
                        toastr.success('Orders sent to courier successfully!');
                    }
                    if(res.failed && res.failed.length > 0){
                        res.failed.forEach(function(fail){
                            console.error('Failed order:', fail);
                            toastr.warning('Order ' + fail.order_id + ': ' + fail.message);
                        });
                    }
                    // Reload page to show courier information
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
               } else {
                    toastr.error(res.message || 'Failed something wrong');
                    $btn.prop('disabled', false).html(originalHtml);
               }
           },
           error: function(xhr){
               console.error('Courier Error:', xhr);
               var errorMsg = 'Something went wrong';
               
               if(xhr.responseJSON){
                   // Check for failed orders with detailed messages
                   if(xhr.responseJSON.failed && xhr.responseJSON.failed.length > 0){
                       xhr.responseJSON.failed.forEach(function(fail){
                           var msg = fail.message || 'Failed to send order';
                           if(fail.status_code === 401){
                               msg = 'Account is not active! Please check your Steadfast account status and API credentials.';
                           } else if(fail.status_code === 403){
                               msg = 'Access forbidden! Please check your API credentials.';
                           } else if(fail.status_code === 404){
                               msg = 'API endpoint not found! Please check the API URL.';
                           }
                           toastr.error('Order ' + fail.order_id + ': ' + msg);
                       });
                   } else if(xhr.responseJSON.message){
                       errorMsg = xhr.responseJSON.message;
                   }
               } else if(xhr.status === 401){
                   errorMsg = 'Account is not active! Please check your Steadfast account status and API credentials.';
               } else if(xhr.status === 403){
                   errorMsg = 'Access forbidden! Please check your API credentials.';
               } else if(xhr.status === 404){
                   errorMsg = 'API endpoint not found! Please check the API URL.';
               }
               
               toastr.error(errorMsg);
               $btn.prop('disabled', false).html(originalHtml);
           }
        });
    });

    // Copy Courier Tracking ID
    $(document).on('click', '.copy-courier-id-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data('id');
        if (!id) return;

        navigator.clipboard.writeText(String(id)).then(function () {
            toastr.success('কুরিয়ার আইডি কপি করা হয়েছে: ' + id);
        }).catch(function () {
            var temp = $('<input>');
            $('body').append(temp);
            temp.val(id).select();
            document.execCommand('copy');
            temp.remove();
            toastr.success('কুরিয়ার আইডি কপি করা হয়েছে: ' + id);
        });
    });

    // Sync Live Courier Status (Recall)
    $(document).on('click', '.sync-courier-status-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this);
        var orderId = $btn.data('order-id');
        var invoice = $btn.data('invoice');

        if (!orderId) return;

        var $icon = $btn.find('i');
        $icon.addClass('fa-spin');
        $btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.order.sync_courier_status') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                order_id: orderId,
                invoice_id: invoice
            },
            dataType: "json",
            success: function (res) {
                $icon.removeClass('fa-spin');
                $btn.prop('disabled', false);

                if (res.success) {
                    toastr.success(res.message || 'কুরিয়ার স্ট্যাটাস আপডেট হয়েছে');

                    // Update status badge on the row if changed
                    if (res.order_status_name) {
                        var $row = $btn.closest('tr');
                        var $pill = $row.find('.quick-change-status-btn .oi-status-pill');
                        if ($pill.length) {
                            $pill.html(res.order_status_name + ' <i class="fas fa-caret-down text-muted" style="font-size: 8.5px; margin-left: 2px;"></i>');
                        }
                    }
                } else {
                    toastr.warning(res.message || 'কুরিয়ার স্ট্যাটাস পাওয়া যায়নি');
                }
            },
            error: function (xhr) {
                $icon.removeClass('fa-spin');
                $btn.prop('disabled', false);
                var msg = 'কুরিয়ার স্ট্যাটাস যাচাই করতে সমস্যা হয়েছে';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
            }
        });
    });

    // Quick IP Block from order page

    $(document).on('click', '.block-ip-btn', function(e){
        e.preventDefault();
        var $btn = $(this);
        var ip = $btn.data('ip');
        var reason = $btn.data('reason') || 'ফেইক অর্ডার';
        
        if(!ip){
            toastr.error('IP address not found');
            return;
        }
        
        // Disable button and show loading
        $btn.prop('disabled', true);
        var originalHtml = $btn.html();
        $btn.html('<i class="fe-loader"></i> Blocking...');
        
        $.ajax({
            url: "{{ route('customers.ipblock.quick') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ip: ip,
                reason: reason
            },
            success: function(res){
                if(res.status === 'success'){
                    toastr.success(res.message || 'IP blocked successfully');
                    // Change button to show blocked state (badge style)
                    $btn.replaceWith('<span class="badge bg-secondary" title="This IP is already blocked"><i class="fe-shield"></i> Blocked</span>');
                } else {
                    toastr.error(res.message || 'Failed to block IP');
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                }
            },
            error: function(xhr){
                var errorMsg = 'Failed to block IP';
                if(xhr.responseJSON && xhr.responseJSON.message){
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
                $btn.prop('disabled', false);
                $btn.html(originalHtml);
            }
        });
    });

    // Pathao Modal Open - Set selected order IDs
    $(document).on('click', '[data-bs-target="#pathao"]', function(e){
        var order = $('input.checkbox:checked').map(function(){
            return $(this).val();
        });
        var order_ids = order.get();
        
        if(order_ids.length == 0){
            toastr.error('Please Select Atleast One Order First!');
            e.preventDefault();
            return false;
        }
        
        $('#pathao_order_ids').val(order_ids.join(','));
    });

    // Pathao City Change - Load Zones
    $(document).on('change', '#pathaocity', function(){
        var cityId = $(this).val();
        if(!cityId){
            $('#pathaozone').html('<option value="">Select Zone...</option>');
            $('#pathaoarea').html('<option value="">Select Area...</option>');
            return;
        }
        
        $.ajax({
            url: "{{ route('pathaocity') }}",
            type: "GET",
            data: { city_id: cityId },
            success: function(res){
                var options = '<option value="">Select Zone...</option>';
                if(res && res.data && res.data.data && res.data.data.length > 0){
                    $.each(res.data.data, function(key, zone){
                        options += '<option value="' + zone.zone_id + '">' + zone.zone_name + '</option>';
                    });
                } else {
                    toastr.warning('No zones found for this city');
                }
                $('#pathaozone').html(options);
                $('#pathaoarea').html('<option value="">Select Area...</option>');
            },
            error: function(xhr){
                var errorMsg = 'Failed to load zones';
                if(xhr.responseJSON && xhr.responseJSON.message){
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
                $('#pathaozone').html('<option value="">Select Zone...</option>');
                $('#pathaoarea').html('<option value="">Select Area...</option>');
            }
        });
    });

    // Pathao Zone Change - Load Areas
    $(document).on('change', '#pathaozone', function(){
        var zoneId = $(this).val();
        if(!zoneId){
            $('#pathaoarea').html('<option value="">Select Area...</option>');
            return;
        }
        
        $.ajax({
            url: "{{ route('pathaozone') }}",
            type: "GET",
            data: { zone_id: zoneId },
            success: function(res){
                var options = '<option value="">Select Area...</option>';
                if(res && res.data && res.data.data && res.data.data.length > 0){
                    $.each(res.data.data, function(key, area){
                        options += '<option value="' + area.area_id + '">' + area.area_name + '</option>';
                    });
                } else {
                    toastr.warning('No areas found for this zone');
                }
                $('#pathaoarea').html(options);
            },
            error: function(xhr){
                var errorMsg = 'Failed to load areas';
                if(xhr.responseJSON && xhr.responseJSON.message){
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
                $('#pathaoarea').html('<option value="">Select Area...</option>');
            }
        });
    });

    // Pathao Form Submit
    $(document).on('submit', '#order_sendto_pathao', function(e){
        e.preventDefault();
        
        var orderIds = $('#pathao_order_ids').val();
        if(!orderIds){
            toastr.error('Please select orders first');
            return;
        }
        
        var formData = $(this).serialize();
        formData += '&order_ids=' + orderIds.split(',').map(function(id){ return id.trim(); }).join(',');
        
        // Validate required fields
        if(!$('#pathaostore').val() || !$('#pathaocity').val() || !$('#pathaozone').val() || !$('#pathaoarea').val()){
            toastr.error('Please fill all required fields (Store, City, Zone, Area)');
            return;
        }
        
        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: formData,
            success: function(res){
                if(res.status === 'success'){
                    var successCount = res.result.success ? res.result.success.length : 0;
                    var failedCount = res.result.failed ? res.result.failed.length : 0;
                    
                    if(successCount > 0){
                        toastr.success(successCount + ' order(s) sent to Pathao successfully');
                    }
                    if(failedCount > 0){
                        toastr.warning(failedCount + ' order(s) failed to send');
                    }
                    
                    $('#pathao').modal('hide');
                    setTimeout(function(){
                        window.location.reload();
                    }, 1500);
                } else {
                    toastr.error(res.message || 'Failed to send orders');
                }
            },
            error: function(xhr){
                var errorMsg = 'Failed to send orders';
                if(xhr.responseJSON && xhr.responseJSON.message){
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
            }
        });
    });

    // Copy Customer Phone Number
    $(document).on('click', '.copy-phone-btn', function(e) {
        e.preventDefault();
        var phone = $(this).data('phone');
        if (!phone) return;

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(phone).then(function() {
                toastr.success('ফোন নাম্বার কপি করা হয়েছে: ' + phone);
            }).catch(function() {
                fallbackCopyText(phone, 'ফোন নাম্বার কপি করা হয়েছে: ');
            });
        } else {
            fallbackCopyText(phone, 'ফোন নাম্বার কপি করা হয়েছে: ');
        }
    });

    // Copy Invoice Number
    $(document).on('click', '.copy-invoice-btn', function(e) {
        e.preventDefault();
        var inv = $(this).data('invoice');
        if (!inv) return;

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(inv).then(function() {
                toastr.success('ইনভয়েস নম্বর কপি করা হয়েছে: #' + inv);
            }).catch(function() {
                fallbackCopyText(inv, 'ইনভয়েস নম্বর কপি করা হয়েছে: #');
            });
        } else {
            fallbackCopyText(inv, 'ইনভয়েস নম্বর কপি করা হয়েছে: #');
        }
    });

    function fallbackCopyText(text, prefix) {
        var tempInput = document.createElement("input");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        toastr.success((prefix || 'কপি করা হয়েছে: ') + text);
    }

    // Single Pathao Booking Modal Trigger
    $(document).on('click', '.single-pathao-btn', function(e) {
        e.preventDefault();
        var orderId = $(this).data('order-id');
        $('#pathao_order_ids').val(orderId);
        $('#pathao').modal('show');
    });

});

})(window.jQuery);
</script>

@include('backEnd.order.partials.courier_booking_modal')
@endsection