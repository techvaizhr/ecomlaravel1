<div class="mb-3">
    <label class="form-label">API Base URL <small class="text-muted fw-normal">(ঐচ্ছিক)</small></label>
    <input type="text" class="form-control" name="url"
           value="{{ $steadfast->url ?? 'https://portal.packzy.com/api/v1' }}"
           placeholder="https://portal.packzy.com/api/v1" autocomplete="off" />
    <small class="text-muted small-hint d-block mt-1">ডিফল্ট: portal.packzy.com/api/v1</small>
</div>

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <label class="form-label mb-0">Webhook URL <small class="text-muted fw-normal">(Steadfast ড্যাশবোর্ডে বসান)</small></label>
        <a href="https://steadfast.com.bd/user/webhook/add" target="_blank" class="badge bg-light text-primary border" style="text-decoration: none; font-size: 0.72rem;">
            <i class="fe-external-link"></i> Steadfast Webhook Setup
        </a>
    </div>
    @php
        $steadfastWebhookVal = $steadfast->webhook_url ?: (rtrim(config('app.url'), '/') . '/api/steadfast/webhook');
    @endphp
    <div class="input-group">
        <input type="text" class="form-control" name="webhook_url" id="steadfast_webhook_url"
               value="{{ $steadfastWebhookVal }}"
               placeholder="{{ rtrim(config('app.url'), '/') }}/api/steadfast/webhook"
               autocomplete="off" />
        <button type="button" class="btn btn-outline-secondary copy-btn" data-clipboard-target="#steadfast_webhook_url" title="কপি করুন">
            <i class="fe-copy"></i>
        </button>
    </div>
    <small class="text-muted small-hint d-block mt-1">
        Steadfast পোর্টালে <strong>Delivery Status</strong> ও <strong>Tracking Update</strong> ইভেন্টে এই URL টি যুক্ত করুন।
    </small>
</div>

<div class="mb-3">
    <label class="form-label">Webhook Bearer Token <small class="text-muted fw-normal">(ঐচ্ছিক সিক্রেট টোকেন)</small></label>
    <div class="input-group">
        <input type="text" class="form-control @error('token') is-invalid @enderror"
               name="token" id="steadfast_webhook_token"
               value="{{ $steadfast->token ?? '' }}"
               placeholder="Authorization: Bearer {token}"
               autocomplete="off" />
        <button type="button" class="btn btn-outline-primary" id="generate_steadfast_webhook_token" title="নতুন সিক্রেট টোকেন">
            <i class="fe-refresh-cw"></i>
        </button>
    </div>
    <small class="text-muted small-hint d-block mt-1">
        Steadfast-এ <strong>Authorization: Bearer {token}</strong> হেডারে বা Secret Key ফিল্ডে একই মান দিন।
    </small>
    @error('token')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
