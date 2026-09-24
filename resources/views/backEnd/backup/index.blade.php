@extends('backEnd.layouts.master')
@section('title', 'Backup & Restore Manager')

@section('css')
<style>
    .backup-stat-card {
        border: none;
        border-radius: 14px;
        color: #fff;
        padding: 1.25rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .backup-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .backup-stat-card .stat-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2.2rem;
        opacity: 0.22;
    }
    .card-modern {
        border: none;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        background: #fff;
    }
    .nav-tabs-custom {
        border-bottom: 2px solid #e2e8f0;
        gap: 8px;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        font-weight: 600;
        color: #64748b;
        padding: 10px 20px;
        border-radius: 10px 10px 0 0;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .nav-tabs-custom .nav-link.active {
        color: #1877f2;
        background: #fff;
        border-bottom: 3px solid #1877f2;
    }
    .table-modern th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.9rem 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-modern td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        transition: all 0.2s;
    }
    .btn-action-icon:hover {
        background: #f1f5f9;
        color: #0f172a;
        transform: translateY(-1px);
    }
    .upload-dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        background: #f8fafc;
        transition: border-color 0.2s;
    }
    .upload-dropzone:hover {
        border-color: #3b82f6;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i data-feather="hard-drive" class="text-primary" style="width: 24px; height: 24px;"></i>
                Backup & Restore Manager
            </h4>
            <p class="text-muted small mb-0">ডাটাবেজ এবং সকল প্রোডাক্ট/মিডিয়া ইমেজের পূর্ণাঙ্গ ব্যাকআপ ও রিস্টোর ব্যবস্থা।</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.clear.cache') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1" onclick="return confirm('ক্যাশ ক্লিয়ার করতে চান?')">
                <i data-feather="refresh-cw" style="width: 14px; height: 14px;"></i>
                <span>Clear Cache</span>
            </a>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="backup-stat-card" style="background: linear-gradient(135deg, #1877f2 0%, #0d5ec4 100%);">
                <div class="stat-icon"><i class="fas fa-database"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Database Size</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ $dbStats['size_formatted'] }}</h3>
                <small class="text-white-50">{{ $dbStats['tables_count'] }} Tables in MySQL</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="backup-stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-icon"><i class="fas fa-images"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Total Uploads</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ $uploadsStats['size_formatted'] }}</h3>
                <small class="text-white-50">{{ number_format($uploadsStats['count']) }} Images & Files</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="backup-stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                <div class="stat-icon"><i class="fas fa-history"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">DB Backups</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ count($dbBackups) }}</h3>
                <small class="text-white-50">Saved in storage</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="backup-stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="stat-icon"><i class="fas fa-file-archive"></i></div>
                <div class="small text-white-50 text-uppercase fw-bold">Image Backups</div>
                <h3 class="fw-bold mb-0 text-white mt-1">{{ count($imageBackups) }}</h3>
                <small class="text-white-50">ZIP Archives available</small>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT WITH TABS --}}
    @php
        $activeTab = request('tab', 'db');
    @endphp

    <div class="card card-modern">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs nav-tabs-custom px-3 pt-2" id="backupTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab === 'db' ? 'active' : '' }}" id="db-tab" data-bs-toggle="tab" data-bs-target="#db-pane" type="button" role="tab">
                        <i class="fas fa-database text-primary"></i>
                        <span>Database Backup & Restore (SQL)</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab === 'images' ? 'active' : '' }}" id="images-tab" data-bs-toggle="tab" data-bs-target="#images-pane" type="button" role="tab">
                        <i class="fas fa-images text-success"></i>
                        <span>All Images Backup & Restore (ZIP)</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="backupTabContent">

                {{-- ========================================== --}}
                {{-- TAB 1: DATABASE BACKUP & RESTORE --}}
                {{-- ========================================== --}}
                <div class="tab-pane fade {{ $activeTab === 'db' ? 'show active' : '' }}" id="db-pane" role="tabpanel">

                    {{-- Actions Bar --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">MySQL Database Backups</h5>
                            <p class="text-muted small mb-0">সম্পূর্ণ ডাটাবেজের (টেবিল স্ট্রাকচার ও সকল ডাটা) ব্যাকআপ ফাইল তৈরি বা পূর্বের ফাইল রিস্টোর করুন।</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <form action="{{ route('admin.backups.db.create') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary rounded-3 shadow-sm d-flex align-items-center gap-2 px-3 py-2" onclick="return confirm('এখনই সম্পূর্ণ ডাটাবেজ ব্যাকআপ তৈরি করতে চান?')">
                                    <i data-feather="plus-circle" style="width: 16px; height: 16px;"></i>
                                    <span>Create DB Backup (.sql)</span>
                                </button>
                            </form>
                            <form action="{{ route('admin.backups.db.create') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="compress" value="1">
                                <button type="submit" class="btn btn-outline-primary rounded-3 shadow-sm d-flex align-items-center gap-1 px-3 py-2" title="Gzip compressed smaller file size">
                                    <i class="fas fa-file-archive me-1"></i>
                                    <span>Compressed (.sql.gz)</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Upload & Restore Card --}}
                    <div class="card bg-light border-0 rounded-3 mb-4 p-3">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-upload me-1 text-primary"></i> Upload & Restore Database Backup</h6>
                        <p class="text-muted small mb-3">আপনার কম্পিউটার থেকে পূর্বে সংরক্ষিত <code>.sql</code> অথবা <code>.sql.gz</code> ফাইল আপলোড করে ডাটাবেজ রিস্টোর করুন।</p>
                        <form action="{{ route('admin.backups.db.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('সতর্কতা: ডাটাবেজ রিস্টোর করলে বর্তমান ডাটা ওভাররাইট হতে পারে। আপনি কি নিশ্চিত?');">
                            @csrf
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-8">
                                    <input type="file" name="backup_file" class="form-control form-control-sm" accept=".sql,.gz" required>
                                </div>
                                <div class="col-12 col-md-4">
                                    <button type="submit" class="btn btn-danger btn-sm w-100 fw-semibold">
                                        <i class="fas fa-history me-1"></i> Upload & Restore Now
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Backups Table --}}
                    <h6 class="fw-bold text-dark mb-3">Saved Database Backups ({{ count($dbBackups) }})</h6>
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Backup Filename</th>
                                    <th>File Size</th>
                                    <th>Created At</th>
                                    <th class="text-end" width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dbBackups as $key => $backup)
                                    <tr>
                                        <td class="text-muted small">{{ $key + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-file-code text-primary fs-5"></i>
                                                <span class="fw-semibold text-dark">{{ $backup['filename'] }}</span>
                                                @if($backup['is_compressed'])
                                                    <span class="badge bg-soft-info text-info">Gzip</span>
                                                @else
                                                    <span class="badge bg-soft-secondary text-secondary">SQL</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $backup['size_formatted'] }}</span></td>
                                        <td class="text-muted small">{{ $backup['created_diff'] }}</td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                {{-- Download --}}
                                                <a href="{{ route('admin.backups.db.download', $backup['filename']) }}" class="btn-action-icon text-primary" title="Download Backup">
                                                    <i data-feather="download" style="width: 15px; height: 15px;"></i>
                                                </a>

                                                {{-- Restore --}}
                                                <form action="{{ route('admin.backups.db.restore') }}" method="POST" class="d-inline" onsubmit="return confirm('সতর্কতা: এই ব্যাকআপটি রিস্টোর করলে বর্তমান ডাটাবেজের সকল ডাটা প্রতিস্থাপিত হবে। আপনি কি নিশ্চিত?');">
                                                    @csrf
                                                    <input type="hidden" name="filename" value="{{ $backup['filename'] }}">
                                                    <button type="submit" class="btn-action-icon text-warning" title="Restore this Backup">
                                                        <i data-feather="rotate-ccw" style="width: 15px; height: 15px;"></i>
                                                    </button>
                                                </form>

                                                {{-- Delete --}}
                                                <form action="{{ route('admin.backups.db.delete') }}" method="POST" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ব্যাকআপ ফাইলটি ডিলিট করতে চান?');">
                                                    @csrf
                                                    <input type="hidden" name="filename" value="{{ $backup['filename'] }}">
                                                    <button type="submit" class="btn-action-icon text-danger" title="Delete Backup">
                                                        <i data-feather="trash-2" style="width: 15px; height: 15px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-database fs-1 opacity-25 mb-2"></i>
                                            <p class="fw-bold mb-1">কোন ডাটাবেজ ব্যাকআপ ফাইল পাওয়া যায়নি</p>
                                            <small>উপরের "Create DB Backup" বাটনে ক্লিক করে প্রথম ব্যাকআপ তৈরি করুন।</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

                {{-- ========================================== --}}
                {{-- TAB 2: IMAGES / MEDIA BACKUP & RESTORE --}}
                {{-- ========================================== --}}
                <div class="tab-pane fade {{ $activeTab === 'images' ? 'show active' : '' }}" id="images-pane" role="tabpanel">

                    {{-- Actions Bar --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">All Images & Media Backups (ZIP)</h5>
                            <p class="text-muted small mb-0"><code>public/uploads</code> ফোল্ডারের সকল প্রোডাক্ট ছবি, ব্যানার, ক্যাটাগরি ছবি ইত্যাদি জিপ ফাইল হিসেবে ব্যাকআপ ও রিস্টোর করুন।</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <form action="{{ route('admin.backups.images.create') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-3 shadow-sm d-flex align-items-center gap-2 px-3 py-2" onclick="return confirm('সব ছবির জিপ ব্যাকআপ তৈরি করতে কিছুটা সময় লাগতে পারে। শুরু করতে চান?')">
                                    <i data-feather="package" style="width: 16px; height: 16px;"></i>
                                    <span>Create All Images Backup (.zip)</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Upload & Restore Card --}}
                    <div class="card bg-light border-0 rounded-3 mb-4 p-3">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-upload me-1 text-success"></i> Upload & Restore Images ZIP</h6>
                        <p class="text-muted small mb-3">আপনার পূর্বের সংরক্ষিত <code>.zip</code> ইমেজ ব্যাকআপ ফাইল আপলোড করে সরাসরি <code>public/uploads</code> ফোল্ডারে আনজিপ ও রিস্টোর করুন।</p>
                        <form action="{{ route('admin.backups.images.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('সতর্কতা: জিপ ফাইলটি আনজিপ হয়ে বিদ্যমান ছবিগুলোর সাথে রিস্টোর হবে। আপনি কি নিশ্চিত?');">
                            @csrf
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-8">
                                    <input type="file" name="backup_file" class="form-control form-control-sm" accept=".zip" required>
                                </div>
                                <div class="col-12 col-md-4">
                                    <button type="submit" class="btn btn-danger btn-sm w-100 fw-semibold">
                                        <i class="fas fa-history me-1"></i> Upload & Restore Images
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Backups Table --}}
                    <h6 class="fw-bold text-dark mb-3">Saved Image Backups ({{ count($imageBackups) }})</h6>
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Archive Filename</th>
                                    <th>Archive Size</th>
                                    <th>Created At</th>
                                    <th class="text-end" width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($imageBackups as $key => $backup)
                                    <tr>
                                        <td class="text-muted small">{{ $key + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-file-archive text-warning fs-5"></i>
                                                <span class="fw-semibold text-dark">{{ $backup['filename'] }}</span>
                                                <span class="badge bg-soft-success text-success">ZIP</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $backup['size_formatted'] }}</span></td>
                                        <td class="text-muted small">{{ $backup['created_diff'] }}</td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                {{-- Download --}}
                                                <a href="{{ route('admin.backups.images.download', $backup['filename']) }}" class="btn-action-icon text-primary" title="Download ZIP">
                                                    <i data-feather="download" style="width: 15px; height: 15px;"></i>
                                                </a>

                                                {{-- Restore --}}
                                                <form action="{{ route('admin.backups.images.restore') }}" method="POST" class="d-inline" onsubmit="return confirm('সতর্কতা: এই জিপ ব্যাকআপটি রিস্টোর করে uploads ফোল্ডারে এক্সট্রাক্ট করা হবে। আপনি কি নিশ্চিত?');">
                                                    @csrf
                                                    <input type="hidden" name="filename" value="{{ $backup['filename'] }}">
                                                    <button type="submit" class="btn-action-icon text-warning" title="Restore this Image Backup">
                                                        <i data-feather="rotate-ccw" style="width: 15px; height: 15px;"></i>
                                                    </button>
                                                </form>

                                                {{-- Delete --}}
                                                <form action="{{ route('admin.backups.images.delete') }}" method="POST" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ব্যাকআপ ফাইলটি ডিলিট করতে চান?');">
                                                    @csrf
                                                    <input type="hidden" name="filename" value="{{ $backup['filename'] }}">
                                                    <button type="submit" class="btn-action-icon text-danger" title="Delete Backup">
                                                        <i data-feather="trash-2" style="width: 15px; height: 15px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-file-archive fs-1 opacity-25 mb-2"></i>
                                            <p class="fw-bold mb-1">কোন ইমেজ ব্যাকআপ ফাইল পাওয়া যায়নি</p>
                                            <small>উপরের "Create All Images Backup (.zip)" বাটনে ক্লিক করে সব ছবির জিপ ব্যাকআপ তৈরি করুন।</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
