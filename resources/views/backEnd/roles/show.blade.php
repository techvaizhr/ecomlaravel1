@extends('backEnd.layouts.master')
@section('title','View Role: ' . $role->name)

@section('css')
<style>
    .role-profile-card {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        border-radius: 14px;
        padding: 24px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(30, 27, 75, 0.15);
    }
    .card-modern {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
        background: #fff;
        overflow: hidden;
    }
    .module-card-show {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        overflow: hidden;
        height: 100%;
    }
    .module-header-show {
        background: #f8fafc;
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 700;
        font-size: 13px;
        color: #1e293b;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .module-body-show {
        padding: 12px 14px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .perm-badge-show {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        background-color: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fe-eye me-2 text-primary"></i> Role Details</h4>
            <p class="text-muted small mb-0">Overview of assigned module permissions for this role.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary rounded-pill px-3 shadow-sm fw-bold">
                <i class="fe-edit-2 me-1"></i> Edit Role
            </a>
            <a href="{{ route('roles.index') }}" class="btn btn-light rounded-pill border shadow-sm px-3">
                <i class="fe-arrow-left me-1"></i> Back to Roles
            </a>
        </div>
    </div>

    {{-- HERO CARD --}}
    <div class="role-profile-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:54px;height:54px;border-radius:14px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:24px;">
                    <i class="fe-shield text-white"></i>
                </div>
                <div>
                    <h3 class="text-white mb-1 fw-bold">{{ $role->name }}</h3>
                    <span class="badge bg-white text-dark rounded-pill px-3 py-1 font-size-12 fw-bold">Guard: {{ $role->guard_name ?? 'admin' }}</span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <div class="px-3 py-2 rounded-3 text-center" style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);">
                    <h4 class="text-white mb-0 fw-bold">{{ count($rolePermissions) }}</h4>
                    <small class="text-white-50">Active Permissions</small>
                </div>
            </div>
        </div>
    </div>

    @php
        $grouped = collect($rolePermissions)->groupBy(function($item) {
            $parts = explode('-', $item->name);
            if (count($parts) > 1) {
                array_pop($parts);
                return ucwords(str_replace('_', ' ', implode(' ', $parts)));
            }
            return 'General';
        });
    @endphp

    @if($grouped->isNotEmpty())
        <div class="row g-3">
            @foreach($grouped as $moduleName => $perms)
            <div class="col-lg-4 col-md-6">
                <div class="module-card-show">
                    <div class="module-header-show">
                        <span><i class="fe-folder text-primary me-1"></i> {{ $moduleName }}</span>
                        <span class="badge bg-secondary rounded-pill">{{ count($perms) }}</span>
                    </div>
                    <div class="module-body-show">
                        @foreach($perms as $p)
                        <span class="perm-badge-show">
                            <i class="fe-check font-size-11 text-success"></i> {{ $p->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="card card-modern text-center p-5">
            <i class="fe-alert-circle font-size-36 text-muted mb-2"></i>
            <h5 class="text-dark">No Permissions Assigned</h5>
            <p class="text-muted small mb-3">This role currently has no permissions assigned.</p>
            <div>
                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fe-plus me-1"></i> Assign Permissions Now
                </a>
            </div>
        </div>
    @endif

</div>
@endsection