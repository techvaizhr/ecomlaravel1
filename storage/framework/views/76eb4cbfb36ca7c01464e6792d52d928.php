
<?php $__env->startSection('title', 'ভেন্ডর তালিকা'); ?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('backEnd.vendor.partials.vendor_list_styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid vendor-list-shell">

    <div class="vn-page-header">
        <div>
            <h4>ভেন্ডর ব্যবস্থাপনা <span class="vn-badge-count"><?php echo e($stats['total']); ?></span></h4>
            <p class="vn-sub mb-0">নিবন্ধিত সেলার ও শপের তালিকা, স্ট্যাটাস ও ভেরিফিকেশন পরিচালনা</p>
        </div>
        <div class="vn-header-actions">
            <a href="<?php echo e(route('admin.vendor.verification.index')); ?>" class="vn-btn-ghost">
                <i class="fas fa-shield-alt"></i> ভেরিফিকেশন
            </a>
            <a href="<?php echo e(route('admin.vendor.withdrawals.index')); ?>" class="vn-btn-ghost">
                <i class="fas fa-wallet"></i> উইথড্র
            </a>
        </div>
    </div>

    <div class="vn-stat-grid">
        <div class="vn-stat">
            <div class="vn-stat-label">মোট ভেন্ডর</div>
            <div class="vn-stat-val"><?php echo e($stats['total']); ?></div>
        </div>
        <div class="vn-stat active-stat">
            <div class="vn-stat-label">সক্রিয়</div>
            <div class="vn-stat-val"><?php echo e($stats['active']); ?></div>
        </div>
        <div class="vn-stat verified-stat">
            <div class="vn-stat-label">ভেরিফাইড</div>
            <div class="vn-stat-val"><?php echo e($stats['verified']); ?></div>
        </div>
        <div class="vn-stat pending-stat">
            <div class="vn-stat-label">ভেরিফিকেশন বাকি</div>
            <div class="vn-stat-val"><?php echo e($stats['pending']); ?></div>
        </div>
    </div>

    <div class="vn-card">
        <div class="vn-card-head">
            <h6><i class="fas fa-search"></i> খুঁজুন</h6>
        </div>
        <div class="vn-card-body">
            <form method="GET" action="<?php echo e(route('admin.vendors.index')); ?>" class="vn-filter-grid">
                <div>
                    <label class="vn-label">কীওয়ার্ড</label>
                    <input type="text" name="keyword" class="vn-input"
                           placeholder="শপ, মালিক, ইমেইল, ফোন..."
                           value="<?php echo e(request('keyword')); ?>">
                </div>
                <div class="vn-filter-actions">
                    <button type="submit" class="btn vn-btn-primary">
                        <i class="fas fa-search me-1"></i> খুঁজুন
                    </button>
                    <?php if(request('keyword')): ?>
                    <a href="<?php echo e(route('admin.vendors.index')); ?>" class="vn-btn-ghost">
                        <i class="fas fa-redo"></i> রিসেট
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="vn-card">
        <div class="vn-card-head">
            <h6><i class="fas fa-store"></i> ভেন্ডর তালিকা</h6>
        </div>
        <div class="vn-card-body">
            <p class="d-lg-none vn-scroll-hint"><i class="fas fa-arrows-alt-h me-1"></i> বাকি কলাম দেখতে স্লাইড করুন</p>

            <div class="vn-table-rail">
                <table class="table vn-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>শপ</th>
                            <th>মালিক</th>
                            <th>যোগাযোগ</th>
                            <th>প্রোডাক্ট</th>
                            <th>ব্যালেন্স</th>
                            <th>ভেরিফিকেশন</th>
                            <th>স্ট্যাটাস</th>
                            <th class="text-end">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="text-muted"><?php echo e($loop->iteration + ($vendors->currentPage() - 1) * $vendors->perPage()); ?></td>
                            <td>
                                <div class="vn-shop-cell">
                                    <?php if($vendor->logo): ?>
                                        <img src="<?php echo e(asset($vendor->logo)); ?>" alt="" class="vn-shop-avatar">
                                    <?php else: ?>
                                        <div class="vn-shop-placeholder"><?php echo e(strtoupper(substr($vendor->shop_name, 0, 1))); ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="vn-shop-name"><?php echo e($vendor->shop_name); ?></div>
                                        <div class="vn-shop-meta">ID #<?php echo e($vendor->id); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark"><?php echo e($vendor->owner_name); ?></span>
                            </td>
                            <td>
                                <div class="vn-contact-line">
                                    <i class="far fa-envelope"></i>
                                    <span title="<?php echo e($vendor->email); ?>"><?php echo e(Str::limit($vendor->email, 22)); ?></span>
                                </div>
                                <div class="vn-contact-line mb-0">
                                    <i class="fas fa-phone-alt"></i>
                                    <span><?php echo e($vendor->phone); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="vn-pill vn-pill-products">
                                    <i class="fas fa-box-open"></i>
                                    <?php echo e($vendor->products->count()); ?>

                                </span>
                            </td>
                            <td>
                                <span class="vn-balance">৳<?php echo e(number_format($vendor->wallet ? $vendor->wallet->balance : 0, 2)); ?></span>
                            </td>
                            <td>
                                <?php if($vendor->verification_status == 'approved'): ?>
                                    <span class="vn-pill vn-badge-verified"><span class="vn-dot"></span> Verified</span>
                                <?php elseif($vendor->verification_status == 'rejected'): ?>
                                    <span class="vn-pill vn-badge-rejected"><span class="vn-dot"></span> Rejected</span>
                                <?php else: ?>
                                    <span class="vn-pill vn-badge-pending"><span class="vn-dot"></span> Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($vendor->status == 1): ?>
                                    <span class="vn-pill vn-badge-active">Active</span>
                                <?php else: ?>
                                    <span class="vn-pill vn-badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="vn-actions">
                                    <form method="post" action="<?php echo e(route('admin.vendors.toggle-status', $vendor->id)); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php if($vendor->status == 1): ?>
                                            <button type="submit" class="vn-action-btn deactivate" title="নিষ্ক্রিয় করুন">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="vn-action-btn activate" title="সক্রিয় করুন">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                    <a href="<?php echo e(route('admin.vendors.edit', $vendor->id)); ?>" class="vn-action-btn edit" title="সম্পাদনা">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form method="post" action="<?php echo e(route('admin.vendors.destroy', $vendor->id)); ?>" class="d-inline"
                                          onsubmit="return confirm('এই ভেন্ডর মুছে ফেলবেন? এটি ফিরিয়ে আনা যাবে না।');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="vn-action-btn delete" title="মুছুন">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9">
                                <div class="vn-empty">
                                    <div><i class="fas fa-store-slash"></i></div>
                                    <p class="mb-0 fw-semibold">কোনো ভেন্ডর পাওয়া যায়নি</p>
                                    <?php if(request('keyword')): ?>
                                    <p class="small mb-0 mt-1">অন্য কীওয়ার্ড দিয়ে খুঁজুন অথবা ফিল্টার রিসেট করুন</p>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if($vendors->hasPages() || $vendors->total() > 0): ?>
        <div class="vn-foot">
            <div class="vn-foot-meta">
                দেখানো হচ্ছে <strong><?php echo e($vendors->firstItem() ?? 0); ?></strong>–<strong><?php echo e($vendors->lastItem() ?? 0); ?></strong>
                / মোট <strong><?php echo e($vendors->total()); ?></strong> ভেন্ডর
            </div>
            <div><?php echo e($vendors->links('pagination::bootstrap-4')); ?></div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    if (typeof feather !== 'undefined') { feather.replace(); }
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('backEnd.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/creativedesignbd/ecommerce1.creativedesign.com.bd/resources/views/backEnd/vendor/index.blade.php ENDPATH**/ ?>