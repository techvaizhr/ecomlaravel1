<?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="col-12 product-review-item">
    <div class="gomobd-review-card shadow-sm">
        <div class="gomobd-review-card-header d-flex justify-content-between align-items-start flex-wrap">
            <div class="d-flex align-items-center">
                <div class="gomobd-review-avatar">
                    <?php echo e(strtoupper(substr($review->name, 0, 1))); ?>

                </div>
                <div class="gomobd-review-meta">
                    <h6 class="gomobd-review-name"><?php echo e($review->name); ?></h6>
                    <small class="gomobd-review-date"><?php echo e($review->created_at->format('d M Y')); ?></small>
                </div>
            </div>
            <div class="gomobd-review-stars">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <?php if($i <= $review->ratting): ?>
                        <i class="fa-solid fa-star"></i>
                    <?php else: ?>
                        <i class="fa-regular fa-star"></i>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>
        <div class="gomobd-review-body mt-2">
            <p><i class="fa-regular fa-comment-dots text-success me-1"></i> <?php echo e($review->review); ?></p>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /home/creativedesignbd/ecommerce1.creativedesign.com.bd/resources/views/frontEnd/layouts/ajax/product-reviews.blade.php ENDPATH**/ ?>