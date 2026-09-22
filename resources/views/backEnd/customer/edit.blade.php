@extends('backEnd.layouts.master')
@section('title','Edit Customer')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

<style>
    .customer-edit-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }
    .customer-edit-card .card-header-styled {
        padding: 20px 24px;
        background: #fafbfe;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .customer-edit-card .card-header-styled .icon-circle {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(14, 165, 233, 0.12);
        color: #0ea5e9;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .customer-edit-card .card-header-styled h5 {
        margin: 0;
        font-weight: 700;
        font-size: 16px;
        color: #0f172a;
    }
    .form-label-styled {
        font-weight: 600;
        font-size: 13px;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    .form-control-styled {
        background-color: #f8fafc;
        border: 1.5px solid #e2e8f0;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 13.5px;
        color: #0f172a;
        transition: all 0.2s ease;
        width: 100%;
    }
    .form-control-styled:focus {
        background-color: #ffffff;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
        outline: none;
    }
    .avatar-preview-box {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 15px auto;
        border: 3px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .avatar-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .switch-styled {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
    }
    .switch-styled input { opacity: 0; width: 0; height: 0; }
    .switch-styled .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 34px;
    }
    .switch-styled .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .switch-styled input:checked + .slider {
        background-color: #10b981;
    }
    .switch-styled input:checked + .slider:before {
        transform: translateX(22px);
    }
    .btn-save-cust {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
        transition: all 0.2s ease;
    }
    .btn-save-cust:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(14, 165, 233, 0.35);
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    
    <!-- Breadcrumb & Header -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <h4 class="page-title mb-1" style="font-weight: 700; color: #0f172a;">Edit Customer</h4>
            <p class="text-muted mb-0 font-size-13">Modify customer contact details, login status and credentials.</p>
        </div>
        <div class="col-md-6 text-md-end mt-2 mt-md-0">
            <div class="d-inline-flex gap-2">
                <a href="{{ route('customers.profile', ['id' => $edit_data->id]) }}" class="btn btn-outline-info rounded-pill px-3 shadow-sm font-size-13">
                    <i class="fe-eye me-1"></i> View Profile
                </a>
                <a href="{{ route('customers.index') }}" class="btn btn-light rounded-pill px-3 border shadow-sm font-size-13">
                    <i class="fe-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <form action="{{route('customers.update')}}" method="POST" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <input type="hidden" value="{{$edit_data->id}}" name="hidden_id">
        
        <div class="row g-3">
            <!-- Left: Main Customer Information -->
            <div class="col-lg-8">
                <div class="customer-edit-card mb-3">
                    <div class="card-header-styled">
                        <div class="icon-circle"><i class="fe-user-check"></i></div>
                        <div>
                            <h5>Customer Account Details</h5>
                            <span class="text-muted font-size-12">Primary contact credentials and addresses</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-styled">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control-styled" name="name" value="{{ old('name', $edit_data->name) }}" required placeholder="e.g. John Doe">
                                @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-styled">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control-styled" name="phone" value="{{ old('phone', $edit_data->phone) }}" required placeholder="e.g. 01700000000">
                                @error('phone') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-styled">Email Address</label>
                                <input type="email" class="form-control-styled" name="email" value="{{ old('email', $edit_data->email) }}" placeholder="e.g. customer@domain.com">
                                @error('email') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-styled">Password <span class="text-muted fw-normal font-size-12">(Leave blank to keep current)</span></label>
                                <input type="password" class="form-control-styled" name="password" placeholder="••••••••">
                                @error('password') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label-styled">Full Delivery Address <span class="text-danger">*</span></label>
                                <textarea class="form-control-styled" name="address" rows="3" required placeholder="House, Road, Area, Thana, District...">{{ old('address', $edit_data->address) }}</textarea>
                                @error('address') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Status, Avatar & Actions -->
            <div class="col-lg-4">
                <div class="customer-edit-card mb-3">
                    <div class="card-header-styled">
                        <div class="icon-circle"><i class="fe-image"></i></div>
                        <div>
                            <h5>Profile & Status</h5>
                            <span class="text-muted font-size-12">Customer avatar & status</span>
                        </div>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="avatar-preview-box">
                            @if($edit_data->image)
                                <img src="{{asset($edit_data->image)}}" alt="Avatar" id="cust-preview-image">
                            @else
                                <img src="{{asset('public/avatar-default.png')}}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($edit_data->name) }}&background=0ea5e9&color=fff&size=110'" alt="Avatar" id="cust-preview-image">
                            @endif
                        </div>

                        <div class="mb-3 text-start">
                            <label class="form-label-styled text-center mb-2">Change Profile Photo</label>
                            <input type="file" class="form-control-styled" name="image" id="cust-upload-image" accept="image/*">
                            <span class="text-muted font-size-12 text-center d-block mt-1">Supported: JPG, JPEG, PNG, WEBP</span>
                            @error('image') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <hr class="my-3" style="border-color: #e2e8f0;">

                        <div class="d-flex justify-content-between align-items-center text-start">
                            <div>
                                <label class="form-label-styled mb-0">Active Status</label>
                                <span class="text-muted font-size-12 d-block">Allow customer to login & place orders</span>
                            </div>
                            <label class="switch-styled">
                                <input type="checkbox" name="status" value="1" @if($edit_data->status == 1 || $edit_data->status == 'active') checked @endif>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="customer-edit-card p-3">
                    <button type="submit" class="btn btn-save-cust w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="fe-check-circle font-size-16"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script>
    document.getElementById('cust-upload-image')?.addEventListener('change', function(event) {
        if(event.target.files && event.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e){
                var output = document.getElementById('cust-preview-image');
                output.src = e.target.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    });
</script>
@endsection