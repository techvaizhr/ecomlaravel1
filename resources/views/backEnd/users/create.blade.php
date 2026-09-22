@extends('backEnd.layouts.master')
@section('title','Create User')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<style>
    .card-modern {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
        background: #fff;
        overflow: hidden;
    }
    .card-header-modern {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 18px 24px;
    }
    .img-preview-box {
        width: 90px;
        height: 90px;
        border-radius: 14px;
        border: 2px dashed #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f8fafc;
        color: #94a3b8;
        font-size: 24px;
    }
    .img-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .switch-custom {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .switch-custom input { opacity: 0; width: 0; height: 0; }
    .slider-custom {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 24px;
    }
    .slider-custom:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    input:checked + .slider-custom { background-color: #10b981; }
    input:checked + .slider-custom:before { transform: translateX(20px); }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fe-user-plus me-2 text-primary"></i> Create Staff User</h4>
            <p class="text-muted small mb-0">Add a new admin panel staff user and assign their system role.</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-light rounded-pill border shadow-sm px-4">
            <i class="fe-arrow-left me-1"></i> Back to Users
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-modern">
                <div class="card-header-modern">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fe-info me-1 text-primary"></i> Staff Information</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('users.store') }}" method="POST" data-parsley-validate enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" id="name" placeholder="e.g. John Doe" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" id="email" placeholder="staff@example.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       name="password" id="password" placeholder="••••••••" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="confirm-password" class="form-label fw-bold">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control form-control-lg @error('confirm-password') is-invalid @enderror" 
                                       name="confirm-password" id="confirm-password" placeholder="••••••••" required data-parsley-equalto="#password">
                                @error('confirm-password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="roles" class="form-label fw-bold">Assign Roles <span class="text-danger">*</span></label>
                                <select class="form-control select2-multiple" name="roles[]" data-toggle="select2" multiple="multiple" data-placeholder="Choose one or more roles..." required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">ইউজারের দায়িত্ব অনুযায়ী এক বা একাধিক রোল নির্ধারণ করুন।</small>
                                @error('roles')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="image" class="form-label fw-bold">Profile Photo <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       name="image" id="image" accept="image/*" required onchange="previewUserImage(this)">
                                <small class="text-muted d-block mt-1">Recommended square image (e.g. 200x200 px).</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 d-flex align-items-center justify-content-center">
                                <div class="img-preview-box" id="img-preview-wrap">
                                    <i class="fe-user"></i>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between border">
                                    <div>
                                        <strong class="d-block text-dark font-size-14">Account Status (অ্যাকাউন্ট স্ট্যাটাস)</strong>
                                        <small class="text-muted">অ্যাকাউন্ট সক্রিয় রাখতে সুইচ অন রাখুন।</small>
                                    </div>
                                    <label class="switch-custom mb-0">
                                        <input type="checkbox" value="1" name="status" checked>
                                        <span class="slider-custom"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">
                                    <i class="fe-check-circle me-1"></i> Save Staff User
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script>
    $(document).ready(function(){
        $('[data-toggle="select2"]').select2();
    });

    function previewUserImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img-preview-wrap').html('<img src="' + e.target.result + '" alt="Preview">');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection