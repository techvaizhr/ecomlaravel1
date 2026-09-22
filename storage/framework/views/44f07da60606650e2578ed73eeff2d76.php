
<?php $__env->startSection('title', 'Gemini AI Settings'); ?>

<?php $__env->startSection('css'); ?>
<style>
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(18, 38, 63, 0.03);
        border-radius: 12px;
        overflow: hidden;
    }
    .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f7;
        padding: 20px 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #2d3436;
        margin: 0;
    }
    .header-icon {
        width: 35px;
        height: 35px;
        background: rgba(99, 102, 241, 0.1);
        color: #6366f1;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #636e72;
        margin-bottom: 6px;
    }
    .form-control {
        background-color: #fbfcff;
        border: 1px solid #eef2f7;
        padding: 11px 14px;
        border-radius: 8px;
        font-size: 14px;
    }
    .form-control:focus {
        background-color: #fff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    .small-help {
        font-size: 12px;
        color: #95a5a6;
    }
    .btn-submit {
        background: linear-gradient(45deg, #6366f1, #8b5cf6);
        border: none;
        color: white;
        padding: 10px 24px;
        font-weight: 600;
        letter-spacing: .4px;
        border-radius: 40px;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
    }
    .btn-submit:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(99, 102, 241, 0.45);
    }
    .btn-test {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        padding: 10px 20px;
        font-weight: 600;
        border-radius: 40px;
    }
    .api-key-wrap {
        position: relative;
    }
    .api-key-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #64748b;
        cursor: pointer;
    }
    #test-result {
        display: none;
        margin-top: 16px;
        padding: 14px;
        border-radius: 10px;
        font-size: 13px;
        white-space: pre-wrap;
    }
    #test-result.success {
        display: block;
        background: #ecfdf5;
        border: 1px solid #86efac;
        color: #166534;
    }
    #test-result.error {
        display: block;
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-3 mt-3">
        <div class="col-12">
            <h4 class="page-title mb-0" style="font-weight: 700; color: #2d3436;">
                Gemini AI Settings
            </h4>
            <p class="text-muted font-size-13 mb-0">
                Google Gemini API key এবং model এখান থেকে সেট করুন। সেভ করলে সাইটে সাথে সাথে কাজ করবে।
            </p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fe-cpu"></i>
                    </div>
                    <h5 class="card-title mb-0">API Configuration</h5>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <strong>সেভ ব্যর্থ:</strong>
                        <ul class="mb-0 ps-3">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if(session('gemini_saved') || $setting->hasApiKey()): ?>
                    <div class="alert alert-success d-flex align-items-center gap-2 py-2">
                        <i class="fe-check-circle"></i>
                        <span>
                            <?php if($setting->hasApiKey()): ?>
                                API Key সেভ আছে
                                <?php if($setting->maskedApiKey()): ?>
                                    <code class="ms-1"><?php echo e($setting->maskedApiKey()); ?></code>
                                <?php endif; ?>
                            <?php else: ?>
                                সেটিংস আপডেট হয়েছে
                            <?php endif; ?>
                            <?php if(!$setting->status): ?>
                                <span class="text-warning ms-1">(তবে Gemini AI বন্ধ আছে — Active চালু করুন)</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('admin.gemini_ai.update')); ?>" method="POST" id="gemini-settings-form">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label class="form-label" for="api_key">Gemini API Key</label>
                            <div class="api-key-wrap">
                                <input
                                    type="password"
                                    class="form-control <?php $__errorArgs = ['api_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="api_key"
                                    name="api_key"
                                    value="<?php echo e(old('api_key')); ?>"
                                    placeholder="<?php echo e($setting->hasApiKey() ? 'Current: '.$setting->maskedApiKey().' — new key দিতে টাইপ করুন' : 'Paste your Gemini API key'); ?>"
                                    autocomplete="new-password"
                                >
                                <button type="button" class="api-key-toggle" id="toggle-api-key" aria-label="Show API key">
                                    <i class="fe-eye"></i>
                                </button>
                            </div>
                            <small class="small-help">
                                <a href="https://aistudio.google.com/apikey" target="_blank" rel="noopener">Google AI Studio</a> থেকে API key নিন।
                                <strong>নতুন key টাইপ করে অবশ্যই "Save Settings" চাপুন।</strong> খালি রাখলে আগের key থাকবে।
                            </small>
                            <?php $__errorArgs = ['api_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="model">Model</label>
                            <input
                                type="text"
                                class="form-control <?php $__errorArgs = ['model'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="model"
                                name="model"
                                value="<?php echo e(old('model', $setting->model ?? 'gemini-2.5-flash')); ?>"
                                placeholder="gemini-2.5-flash"
                            >
                            <small class="small-help">প্রস্তাবিত: <code>gemini-2.5-flash</code> বা <code>gemini-2.5-flash-lite</code>। পুরোনো <code>gemini-2.0-flash</code> এ quota error হতে পারে।</small>
                            <?php $__errorArgs = ['model'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="timeout">Request Timeout (seconds)</label>
                            <input
                                type="number"
                                class="form-control <?php $__errorArgs = ['timeout'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="timeout"
                                name="timeout"
                                min="10"
                                max="300"
                                value="<?php echo e(old('timeout', $setting->timeout ?? 60)); ?>"
                            >
                            <?php $__errorArgs = ['timeout'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-4 form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="status"
                                name="status"
                                value="1"
                                <?php echo e(old('status', $setting->status ?? 1) ? 'checked' : ''); ?>

                            >
                            <label class="form-check-label" for="status">
                                Gemini AI Active রাখুন
                            </label>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">🛒 Customer Live Chat (Frontend)</h6>
                        <p class="small-help mb-3">ফ্রন্টএন্ডে tawk.to স্টাইল Gemini চ্যাট — প্রোডাক্ট সার্চ, অর্ডার ট্র্যাক, কমপ্লেইন, রিফান্ড গাইড। এডমিন ডাটা দেখাবে না।</p>

                        <div class="mb-3 form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="customer_chat_enabled"
                                name="customer_chat_enabled"
                                value="1"
                                <?php echo e(old('customer_chat_enabled', $setting->customer_chat_enabled ?? true) ? 'checked' : ''); ?>

                            >
                            <label class="form-check-label" for="customer_chat_enabled">
                                Customer Live Chat চালু করুন
                            </label>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="customer_chat_welcome">Welcome Message (ঐচ্ছিক)</label>
                            <textarea
                                class="form-control"
                                id="customer_chat_welcome"
                                name="customer_chat_welcome"
                                rows="3"
                                placeholder="কাস্টমার চ্যাট খুললে প্রথম মেসেজ..."
                            ><?php echo e(old('customer_chat_welcome', $setting->customer_chat_welcome ?? '')); ?></textarea>
                        </div>

                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                            <button type="button" class="btn btn-test" id="test-gemini-btn">
                                <i class="fe-zap me-1"></i> Test Connection
                            </button>
                            <button type="submit" class="btn btn-submit">
                                <i class="fe-save me-1"></i> Save Settings
                            </button>
                        </div>
                    </form>

                    <div id="test-result"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
document.getElementById('toggle-api-key')?.addEventListener('click', function () {
    var input = document.getElementById('api_key');
    var icon = this.querySelector('i');
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fe-eye-off';
    } else {
        input.type = 'password';
        icon.className = 'fe-eye';
    }
});

document.getElementById('test-gemini-btn')?.addEventListener('click', function () {
    var btn = this;
    var result = document.getElementById('test-result');
    var apiKeyInput = document.getElementById('api_key');
    var apiKey = apiKeyInput ? apiKeyInput.value.trim() : '';

    btn.disabled = true;
    btn.innerHTML = '<i class="fe-loader me-1"></i> Testing...';
    result.className = '';
    result.style.display = 'none';
    result.textContent = '';

    fetch('<?php echo e(route('admin.gemini_ai.test')); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            prompt: 'Reply with only: API OK',
            api_key: apiKey
        })
    })
    .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
    .then(function (res) {
        result.style.display = 'block';
        if (res.ok && res.data.success) {
            result.className = 'success';
            result.textContent = res.data.message + '\n\n' + (res.data.response || '');
        } else {
            result.className = 'error';
            result.textContent = res.data.message || 'Connection failed.';
        }
    })
    .catch(function () {
        result.style.display = 'block';
        result.className = 'error';
        result.textContent = 'Connection test failed.';
    })
    .finally(function () {
        btn.disabled = false;
        btn.innerHTML = '<i class="fe-zap me-1"></i> Test Connection';
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/creativedesignbd/ecommerce1.creativedesign.com.bd/resources/views/backEnd/settings/gemini_ai.blade.php ENDPATH**/ ?>