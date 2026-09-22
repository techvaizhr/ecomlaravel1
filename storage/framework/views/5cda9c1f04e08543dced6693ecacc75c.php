<?php $__env->startSection('title', 'My Products'); ?>
<?php $__env->startSection('page-title', 'My Products'); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-color: #4e73df;
        --secondary-color: #858796;
        --success-color: #1cc88a;
        --info-color: #36b9cc;
        --warning-color: #f6c23e;
        --danger-color: #e74a3b;
        --text-color: #5a5c69;
    }

    body {
        font-family: 'Poppins', sans-serif;
        color: var(--text-color);
    }

    /* Card Styling */
    .dashboard-card {
        background: #fff;
        border-radius: 15px;
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.05);
    }

    /* Product Image */
    .product-img-container {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        overflow: hidden;
        background: #f8f9fc;
        border: 1px solid #eaecf4;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .backend-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Table Styling */
    .table thead th {
        background-color: transparent;
        border-bottom: 2px solid #eaecf4;
        color: var(--primary-color);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.80rem;
        letter-spacing: 0.5px;
        padding: 15px 10px;
    }
    
    .table td {
        vertical-align: middle;
        padding: 15px 10px;
        font-size: 0.9rem;
        border-bottom: 1px solid #f8f9fc;
    }

    /* Soft Badges */
    .badge-soft-success { background-color: rgba(28, 200, 138, 0.1); color: var(--success-color); }
    .badge-soft-danger { background-color: rgba(231, 74, 59, 0.1); color: var(--danger-color); }
    .badge-soft-warning { background-color: rgba(246, 194, 62, 0.1); color: var(--warning-color); }
    .badge-soft-info { background-color: rgba(54, 185, 204, 0.1); color: var(--info-color); }
    
    .badge {
        padding: 6px 12px;
        border-radius: 30px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    /* Search Input */
    .search-input {
        border-radius: 50px;
        padding: 10px 20px;
        border: 1px solid #eaecf4;
        background-color: #f8f9fc;
    }
    .search-input:focus {
        background-color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1);
        border-color: var(--primary-color);
    }

    /* Buttons */
    .btn-primary-soft {
        background-color: rgba(78, 115, 223, 0.1);
        color: var(--primary-color);
        border: none;
        font-weight: 500;
    }
    .btn-primary-soft:hover {
        background-color: var(--primary-color);
        color: #fff;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }
    .btn-edit { background: rgba(78, 115, 223, 0.1); color: var(--primary-color); }
    .btn-edit:hover { background: var(--primary-color); color: #fff; }
    
    .btn-delete { background: rgba(231, 74, 59, 0.1); color: var(--danger-color); border: none; }
    .btn-delete:hover { background: var(--danger-color); color: #fff; }

</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Product Management</h4>
        <p class="text-secondary small mb-0">Manage your product inventory and stock</p>
    </div>
    <div class="mt-3 mt-md-0">
        <?php if($vendor->verification_status == 'approved'): ?>
            <a href="<?php echo e(route('vendor.products.create')); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i>Add New Product
            </a>
        <?php else: ?>
            <button disabled class="btn btn-secondary rounded-pill px-4" title="Please verify your account first">
                <i class="fas fa-lock me-2"></i>Add Product
            </button>
        <?php endif; ?>
    </div>
</div>

<?php if($vendor->verification_status != 'approved'): ?>
<div class="dashboard-card p-4 mb-4 border-start border-4 <?php echo e($vendor->verification_status == 'rejected' ? 'border-danger' : 'border-warning'); ?>">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <div class="avatar-md bg-light-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="fas fa-exclamation-triangle fa-lg <?php echo e($vendor->verification_status == 'rejected' ? 'text-danger' : 'text-warning'); ?>"></i>
            </div>
        </div>
        <div class="flex-grow-1">
            <h6 class="fw-bold mb-1">
                <?php if($vendor->verification_status == 'pending'): ?>
                    Account Verification Pending
                <?php elseif($vendor->verification_status == 'rejected'): ?>
                    Account Verification Rejected
                <?php else: ?>
                    Action Required: Verify Account
                <?php endif; ?>
            </h6>
            <p class="mb-0 text-muted small">
                <?php if($vendor->verification_status == 'pending'): ?>
                    Your documents are under review. Product upload is restricted.
                <?php elseif($vendor->verification_status == 'rejected'): ?>
                    Verification rejected. Please re-upload valid documents.
                <?php else: ?>
                    Upload your ID card and photo to unlock product uploading features.
                <?php endif; ?>
            </p>
        </div>
        <a href="<?php echo e(route('vendor.verification.index')); ?>" class="btn btn-sm btn-outline-dark rounded-pill ms-3">
            Verify Now
        </a>
    </div>
</div>
<?php endif; ?>

<div class="dashboard-card p-4 mb-4">
    <form method="GET" action="<?php echo e(route('vendor.products.index')); ?>">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="position-relative">
                    <i class="fas fa-search position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                    <input type="text" name="keyword" class="form-control search-input ps-5" placeholder="Search by product name, category..." value="<?php echo e(request('keyword')); ?>">
                </div>
            </div>
            <div class="col-md-4 text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Search</button>
                <?php if(request('keyword')): ?>
                    <a href="<?php echo e(route('vendor.products.index')); ?>" class="btn btn-light rounded-pill px-4 ms-2 text-danger">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<div class="dashboard-card p-0 overflow-hidden">
    <div class="p-4 border-bottom border-light">
        <h6 class="fw-bold m-0 text-primary">All Products List</h6>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Product</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Approval</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="product-img-container me-3">
                                <img src="<?php echo e(asset($value->image ? $value->image->image : 'storage/uploads/placeholder.png')); ?>" 
                                     class="backend-image" alt="Product">
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold text-dark" style="font-size: 0.9rem;"><?php echo e(Str::limit($value->name, 30)); ?></h6>
                                <small class="text-muted">ID: #<?php echo e($value->id); ?></small>
                            </div>
                        </div>
                    </td>

                    <td><?php echo e($value->category ? $value->category->name : '-'); ?></td>

                    <td>
                        <?php $isDigital = isset($value->is_digital) ? (bool) $value->is_digital : false; ?>
                        <?php if($isDigital): ?>
                            <span class="badge badge-soft-info"><i class="fas fa-cloud-download-alt me-1"></i> Digital</span>
                        <?php else: ?>
                            <span class="badge badge-soft-success"><i class="fas fa-box me-1"></i> Physical</span>
                        <?php endif; ?>
                    </td>

                    <td class="fw-bold text-dark">৳<?php echo e(number_format($value->new_price, 2)); ?></td>

                    <td>
                        <?php if($value->stock > 0): ?>
                            <span class="text-dark fw-medium"><?php echo e($value->stock); ?></span>
                        <?php else: ?>
                            <span class="text-danger fw-bold">Out of Stock</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if($value->status == 1): ?>
                            <span class="badge badge-soft-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-soft-danger">Inactive</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if($value->approval_status == 'approved'): ?>
                            <span class="badge badge-soft-success">Approved</span>
                        <?php elseif($value->approval_status == 'pending'): ?>
                            <span class="badge badge-soft-warning">Pending</span>
                        <?php elseif($value->approval_status == 'rejected'): ?>
                            <span class="badge badge-soft-danger">Rejected</span>
                        <?php endif; ?>
                    </td>

                    <td class="text-end pe-4">
                        <div class="d-inline-flex gap-2">
                            <a href="<?php echo e(route('vendor.products.edit', $value->id)); ?>" class="action-btn btn-edit" title="Edit">
                                <i class="fas fa-pen fa-xs"></i>
                            </a>
                            
                            <form method="post" action="<?php echo e(route('vendor.products.destroy')); ?>" class="d-inline delete-form">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" value="<?php echo e($value->id); ?>" name="hidden_id">
                                <button type="submit" class="action-btn btn-delete" title="Delete">
                                    <i class="fas fa-trash-alt fa-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center">
                            <div class="mb-3 p-3 bg-light rounded-circle">
                                <i class="fas fa-box-open fa-2x text-muted"></i>
                            </div>
                            <h6 class="text-muted">No products found</h6>
                            <a href="<?php echo e(route('vendor.products.create')); ?>" class="btn btn-sm btn-primary-soft mt-2">Create New Product</a>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
<?php if($data->hasPages()): ?>
<div class="pagination-wrapper p-4 border-top border-light d-flex justify-content-center bg-white rounded-bottom">
    <ul class="premium-pagination mb-0">
        
        
        <?php if($data->onFirstPage()): ?>
            <li class="page-item disabled">
                <span class="page-link icon-box"><i class="fas fa-chevron-left"></i></span>
            </li>
        <?php else: ?>
            <li class="page-item">
                <a class="page-link icon-box" href="<?php echo e($data->previousPageUrl()); ?>" rel="prev">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        <?php endif; ?>

        
        <?php $__currentLoopData = $data->links()->elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            
            <?php if(is_string($element)): ?>
                <li class="page-item disabled"><span class="page-link dots"><?php echo e($element); ?></span></li>
            <?php endif; ?>

            
            <?php if(is_array($element)): ?>
                <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $data->currentPage()): ?>
                        <li class="page-item active"><span class="page-link"><?php echo e($page); ?></span></li>
                    <?php else: ?>
                        <li class="page-item"><a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a></li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php if($data->hasMorePages()): ?>
            <li class="page-item">
                <a class="page-link icon-box" href="<?php echo e($data->nextPageUrl()); ?>" rel="next">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link icon-box"><i class="fas fa-chevron-right"></i></span>
            </li>
        <?php endif; ?>
    </ul>
</div>

<style>
    .premium-pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        gap: 6px;
        align-items: center;
    }
    
    .premium-pagination .page-link {
        border: none;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-weight: 700;
        color: #64748b;
        background: #f8fafc;
        transition: all 0.2s ease;
        text-decoration: none;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .premium-pagination .page-link:hover {
        background: #eef2ff;
        color: #4f46e5;
        transform: translateY(-2px);
    }

    .premium-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
    }

    .premium-pagination .page-item.disabled .page-link {
        background: #fff;
        color: #cbd5e1;
        cursor: not-allowed;
    }
    
    .premium-pagination .dots { background: transparent; cursor: default; }
</style>
<?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Confirmation for delete
        $('.delete-form').on('submit', function(e) {
            if (!confirm('Are you sure you want to delete this product?')) {
                e.preventDefault();
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('vendor.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/creativedesignbd/ecommerce1.creativedesign.com.bd/resources/views/vendor/products/index.blade.php ENDPATH**/ ?>