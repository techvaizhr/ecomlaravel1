@extends('backEnd.layouts.master')
@section('title','Edit Permission')

@section('css')
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
    .warn-callout {
        background: #fffbeb;
        border-left: 4px solid #f59e0b;
        padding: 12px 16px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 20px;
        font-size: 12.5px;
        color: #92400e;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fe-edit me-2 text-primary"></i> Edit Permission</h4>
            <p class="text-muted small mb-0">Update system access control key definition.</p>
        </div>
        <a href="{{ route('permissions.index') }}" class="btn btn-light rounded-pill border shadow-sm px-4">
            <i class="fe-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card card-modern">
                <div class="card-header-modern">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fe-lock me-1 text-primary"></i> Edit Permission Key</h5>
                </div>
                <div class="card-body p-4">
                    <div class="warn-callout">
                        <strong>⚠️ সতর্কতা:</strong> পারমিশনের নাম পরিবর্তন করলে কোডের কন্ট্রোলার ও রোলের সাথে মিসম্যাচ হতে পারে। নিশ্চিত হয়ে পরিবর্তন করুন।
                    </div>

                    <form action="{{ route('permissions.update') }}" method="POST" data-parsley-validate>
                        @csrf
                        <input type="hidden" name="hidden_id" value="{{ $edit_data->id }}">
                        
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">Permission Key (Name) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   name="name" value="{{ $edit_data->name }}" id="name" required>
                            
                            <small class="text-muted d-block mt-2">
                                <i class="fe-info text-primary"></i> ফরম্যাট: <code>module-action</code> (যেমন: <code>category-edit</code>, <code>order-list</code>)
                            </small>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                                <i class="fe-check-circle me-1"></i> Update Permission
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection