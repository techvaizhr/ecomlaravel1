@extends('frontEnd.layouts.master') 
@section('title','Hot Deals')

@push('css')
<link rel="stylesheet" href="{{asset('public/frontEnd/css/jquery-ui.css')}}" />
@endpush 


@section('content')
<section class="product-section">
    <div class="container">
        <div class="sorting-section">
            <div class="row">
                <div class="col-sm-6">
                    <div class="category-breadcrumb d-flex align-items-center">
                        <a href="{{ route('home') }}">Home</a>
                        <span>/</span>
                        <strong>Hot Deals</strong>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="showing-data">
                                <span>Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} Results</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mobile-filter-toggle">
                                <i class="fa fa-list-ul"></i><span>filter</span>
                            </div>
                            <div class="page-sort">
                                <form action="" class="sort-form">
                                    <select name="sort" class="form-control form-select sort">
                                        <option value="1" @if(request()->get('sort')==1)selected @endif>Product: Latest</option>
                                        <option value="2" @if(request()->get('sort')==2)selected @endif>Product: Oldest</option>
                                        <option value="3" @if(request()->get('sort')==3)selected @endif>Price: High To Low</option>
                                        <option value="4" @if(request()->get('sort')==4)selected @endif>Price: Low To High</option>
                                        <option value="5" @if(request()->get('sort')==5)selected @endif>Name: A-Z</option>
                                        <option value="6" @if(request()->get('sort')==6)selected @endif>Name: Z-A</option>
                                    </select>
                                    <input type="hidden" name="min_price" value="{{request()->get('min_price')}}" />
                                    <input type="hidden" name="max_price" value="{{request()->get('max_price')}}" />
                                </form>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12">
                 <div class="offer_timer" id="simple_timer"></div>
            </div>
            <div class="col-sm-12">
                <div class="category-product main_product_inner">
                    @foreach($products as $key=>$value)
                    <div class="product_item wist_item">
                        <div class="product_item_inner">
                            <div class="pro_img">
                                <a href="{{ route('product', $value->slug) }}">
                                    <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                        alt="{{ $value->name }}"
                                        loading="lazy"
                                        decoding="async" />
                                </a>
                            </div>
                            <div class="pro_des">
                                <div class="pro_name">
                                    <a href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 35) }}</a>
                                </div>
                            </div>
                        </div>

                        @php
                            $averageRating = (float) ($value->reviews_avg_ratting ?? ($value->relationLoaded('reviews') ? $value->reviews->avg('ratting') : 0)); 
                            $filledStars = floor($averageRating);
                            $hasHalfStar = $averageRating - $filledStars >= 0.5;
                            $emptyStars = 5 - $filledStars - ($hasHalfStar ? 1 : 0);
                        @endphp

                        @if ($averageRating >= 0 && $averageRating <= 5)
                            {{-- Filled stars --}}
                            @for ($i = 0; $i < $filledStars; $i++)
                                <i class="fas fa-star"></i>
                            @endfor

                            {{-- Half star --}}
                            @if ($hasHalfStar)
                                <i class="fas fa-star-half-alt"></i>
                            @endif

                            {{-- Empty stars --}}
                            @for ($i = 0; $i < $emptyStars; $i++)
                                <i class="far fa-star"></i>
                            @endfor
                        @else
                            <span>Invalid rating range</span>
                        @endif

                        <div class="pro_price">
                            <p>
                                @if($value->old_price)
                                    <del>৳ {{ $value->old_price }}</del>
                                @endif
                                ৳ {{ $value->new_price }} 
                                @if($value->old_price && $value->old_price > $value->new_price)
                                    @php
                                        $discount = round((($value->old_price - $value->new_price) * 100) / $value->old_price);
                                    @endphp
                                    <span class="pro_discount_tag">-{{ $discount }}%</span>
                                @endif
                            </p>
                        </div>

                        {{-- === বাটন সেকশন === --}}
                        @if ($value->has_variants)
                            {{-- ভ্যারিয়েন্ট আছে: কুইক ভ্যারিয়েন্ট মডাল খুলবে --}}
                            <div class="pro_btn">
                                <button type="button" class="order-btn-link order-btn quick_variant_modal" data-id="{{ $value->id }}" data-action="order">
                                    অর্ডার করুন
                                </button>
                                <button type="button" class="cart-icon-link cart-icon-btn quick_variant_modal" data-id="{{ $value->id }}" data-action="cart">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </button>
                            </div>
                        @else
                            {{-- ভ্যারিয়েন্ট নেই: একটিতে অর্ডার Now, আরেকটিতে শুধু কার্টে যোগ --}}
                            <div class="pro_btn">
                                {{-- অর্ডার করুন বাটন --}}
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $value->id }}" />
                                    <input type="hidden" name="qty" value="1" />
                                    <input type="hidden" name="order_now" value="1">
                                    <button type="submit" class="order-btn">
                                        অর্ডার করুন
                                    </button>
                                </form>

                                {{-- কার্ট আইকন বাটন --}}
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $value->id }}" />
                                    <input type="hidden" name="qty" value="1" />
                                    <button type="submit" class="cart-icon-btn cart_store" data-id="{{ $value->id }}">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="custom_paginate">
                    {{$products->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@push('script')
<script>
    $(".sort").change(function(){
       $('#loading').show();
       $(".sort-form").submit();
    })
</script>
<script>
    $("#simple_timer").syotimer({
        date: new Date(2015, 0, 1),
        layout: "hms",
        doubleNumbers: false,
        effectType: "opacity",

        periodUnit: "d",
        periodic: true,
        periodInterval: 1,
    });
</script>
@endpush
