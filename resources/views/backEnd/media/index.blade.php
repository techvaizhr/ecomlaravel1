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
                    <span class="badge bg-soft-info text-info border border-info border-opacity-25 px-3 py-2 rounded-pill fs-12 fw-semibold">
                        <i class="fas fa-images me-1"></i> মোট {{ number_format($stats['total_files']) }} টি ফাইল ({{ $stats['total_size'] }})
                    </span>
                    <button type="button" class="btn btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#uploadMediaModal">
                        <i data-feather="upload-cloud" style="width: 16px; height: 16px;"></i> নতুন ইমেজ আপলোড
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter, Search & Per-Page Controls --}}
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 14px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.media.index') }}" method="GET" id="mediaFilterForm" class="row g-2 align-items-center">
                
                {{-- Folder Filter --}}
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
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
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
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
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label fs-12 fw-semibold text-muted mb-1">সাজানোর ক্রম (Sort)</label>
                    <select name="sort" class="form-select form-select-sm" onchange="document.getElementById('mediaFilterForm').submit()">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>সর্বশেষ আপলোড (Newest First)</option>
                        <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>পুরাতন প্রথমে (Oldest First)</option>
                        <option value="largest" {{ $sort === 'largest' ? 'selected' : '' }}>সর্বোচ্চ সাইজ (Largest Size)</option>
                        <option value="smallest" {{ $sort === 'smallest' ? 'selected' : '' }}>ক্ষুদ্রতম সাইজ (Smallest Size)</option>
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>নামানুসারে (A - Z)</option>
                    </select>
                </div>

                {{-- Per Page Select --}}
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label fs-12 fw-semibold text-muted mb-1">প্রতি পেজে প্রদর্শন (Per Page)</label>
                    <div class="d-flex align-items-center gap-2">
                        <select name="per_page" class="form-select form-select-sm" onchange="document.getElementById('mediaFilterForm').submit()">
                            <option value="12" {{ $perPage == 12 ? 'selected' : '' }}>১২ টি করে</option>
                            <option value="24" {{ $perPage == 24 ? 'selected' : '' }}>২৪ টি করে</option>
                            <option value="48" {{ $perPage == 48 ? 'selected' : '' }}>৪৮ টি করে</option>
                            <option value="96" {{ $perPage == 96 ? 'selected' : '' }}>৯৬ টি করে</option>
                            <option value="150" {{ $perPage == 150 ? 'selected' : '' }}>১৫০ টি করে</option>
                        </select>
                        <a href="{{ route('admin.media.index') }}" class="btn btn-light btn-sm text-secondary border" title="রিসেট">
                            <i class="fas fa-redo-alt"></i>
                        </a>
                    </div>
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

    {{-- Media Grid --}}
    @if($paginatedMedia->count() > 0)
        <div class="row g-3" id="mediaGrid">
            @foreach($paginatedMedia as $media)
                <div class="col-xxl-2 col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6 media-item-col" data-path="{{ $media['path'] }}">
                    <div class="card h-100 border-0 shadow-sm media-card position-relative overflow-hidden" style="border-radius: 12px; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        
                        {{-- Top Badges & Selection --}}
                        <div class="position-absolute top-0 start-0 p-2 z-index-2 d-flex align-items-center gap-1">
                            <input type="checkbox" class="form-check-input media-checkbox shadow-sm" 
                                   value="{{ $media['path'] }}" 
                                   onchange="updateSelectionState()" 
                                   style="cursor: pointer; width: 18px; height: 18px;">
                        </div>

                        <div class="position-absolute top-0 end-0 p-2 z-index-2">
                            <span class="badge bg-dark bg-opacity-75 text-white fs-10 px-2 py-1 rounded-pill text-uppercase">
                                {{ $media['extension'] }}
                            </span>
                        </div>

                        {{-- Image Thumbnail Container --}}
                        <div class="media-thumb-wrapper bg-light d-flex align-items-center justify-content-center position-relative" style="height: 155px; cursor: pointer;" onclick="openMediaPreview({{ json_encode($media) }})">
                            <img src="{{ $media['url'] }}" 
                                 alt="{{ $media['name'] }}" 
                                 loading="lazy" 
                                 class="img-fluid media-thumb-img" 
                                 style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            <div class="media-overlay d-flex align-items-center justify-content-center">
                                <i class="fas fa-search-plus text-white fs-18"></i>
                            </div>
                        </div>

                        {{-- Card Details --}}
                        <div class="card-body p-2 d-flex flex-column justify-content-between" style="font-size: 12px;">
                            <div>
                                <div class="text-truncate fw-semibold text-dark mb-1" title="{{ $media['name'] }}">
                                    {{ $media['name'] }}
                                </div>
                                <div class="d-flex justify-content-between text-muted fs-11 mb-1">
                                    <span>{{ $media['size_formatted'] }}</span>
                                    <span>{{ $media['dimensions'] ?: $media['extension'] }}</span>
                                </div>
                                <div class="badge bg-light text-secondary text-truncate d-block text-start border fs-10 py-1" title="📁 {{ $media['folder'] }}{{ $media['subfolder'] ? '/' . $media['subfolder'] : '' }}">
                                    📁 {{ $media['folder'] }}{{ $media['subfolder'] ? '/' . $media['subfolder'] : '' }}
                                </div>
                            </div>

                            {{-- Actions Toolbar --}}
                            <div class="d-flex align-items-center justify-content-between pt-2 mt-2 border-top">
                                <button type="button" class="btn btn-sm btn-light border p-1 px-2 text-primary copy-btn" 
                                        onclick="copyMediaUrl('{{ $media['url'] }}', this)" 
                                        title="লিংক কপি করুন">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light border p-1 px-2 text-info" 
                                        onclick="openMediaPreview({{ json_encode($media) }})" 
                                        title="বিস্তারিত ও প্রিভিউ">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light border p-1 px-2 text-danger" 
                                        onclick="confirmSingleDelete('{{ $media['path'] }}', '{{ $media['name'] }}')" 
                                        title="মুছে ফেলুন">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4 pb-4">
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
                    <i class="far fa-images text-muted" style="font-size: 54px; opacity: 0.5;"></i>
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
    .media-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .media-thumb-wrapper .media-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.35);
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .media-thumb-wrapper:hover .media-overlay {
        opacity: 1;
    }
    .media-card.selected {
        border: 2px solid #4f46e5 !important;
        background: #f5f3ff;
    }
</style>

<script>
    // Copy URL helper
    function copyMediaUrl(url, btn) {
        navigator.clipboard.writeText(url).then(function() {
            var origHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check text-success"></i>';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-light');
            setTimeout(function() {
                btn.innerHTML = origHtml;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-light');
            }, 1800);
        });
    }

    function copyModalUrl() {
        var input = document.getElementById('previewModalUrlInput');
        input.select();
        navigator.clipboard.writeText(input.value).then(function() {
            alert('ইমেজ লিংকটি সফলভাবে ক্লিপবোর্ডে কপি করা হয়েছে!');
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
        var checkboxes = document.querySelectorAll('.media-checkbox:checked');
        var count = checkboxes.length;
        var bar = document.getElementById('bulkActionBar');
        var badge = document.getElementById('selectedCountBadge');
        var selectAll = document.getElementById('selectAllCheckbox');

        if (count > 0) {
            bar.style.display = 'block';
            badge.innerText = count + ' টি নির্বাচিত';
        } else {
            bar.style.display = 'none';
        }

        // Highlight selected cards
        document.querySelectorAll('.media-item-col').forEach(function(col) {
            var cb = col.querySelector('.media-checkbox');
            var card = col.querySelector('.media-card');
            if (cb && cb.checked) {
                card.classList.add('selected');
            } else if (card) {
                card.classList.remove('selected');
            }
        });

        var allCheckboxes = document.querySelectorAll('.media-checkbox');
        selectAll.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
    }

    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        var checked = this.checked;
        document.querySelectorAll('.media-checkbox').forEach(function(cb) {
            cb.checked = checked;
        });
        updateSelectionState();
    });

    function clearMediaSelection() {
        document.querySelectorAll('.media-checkbox').forEach(function(cb) {
            cb.checked = false;
        });
        document.getElementById('selectAllCheckbox').checked = false;
        updateSelectionState();
    }

    function confirmBulkDelete() {
        var selected = document.querySelectorAll('.media-checkbox:checked');
        if (selected.length === 0) {
            alert('অনুগ্রহ করে অন্তত একটি ইমেজ নির্বাচন করুন।');
            return;
        }

        if (confirm('আপনি কি নিশ্চিত যে নির্বাচিত ' + selected.length + ' টি ইমেজ স্থায়ীভাবে ডিলিট করতে চান?')) {
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
