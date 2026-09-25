<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ $generalsetting->name }}</title>
        <link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" type="image/x-icon" />
        <!-- fot awesome -->
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/all.css" />
        <!-- core css -->
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/bootstrap.min.css" />
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/animate.css" />
        <!-- owl carousel -->
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/owl.theme.default.css" />
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/owl.carousel.min.css" />
        <!-- owl carousel -->
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/select2.min.css" />
        <!-- common css -->
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/style.css" />
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/responsive.css" />
        <link rel="stylesheet" href="{{ asset('public/backEnd/assets/css/toastr.min.css') }}" />
        <!-- ========== DataLayer Initialization ========== -->
        @php
            $camp_name      = strip_tags($campaign_data->name ?? '');
            $camp_slug      = $campaign_data->slug ?? '';
            $camp_id        = (string) $campaign_data->id;
            $_firstProd     = $products->first();
            $camp_value     = $_firstProd ? (float) $_firstProd->new_price : 0.0;
            $camp_products  = $products->map(function($p) {
                return [
                    'id'        => (string) $p->id,
                    'name'      => strip_tags($p->name ?? ''),
                    'price'     => (float)  $p->new_price,
                    'old_price' => (float)  $p->old_price,
                ];
            })->values();
            $_camp_idx      = 0;
            $camp_items_gtm = $products->map(function($p) use (&$_camp_idx) {
                return [
                    'item_id'   => (string) $p->id,
                    'item_name' => strip_tags($p->name ?? ''),
                    'price'     => (float)  $p->new_price,
                    'index'     => $_camp_idx++,
                    'quantity'  => 1,
                ];
            })->values();
            $primary_gtm_item = $_firstProd ? [
                'item_id'   => (string) $_firstProd->id,
                'item_name' => strip_tags($_firstProd->name ?? ''),
                'price'     => (float)  $_firstProd->new_price,
                'quantity'  => 1,
            ] : null;
        @endphp
        <script>
            window.dataLayer = window.dataLayer || [];
            window._campaignData = {
                id:          @json($camp_id),
                name:        @json($camp_name),
                slug:        @json($camp_slug),
                currency:    'BDT'
            };
            window._campaignProducts = @json($camp_products);
            window._campaignVariants = @json($campaignVariants ?? []);
            window._singleCampaignProductId = @json($products->isNotEmpty() ? (string) $products->first()->id : null);

            // 1. General page data for GTM
            dataLayer.push({
                event:         'site_page_data',
                page_type:     'campaign_landing',
                page_url:      window.location.href,
                currency:      'BDT',
                campaign_id:   @json($camp_id),
                campaign_name: @json($camp_name)
            });

            // 2. GA4 Standard view_item (for Google Analytics 4 Ecommerce)
            @if($primary_gtm_item)
            dataLayer.push({ ecommerce: null });
            dataLayer.push({
                event: 'view_item',
                ecommerce: {
                    currency: 'BDT',
                    value: {{ $camp_value }},
                    items: [@json($primary_gtm_item)]
                }
            });
            @endif

            // 3. Campaign page loaded event
            dataLayer.push({
                event:         'campaign_page_loaded',
                page_type:     'campaign_landing',
                campaign_id:   @json($camp_id),
                campaign_name: @json($camp_name),
                currency:      'BDT',
                value:         {{ $camp_value }},
                ecommerce: {
                    currency: 'BDT',
                    items:    @json($camp_items_gtm)
                }
            });
        </script>
        <!-- ========== Google Tag Manager ========== -->
        @foreach($gtm_code ?? [] as $gtm)
        @php
            $gtm_container_id = preg_match('/^GTM-/i', trim($gtm->code))
                ? trim($gtm->code)
                : 'GTM-' . trim($gtm->code);
        @endphp
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $gtm_container_id }}');</script>
        @endforeach
        <!-- ========== End Google Tag Manager ========== -->

        <meta name="app-url" content="{{route('campaign',$campaign_data->slug)}}" />
        <meta name="robots" content="index, follow" />
        <meta name="description" content="{{$campaign_data->description}}" />
        <meta name="keywords" content="{{ $campaign_data->slug }}" />

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="product" />
        <meta name="twitter:site" content="{{$campaign_data->name}}" />
        <meta name="twitter:title" content="{{$campaign_data->name}}" />
        <meta name="twitter:description" content="{{ $campaign_data->description}}" />
        <meta name="twitter:creator" content="{{ $generalsetting->name }}" />
        <meta property="og:url" content="{{route('campaign',$campaign_data->slug)}}" />
        <meta name="twitter:image" content="{{asset($campaign_data->image_one)}}" />

        <!-- Open Graph data -->
        <meta property="og:title" content="{{$campaign_data->name}}" />
        <meta property="og:type" content="product" />
        <meta property="og:url" content="{{route('campaign',$campaign_data->slug)}}" />
        <meta property="og:image" content="{{asset($campaign_data->image_one)}}" />
        <meta property="og:description" content="{{ $campaign_data->description}}" />
        <meta property="og:site_name" content="{{$campaign_data->name}}" />

        <!-- ========== Facebook Pixel (single init) ========== -->
        @if(isset($pixels) && $pixels->count() > 0)
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}
            (window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
            @foreach($pixels as $pixel)
            fbq('init', '{{{ $pixel->code }}}');
            @endforeach
            fbq('track', 'PageView');
            fbq('track', 'ViewContent', {
                content_name: @json($camp_name),
                content_ids:  @json($products->pluck('id')->map(fn($id) => (string)$id)->values()->toArray()),
                content_type: 'product',
                value:        {{ $camp_value }},
                currency:     'BDT',
                num_items:    {{ $products->count() }}
            });
        </script>
        @foreach($pixels as $pixel)
        <noscript>
            <img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{{ $pixel->code }}}&ev=PageView&noscript=1" />
        </noscript>
        @endforeach
        @endif
        <!-- ========== End Facebook Pixel ========== -->

        <!-- ========== TikTok Pixel ========== -->
        @if(isset($tiktok_pixels) && $tiktok_pixels->count() > 0)
        <script>
            !function (w, d, t) {
                w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];
                ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"];
                ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
                for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
                ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};
                ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";
                    ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};
                    var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;
                    var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
            }(window, document, 'ttq');
            @foreach($tiktok_pixels as $tiktok)
            ttq.load('{{ $tiktok->code }}');
            @endforeach
            ttq.page();
            ttq.track('ViewContent', {
                content_id:   @json($_firstProd ? (string)$_firstProd->id : $camp_id),
                content_name: @json($camp_name),
                content_type: 'product',
                value:        {{ $camp_value }},
                currency:     'BDT',
                quantity:     1,
                contents: [
                    @foreach($products as $p)
                    {
                        content_id:   @json((string)$p->id),
                        content_name: @json(strip_tags($p->name)),
                        content_type: 'product',
                        price:        {{ (float)$p->new_price }},
                        quantity:     1
                    }@if(!$loop->last),@endif
                    @endforeach
                ]
            });
        </script>
        @endif
        <!-- ========== End TikTok Pixel ========== -->
        <style>
            /* Style for selected product card */
            .product-card.selected {
                border: 2px solid #198754;
                box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.35);
            }
            .campaign-product-select {
                position: relative;
                cursor: pointer;
            }
            .campaign-product-radio {
                position: absolute;
                opacity: 0;
                width: 0;
                height: 0;
                pointer-events: none;
            }
            .countdown-container {
                text-align: center;
            }
            .counter-card {
                border: 2px dotted white; /* Dotted border */
                border-radius: 15px; /* Rounded corners */
                padding: 5px; /* Padding for the card */
                background-color: transparent; /* Slightly transparent white background */
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
                text-align: center; /* Center the text within each card */
               
            }
            .counter-card div{
                font-size: 1.2em;
                font-weight:bolder;
                color:white;
            }
            
            
            .counter-card span {
                display: block; /* Make the span block-level for better spacing */
                font-size: 0.8em; /* Font size for labels */
                color:orange;
            }
            @keyframes colorAnimation {
                0% {
                    color: pink; /* Start with pink */
                }
                33% {
                    color: green; /* Transition to green */
                }
                66% {
                    color: red; /* Transition to red */
                }
                100% {
                    color: pink; /* Return to pink */
                }
            }
            
            .animated-heading {
                font-size: 2em; /* Adjust font size as needed */
                font-weight: bold; /* Make the heading bold */
                animation: colorAnimation 3s linear infinite; /* Apply the animation */
                
               
            }
            .form_inn{
                padding:10px;
            }
            @media (max-width: 992px) {
                .campro_inn,.cont_inner,.cont_num ,.discount_inn{
                    padding: 10px!important; /* Add 10px padding for tablet and smaller devices */
                    width: 100%;
                }
                .discount_inn{
                    margin:10px 0 0 0;
                }
                .campro_inn h2{
                    font-size:20px;
                }
            }

            /* High-visibility inputs with distinct focus borders for Campaign Form */
            #order_form .form-control,
            #order_form select,
            #order_form input[type="text"],
            #order_form input[type="tel"] {
                border: 1.5px solid #94a3b8 !important;
                border-radius: 8px !important;
                padding: 10px 14px !important;
                height: 48px !important;
                font-size: 15px !important;
                color: #0f172a !important;
                background-color: #ffffff !important;
                transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            }
            #order_form .form-control:focus,
            #order_form select:focus,
            #order_form input[type="text"]:focus,
            #order_form input[type="tel"]:focus {
                border: 2px solid #2563eb !important;
                outline: none !important;
                box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.18) !important;
                background-color: #ffffff !important;
            }
            #order_form .form-control.is-invalid {
                border: 2px solid #dc2626 !important;
                box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15) !important;
            }
        </style>
        <style>
            .button-3d {
                position: relative;
                overflow: hidden;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
        
           
            
        
            .button-3d:hover {
                transform: scale(1.05);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            }
        
           
        
        </style>
        <style>
            .button-animated-border {
                position: relative;
                overflow: hidden;
                border: 3px solid white; /* Initial border */
                border-radius: 10px; /* Optional: for rounded corners */
                transition: color 0.3s ease; /* Transition for text color */
                animation: border-animation 3s linear infinite; /* Animation */
            }
        
            
        
            @keyframes border-animation {
                0% {
                    border-color: white; /* Transparent at start */
                    transform: scale(0.95); /* Initial scale */
                }
                25% {
                    border-color: yellow; /* Fill with white */
                    transform: scale(1); /* Slightly grow */
                }
                50% {
                    border-color: white; /* Transparent in middle */
                    transform: scale(0.95); /* Back to original scale */
                }
                75% {
                    border-color: yellow; /* Fill with white again */
                    transform: scale(1); /* Slightly grow again */
                }
                100% {
                    border-color: white; /* Transparent at end */
                    transform: scale(0.95); /* Back to original scale */
                }
            }

            /* --- OUTLINED / NOTCHED BORDER LABEL FORM STYLING --- */
            .modern-outline-group {
                position: relative;
                margin-top: 14px;
                margin-bottom: 16px;
            }
            .modern-outline-group .modern-outline-label {
                position: absolute;
                top: -9px;
                left: 12px;
                background: #ffffff;
                padding: 0 6px;
                font-size: 12px;
                font-weight: 600;
                color: #374151;
                z-index: 2;
                pointer-events: none;
                line-height: 1.2;
                border-radius: 2px;
                margin-bottom: 0;
                white-space: nowrap;
            }
            .modern-outline-group .modern-outline-input,
            .modern-outline-group .form-control {
                width: 100%;
                min-height: 48px;
                padding: 10px 14px;
                background: #ffffff;
                border: 1.5px solid #d1d5db !important;
                border-radius: 8px !important;
                font-size: 14px;
                color: #111827;
                outline: none !important;
                transition: all 0.2s ease;
                box-sizing: border-box;
                box-shadow: none !important;
            }
            .modern-outline-group .modern-outline-input:focus,
            .modern-outline-group .form-control:focus {
                border-color: #6366f1 !important;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12) !important;
            }
            .modern-outline-group textarea.modern-outline-input,
            .modern-outline-group textarea.form-control {
                min-height: 72px;
                padding-top: 12px;
                resize: vertical;
            }
            .modern-outline-group input::placeholder,
            .modern-outline-group textarea::placeholder,
            .modern-outline-group .form-control::placeholder {
                color: #9ca3af !important;
                opacity: 0.55 !important;
                font-size: 13.5px !important;
                font-weight: 400 !important;
            }

            /* Order note collapsible button */
            .order-note-collapse-wrapper {
                margin-top: 6px;
                margin-bottom: 12px;
            }
            .order-note-toggle-btn {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                color: #4f46e5;
                font-size: 13.5px;
                font-weight: 600;
                text-decoration: none;
                cursor: pointer;
                background: transparent;
                border: none;
                padding: 4px 0;
                transition: color 0.15s ease;
            }
            .order-note-toggle-btn:hover {
                color: #3730a3;
                text-decoration: underline;
            }
            .order-note-toggle-btn i {
                font-size: 14px;
            }
        
            .button-animated-border:hover {
                color: #fff; /* Change text color on hover */
            }
        </style>

{!! $generalsetting->header_code !!}
    </head>

    <body>
        <!-- ========== GTM noscript ========== -->
        @foreach($gtm_code ?? [] as $gtm)
        @php $gtm_noscript_id = preg_match('/^GTM-/i', trim($gtm->code)) ? trim($gtm->code) : 'GTM-'.trim($gtm->code); @endphp
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm_noscript_id }}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        @endforeach
        <!-- ========== TikTok Pixel noscript ========== -->
        @if(isset($tiktok_pixels) && $tiktok_pixels->count() > 0)
        @foreach($tiktok_pixels as $tiktok)
        <noscript><img height="1" width="1" style="display:none" alt=""
            src="https://analytics.tiktok.com/i18n/pixel/events.js?sdkid={{ $tiktok->code }}&noscript=1" /></noscript>
        @endforeach
        @endif

         @php
            $subtotal = Cart::instance('shopping')->subtotal();
            $subtotal=str_replace(',','',$subtotal);
            $subtotal=str_replace('.00', '',$subtotal);
            $shipping = Session::get('shipping')?Session::get('shipping'):0;
        @endphp
        <section style="background-image: radial-gradient(at center center, #139525 28%, #0E320F 79%)">
            <div class="container py-2 py-md-4">
                <div class="row gy-2">
                    <div class="col-md-7">
                        <h4 class="text-light text-center py-2 py-md-4 fw-bolder">{!! $campaign_data->top_title_1  !!} <span class="text-warning"> {!! $campaign_data->top_title_2  !!}</span> </h4>
                    </div>
                     <div class="col-md-5">
                        <div class="countdown-container">
                            <div class="countdown" id="countdown">
                                <div class="row g-1">
                                    <div class="col-3">
                                       <div class="counter-card">
                                            <div id="days"></div>
                                            <span>Days</span>
                                        </div> 
                                    </div>
                                    <div class="col-3">
                                        <div class="counter-card">
                                            <div id="hours"></div>
                                            <span>Hours</span>
                                        </div>                                        
                                    </div>
                                    <div class="col-3">
                                        <div class="counter-card">
                                            <div id="minutes"></div>
                                            <span>Minutes</span>
                                        </div>                                    
                                    </div>
                                    <div class="col-3">
                                        <div class="counter-card">
                                            <div id="seconds"></div>
                                            <span>Seconds</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="container py-2 py-md-4">
                <div class="py-2 py-md-4  rounded" style="border:2px dashed green">
                    <h2 class="animated-heading text-center">{!! $campaign_data->heading_1 !!}</h2>
                </div>
            </div>
        </section>
        <section>
            <div class="container py-2 py-md-4">
                <div class="row gy-2">
                    @if($campaign_data->image_one)
                    <div class="col-sm-6">
                        <img class="img-fluid shadow" src="{{asset($campaign_data->image_one)}}" >
                    </div>
                    @endif
                    @if($campaign_data->image_two)
                    <div class="col-sm-6">
                        <img class="img-fluid shadow" src="{{asset($campaign_data->image_two)}}" >
                    </div>
                    @endif
                </div>
            </div>
        </section>
        <section>
            <div class="container py-2 py-md-4">
                <div class="row gy-2">
                    @if($campaign_data->feature_1)
                    <div class="col-sm-6">
                       <div class="py-2 py-md-4  rounded" style="border:1px dashed green">
                            <h2 class="text-center">{!! $campaign_data->feature_1 !!}</h2>
                        </div>
                    </div>
                    @endif
                    @if($campaign_data->feature_2)
                    <div class="col-sm-6">
                       <div class="py-2 py-md-4  rounded" style="border:1px dashed green">
                            <h2 class="text-center">{!! $campaign_data->feature_2 !!}</h2>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </section>
        <section>
            <div class="container py-2">
                <div class="py-2 py-md-4  rounded" style="border:2px dashed green">
                    <h2 class="animated-heading text-center">{!! $campaign_data->heading_2 !!}</h2>
                </div>
            </div>
        </section>
        <section>
            <div class="container py-2 ">
                <div class="py-2 py-md-4  rounded" style="border:2px dashed green">
                    <h2 class="animated-heading text-center">{!! $campaign_data->heading_3 !!}</h2>
                </div>
            </div>
        </section>
        {{--
        <section style="background: url('{{asset($campaign_data->banner)}}'); background-repeat: no-repeat; background-size:cover; background-position: center;" >
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="campaign_image">
                            <div class="campaign_item">
                                <div class="banner_t">
                                    <h2>{{$campaign_data->banner_title}}</h2>
                                    
                                    <a href="#order_form" class="cam_order_now" id="cam_order_now"><i class="fa-solid fa-cart-shopping"></i> অর্ডার করুন </a>
                                    <p class="megaoffer_btn">মেগা অফার {{$subtotal}} Tk টাকা</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        --}}
        @if($campaign_data->video!=null)
        <section class="camp_video_sec">
            <div class="container">
            
                <div class="row justify-content-center gy-2 gy-md-4">
                    <div class="col-md-8">
                        <h2 class="p-2 py-md-3 rounded text-center" style="background-color:black;border:green 2px solid;color:white;font-weight:bolder">প্রডাক্টের "ভিডিও দেখুন"</h2>
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <div class="camp_vid rounded" style="border:5px solid red">
                            <iframe width="100%" height="480" 
                            src="https://www.youtube.com/embed/{{$campaign_data->video}}" 
                            title="{{$campaign_data->banner_title}}" frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen=""></iframe>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="ord_btn">
                            <a href="#order_form" class="cam_order_now" id="cam_order_now"> অর্ডার করতে ক্লিক করুন <i class="fa-solid fa-hand-point-right"></i> </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif
        
        <section class="py-2 py-md-4" style="background: linear-gradient(to bottom, #FAF4B3, #ECC7CF);">
            <div class="container my-2 my-md-4">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <h2 class="text-center p-2 p-md-4 rounded" style="background-color:#FBEFF7;border:2px dashed #F1ACE7">আমাদের থেকে বিস্তারিত জানতে এই নাম্বারে কল করুন {{$contact->phone}}</h2>
                        <div class="row justify-content-center my-2 my-md-4 gy-2">
                            <div class="col-md-6 custom_btn">
                                <div class="shadow-lg">
                                    <a href="tel:{{$contact->phone}}" 
                                    class="btn btn-danger btn-lg d-block py-md-3 fs-2 fw-bolder button-3d button-animated-border" >
                                        <i class="fa-solid fa-phone"></i> আমাদের কল করুন </a>
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                            <div class="shadow-lg">
                                <a href="https://wa.me/{{$contact->whatsapp}}" 
                                class="btn btn-success btn-lg d-block py-md-3 fs-2 text-light fw-bolder button-3d button-animated-border">
                                    <i class="fa-brands fa-whatsapp"></i> হোয়াটসঅ্যাপ  
                                    </a>
                             </div>
                                
                            </div>
                        </div>
                        
                        <h2 class="text-center p-2 p-md-4 rounded" style="background-color:#FBEFF7;border:2px dashed #F1ACE7">{!! $campaign_data->heading_4 !!}</h2>
                    
                    </div>
                </div>
            </div>
        </section>

        @if(optional($campaign_data)->short_description && strlen($campaign_data->short_description) > 15 || 
    optional($campaign_data)->description && strlen($campaign_data->description) > 15)
        <section class="rules_sec">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h2>বিস্তারিত</h2>
                                {!! $campaign_data->short_description !!}
                                <br>
                                <br>
                                {!!$campaign_data->description !!} 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="campro_inn">
                            <div class="campro_head">
                                <h2>{{$campaign_data->name}}</h2>
                            </div>

                            <div class="campro_img_slider owl-carousel">
                                @if($campaign_data->image_one)
                               <div class="campro_img_item">
                                   <img src="{{asset($campaign_data->image_one)}}" alt="">
                               </div> 
                               @endif
                                @if($campaign_data->image_two)
                               <div class="campro_img_item">
                                   <img src="{{asset($campaign_data->image_two)}}" alt="">
                               </div> 
                               @endif
                                @if($campaign_data->image_three)
                               <div class="campro_img_item">
                                   <img src="{{asset($campaign_data->image_three)}}" alt="">
                               </div>
                               @endif
                            </div>
                            <div class="col-sm-12">
                                <div class="ord_btn">
                                    <a href="#order_form" class="cam_order_now" id="cam_order_now"> অর্ডার করতে ক্লিক করুন <i class="fa-solid fa-hand-point-right"></i> </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>


        <section>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="rev_inn">
                            
                            <h2 class="campaign_offer">{{$campaign_data->review}}</h2>
                            
                            <div class="review_slider owl-carousel">
                            @foreach($campaign_data->images as $key=>$value)
                            <div class="review_item">
                                <img src="{{asset($value->image)}}" alt="">
                            </div>
                            @endforeach
                           </div>
                            <div class="col-sm-12">
                                <div class="ord_btn">
                                    <a href="#order_form" class="cam_order_now" id="cam_order_now"> অর্ডার করতে ক্লিক করুন <i class="fa-solid fa-hand-point-right"></i> </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <section class="form_sec">
        <div class="container">
           <div class="row">
             <div class="col-sm-12">
                <div class="form_inn">
                    <div class="col-sm-12">
                        <div class="row">
                <div class="col-sm-12">
                    <h2 class="campaign_offer">অফারটি সীমিত সময়ের জন্য, তাই অফার শেষ হওয়ার আগেই অর্ডার করুন</h2>
                    @if($campaign_data->note)
                    <p class="my-1 text-center">
                        {!! $campaign_data->note !!}
                    </p>
                    @endif
                </div>
                
            </div>
            <div class="row order_by">
                @if($products->isEmpty())
                <div class="col-12">
                    <div class="alert alert-warning text-center fw-bold mb-3" role="alert">
                        প্রোডাক্ট এড নেই
                    </div>
                </div>
                @else
                <div class="col-lg-7 cust-order-1">
                    <div class="cart_details">
                        @if($products->count()>1)
                        <div class="card mb-2 ">
                          <div class="card-header">
                                <h5 class="potro_font">একটি পণ্য সিলেক্ট করুনণ </h5>
                            </div>  
                             <div class="card-body">
                                <div class="row g-2">
                                    @foreach($products as $product)
                                        @php
                                            $cardVariantColors = $product->variantPrices->pluck('color')->unique('id')->filter()->map(function ($c) {
                                                $label = $c->getDisplayName() ?? $c->colorName ?? $c->color_name ?? null;
                                                if (empty($label) && !empty($c->color)) {
                                                    $label = $c->color;
                                                }
                                                return ['id' => (int) $c->id, 'name' => $label ?: ('Color #'.$c->id), 'hex' => $c->color ?? null];
                                            })->values();
                                            $cardVariantSizes = $product->variantPrices->pluck('size')->unique('id')->filter()->map(function ($s) {
                                                $label = $s->sizeName ?? $s->size_name ?? $s->name ?? null;
                                                return ['id' => (int) $s->id, 'name' => $label ?: ('Size #'.$s->id)];
                                            })->values();
                                        @endphp
                                        <div class="col-md-3 col-6">
                                            <div class="campaign-product-select border shadow"
                                                data-product-id="{{ $product->id }}"
                                                data-variants="{{ htmlspecialchars(json_encode(['colors' => $cardVariantColors, 'sizes' => $cardVariantSizes]), ENT_QUOTES, 'UTF-8') }}">
                                                <input type="radio"
                                                    class="campaign-product-radio"
                                                    name="product"
                                                    id="product_{{ $product->id }}"
                                                    value="{{ $product->id }}"
                                                    {{ $loop->first ? 'checked' : '' }}>
                                                <label for="product_{{ $product->id }}" class="card shadow-sm product-card mb-0 w-100 {{ $loop->first ? 'selected' : '' }}">
                                                    <img src="{{ asset(optional($product->image)->image ?? 'public/uploads/default.webp') }}" class="card-img-top" alt="{{ $product->name }}" style="height: 100px; object-fit: cover;">
                                                    <div class="card-body p-1 text-center">
                                                        <div class="card-title">{{ Str::limit($product->name, 20) }}</div>
                                                        <div class="card-text mb-1">৳{{ round($product->new_price) }} @if($product->old_price)<del>৳{{ round($product->old_price) }}</del>@endif</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                             </div>
                        </div>
                        @endif
                        @php
                            $showCampaignVariantCard = $products->contains(function ($prod) {
                                return $prod->variantPrices->whereNotNull('color_id')->isNotEmpty()
                                    || $prod->variantPrices->whereNotNull('size_id')->isNotEmpty();
                            });
                        @endphp
                        <div class="card">
                            <div class="card-header">
                                <h5 class="potro_font">পণ্যের বিবরণ </h5>
                            </div>
                            <div class="card-body p-2 p-md-3">
                                @if($showCampaignVariantCard)
                                <div id="campaign-variant-box" class="mb-3">
                                    <p class="fw-bold mb-2 potro_font">কালার ও সাইজ বাছুন</p>
                                    @foreach($products as $product)
                                        @php
                                            $panelColors = $product->variantPrices->pluck('color')->unique('id')->filter();
                                            $panelSizes  = $product->variantPrices->pluck('size')->unique('id')->filter();
                                            $panelHasVariants = $panelColors->isNotEmpty() || $panelSizes->isNotEmpty();
                                        @endphp
                                        <div class="campaign-product-variant-panel border rounded p-2 p-md-3 bg-light"
                                            id="campaign-variants-{{ $product->id }}"
                                            data-product-id="{{ $product->id }}"
                                            style="{{ $loop->first ? '' : 'display:none;' }}">
                                            @if($panelHasVariants)
                                            <div class="row g-2">
                                                @if($panelColors->isNotEmpty())
                                                <div class="col-md-6 campaign-panel-color">
                                                    <label class="form-label mb-1">কালার</label>
                                                    <select class="form-select form-select-lg campaign-pick-color" data-product-id="{{ $product->id }}">
                                                        <option value="">কালার সিলেক্ট করুন</option>
                                                        @foreach($panelColors as $campColor)
                                                            <option value="{{ $campColor->id }}">
                                                                {{ $campColor->getDisplayName() ?? $campColor->colorName ?? $campColor->color ?? ('Color #'.$campColor->id) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @endif
                                                @if($panelSizes->isNotEmpty())
                                                <div class="col-md-6 campaign-panel-size">
                                                    <label class="form-label mb-1">সাইজ</label>
                                                    <select class="form-select form-select-lg campaign-pick-size" data-product-id="{{ $product->id }}">
                                                        <option value="">সাইজ সিলেক্ট করুন</option>
                                                        @foreach($panelSizes as $campSize)
                                                            <option value="{{ $campSize->id }}">
                                                                {{ $campSize->sizeName ?? $campSize->name ?? ('Size #'.$campSize->id) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @endif
                                            </div>
                                            <p class="mb-0 small text-muted mt-2">কালার/সাইজ পরিবর্তন করলে দাম অটোমেটিক আপডেট হবে।</p>
                                            @else
                                            <p class="mb-0 small text-muted">এই পণ্যের জন্য কালার/সাইজ অপশন নেই।</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                                <div id="campaign-cartlist" class="cartlist table-responsive">
                                    @include('frontEnd.layouts.ajax.campaign-cart-table')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 cus-order-2">
                    <div class="checkout-shipping" id="order_form">
                        <form action="{{route('customer.ordersave')}}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="cod">
                        <input type="hidden" name="campaign" value="1">
                        <input type="hidden" name="traffic_source" id="inp_ts" value="{{ old('traffic_source', session('order_traffic_source', 'direct')) }}">
                        <input type="hidden" name="traffic_referrer" id="inp_tsr" value="{{ old('traffic_referrer', session('order_traffic_referrer', '')) }}">
                        <script>
                            (function () {
                                var elTs = document.getElementById('inp_ts');
                                var elTsr = document.getElementById('inp_tsr');
                                if (elTs) {
                                    var st = sessionStorage.getItem('_ts');
                                    if (st) elTs.value = st;
                                }
                                if (elTsr) {
                                    var str = sessionStorage.getItem('_tsr');
                                    if (str) elTsr.value = str;
                                }
                            })();
                        </script>
                        @include('frontEnd.layouts.partials.traffic-attribution')
                        <div class="card">
                            <div class="card-header">
                                <h5 class="potro_font">আপনার ইনফরমেশন দিন  </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- ১. আপনার নাম * --}}
                                    <div class="col-sm-12">
                                        <div class="modern-outline-group">
                                            <label class="modern-outline-label">আপনার নাম <span class="text-danger">*</span></label>
                                            <input type="text" id="name" class="modern-outline-input form-control @error('name') is-invalid @enderror" name="name" value="{{old('name')}}" placeholder="আপনার সম্পূর্ণ নাম লিখুন" required>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- ২. মোবাইল নাম্বার * --}}
                                    <div class="col-sm-12">
                                        <div class="modern-outline-group">
                                            <label class="modern-outline-label">মোবাইল নাম্বার <span class="text-danger">*</span></label>
                                            <input type="tel" id="phone" class="modern-outline-input form-control @error('phone') is-invalid @enderror" name="phone" value="{{old('phone')}}" placeholder="01XXXXXXXXX" pattern="0[0-9]{10}" maxlength="11" minlength="11" required>
                                            @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- ৩. ডেলিভারি এরিয়া * (3-in-1 modal) --}}
                                    <div class="col-sm-12">
                                        @include('frontEnd.layouts.partials.delivery_location_modal', [
                                            'prefix' => 'campaign',
                                            'fieldLabel' => 'ডেলিভারি এরিয়া',
                                            'divisions' => $divisions,
                                            'selectedDivisionId' => old('division_id'),
                                            'selectedDistrictId' => old('district_id'),
                                            'selectedUpazilaId'  => old('upazila_id')
                                        ])
                                    </div>

                                    {{-- ৪. ডেলিভারির স্থান --}}
                                    <div class="col-sm-12">
                                        <div class="modern-outline-group">
                                            <label class="modern-outline-label">ডেলিভারির স্থান</label>
                                            <input type="text" id="address" class="modern-outline-input form-control @error('address') is-invalid @enderror" placeholder="বাসা নং, রোড নং, এলাকা ইত্যাদি (ঐচ্ছিক)" name="address" value="{{old('address')}}">
                                            @error('address')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- ডেলিভারি এরিয়া / চার্জ * (UI থেকে রিমুভ, ব্যাকএন্ড ও শিপিং সিঙ্কের জন্য হিডেন) --}}
                                    <div style="display: none !important;">
                                        <select id="area" name="area">
                                            @foreach($shippingcharge as $key=>$value)
                                            <option value="{{$value->id}}" {{ $loop->first ? 'selected' : '' }}>{{$value->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- ৫. + অর্ডার নোট (কোল্যাপসিবল) --}}
                                    <div class="col-sm-12">
                                        <div class="order-note-collapse-wrapper">
                                            <button type="button" class="order-note-toggle-btn" id="toggle_campaign_order_note">
                                                <i class="fas {{ old('note', old('order_note')) ? 'fa-minus-circle' : 'fa-plus-circle' }}" id="campaign_note_icon"></i>
                                                <span id="campaign_note_text">{{ old('note', old('order_note')) ? 'অর্ডার নোট বন্ধ করুন' : 'অর্ডার নোট' }}</span>
                                            </button>
                                            <div id="campaign_note_collapse_box" class="modern-outline-group mt-2" style="{{ old('note', old('order_note')) ? '' : 'display: none;' }}">
                                                <label class="modern-outline-label">অর্ডার নোট</label>
                                                <textarea name="note" id="campaign_order_note" class="modern-outline-input form-control" rows="2" 
                                                    placeholder="অর্ডার সম্পর্কে কোনো বিশেষ নির্দেশনা থাকলে লিখুন...">{{ old('note', old('order_note')) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- সাবমিট বাটন --}}
                                    <div class="col-sm-12">
                                        <div class="form-group mt-2">
                                            <button class="order_place" type="submit">অর্ডার কন্ফার্ম করুন </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- card end -->
                    </form>
                    </div>
                    @if($campaign_data->billing_details)
                    <p class="my-1 text-center">
                        {!! $campaign_data->billing_details !!}
                    </p>
                    @endif
                </div>
                <!-- col end -->
                
            <!-- col end -->
            </div>
            @endif
                    </div>
                </div>

             </div>
            </div>
        </div>
    </section>

        <script src="{{ asset('public/frontEnd/campaign/js') }}/jquery-2.1.4.min.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/all.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/bootstrap.min.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/owl.carousel.min.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/select2.min.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/script.js"></script>
        <!-- bootstrap js -->
        <script>
            $(document).ready(function () {
                $(".owl-carousel").owlCarousel({
                    margin: 15,
                    loop: true,
                    dots: false,
                    autoplay: true,
                    autoplayTimeout: 6000,
                    autoplayHoverPause: true,
                    items: 1,
                    });
                $('.owl-nav').remove();
            });
        </script>
        <script>
            $(document).ready(function() {
                $('.select2').select2();
            });
        </script>
        <script>
            $(document).ready(function() {
                // Area change → update cart total
                $("#area").on("change", function () {
                    var id = $(this).val();
                    $.ajax({
                        type: "GET",
                        data: { id: id, campaign: 1 },
                        url: "{{route('shipping.charge')}}",
                        dataType: "html",
                        success: function(response){
                            $('#campaign-cartlist').html(response);
                        }
                    });
                });

                // Location Modal selection → update cart total
                document.addEventListener('deliveryLocationSelected', function(e) {
                    var detail = e.detail || {};
                    var divId = detail.division ? detail.division.id : null;
                    var distId = detail.district ? detail.district.id : null;
                    var upaId = detail.upazila ? detail.upazila.id : null;
                    $.ajax({
                        type: "GET",
                        data: { division_id: divId, district_id: distId, upazila_id: upaId, campaign: 1 },
                        url: "{{route('shipping.charge')}}",
                        dataType: "html",
                        success: function(response){
                            $('#campaign-cartlist').html(response);
                        }
                    });
                });

                // Collapsible Order Note Toggle
                $(document).on('click', '#toggle_campaign_order_note', function(e) {
                    e.preventDefault();
                    var $box = $('#campaign_note_collapse_box');
                    var $icon = $('#campaign_note_icon');
                    var $text = $('#campaign_note_text');
                    $box.slideToggle(200, function() {
                        if ($box.is(':visible')) {
                            $icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
                            $text.text('অর্ডার নোট বন্ধ করুন');
                            $box.find('textarea').focus();
                        } else {
                            $icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                            $text.text('অর্ডার নোট');
                        }
                    });
                });

                $('#campaign_division').on('change', function () {
                    var divId = $(this).val();
                    $('#campaign_district').prop('disabled', !divId).html(divId ? '<option value="">লোড হচ্ছে...</option>' : '<option value="">আগে বিভাগ সিলেক্ট করুন</option>');
                    $('#campaign_upazila').prop('disabled', true).html('<option value="">আগে জেলা সিলেক্ট করুন</option>');
                    if (!divId) return;
                    $.get('{{ url('/ajax/delivery/districts') }}/' + divId, function (res) {
                        var opts = '<option value="">জেলা নির্বাচন করুন</option>';
                        (res.data || []).forEach(function (r) {
                            opts += '<option value="' + r.id + '" data-charge="' + r.delivery_charge + '">' + r.name + ' (৳' + r.delivery_charge + ')</option>';
                        });
                        $('#campaign_district').html(opts).prop('disabled', false);
                    }).fail(function () {
                        $('#campaign_district').html('<option value="">লোড ব্যর্থ</option>');
                    });
                });

                $('#campaign_district').on('change', function () {
                    var distId = $(this).val();
                    $('#campaign_upazila').prop('disabled', !distId).html(distId ? '<option value="">লোড হচ্ছে...</option>' : '<option value="">আগে জেলা সিলেক্ট করুন</option>');
                    if (!distId) return;
                    $.get('{{ url('/ajax/delivery/upazilas') }}/' + distId, function (res) {
                        var opts = '<option value="">উপজেলা নির্বাচন করুন</option>';
                        (res.data || []).forEach(function (r) {
                            opts += '<option value="' + r.id + '">' + r.name + '</option>';
                        });
                        $('#campaign_upazila').html(opts).prop('disabled', false);
                    }).fail(function () {
                        $('#campaign_upazila').html('<option value="">লোড ব্যর্থ</option>');
                    });
                });
            });
        </script>
           <script>
            $(document).on("click", ".cart_remove", function () {
                var id = $(this).data("id");
                var useCampaign = $(this).data("campaign");
                $("#loading").show();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id, campaign: useCampaign ? 1 : undefined },
                        url: "{{route('cart.remove')}}",
                        success: function (data) {
                            if (data) {
                                $("#campaign-cartlist").html(data);
                                $("#loading").hide();
                                if (typeof cart_count === 'function') cart_count();
                                if (typeof mobile_cart === 'function') mobile_cart();
                                if (typeof cart_summary === 'function') cart_summary();
                            }
                        },
                    });
                }
            });
            $(document).on("click", ".cart_increment", function () {
                var id = $(this).data("id");
                var useCampaign = $(this).data("campaign");
                $("#loading").show();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id, campaign: useCampaign ? 1 : undefined },
                        url: "{{route('cart.increment')}}",
                        success: function (data) {
                            if (data) {
                                $("#campaign-cartlist").html(data);
                                $("#loading").hide();
                                if (typeof cart_count === 'function') cart_count();
                                if (typeof mobile_cart === 'function') mobile_cart();
                            }
                        },
                    });
                }
            });

            $(document).on("click", ".cart_decrement", function () {
                var id = $(this).data("id");
                var useCampaign = $(this).data("campaign");
                $("#loading").show();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id, campaign: useCampaign ? 1 : undefined },
                        url: "{{route('cart.decrement')}}",
                        success: function (data) {
                            if (data) {
                                $("#campaign-cartlist").html(data);
                                $("#loading").hide();
                                if (typeof cart_count === 'function') cart_count();
                                if (typeof mobile_cart === 'function') mobile_cart();
                            }
                        },
                    });
                }
            });

        </script>
        <script>
            $('.review_slider').owlCarousel({   
                dots: false,
                arrow: false,
                autoplay: true,
                loop: true,
                margin: 10,
                smartSpeed: 1000,
                mouseDrag: true,
                touchDrag: true,
                items: 6,
                responsiveClass: true,
                responsive: {
                    300: {
                        items: 1,
                    },
                    480: {
                        items: 2,
                    },
                    768: {
                        items: 5,
                    },
                    1170: {
                        items: 5,
                    },
                }
            });
        </script>

        <script>
            $('.campro_img_slider').owlCarousel({   
                dots: false,
                arrow: false,
                autoplay: true,
                loop: true,
                margin: 10,
                smartSpeed: 1000,
                mouseDrag: true,
                touchDrag: true,
                items: 3,
                responsiveClass: true,
                responsive: {
                    300: {
                        items: 1,
                    },
                    480: {
                        items: 2,
                    },
                    768: {
                        items: 3,
                    },
                    1170: {
                        items: 3,
                    },
                }
            });
        </script>
        <script>
            function getCurrentCampaignProductId() {
                const checked = document.querySelector('input[name="product"]:checked');
                if (checked) return checked.value;
                return window._singleCampaignProductId || null;
            }

            function showCampaignVariantPanel(productId) {
                productId = String(productId);
                $('.campaign-product-variant-panel').hide();
                const panel = document.getElementById('campaign-variants-' + productId);
                if (panel) {
                    panel.style.display = '';
                }
            }

            function getCampaignVariantValues(productId) {
                const panel = document.getElementById('campaign-variants-' + productId);
                if (!panel) {
                    return { colorId: '', sizeId: '' };
                }
                const colorEl = panel.querySelector('.campaign-pick-color');
                const sizeEl  = panel.querySelector('.campaign-pick-size');
                return {
                    colorId: colorEl ? colorEl.value : '',
                    sizeId:  sizeEl ? sizeEl.value : ''
                };
            }

            function highlightCampaignProduct(productId) {
                productId = String(productId);
                document.querySelectorAll('.product-card').forEach(function (card) {
                    card.classList.remove('selected');
                });
                const label = document.querySelector('label[for="product_' + productId + '"]');
                if (label) {
                    label.classList.add('selected');
                }
            }

            function trackCampaignAddToCart(productId, prodPrice, prodName) {
                dataLayer.push({'ecommerce': null});
                dataLayer.push({
                    'event': 'add_to_cart',
                    'ecommerce': {
                        'currency': 'BDT',
                        'value': prodPrice,
                        'items': [{
                            'item_id':   String(productId),
                            'item_name': prodName,
                            'price':     prodPrice,
                            'quantity':  1
                        }]
                    }
                });

                if (typeof fbq !== 'undefined') {
                    fbq('track', 'AddToCart', {
                        content_ids:  [String(productId)],
                        content_name: prodName,
                        content_type: 'product',
                        value:        prodPrice,
                        currency:     'BDT'
                    }, {eventID: 'atc_' + productId + '_' + Math.floor(Date.now()/1000)});
                }

                if (typeof ttq !== 'undefined') {
                    ttq.track('AddToCart', {
                        content_id:   String(productId),
                        content_name: prodName,
                        content_type: 'product',
                        value:        prodPrice,
                        currency:     'BDT',
                        quantity:     1,
                        contents: [{
                            content_id:   String(productId),
                            content_name: prodName,
                            content_type: 'product',
                            price:        prodPrice,
                            quantity:     1
                        }]
                    });
                }
            }

            function requestCampaignCart(productId, colorId, sizeId, trackAdd) {
                if (!productId) return;

                $.ajax({
                    type: "GET",
                    cache: false,
                    dataType: "html",
                    data: {
                        id: productId,
                        color_id: colorId || '',
                        size_id: sizeId || '',
                        campaign: '1'
                    },
                    url: "{{ route('cart.changeProduct') }}",
                    success: function (data) {
                        if (data && String(data).indexOf('<table') !== -1) {
                            $("#campaign-cartlist").html(data);
                        }

                        if (trackAdd) {
                            var selProd = window._campaignProducts
                                ? window._campaignProducts.find(function (p) { return p.id === String(productId); })
                                : null;
                            var prodPrice = selProd ? selProd.price : 0;
                            var prodName  = selProd ? selProd.name  : '';
                            var priceText = $('#campaign-cartlist tbody tr:first td:last').text().replace(/[^0-9.]/g, '');
                            if (priceText) {
                                prodPrice = parseFloat(priceText) || prodPrice;
                            }
                            trackCampaignAddToCart(productId, prodPrice, prodName);
                        }
                    }
                });
            }

            function updateCart(productId, trackAdd) {
                productId = String(productId);

                const $radio = $('#product_' + productId);
                if ($radio.length) {
                    $radio.prop('checked', true);
                }

                highlightCampaignProduct(productId);
                showCampaignVariantPanel(productId);
                const variantVals = getCampaignVariantValues(productId);
                requestCampaignCart(productId, variantVals.colorId, variantVals.sizeId, !!trackAdd);
            }

            $(document).on('change', 'input.campaign-product-radio', function () {
                updateCart($(this).val(), true);
            });

            $(document).on('click', '.campaign-product-select', function (e) {
                const $radio = $(this).find('input.campaign-product-radio');
                if (!$radio.length) return;
                if (!$radio.prop('checked')) {
                    $radio.prop('checked', true).trigger('change');
                } else if (!$(e.target).is('input.campaign-product-radio')) {
                    updateCart($radio.val(), false);
                }
            });

            $(document).on('change', '.campaign-pick-color, .campaign-pick-size', function () {
                const productId = String($(this).data('product-id') || getCurrentCampaignProductId());
                if (!productId) return;
                const variantVals = getCampaignVariantValues(productId);
                requestCampaignCart(productId, variantVals.colorId, variantVals.sizeId, false);
            });

            $(document).ready(function () {
                const firstInput = document.querySelector('input.campaign-product-radio:checked')
                    || document.querySelector('input.campaign-product-radio');
                const productId = firstInput ? firstInput.value : window._singleCampaignProductId;
                if (productId) {
                    highlightCampaignProduct(productId);
                    showCampaignVariantPanel(productId);
                }
            });
        </script>
        <script>
            @if($campaign_data->deadline)
            // Set the deadline from the campaign data
            const deadline = new Date("{{ $campaign_data->deadline }}").getTime();
        
            // Update the countdown every 1 second
            const x = setInterval(function() {
                // Get current date and time
                const now = new Date().getTime();
        
                // Calculate the distance between now and the deadline
                const distance = deadline - now;
        
                // Time calculations for days, hours, minutes and seconds
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
                // Display the result in the respective elements
                document.getElementById("days").innerHTML = days;
                document.getElementById("hours").innerHTML = hours;
                document.getElementById("minutes").innerHTML = minutes;
                document.getElementById("seconds").innerHTML = seconds;
        
                // If the countdown is over, write some text
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("countdown").innerHTML = "EXPIRED";
                }
            }, 1000);
            @else
            document.getElementById("countdown").style.display = "none";
            @endif
        </script>
        <script>
            // ========== GTM — view_item_list (সব প্রোডাক্ট) ==========
            dataLayer.push({'ecommerce': null});
            dataLayer.push({
                'event': 'view_item_list',
                'ecommerce': {
                    'currency': 'BDT',
                    'items': window._campaignProducts
                        ? window._campaignProducts.map(function(p, i) {
                            return {item_id: p.id, item_name: p.name, price: p.price, index: i, quantity: 1};
                          })
                        : []
                }
            });

            $(document).ready(function() {
                // ========== InitiateCheckout + Lead — Order Form Submit ==========
                $('form[action="{{ route("customer.ordersave") }}"]').on('submit', function(e) {
                    // ডেলিভারি এরিয়া (বিভাগ > জেলা > থানা) ভ্যালিডেশন
                    var divVal = $('#campaign_division_id').val();
                    var distVal = $('#campaign_district_id').val();
                    var upaVal = $('#campaign_upazila_id').val();
                    if (!divVal || !distVal || !upaVal) {
                        e.preventDefault();
                        $('#campaign_delivery_area_trigger').addClass('is-invalid');
                        if (typeof toastr !== 'undefined') {
                            toastr.error('অনুগ্রহ করে আপনার ডেলিভারি এরিয়া (বিভাগ > জেলা > থানা) নির্বাচন করুন', 'এরিয়া নির্বাচন');
                        } else {
                            alert('অনুগ্রহ করে আপনার ডেলিভারি এরিয়া (বিভাগ > জেলা > থানা) নির্বাচন করুন');
                        }
                        $('#campaign_delivery_area_trigger').trigger('click');
                        return false;
                    }

                    var subtotalVal   = parseFloat($('#net_total strong').text().replace(/[^0-9.]/g, '')) || 0;
                    var currentProdId = String(getCurrentCampaignProductId() || window._singleCampaignProductId || '');
                    var selProd       = window._campaignProducts
                        ? window._campaignProducts.find(function(p){ return p.id === currentProdId; })
                        : null;
                    var prodPrice     = selProd ? selProd.price : subtotalVal;
                    var prodName      = selProd ? selProd.name : @json($camp_name);
                    var contentIds    = currentProdId ? [currentProdId] : (window._campaignProducts ? window._campaignProducts.map(function(p){ return p.id; }) : []);
                    var icEventId     = 'ic_camp{{ $campaign_data->id }}_' + Math.floor(Date.now()/1000);
                    var leadEventId   = 'lead_camp{{ $campaign_data->id }}_' + Math.floor(Date.now()/1000);
                    var campItems     = window._campaignProducts
                        ? window._campaignProducts.map(function(p, i){
                            return {item_id: p.id, item_name: p.name, price: p.price, index: i, quantity: 1};
                          })
                        : [{
                            item_id:   currentProdId || '{{ $campaign_data->id }}',
                            item_name: prodName,
                            price:     prodPrice,
                            quantity:  1
                          }];

                    // GTM — begin_checkout
                    dataLayer.push({'ecommerce': null});
                    dataLayer.push({
                        'event': 'begin_checkout',
                        'ecommerce': {
                            'currency': 'BDT',
                            'value':    subtotalVal,
                            'items':    campItems
                        }
                    });

                    // Facebook Pixel — InitiateCheckout + Lead with user matching
                    if (typeof fbq !== 'undefined') {
                        var rawPhone = ($('#phone').val() || '').replace(/\D/g, '');
                        if (rawPhone.length === 11 && rawPhone.charAt(0) === '0') rawPhone = '880' + rawPhone.slice(1);
                        var rawName = ($('#name').val() || '').trim();
                        var nameParts = rawName.split(/\s+/);
                        var fbUserData = { country: 'bd' };
                        if (rawPhone) fbUserData.ph = rawPhone;
                        if (nameParts[0]) fbUserData.fn = nameParts[0].toLowerCase();
                        if (nameParts.slice(1).join(' ')) fbUserData.ln = nameParts.slice(1).join(' ').toLowerCase();
                        var selectedLocation = $('#campaign_delivery_area_label').text();
                        if (selectedLocation && selectedLocation.indexOf('>') !== -1) {
                            var locParts = selectedLocation.split('>');
                            if (locParts[1]) fbUserData.ct = locParts[1].trim().toLowerCase();
                            if (locParts[0]) fbUserData.st = locParts[0].trim().toLowerCase();
                        }
                        try { fbq('set', 'userData', fbUserData); } catch(e) {}

                        fbq('track', 'InitiateCheckout', {
                            content_ids:  contentIds,
                            content_type: 'product',
                            value:        subtotalVal,
                            currency:     'BDT',
                            num_items:    contentIds.length
                        }, {eventID: icEventId});

                        fbq('track', 'Lead', {
                            value:        subtotalVal,
                            currency:     'BDT',
                            content_name: @json($camp_name)
                        }, {eventID: leadEventId});
                    }

                    // TikTok Pixel — InitiateCheckout + PlaceAnOrder
                    if (typeof ttq !== 'undefined') {
                        var ttPhone = ($('#phone').val() || '').replace(/\D/g, '');
                        if (ttPhone.length === 11 && ttPhone.charAt(0) === '0') ttPhone = '+880' + ttPhone.slice(1);
                        if (ttPhone && typeof ttq.identify === 'function') {
                            try { ttq.identify({ phone_number: ttPhone }); } catch(e) {}
                        }

                        var ttContents = (window._campaignProducts && window._campaignProducts.length > 0)
                            ? window._campaignProducts.map(function(p){
                                return {
                                    content_id:   String(p.id),
                                    content_name: p.name,
                                    content_type: 'product',
                                    price:        p.price,
                                    quantity:     1
                                };
                              })
                            : [{
                                content_id:   String(currentProdId || '{{ $camp_id }}'),
                                content_name: @json($camp_name),
                                content_type: 'product',
                                price:        subtotalVal,
                                quantity:     1
                              }];

                        ttq.track('InitiateCheckout', {
                            content_type: 'product',
                            value:        subtotalVal,
                            currency:     'BDT',
                            quantity:     ttContents.length,
                            contents:     ttContents
                        });

                        ttq.track('PlaceAnOrder', {
                            content_type: 'product',
                            value:        subtotalVal,
                            currency:     'BDT',
                            quantity:     ttContents.length,
                            contents:     ttContents
                        });
                    }
                });

                // ========== Order Now Button Click ==========
                $('.cam_order_now').on('click', function() {
                    dataLayer.push({
                        event:         'click_order_now_button',
                        campaign_id:   @json($camp_id),
                        campaign_name: @json($camp_name)
                    });
                });
            });
        </script>
        <script src="{{ asset('public/backEnd/assets/js/toastr.min.js') }}"></script>
        {!! Toastr::message() !!}
    </body>
</html>
