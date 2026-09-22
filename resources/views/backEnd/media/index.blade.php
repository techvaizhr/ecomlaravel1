@extends('backEnd.layouts.master')
@section('title', 'মিডিয়া লাইব্রেরি | Media Manager')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb & Title --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                <div>
                    <h4 class="page-title mb-1 fw-bold text-dark">
                        <i data-feather="image" class="me-1 text-primary"></i> মিডিয়া ম্যানেজার (Media Library)
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        ওয়েবসাইটের সমস্ত ইমেজ ফাইল ফোল্ডার অনুযায়ী পরিদর্শন, অপ্টিমাইজড আপলোড এবং বাল্ক ম্যানেজমেন্ট।
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-soft-primary text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fs-12 fw-semibold">
                        <i class="fas fa-images me-1"></i> মোট {{ number_format($stats['total_files']) }} টি ফাইল ({{ $stats['total_size'] }})
                    </span>
                    <button type="button" class="btn btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#uploadMediaModal">
                        <i data-feather="upload-cloud" style="width: 16px; height: 16px;"></i> নতুন ইমেজ আপলোড
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter, Search, Per-Page & View Controls --}}
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 14px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.media.index') }}" method="GET" id="mediaFilterForm" class="row g-2 align-items-center">
                
                {{-- Folder Filter --}}
                <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label fs-12 fw-semibold text-muted mb-1">ফোল্ডার ফিল্টার</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-folder text-warning"></i></span>
                        <select name="folder" class="form-select form-select-sm border-start-0" onchange="document.getElementById('mediaFilterForm').submit()">
                            <option value="all" {{ $selectedFolder === 'all' ? 'selected' : '' }}>সকল ফোল্ডার (All Folders)</option>
                            @foreach($foldersSummary as $fName => $count)
                                <option value="{{ $fName }}" {{ $selectedFolder === $fName ? 'selected' : '' }}>
                                    📁 {{ ucfirst($fName) }} ({{ $count }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Search by name --}}
                <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label fs-12 fw-semibold text-muted mb-1">ইমেজের নাম দিয়ে খুঁজুন</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="যেমন: banner, shoes..." value="{{ $searchKeyword }}">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                        @if($searchKeyword)
                            <a href="{{ route('admin.media.index', ['folder' => $selectedFolder, 'per_page' => $perPage]) }}" class="btn btn-outline-secondary btn-sm" title="ক্লিয়ার"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </div>

                {{-- Sorting --}}
                <div class="col-xxl-2 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label fs-12 fw-semibold text-muted mb-1">সাজানোর ক্রম</label>
                    <select name="sort" class="form-select form-select-sm" onchange="document.getElementById('mediaFilterForm').submit()">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>সর্বশেষ আপলোড (Newest)</option>
                        <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>পুরাতন প্রথমে (Oldest)</option>
                        <option value="largest" {{ $sort === 'largest' ? 'selected' : '' }}>সর্বোচ্চ সাইজ (Largest)</option>
                        <option value="smallest" {{ $sort === 'smallest' ? 'selected' : '' }}>ক্ষুদ্রতম সাইজ (Smallest)</option>
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>নামানুসারে (A - Z)</option>
                    </select>
                </div>

                {{-- Per Page Select --}}
                <div class="col-xxl-2 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label fs-12 fw-semibold text-muted mb-1">প্রতি পেজে প্রদর্শন</label>
                    <select name="per_page" class="form-select form-select-sm" onchange="document.getElementById('mediaFilterForm').submit()">
                        <option value="12" {{ $perPage == 12 ? 'selected' : '' }}>১২ টি করে</option>
                        <option value="24" {{ $perPage == 24 ? 'selected' : '' }}>২৪ টি করে</option>
                        <option value="48" {{ $perPage == 48 ? 'selected' : '' }}>৪৮ টি করে</option>
                        <option value="96" {{ $perPage == 96 ? 'selected' : '' }}>৯৬ টি করে</option>
                        <option value="150" {{ $perPage == 150 ? 'selected' : '' }}>১৫০ টি করে</option>
                    </select>
                </div>

                {{-- View Mode Toggle (Grid / List) & Reset --}}
                <div class="col-xxl-2 col-xl-2 col-lg-12 col-md-8 col-sm-12 d-flex align-items-end justify-content-end gap-2">
                    <div class="btn-group btn-group-sm" role="group" aria-label="View switcher">
                        <button type="button" class="btn btn-outline-primary" id="btnViewGrid" onclick="setMediaView('grid')" title="গ্রিড ভিউ">
                            <i class="fas fa-th-large me-1"></i> গ্রিড
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnViewList" onclick="setMediaView('list')" title="লিস্ট ভিউ">
                            <i class="fas fa-list me-1"></i> লিস্ট
                        </button>
                    </div>
                    <a href="{{ route('admin.media.index') }}" class="btn btn-light btn-sm text-secondary border" title="ফিল্টার রিসেট">
                        <i class="fas fa-redo-alt"></i>
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- Bulk Action Bar (Floating / Toggle) --}}
    <div id="bulkActionBar" class="card shadow border-primary border-opacity-25 mb-3 bg-soft-primary" style="display: none; border-radius: 12px;">
        <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <input type="checkbox" id="selectAllCheckbox" class="form-check-input" style="cursor: pointer; width: 18px; height: 18px;">
                <label for="selectAllCheckbox" class="mb-0 fw-semibold fs-13 text-primary" style="cursor: pointer;">
                    সবগুলো নির্বাচন করুন
                </label>
                <span class="badge bg-primary rounded-pill px-2 py-1 fs-12 ms-2" id="selectedCountBadge">০ টি নির্বাচিত</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="clearMediaSelection()">
                    সিলেকশন বাতিল
                </button>
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm" onclick="confirmBulkDelete()">
                    <i class="fas fa-trash-alt me-1"></i> নির্বাচিত ফাইল ডিলিট করুন
                </button>
            </div>
        </div>
    </div>

    {{-- Media Display Container --}}
    @if($paginatedMedia->count() > 0)
        
        {{-- 1. Modern Responsive Grid View --}}
        <div id="mediaGridView" class="media-responsive-grid mb-4">
            @foreach($paginatedMedia as $media)
                <div class="media-grid-item" data-path="{{ $media['path'] }}">
                    <div class="media-pro-card">
                        
                        {{-- Top Floating Toolbar --}}
                        <div class="media-card-top-bar">
                            <div class="form-check m-0">
                                <input type="checkbox" class="form-check-input media-checkbox" 
                                       value="{{ $media['path'] }}" 
                                       onchange="updateSelectionState()">
                            </div>
                            <span class="media-ext-badge">{{ $media['extension'] }}</span>
                        </div>

                        {{-- Thumbnail Container --}}
                        <div class="media-img-box" onclick="openMediaPreview({{ json_encode($media) }})">
                            <img src="{{ $media['url'] }}" alt="{{ $media['name'] }}" loading="lazy" class="media-img-elem">
                            <div class="media-hover-overlay">
                                <span class="media-zoom-btn">
                                    <i class="fas fa-search-plus"></i>
                                </span>
                            </div>
                        </div>

                        {{-- Info Body --}}
                        <div class="media-info-box">
                            <div class="media-name-title" title="{{ $media['name'] }}">
                                {{ $media['name'] }}
                            </div>
                            
                            <div class="media-specs-row">
                                <span class="media-pill-tag size">{{ $media['size_formatted'] }}</span>
                                <span class="media-pill-tag dim">{{ $media['dimensions'] ?: $media['extension'] }}</span>
                            </div>

                            <div class="media-folder-row">
                                <span class="media-folder-tag" title="📁 {{ $media['folder'] }}{{ $media['subfolder'] ? '/' . $media['subfolder'] : '' }}">
                                    <i class="fas fa-folder text-warning me-1"></i> {{ $media['folder'] }}{{ $media['subfolder'] ? '/' . $media['subfolder'] : '' }}
                                </span>
                            </div>

                            {{-- Bottom Compact Action Buttons --}}
                            <div class="media-action-row">
                                <button type="button" class="btn-media-act btn-media-copy copy-btn" onclick="copyMediaUrl('{{ $media['url'] }}', this)" title="লিংক কপি করুন">
                                    <i class="fas fa-copy me-1"></i> <span>কপি লিংক</span>
                                </button>
                                <button type="button" class="btn-media-act btn-media-preview" onclick="openMediaPreview({{ json_encode($media) }})" title="বিস্তারিত ও প্রিভিউ">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn-media-act btn-media-del" onclick="confirmSingleDelete('{{ $media['path'] }}', '{{ $media['name'] }}')" title="ডিলিট করুন">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- 2. Clean Data Table List View --}}
        <div id="mediaListView" class="card shadow-sm border-0 mb-4" style="border-radius: 14px; display: none;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 fs-13">
                    <thead class="table-light fs-12 text-muted text-uppercase">
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="listSelectAllCheckbox" onchange="toggleListSelectAll(this)">
                            </th>
                            <th style="width: 60px;">প্রিভিউ</th>
                            <th>ফাইলের নাম ও পাথ</th>
                            <th>ফোল্ডার</th>
                            <th>রেজোলিউশন</th>
                            <th>সাইজ</th>
                            <th>আপলোড তারিখ</th>
                            <th class="text-end" style="width: 130px;">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paginatedMedia as $media)
                            <tr class="media-list-row" data-path="{{ $media['path'] }}">
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input media-checkbox list-checkbox" 
                                           value="{{ $media['path'] }}" 
                                           onchange="updateSelectionState()">
                                </td>
                                <td>
                                    <div class="media-list-thumb" onclick="openMediaPreview({{ json_encode($media) }})">
                                        <img src="{{ $media['url'] }}" alt="{{ $media['name'] }}" loading="lazy">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 320px;" title="{{ $media['name'] }}">
                                        {{ $media['name'] }}
                                    </div>
                                    <small class="text-muted text-truncate d-block font-monospace fs-11" style="max-width: 320px;">
                                        {{ $media['path'] }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-soft-warning text-dark border">
                                        📁 {{ $media['folder'] }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $media['dimensions'] ?: '—' }}</td>
                                <td class="fw-semibold text-primary">{{ $media['size_formatted'] }}</td>
                                <td class="text-muted fs-12">{{ $media['date_formatted'] }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-light border text-primary copy-btn" onclick="copyMediaUrl('{{ $media['url'] }}', this)" title="লিংক কপি">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <button type="button" class="btn btn-light border text-info" onclick="openMediaPreview({{ json_encode($media) }})" title="প্রিভিউ">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-light border text-danger" onclick="confirmSingleDelete('{{ $media['path'] }}', '{{ $media['name'] }}')" title="ডিলিট">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-4">
            <div class="text-muted fs-13">
                মোট <strong>{{ $stats['filtered'] }}</strong> টি ইমেজের মধ্যে 
                <strong>{{ $paginatedMedia->firstItem() ?? 0 }} - {{ $paginatedMedia->lastItem() ?? 0 }}</strong> দেখানো হচ্ছে
            </div>
            <div>
                {{ $paginatedMedia->links('pagination::bootstrap-4') }}
            </div>
        </div>

    @else
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 14px;">
            <div class="card-body">
                <div class="mb-3">
                    <i class="far fa-images text-muted" style="font-size: 54px; opacity: 0.4;"></i>
                </div>
                <h5 class="fw-bold text-dark">কোনো ইমেজ খুঁজে পাওয়া যায়নি</h5>
                <p class="text-muted fs-13">নির্বাচিত ফোল্ডারে বা সার্চ অনুযায়ী কোনো ইমেজ পাওয়া যায়নি। নতুন ইমেজ আপলোড করতে পারেন।</p>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadMediaModal">
                    <i class="fas fa-upload me-1"></i> ইমেজ আপলোড করুন
                </button>
            </div>
        </div>
    @endif

</div>

{{-- Upload Media Modal --}}
<div class="modal fade" id="uploadMediaModal" tabindex="-1" aria-labelledby="uploadMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark" id="uploadMediaModalLabel">
                    <i class="fas fa-cloud-upload-alt text-primary me-2"></i> ইমেজ আপলোড ও স্বয়ংক্রিয় WebP অপ্টিমাইজেশন
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" id="mediaUploadForm">
                @csrf
                <div class="modal-body p-4">
                    
                    {{-- Target Directory Selector --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-13">টার্গেট ফোল্ডার নির্বাচন করুন <span class="text-danger">*</span></label>
                        <select name="folder" class="form-select" id="uploadFolderSelect" required>
                            <option value="product">🛍️ Products (স্বয়ংক্রিয় ডায়নামিক Year/Month সাবফোল্ডার)</option>
                            <option value="banner">🖼️ Banners (সর্বোচ্চ ৩০০ KB WebP)</option>
                            <option value="brand">🏷️ Brands / Logos (সর্বোচ্চ ১০০ KB WebP)</option>
                            <option value="category">📂 Categories (স্বয়ংক্রিয় অপ্টিমাইজড WebP)</option>
                            <option value="settings">⚙️ Settings & System (লোগো, ফেভিকন)</option>
                            <option value="general">📁 General Media (সাধারণ গ্যালারি)</option>
                        </select>
                        <small class="text-muted fs-12 d-block mt-1">
                            <i class="fas fa-shield-alt text-success me-1"></i> আপলোড করা ইমেজ স্বয়ংক্রিয়ভাবে অ্যান্টি-হ্যাকিং ভ্যালিডেশন পার হয়ে হাই-কোয়ালিটি WebP ফরম্যাটে সেভ হবে।
                        </small>
                    </div>

                    {{-- Drag & Drop Upload Zone --}}
                    <div class="dropzone-area border border-2 border-dashed rounded-3 p-4 text-center bg-light position-relative" id="dropzoneArea" style="cursor: pointer; border-color: #cbd5e1 !important;">
                        <input type="file" name="images[]" id="fileUploadInput" multiple accept="image/*" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;" required onchange="handleFileSelect(this)">
                        <div class="py-3">
                            <i class="fas fa-cloud-upload-alt text-primary" style="font-size: 42px;"></i>
                            <h6 class="fw-bold text-dark mt-2 mb-1">ইমেজ ফাইলগুলো এখানে টেনে এনে ছেড়ে দিন অথবা ক্লিক করুন</h6>
                            <p class="text-muted fs-12 mb-0">একাধিক ছবি একসাথে নির্বাচন করা যাবে (JPG, PNG, WEBP, GIF)</p>
                        </div>
                    </div>

                    {{-- Selected Files Preview List --}}
                    <div id="filePreviewList" class="row g-2 mt-3" style="max-height: 220px; overflow-y: auto;"></div>

                </div>
                <div class="modal-footer border-top py-2 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" id="btnUploadSubmit">
                        <i class="fas fa-upload me-1"></i> আপলোড শুরু করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Media Details / Preview Modal --}}
<div class="modal fade" id="mediaPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark" id="previewModalTitle">ইমেজ বিস্তারিত</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-6 text-center bg-light p-3 rounded-3 border">
                        <img id="previewModalImg" src="" alt="" class="img-fluid rounded" style="max-height: 320px; object-fit: contain;">
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless fs-13 mb-0">
                                <tr>
                                    <th class="text-muted" style="width: 110px;">ফাইলের নাম:</th>
                                    <td class="fw-bold text-dark text-break" id="previewModalName"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">ফোল্ডার:</th>
                                    <td id="previewModalFolder"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">সাইজ:</th>
                                    <td class="fw-semibold text-primary" id="previewModalSize"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">রেজোলিউশন:</th>
                                    <td id="previewModalDimensions"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">ফরম্যাট:</th>
                                    <td id="previewModalExt"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">আপলোড সময়:</th>
                                    <td id="previewModalDate"></td>
                                </tr>
                            </table>
                        </div>

                        {{-- URL Copy Section --}}
                        <div class="mt-3">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">ইমেজের সরাসরি লিংক (URL)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="previewModalUrlInput" class="form-control" readonly>
                                <button class="btn btn-primary" type="button" onclick="copyModalUrl()">
                                    <i class="fas fa-copy me-1"></i> কপি করুন
                                </button>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4 pt-2 border-top">
                            <a id="previewModalOpenTab" href="" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill">
                                <i class="fas fa-external-link-alt me-1"></i> নতুন ট্যাবে খুলুন
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" id="previewModalDeleteBtn">
                                <i class="fas fa-trash-alt me-1"></i> ডিলিট করুন
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Single Delete Hidden Form --}}
<form id="singleDeleteForm" action="{{ route('admin.media.destroy') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="file_path" id="singleDeleteFilePath">
</form>

{{-- Bulk Delete Hidden Form --}}
<form id="bulkDeleteForm" action="{{ route('admin.media.bulkDestroy') }}" method="POST" style="display: none;">
    @csrf
    <div id="bulkDeleteInputs"></div>
</form>

<style>
    /* Responsive Fluid Grid: Ensures balanced cards on all screens */
    .media-responsive-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(195px, 1fr));
        gap: 16px;
    }

    @media (max-width: 576px) {
        .media-responsive-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
    }

    /* Pro Card Design */
    .media-pro-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
    }

    .media-pro-card:hover {
        transform: translateY(-3px);
        border-color: #cbd5e1;
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    }

    .media-grid-item.selected .media-pro-card,
    .media-list-row.selected {
        border-color: #4f46e5 !important;
        background-color: #f5f3ff !important;
        box-shadow: 0 0 0 2px #4f46e5 !important;
    }

    /* Floating Header inside Card */
    .media-card-top-bar {
        position: absolute;
        top: 8px;
        left: 8px;
        right: 8px;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: space-between;
        pointer-events: auto;
    }

    .media-ext-badge {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #ffffff;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(4px);
        padding: 2px 7px;
        border-radius: 6px;
    }

    /* Thumbnail Box with Subtle Checkerboard */
    .media-img-box {
        position: relative;
        height: 135px;
        width: 100%;
        background-color: #f8fafc;
        background-image: 
            linear-gradient(45deg, #f1f5f9 25%, transparent 25%), 
            linear-gradient(-45deg, #f1f5f9 25%, transparent 25%), 
            linear-gradient(45deg, transparent 75%, #f1f5f9 75%), 
            linear-gradient(-45deg, transparent 75%, #f1f5f9 75%);
        background-size: 16px 16px;
        background-position: 0 0, 0 8px, 8px -8px, -8px 0px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .media-img-elem {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.25s ease;
    }

    .media-pro-card:hover .media-img-elem {
        transform: scale(1.05);
    }

    .media-hover-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.35);
        backdrop-filter: blur(1.5px);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .media-img-box:hover .media-hover-overlay {
        opacity: 1;
    }

    .media-zoom-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #ffffff;
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    /* Info Box */
    .media-info-box {
        padding: 9px 11px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        justify-content: space-between;
    }

    .media-name-title {
        font-size: 12.5px;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 5px;
    }

    .media-specs-row {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 5px;
    }

    .media-pill-tag {
        font-size: 10.5px;
        padding: 1px 6px;
        border-radius: 4px;
        font-weight: 500;
    }

    .media-pill-tag.size {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
    }

    .media-pill-tag.dim {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .media-folder-row {
        margin-bottom: 8px;
    }

    .media-folder-tag {
        font-size: 11px;
        color: #475569;
        background: #fefce8;
        border: 1px solid #fef08a;
        padding: 2px 7px;
        border-radius: 5px;
        display: inline-block;
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Bottom Action Row */
    .media-action-row {
        display: flex;
        align-items: center;
        gap: 4px;
        padding-top: 7px;
        border-top: 1px solid #f1f5f9;
    }

    .btn-media-act {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .btn-media-act:hover {
        background: #f8fafc;
    }

    .btn-media-copy {
        flex-grow: 1;
        color: #2563eb;
        border-color: #dbeafe;
        background: #f8faff;
    }

    .btn-media-copy:hover {
        background: #2563eb;
        color: #ffffff;
    }

    .btn-media-preview {
        color: #0284c7;
    }

    .btn-media-preview:hover {
        background: #0284c7;
        color: #ffffff;
    }

    .btn-media-del {
        color: #ef4444;
    }

    .btn-media-del:hover {
        background: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
    }

    /* List View Thumbnail */
    .media-list-thumb {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3px;
        cursor: pointer;
        overflow: hidden;
    }

    .media-list-thumb img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>

<script>
    // View Switcher (Grid / List) with localStorage memory
    function setMediaView(mode) {
        var gridView = document.getElementById('mediaGridView');
        var listView = document.getElementById('mediaListView');
        var btnGrid = document.getElementById('btnViewGrid');
        var btnList = document.getElementById('btnViewList');

        if (mode === 'list') {
            if (gridView) gridView.style.display = 'none';
            if (listView) listView.style.display = 'block';
            btnList.className = 'btn btn-primary';
            btnGrid.className = 'btn btn-outline-secondary';
            localStorage.setItem('media_view_mode', 'list');
        } else {
            if (gridView) gridView.style.display = 'grid';
            if (listView) listView.style.display = 'none';
            btnGrid.className = 'btn btn-primary';
            btnList.className = 'btn btn-outline-secondary';
            localStorage.setItem('media_view_mode', 'grid');
        }
    }

    // Initialize View on load
    document.addEventListener('DOMContentLoaded', function() {
        var savedMode = localStorage.getItem('media_view_mode') || 'grid';
        setMediaView(savedMode);
    });

    // Copy URL helper
    function copyMediaUrl(url, btn) {
        navigator.clipboard.writeText(url).then(function() {
            var origHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check text-success me-1"></i> <span>কপি হয়েছে!</span>';
            btn.style.backgroundColor = '#ecfdf5';
            btn.style.borderColor = '#a7f3d0';
            btn.style.color = '#059669';
            setTimeout(function() {
                btn.innerHTML = origHtml;
                btn.removeAttribute('style');
            }, 1800);
        });
    }

    function copyModalUrl() {
        var input = document.getElementById('previewModalUrlInput');
        input.select();
        navigator.clipboard.writeText(input.value).then(function() {
            alert('ইমেজ লিংকটি ক্লিপবোর্ডে কপি করা হয়েছে!');
        });
    }

    // Preview Modal
    function openMediaPreview(media) {
        document.getElementById('previewModalTitle').innerText = media.name;
        document.getElementById('previewModalImg').src = media.url;
        document.getElementById('previewModalName').innerText = media.name;
        document.getElementById('previewModalFolder').innerText = media.folder + (media.subfolder ? '/' + media.subfolder : '');
        document.getElementById('previewModalSize').innerText = media.size_formatted;
        document.getElementById('previewModalDimensions').innerText = media.dimensions || 'N/A';
        document.getElementById('previewModalExt').innerText = media.extension;
        document.getElementById('previewModalDate').innerText = media.date_formatted;
        document.getElementById('previewModalUrlInput').value = media.url;
        document.getElementById('previewModalOpenTab').href = media.url;
        
        document.getElementById('previewModalDeleteBtn').onclick = function() {
            var modal = bootstrap.Modal.getInstance(document.getElementById('mediaPreviewModal'));
            if (modal) modal.hide();
            confirmSingleDelete(media.path, media.name);
        };

        var myModal = new bootstrap.Modal(document.getElementById('mediaPreviewModal'));
        myModal.show();
    }

    // Single Delete
    function confirmSingleDelete(path, name) {
        if (confirm('আপনি কি নিশ্চিত যে "' + name + '" ইমেজটি স্থায়ীভাবে ডিলিট করতে চান?')) {
            document.getElementById('singleDeleteFilePath').value = path;
            document.getElementById('singleDeleteForm').submit();
        }
    }

    // Checkbox & Bulk Actions
    function updateSelectionState() {
        var checkedBoxes = document.querySelectorAll('.media-checkbox:checked');
        var count = checkedBoxes.length;
        var bar = document.getElementById('bulkActionBar');
        var badge = document.getElementById('selectedCountBadge');
        var selectAll = document.getElementById('selectAllCheckbox');
        var listSelectAll = document.getElementById('listSelectAllCheckbox');

        if (count > 0) {
            bar.style.display = 'block';
            badge.innerText = count + ' টি নির্বাচিত';
        } else {
            bar.style.display = 'none';
        }

        // Highlight selected grid items
        document.querySelectorAll('.media-grid-item').forEach(function(item) {
            var cb = item.querySelector('.media-checkbox');
            if (cb && cb.checked) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
        });

        // Highlight selected list rows
        document.querySelectorAll('.media-list-row').forEach(function(row) {
            var cb = row.querySelector('.media-checkbox');
            if (cb && cb.checked) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }
        });

        var allBoxes = document.querySelectorAll('.media-checkbox');
        var isAll = allBoxes.length > 0 && checkedBoxes.length === allBoxes.length;
        if (selectAll) selectAll.checked = isAll;
        if (listSelectAll) listSelectAll.checked = isAll;
    }

    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        var checked = this.checked;
        document.querySelectorAll('.media-checkbox').forEach(function(cb) {
            cb.checked = checked;
        });
        updateSelectionState();
    });

    function toggleListSelectAll(elem) {
        var checked = elem.checked;
        document.querySelectorAll('.media-checkbox').forEach(function(cb) {
            cb.checked = checked;
        });
        updateSelectionState();
    }

    function clearMediaSelection() {
        document.querySelectorAll('.media-checkbox').forEach(function(cb) {
            cb.checked = false;
        });
        document.getElementById('selectAllCheckbox').checked = false;
        var listSelectAll = document.getElementById('listSelectAllCheckbox');
        if (listSelectAll) listSelectAll.checked = false;
        updateSelectionState();
    }

    function confirmBulkDelete() {
        var selected = document.querySelectorAll('.media-checkbox:checked');
        if (selected.length === 0) {
            alert('অনুগ্রহ করে অন্তত একটি ইমেজ নির্বাচন করুন।');
            return;
        }

        if (confirm('আপনি কি নিশ্চিত যে নির্বাচিত ' + selected.length + ' টি ইমেজ স্থায়ীভাবে সার্ভার থেকে ডিলিট করতে চান?')) {
            var container = document.getElementById('bulkDeleteInputs');
            container.innerHTML = '';
            selected.forEach(function(cb) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'files[]';
                input.value = cb.value;
                container.appendChild(input);
            });
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    // Preview of selected files before upload
    function handleFileSelect(input) {
        var previewList = document.getElementById('filePreviewList');
        previewList.innerHTML = '';
        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach(function(file) {
                var col = document.createElement('div');
                col.className = 'col-3 text-center';
                var reader = new FileReader();
                reader.onload = function(e) {
                    col.innerHTML = '<div class="p-1 border rounded bg-white shadow-sm">' +
                        '<img src="' + e.target.result + '" style="height: 60px; width: 100%; object-fit: contain;">' +
                        '<div class="fs-10 text-truncate mt-1 text-muted">' + file.name + '</div>' +
                        '</div>';
                };
                reader.readAsDataURL(file);
                previewList.appendChild(col);
            });
        }
    }
</script>
@endsection
