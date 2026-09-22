@extends('backEnd.layouts.master')
@section('title','Edit Role')

@section('css')
<style>
    .card-modern {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
        background: #fff;
        overflow: hidden;
    }
    .module-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        transition: all 0.2s ease;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .module-card:hover {
        border-color: #6366f1;
        box-shadow: 0 6px 18px rgba(99, 102, 241, 0.08);
    }
    .module-header {
        background: #f8fafc;
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .module-title {
        font-weight: 700;
        font-size: 13px;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .module-body {
        padding: 12px 14px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .perm-checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 12px;
        color: #334155;
        cursor: pointer;
        transition: all 0.15s ease;
        margin: 0;
        user-select: none;
    }
    .perm-checkbox-label:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .perm-checkbox-label input:checked ~ span {
        color: #4338ca;
        font-weight: 700;
    }
    .perm-checkbox-label:has(input:checked) {
        background: #eef2ff;
        border-color: #a5b4fc;
    }

    .select-all-box {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        border-radius: 12px;
        padding: 14px 20px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .switch-custom {
        position: relative;
        display: inline-block;
        width: 38px;
        height: 20px;
    }
    .switch-custom input { opacity: 0; width: 0; height: 0; }
    .slider-custom {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 20px;
    }
    .slider-custom:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    input:checked + .slider-custom { background-color: #4f46e5; }
    input:checked + .slider-custom:before { transform: translateX(18px); }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fe-edit me-2 text-primary"></i> Edit Role: {{ $edit_data->name }}</h4>
            <p class="text-muted small mb-0">Modify role name and toggle permitted system actions by module.</p>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-light rounded-pill border shadow-sm px-4">
            <i class="fe-arrow-left me-1"></i> Back to Roles
        </a>
    </div>

    @php
        $assignedIds = $edit_data->permissions->pluck('id')->toArray();
        $groupedPermissions = $permission->groupBy(function($item) {
            $parts = explode('-', $item->name);
            if (count($parts) > 1) {
                array_pop($parts);
                return ucwords(str_replace('_', ' ', implode(' ', $parts)));
            }
            return 'General';
        });
    @endphp

    <form action="{{ route('roles.update') }}" method="POST" data-parsley-validate id="role-form">
        @csrf
        <input type="hidden" name="hidden_id" value="{{ $edit_data->id }}">

        {{-- ROLE NAME CARD --}}
        <div class="card card-modern mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold font-size-14">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                               name="name" value="{{ $edit_data->name }}" id="name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <div class="p-3 bg-light rounded-3 border">
                            <small class="text-muted d-block">
                                <i class="fe-info text-primary me-1"></i> রোলের নাম ও মডিউল ভিত্তিক পারমিশন আপডেট করে নিচের 'Update Role' বাটনে ক্লিক করুন।
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MASTER SELECT ALL --}}
        <div class="select-all-box">
            <div class="d-flex align-items-center gap-3">
                <label class="switch-custom mb-0">
                    <input type="checkbox" id="checkall">
                    <span class="slider-custom"></span>
                </label>
                <div>
                    <strong class="d-block font-size-14">Select All Permissions (সব পারমিশন একসাথে নির্বাচন)</strong>
                    <span class="text-white-50 small" id="selected-counter">0 of {{ $permission->count() }} permissions selected</span>
                </div>
            </div>
            <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                <i class="fe-check-circle me-1"></i> Update Role
            </button>
        </div>

        {{-- MODULE-WISE PERMISSIONS GRID --}}
        <div class="row g-3">
            @foreach($groupedPermissions as $moduleName => $modulePerms)
            @php
                $modSlug = \Illuminate\Support\Str::slug($moduleName);
            @endphp
            <div class="col-lg-4 col-md-6">
                <div class="module-card">
                    <div class="module-header">
                        <h6 class="module-title">
                            <i class="fe-folder text-primary"></i> {{ $moduleName }}
                            <span class="badge bg-secondary rounded-pill font-size-10">{{ $modulePerms->count() }}</span>
                        </h6>
                        <div class="d-flex align-items-center gap-1">
                            <small class="text-muted font-size-11">All</small>
                            <label class="switch-custom mb-0" style="width:32px;height:16px;">
                                <input type="checkbox" class="module-toggle" data-module="{{ $modSlug }}">
                                <span class="slider-custom"></span>
                            </label>
                        </div>
                    </div>
                    <div class="module-body">
                        @foreach($modulePerms as $perm)
                        @php
                            $isChecked = in_array($perm->id, $assignedIds);
                        @endphp
                        <label class="perm-checkbox-label" for="perm_{{ $perm->id }}">
                            <input type="checkbox" class="form-check-input permission-checkbox mod-item-{{ $modSlug }}" 
                                   value="{{ $perm->id }}" id="perm_{{ $perm->id }}" 
                                   name="permission[]" {{ $isChecked ? 'checked' : '' }}>
                            <span class="text-truncate">{{ $perm->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row mt-4 mb-4">
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">
                    <i class="fe-check-circle me-1"></i> Update & Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        function updateCounter() {
            var total = $('.permission-checkbox').length;
            var checked = $('.permission-checkbox:checked').length;
            $('#selected-counter').text(checked + ' of ' + total + ' permissions selected');

            if (checked === total && total > 0) {
                $('#checkall').prop('checked', true);
            } else {
                $('#checkall').prop('checked', false);
            }

            $('.module-toggle').each(function() {
                var mod = $(this).data('module');
                var modTotal = $('.mod-item-' + mod).length;
                var modChecked = $('.mod-item-' + mod + ':checked').length;
                $(this).prop('checked', modChecked === modTotal && modTotal > 0);
            });
        }

        // Global checkall
        $('#checkall').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.permission-checkbox').prop('checked', isChecked);
            $('.module-toggle').prop('checked', isChecked);
            updateCounter();
        });

        // Module toggle
        $('.module-toggle').on('change', function() {
            var mod = $(this).data('module');
            var isChecked = $(this).is(':checked');
            $('.mod-item-' + mod).prop('checked', isChecked);
            updateCounter();
        });

        // Individual checkbox change
        $(document).on('change', '.permission-checkbox', function() {
            updateCounter();
        });

        updateCounter();
    });
</script>
@endsection