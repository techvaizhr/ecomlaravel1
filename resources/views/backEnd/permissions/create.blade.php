@extends('backEnd.layouts.master')
@section('title','Create Permission')

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
    .builder-box {
        background: #f8fafc;
        border: 1.5px dashed #cbd5e1;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .code-preview {
        font-family: 'Consolas', 'Courier New', monospace;
        background: #1e1b4b;
        color: #38bdf8;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 700;
        display: block;
        margin-top: 6px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fe-shield me-2 text-primary"></i> Create Permission</h4>
            <p class="text-muted small mb-0">Define specific access rights for system roles.</p>
        </div>
        <a href="{{ route('permissions.index') }}" class="btn btn-light rounded-pill border shadow-sm px-4">
            <i class="fe-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card card-modern">
                <div class="card-header-modern">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fe-plus-circle me-1 text-primary"></i> New Permission Details</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('permissions.store') }}" method="POST" data-parsley-validate id="perm-form">
                        @csrf
                        
                        {{-- QUICK BUILDER --}}
                        <div class="builder-box">
                            <label class="form-label fw-bold text-primary font-size-13 mb-2">
                                <i class="fe-zap me-1"></i> Quick Helper Builder (Optional)
                            </label>
                            <p class="text-muted small mb-2">নিচের ড্রপডাউন থেকে মডিউল ও অ্যাকশন সিলেক্ট করে স্বয়ংক্রিয় সঠিক পারমিশন কোড তৈরি করুন:</p>
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <select id="builder-module" class="form-select">
                                        <option value="">-- Select Module --</option>
                                        <option value="order">Order (অর্ডার)</option>
                                        <option value="product">Product (প্রোডাক্ট)</option>
                                        <option value="category">Category (ক্যাটাগরি)</option>
                                        <option value="subcategory">Subcategory (সাবক্যাটাগরি)</option>
                                        <option value="childcategory">Childcategory (চাইল্ডক্যাটাগরি)</option>
                                        <option value="brand">Brand (ব্র্যান্ড)</option>
                                        <option value="banner">Banner (ব্যানার)</option>
                                        <option value="coupon">Coupon (কুপন)</option>
                                        <option value="user">User (ইউজার)</option>
                                        <option value="role">Role (রোল)</option>
                                        <option value="customer">Customer (কাস্টমার)</option>
                                        <option value="report">Report (রিপোর্ট)</option>
                                        <option value="setting">Setting (সেটিংস)</option>
                                        <option value="blog">Blog (ব্লগ)</option>
                                        <option value="expense">Expense (খরচ)</option>
                                        <option value="shipping">Shipping (ডেলিভারি)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select id="builder-action" class="form-select">
                                        <option value="list">list (ভিউ / তালিকা)</option>
                                        <option value="create">create (তৈরি / যোগ)</option>
                                        <option value="edit">edit (এডিট / আপডেট)</option>
                                        <option value="delete">delete (মুছে ফেলা)</option>
                                        <option value="status">status (স্ট্যাটাস পরিবর্তন)</option>
                                        <option value="export">export (এক্সপোর্ট / ডাউনলোড)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Permission Key (Name) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" id="name" 
                                   placeholder="e.g. product-create, order-edit" required>
                            
                            <small class="text-muted d-block mt-2">
                                <i class="fe-info text-primary"></i> ফরম্যাট: <code>module-action</code> (যেমন: <code>order-create</code>, <code>product-delete</code>)
                            </small>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small">Generated Code Preview:</label>
                            <span class="code-preview" id="perm-preview">waiting input...</span>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                                <i class="fe-check-circle me-1"></i> Save Permission
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        function updateBuilder() {
            var mod = $('#builder-module').val();
            var act = $('#builder-action').val();
            if (mod && act) {
                var generated = mod + '-' + act;
                $('#name').val(generated);
                $('#perm-preview').text(generated);
            }
        }

        $('#builder-module, #builder-action').on('change', updateBuilder);

        $('#name').on('input', function() {
            var val = $(this).val().trim();
            $('#perm-preview').text(val || 'waiting input...');
        });

        if ($('#name').val()) {
            $('#perm-preview').text($('#name').val());
        }
    });
</script>
@endsection