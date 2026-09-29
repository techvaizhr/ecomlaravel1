@foreach ($reviews as $review)
<div class="col-12 product-review-item">
    <div class="gomobd-review-card shadow-sm">
        <div class="gomobd-review-card-header d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="gomobd-review-avatar">
                    {{ strtoupper(substr($review->name ?: 'C', 0, 1)) }}
                </div>
                <div class="gomobd-review-meta">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h6 class="gomobd-review-name mb-0 fw-bold">{{ $review->name ?: 'Verified Customer' }}</h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 11px; font-weight: 500; padding: 2px 6px;">
                            <i class="fa fa-check-circle me-1"></i>Verified Buyer
                        </span>
                    </div>
                    <small class="gomobd-review-date text-muted" style="font-size: 12px;">
                        <i class="fa-regular fa-calendar-check me-1"></i>
                        @if($review->created_at)
                            {{ $review->created_at->format('d M Y') }}
                        @elseif($review->review_date)
                            {{ \Carbon\Carbon::parse($review->review_date)->format('d M Y') }}
                        @else
                            Recent
                        @endif
                    </small>
                </div>
            </div>
            <div class="gomobd-review-stars">
                @php
                    $rate = (int) ($review->ratting ?? 5);
                @endphp
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $rate)
                        <i class="fa-solid fa-star text-warning"></i>
                    @else
                        <i class="fa-regular fa-star text-muted"></i>
                    @endif
                @endfor
            </div>
        </div>

        <div class="gomobd-review-body mt-2">
            <p class="mb-2 text-secondary" style="font-size: 14.5px; line-height: 1.6;">
                <i class="fa-regular fa-comment-dots text-success me-1"></i> {{ $review->review }}
            </p>

            {{-- Real Review Photo / Customer Attachment --}}
            @if(!empty($review->image))
                <div class="gomobd-review-photo-box mt-2">
                    <a href="{{ asset($review->image) }}" target="_blank" class="review-image-link d-inline-block" title="Click to view full photo">
                        <img src="{{ asset($review->image) }}" alt="Customer Review Photo" class="img-fluid rounded border shadow-sm review-customer-photo" loading="lazy" style="max-height: 140px; max-width: 180px; object-fit: cover; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endforeach

