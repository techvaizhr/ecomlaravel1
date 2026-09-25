<div class="card pf-sidebar-panel mb-4">
    <div class="card-body">
        <div class="pf-side-block">
            <div class="pf-side-head"><i class="fe-dollar-sign"></i> Pricing & Inventory</div>
            <div class="row g-compact">
                <div class="col-6">
                    <label for="purchase_price" class="form-label">Purchase <small class="text-muted">(Opt.)</small></label>
                    <input type="text" class="form-control border-primary @error('purchase_price') is-invalid @enderror"
                           name="purchase_price" value="{{ $edit_data->purchase_price}}" id="purchase_price" placeholder="0" />
                    @error('purchase_price')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="col-6">
                    <label for="old_price" class="form-label">Old Price</label>
                    <input type="text" class="form-control @error('old_price') is-invalid @enderror"
                           name="old_price" value="{{ $edit_data->old_price }}" id="old_price" />
                    @error('old_price')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="col-6">
                    <label for="new_price" class="form-label">New <small class="text-muted">(Opt.)</small></label>
                    <input type="text" class="form-control font-weight-bold @error('new_price') is-invalid @enderror"
                           name="new_price" value="{{ $edit_data->new_price }}" id="new_price" placeholder="0" />
                    @error('new_price')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="col-6">
                    <label for="reseller_price" class="form-label">Reseller</label>
                    <input type="text" step="0.01" class="form-control @error('reseller_price') is-invalid @enderror"
                           name="reseller_price" value="{{ old('reseller_price', $edit_data->reseller_price) }}" id="reseller_price" placeholder="Optional" />
                    @error('reseller_price')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="col-6">
                    <label for="stock" class="form-label">Stock <small class="text-muted">(Opt.)</small></label>
                    <input type="text" class="form-control @error('stock') is-invalid @enderror"
                           name="stock" value="{{ $edit_data->stock }}" id="stock" placeholder="0" />
                    @error('stock')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="col-6">
                    <label for="pro_unit" class="form-label">Unit</label>
                    <input type="text" class="form-control @error('pro_unit') is-invalid @enderror"
                           name="pro_unit" value="{{ $edit_data->pro_unit }}" id="pro_unit" />
                </div>
                <div class="col-12">
                    <label for="brand_id" class="form-label">Brand</label>
                    <select class="form-control form-control-sm select2 @error('brand_id') is-invalid @enderror" name="brand_id">
                        <option value="">Select..</option>
                        @foreach($brands as $value)
                            <option value="{{$value->id}}" @if($edit_data->brand_id==$value->id) selected @endif>{{$value->name}}</option>
                        @endforeach
                    </select>
                    @error('brand_id')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
            </div>
        </div>

        <div class="pf-side-block">
            <div class="pf-side-head">
                <div class="pf-side-head-left">
                    <i class="fe-image"></i>
                    <span>Product Gallery</span>
                </div>
            </div>

            {{-- 1. Existing Gallery Images with Drag & Drop & Set Main --}}
            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">বর্তমান গ্যালারি ছবি</label>
            <div id="existing_images_grid" class="d-flex flex-wrap gap-2 p-2 bg-light rounded border mb-3" style="min-height:80px;">
                @php 
                    $mainGalleryImgs = $edit_data->images->filter(fn($img) => !$img->color_id && !$img->size_id)->values(); 
                @endphp
                @forelse($mainGalleryImgs as $idx => $image)
                    <div class="position-relative border rounded p-1 bg-white existing-img-card" draggable="true" data-id="{{ $image->id }}" style="width: 80px; height: 80px; cursor: grab; user-select: none; transition: transform 0.15s, box-shadow 0.15s; border-radius: 8px;">
                        <img src="{{ asset($image->image) }}" alt="Preview" style="width:100%; height:100%; object-fit:cover; border-radius:5px; pointer-events: none;" loading="lazy">
                        <input type="hidden" name="existing_image_order[]" value="{{ $image->id }}">
                        
                        <span class="position-absolute bg-dark bg-opacity-75 text-white rounded-circle d-flex align-items-center justify-content-center" style="top:3px; left:3px; width:17px; height:17px; font-size:9.5px; cursor:grab;" title="Drag to reorder"><i class="fe-move"></i></span>
                        
                        <a href="{{ route('products.image.destroy',['id'=>$image->id]) }}" class="btn btn-xs btn-danger position-absolute" style="top:-5px; right:-5px; border-radius:50%; width:18px; height:18px; padding:0; display:flex; align-items:center; justify-content:center; z-index:3;" title="Delete image" onclick="return confirm('Delete this image?')">
                            <i class="mdi mdi-close" style="font-size:10px;"></i>
                        </a>

                        <div class="existing-card-footer position-absolute" style="bottom:3px; left:3px; right:3px; z-index:3;">
                            @if($idx === 0)
                                <span class="badge bg-primary w-100 py-0.5" style="font-size:8.5px; border-radius:3px;"><i class="fe-star me-0.5"></i> Main</span>
                            @else
                                <button type="button" class="btn btn-xs btn-light w-100 py-0 border shadow-sm btn-set-existing-main" style="font-size:8px; font-weight:600; color:#1e293b; border-radius:3px;" title="প্রধান ছবি নির্ধারণ করুন"><i class="fe-star text-warning me-0.5"></i> Set Main</button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0 p-2">কোনো ছবি নেই। নিচে থেকে নতুন ছবি যোগ করুন।</p>
                @endforelse
            </div>

            {{-- 2. Add New Gallery Images with Drag & Drop & Previews --}}
            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">নতুন ছবি যোগ করুন</label>
            <div class="gallery-upload-zone mb-2">
                <input type="file" id="local_gallery_file_input_edit" multiple accept="image/*" class="d-none">
                <button type="button" class="btn btn-outline-primary w-100 py-2.5 rounded-3 d-flex align-items-center justify-content-center gap-2 border-2" id="btn_pick_gallery_files_edit" style="border-style:dashed;">
                    <i class="fe-image fs-5"></i>
                    <span class="fw-semibold">নতুন ছবি নির্বাচন করুন (একাধিক)</span>
                </button>
            </div>

            <div id="new_images_preview_area_edit" class="mb-2 d-none">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="text-muted small fw-semibold" style="font-size:11px;">নতুন বাছাইকৃত ছবি (<span id="new_images_count_edit">0</span>) - টেনে সাজান</span>
                    <button type="button" class="btn btn-xs btn-link text-danger p-0 text-decoration-none" id="btn_clear_new_images_edit" style="font-size:11px;">
                        <i class="fe-x"></i> সব মুছুন
                    </button>
                </div>
                <div id="new_images_grid_edit" class="d-flex flex-wrap gap-2 p-2 bg-light rounded border" style="min-height:90px;"></div>
            </div>

            {{-- Hidden Actual File Input Synchronized by JavaScript --}}
            <input type="file" name="image[]" id="final_gallery_file_input_edit" multiple class="d-none">

            <small class="text-muted d-block mt-1" style="font-size:11px;">
                <i class="fa fa-info-circle text-primary me-1"></i> ১ম ছবিটি স্বয়ংক্রিয়ভাবে <strong>Main Image</strong> হিসেবে থাকবে। মাউস দিয়ে ড্র্যাগ করে অথবা "⭐ Set Main" বাটনে ক্লিক করে পছন্দমতো প্রধান ছবি নির্ধারণ করুন।
            </small>
            @php $colorSizeImages = $edit_data->images->filter(fn($img) => $img->color_id || $img->size_id); @endphp
            @if($colorSizeImages->isNotEmpty())
            <div class="mt-1">
                <label class="form-label small text-muted mb-0">Color/Size imgs</label>
                <div class="d-flex flex-wrap gap-1 product_img">
                    @foreach($colorSizeImages as $img)
                        <div class="position-relative">
                            <img src="{{asset($img->image)}}" class="edit-image border" alt="">
                            <a href="{{route('products.image.destroy',['id'=>$img->id])}}" class="btn btn-xs btn-danger position-absolute top-0 end-0 rounded-circle" style="padding:0 4px;top:-5px;right:-5px;" onclick="return confirm('Delete?')"><i class="mdi mdi-close"></i></a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @php $existingVideoType = $edit_data->pro_video_type ?? ($edit_data->pro_video ? 'youtube' : null); @endphp
            <div class="pf-side-head mt-2">
                <div class="pf-side-head-left">
                    <i class="fe-video"></i>
                    <span>Product Video</span>
                </div>
                <span class="pf-video-size-badge" id="video_limit_badge_e">
                    <i class="fa fa-shield"></i> Max 5 MB
                </span>
            </div>

            <div class="pf-video-card">
                <div class="pf-video-source-pill">
                    <input type="radio" name="pro_video_source" id="vs_yt_e" value="youtube" {{ $existingVideoType !== 'upload' ? 'checked' : '' }}>
                    <label for="vs_yt_e"><i class="fa fa-youtube-play text-danger"></i> YouTube</label>

                    <input type="radio" name="pro_video_source" id="vs_up_e" value="upload" {{ $existingVideoType === 'upload' ? 'checked' : '' }}>
                    <label for="vs_up_e"><i class="fa fa-cloud-upload text-primary"></i> Upload</label>
                </div>

                <div id="yt_section_e" style="{{ $existingVideoType === 'upload' ? 'display:none;' : '' }}">
                    <input type="text" name="pro_video" id="pro_video_e" class="form-control form-control-sm @error('pro_video') is-invalid @enderror"
                           value="{{ $edit_data->pro_video }}" placeholder="YouTube URL / Video ID">
                    @error('pro_video')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                    @if($edit_data->pro_video)
                    <div id="yt_preview_e" class="mt-2">
                        <iframe id="yt_iframe_e" width="100%" height="130" src="https://www.youtube.com/embed/{{ $edit_data->pro_video }}" frameborder="0" allowfullscreen style="border-radius:10px;"></iframe>
                    </div>
                    @else
                    <div id="yt_preview_e" class="mt-2" style="display:none;">
                        <iframe id="yt_iframe_e" width="100%" height="130" src="" frameborder="0" allowfullscreen style="border-radius:10px;"></iframe>
                    </div>
                    @endif
                </div>

                <div id="up_section_e" style="{{ $existingVideoType === 'upload' ? '' : 'display:none;' }}">
                    @if($existingVideoType === 'upload' && $edit_data->pro_video_path)
                    <div class="mb-2 p-2 bg-light rounded d-flex align-items-center gap-2" style="font-size:12px;">
                        <i class="fa fa-film text-primary"></i>
                        <span class="text-truncate fw-bold">{{ basename($edit_data->pro_video_path) }}</span>
                        <a href="{{ asset($edit_data->pro_video_path) }}" target="_blank" class="btn btn-xs btn-outline-primary ms-auto py-0 px-2"><i class="fa fa-play"></i></a>
                    </div>
                    @endif
                    <input type="file" name="pro_video_file" id="pro_video_file_e" class="form-control form-control-sm" accept="video/mp4,video/webm,video/ogg">
                    
                    <div id="up_preview_e" class="mt-2" style="display:none;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge bg-soft-success text-success" id="video_file_size_text_e" style="font-size:11px;">0 MB / 5 MB</span>
                            <button type="button" class="btn btn-xs btn-link text-danger p-0 text-decoration-none" id="clear_video_btn_e">
                                <i class="fe-trash-2"></i> রিমুভ
                            </button>
                        </div>
                        <video id="up_video_e" width="100%" height="130" controls style="border-radius:10px;background:#000;"></video>
                    </div>

                    <div id="video_size_error_e" class="video-error-alert">
                        <i class="fa fa-exclamation-triangle me-1"></i>
                        <span id="video_error_msg_e">ভিডিও সাইজ ৫ MB এর বেশি হতে পারবে না!</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <small class="text-muted" style="font-size:11px;">MP4, WebM, OGG</small>
                        <small class="text-danger fw-bold" style="font-size:11px;">সর্বোচ্চ ৫ MB</small>
                    </div>
                </div>
            </div>
        </div>

        @php
            $currentType = old('product_type', $edit_data->is_digital ? 'digital' : 'physical');
            $isDigital   = $currentType === 'digital';
        @endphp
        <div class="pf-side-block">
            <div class="pf-side-head"><i class="fe-settings"></i> Settings</div>
            <div class="row g-compact">
                <div class="col-6">
                    <label for="product_type" class="form-label">Type</label>
                    <select class="form-control form-control-sm bg-light" id="product_type" name="product_type">
                        <option value="physical" {{ $currentType === 'physical' ? 'selected' : '' }}>Physical</option>
                        <option value="digital" {{ $currentType === 'digital' ? 'selected' : '' }}>Digital</option>
                    </select>
                </div>
                <div id="advance_area" class="col-6" style="{{ $isDigital ? 'display:none;' : '' }}">
                    <label for="advance_amount" class="form-label">Advance</label>
                    <input type="text" class="form-control form-control-sm @error('advance_amount') is-invalid @enderror"
                           name="advance_amount" id="advance_amount" value="{{ old('advance_amount', $edit_data->advance_amount) }}" />
                </div>
                <div class="col-6">
                    <label for="sold" class="form-label">Sold</label>
                    <input type="text" class="form-control form-control-sm @error('sold') is-invalid @enderror"
                           name="sold" value="{{ $edit_data->sold }}" id="sold" />
                </div>
            </div>

            {{-- Product Weight & Delivery Rules Section --}}
            <div class="p-3 border rounded-3 mb-3 bg-light">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-1">
                        <i class="fe-truck text-primary"></i>
                        <strong style="font-size:12px;">ডেলিভারি ও ওজন সেটিংস</strong>
                    </div>
                    <a href="{{ route('admin.delivery.settings') }}" target="_blank" class="text-primary text-decoration-none" style="font-size:11px;" title="ডেলিভারি চার্জ সেটিংস ম্যানেজ করুন">
                        <i class="fe-external-link"></i> সেটিংস
                    </a>
                </div>

                <div class="row g-2 mb-1">
                    <div class="col-5">
                        <label class="form-label mb-1" style="font-size:11px;">ওজন (KG)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" min="0" name="weight" class="form-control" placeholder="0.5" value="{{ old('weight', $edit_data->weight ?? '0.00') }}">
                            <span class="input-group-text">KG</span>
                        </div>
                    </div>
                    <div class="col-7">
                        <label class="form-label mb-1" style="font-size:11px;">ডেলিভারি চার্জ নিয়ম</label>
                        @php
                            $savedType = old('delivery_charge_type');
                            if (!$savedType) {
                                if ($edit_data->delivery_charge_type === 'custom' && $edit_data->custom_delivery_charge_id) {
                                    $savedType = 'custom:' . $edit_data->custom_delivery_charge_id;
                                } elseif ($edit_data->delivery_charge_type) {
                                    $savedType = $edit_data->delivery_charge_type;
                                } elseif ($edit_data->free_delivery) {
                                    $savedType = 'free';
                                } else {
                                    $savedType = 'global';
                                }
                            }
                        @endphp
                        <select class="form-select form-select-sm fw-semibold" name="delivery_charge_type">
                            <option value="global" {{ $savedType == 'global' ? 'selected' : '' }}>🌐 সিস্টেম ডিফল্ট (Global)</option>
                            <option value="free" {{ $savedType == 'free' ? 'selected' : '' }}>🎁 ফ্রি ডেলিভারি (Free - ৳0)</option>
                            <option value="weight_based" {{ $savedType == 'weight_based' ? 'selected' : '' }}>⚖️ ওজন ভিত্তিক (Weight Based)</option>
                            @if(isset($customCharges) && $customCharges->count() > 0)
                                <optgroup label="🏷️ কাস্টম ডেলিভারি চার্জ">
                                    @foreach($customCharges as $cc)
                                        <option value="custom:{{ $cc->id }}" {{ $savedType == 'custom:'.$cc->id ? 'selected' : '' }}>
                                            🏷️ {{ $cc->name }} (৳{{ number_format($cc->amount, 0) }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </div>
                </div>
                <small class="text-muted" style="font-size:10.5px; display:block; line-height:1.3; margin-top:4px;">
                    * 'সিস্টেম ডিফল্ট' দিলে গ্লোবাল সেটিংস (এরিয়া/ফ্ল্যাট) অনুযায়ী চার্জ প্রযোজ্য হবে।
                </small>
            </div>
            <div id="digital_area" style="{{ $isDigital ? '' : 'display:none;' }}" class="p-2 border rounded mb-2 bg-light">
                @if($edit_data->digital_file)
                    <small class="d-block text-truncate mb-1">File: <code>{{ $edit_data->digital_file }}</code></small>
                @endif
                <input type="file" class="form-control form-control-sm mb-1" name="digital_file" id="digital_file">
                <div class="row g-1">
                    <div class="col-6">
                        <label class="form-label"><small>Limit</small></label>
                        <input type="number" class="form-control form-control-sm" name="download_limit" id="download_limit"
                               value="{{ old('download_limit', $edit_data->download_limit ?? 5) }}" min="1">
                    </div>
                    <div class="col-6">
                        <label class="form-label"><small>Days</small></label>
                        <input type="number" class="form-control form-control-sm" name="download_expire_days" id="download_expire_days"
                               value="{{ old('download_expire_days', $edit_data->download_expire_days ?? 7) }}" min="1">
                    </div>
                </div>
            </div>
            <div class="row text-center g-compact">
                <div class="col-4">
                    <label class="d-block form-label">Status</label>
                    <label class="switch"><input type="checkbox" value="1" name="status" @if($edit_data->status==1) checked @endif><span class="slider round"></span></label>
                </div>
                <div class="col-4">
                    <label class="d-block form-label">Hot</label>
                    <label class="switch"><input type="checkbox" value="1" name="topsale" @if($edit_data->topsale==1) checked @endif><span class="slider round"></span></label>
                </div>
                <div class="col-4">
                    <label class="d-block form-label">Flash</label>
                    <label class="switch"><input type="checkbox" value="1" name="flashsale" @if($edit_data->flashsale==1) checked @endif><span class="slider round"></span></label>
                </div>
            </div>
        </div>

        <div class="pf-side-foot">
            <button type="submit" class="btn btn-success w-100 shadow rounded-pill"><i class="fe-check-circle me-1"></i> Update Product</button>
        </div>
    </div>
</div>
