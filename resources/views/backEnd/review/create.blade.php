@extends('backEnd.layouts.master')
@section('title','Create Review')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

<style>
    .card-modern {
        border: none;
        box-shadow: 0 4px 20px rgba(18, 38, 63, 0.05);
        border-radius: 12px;
        background: #fff;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .card-header-modern {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 18px 22px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .header-icon {
        width: 36px;
        height: 36px;
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #334155;
        margin-bottom: 6px;
    }
    .form-control, .select2-container .select2-selection--single {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 9px 14px;
        border-radius: 8px;
        font-size: 13.5px;
        color: #1e293b;
        transition: all 0.2s;
    }
    .form-control:focus {
        background-color: #fff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    .form-control:not(textarea), .select2-container .select2-selection--single {
        height: 44px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
        right: 10px;
    }
    
    /* Image Upload Box */
    .image-drop-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
    }
    .image-drop-zone:hover {
        border-color: #6366f1;
        background: #f1f5f9;
    }
    .image-drop-zone input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    .preview-container {
        display: none;
        position: relative;
        margin-top: 10px;
        text-align: center;
    }
    .preview-container img {
        max-height: 150px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }
    .btn-remove-preview {
        position: absolute;
        top: -8px;
        right: calc(50% - 80px);
        background: #ef4444;
        color: #fff;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        font-size: 12px;
        cursor: pointer;
    }

    /* Switch */
    .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e2e8f0; transition: .3s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.15); }
    input:checked + .slider { background-color: #10b981; }
    input:checked + .slider:before { transform: translateX(20px); }

    .btn-submit {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        border: none;
        color: white;
        padding: 12px 20px;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        transition: 0.2s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fe-message-square text-primary"></i>
                Create New Customer Review
            </h4>
            <p class="text-muted small mb-0">Add genuine or custom customer reviews with real images, star ratings, and custom dates.</p>
        </div>
        <div>
            <a href="{{ route('reviews.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                <i class="fe-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <div class="row">
            
            {{-- Left Main Column --}}
            <div class="col-lg-8">
                
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <div class="header-icon"><i class="fe-edit-3"></i></div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Review Details</h5>
                            <small class="text-muted">Select product and enter reviewer feedback</small>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        
                        {{-- Product --}}
                        <div class="form-group mb-3">
                            <label class="form-label">Select Product <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="product_id" required>
                                <option value="">Choose a product...</option>
                                @foreach ($products as $value)
                                    <option value="{{ $value->id }}" {{ old('product_id') == $value->id ? 'selected' : '' }}>
                                        {{ $value->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Customer Selection & Manual Fields --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Existing Customer (Optional)</label>
                                <select class="form-control select2" name="customer_id" id="customer_select">
                                    <option value="">-- Choose Registered Customer or Enter Below --</option>
                                    @foreach ($customers as $value)
                                        <option value="{{ $value->id }}" data-name="{{ $value->name }}" data-email="{{ $value->email }}" {{ old('customer_id') == $value->id ? 'selected' : '' }}>
                                            {{ $value->name }} ({{ $value->phone ?? $value->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Selecting a customer will automatically fill their name & email.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Customer / Reviewer Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="reviewer_name" placeholder="e.g. Tanvir Ahmed" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Customer Email (Optional)</label>
                                <input type="text" class="form-control" name="email" id="reviewer_email" placeholder="Optional (e.g. tanvir@gmail.com)" value="{{ old('email') }}">
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Review Text --}}
                        <div class="form-group mb-3">
                            <label class="form-label">Review Comment / Feedback <span class="text-danger">*</span></label>
                            <textarea name="review" class="form-control" rows="4" placeholder="Write the customer's real feedback, unboxing impression, or product review here..." required>{{ old('review') }}</textarea>
                            @error('review')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Real Review Image Upload --}}
                        <div class="form-group mb-0">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span><i class="fe-image text-primary me-1"></i> Real Customer/Product Image (ছবি আপলোড)</span>
                                <span class="badge bg-light text-muted border">Optional</span>
                            </label>
                            
                            <div class="image-drop-zone" id="dropZone">
                                <input type="file" name="image" id="reviewImageInput" accept="image/*">
                                <div id="dropZonePrompt">
                                    <i class="fe-upload-cloud text-primary" style="font-size: 28px;"></i>
                                    <p class="mb-1 mt-2 text-dark fw-semibold" style="font-size: 13.5px;">Click to upload or drag & drop customer review photo</p>
                                    <small class="text-muted">JPG, PNG, JPEG, WEBP up to 5MB (Real unboxing/customer photo)</small>
                                </div>
                                <div class="preview-container" id="previewContainer">
                                    <img id="imagePreview" src="#" alt="Preview">
                                    <button type="button" class="btn-remove-preview" id="btnRemoveImage" title="Remove Image">&times;</button>
                                </div>
                            </div>
                            @error('image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- Right Sidebar Settings --}}
            <div class="col-lg-4">
                
                {{-- Rating & Date Card --}}
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <div class="header-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;"><i class="fe-star"></i></div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Rating & Timeline</h5>
                            <small class="text-muted">Star rating & review date</small>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        
                        {{-- Star Rating --}}
                        <div class="form-group mb-4">
                            <label class="form-label">Star Rating <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="ratting" required>
                                <option value="5" {{ old('ratting', '5') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5.0 - Excellent)</option>
                                <option value="4" {{ old('ratting') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4.0 - Very Good)</option>
                                <option value="3" {{ old('ratting') == '3' ? 'selected' : '' }}>⭐⭐⭐ (3.0 - Good)</option>
                                <option value="2" {{ old('ratting') == '2' ? 'selected' : '' }}>⭐⭐ (2.0 - Fair)</option>
                                <option value="1" {{ old('ratting') == '1' ? 'selected' : '' }}>⭐ (1.0 - Poor)</option>
                            </select>
                            @error('ratting')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Review Date (তারিখ) --}}
                        <div class="form-group mb-4">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span><i class="fe-calendar text-primary me-1"></i> Review Date & Time (তারিখ)</span>
                                <span class="badge bg-light text-primary border">Custom Date</span>
                            </label>
                            <input type="datetime-local" class="form-control" name="created_at" id="reviewDateInput" value="{{ old('created_at', now()->format('Y-m-d\TH:i')) }}">
                            <div class="d-flex gap-1 flex-wrap mt-2">
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateQuick(0)">Today</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateQuick(-1)">Yesterday</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateQuick(-3)">3 days ago</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateQuick(-7)">1 week ago</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateQuick(-30)">1 month ago</button>
                            </div>
                            <small class="text-muted d-block mt-1">Set any past date so the review appears natural on the frontend.</small>
                            @error('created_at')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status Switch --}}
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 border mb-4">
                            <div>
                                <h6 class="mb-0 text-dark fw-bold" style="font-size: 13.5px;">Publish Status</h6>
                                <p class="text-muted small mb-0">Publish on product page immediately?</p>
                            </div>
                            <label class="switch mb-0">
                                <input type="checkbox" name="status" value="active" {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-submit w-100 py-2">
                            <i class="fe-check-circle me-1"></i> Save Customer Review
                        </button>

                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="{{ asset('public/backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/libs/select2/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2();

        // Customer auto-fill
        $('#customer_select').on('change', function() {
            var selected = $(this).find(':selected');
            var name = selected.data('name');
            var email = selected.data('email');
            if (name) {
                $('#reviewer_name').val(name);
            }
            if (email && email !== 'N / A' && email !== 'N/A' && email !== 'customer@review.local') {
                $('#reviewer_email').val(email);
            } else {
                $('#reviewer_email').val('');
            }
        });

        // Image Preview handler
        var imageInput = document.getElementById('reviewImageInput');
        var previewContainer = document.getElementById('previewContainer');
        var imagePreview = document.getElementById('imagePreview');
        var prompt = document.getElementById('dropZonePrompt');
        var btnRemove = document.getElementById('btnRemoveImage');

        imageInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.src = event.target.result;
                    previewContainer.style.display = 'block';
                    prompt.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });

        btnRemove.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            imageInput.value = '';
            imagePreview.src = '#';
            previewContainer.style.display = 'none';
            prompt.style.display = 'block';
        });
    });

    // Quick Date setter helper
    function setDateQuick(daysOffset) {
        var d = new Date();
        d.setDate(d.getDate() + daysOffset);
        var iso = d.toISOString();
        var formatted = iso.substring(0, 16);
        // adjust for local timezone
        var localDate = new Date(d.getTime() - (d.getTimezoneOffset() * 60000)).toISOString().substring(0, 16);
        document.getElementById('reviewDateInput').value = localDate;
    }
</script>
@endsection