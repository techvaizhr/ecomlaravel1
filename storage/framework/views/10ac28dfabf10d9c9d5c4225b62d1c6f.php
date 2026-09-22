

<?php $__env->startSection('title', 'Customers'); ?>
<?php $__env->startSection('page-title', 'Customer List'); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: #4f46e5;
        --secondary: #64748b;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --dark: #1e293b;
        --light: #f8fafc;
        --border: #e2e8f0;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f1f5f9;
        color: var(--dark);
    }

    /* Filter Card */
    .filter-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid var(--border);
        padding: 10px 15px;
        font-size: 0.9rem;
        background-color: #fff;
    }
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Table Card */
    .table-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .custom-table thead th {
        background-color: #f8fafc;
        color: var(--secondary);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
    }

    .custom-table tbody tr {
        transition: all 0.2s;
    }
    .custom-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .custom-table td {
        padding: 16px 24px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border);
        font-size: 0.9rem;
        color: var(--dark);
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Avatar */
    .avatar-initial {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background-color: #eef2ff;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        border: 1px solid rgba(79, 70, 229, 0.1);
    }

    /* Soft Badges */
    .badge-soft {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
    }
    .badge-soft-success { background-color: #d1fae5; color: #065f46; }
    .badge-soft-secondary { background-color: #f1f5f9; color: #475569; }

    /* Icons */
    .icon-box {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--secondary);
        font-size: 0.85rem;
    }

</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Customer List</h4>
            <p class="text-secondary small mb-0">Manage and view your store's customer details.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <span class="badge bg-white text-dark border py-2 px-3 rounded-pill shadow-sm fw-medium">
                Total Customers: <strong><?php echo e($customers->total()); ?></strong>
            </span>
        </div>
    </div>

    <div class="filter-card">
        <form action="" method="GET">
            <div class="row g-3">
                <div class="col-md-10">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute text-secondary" style="top: 13px; left: 15px;"></i>
                        <input type="text" name="keyword" class="form-control ps-5" placeholder="Search by name, phone, or email..." value="<?php echo e(request('keyword')); ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-medium">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Customer Profile</th>
                        <th>Contact Info</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Last Activity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-initial me-3">
                                    <?php echo e(strtoupper(substr($customer->name ?? 'G', 0, 1))); ?>

                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold text-dark"><?php echo e($customer->name ?? 'Guest User'); ?></h6>
                                    <small class="text-secondary"><?php echo e($customer->email ?? 'No email provided'); ?></small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column gap-1">
                                <div class="icon-box">
                                    <i class="fas fa-phone-alt fa-xs"></i>
                                    <span class="text-dark fw-medium"><?php echo e($customer->phone ?? 'N/A'); ?></span>
                                </div>
                                <?php if($customer->address): ?>
                                <div class="icon-box">
                                    <i class="fas fa-map-marker-alt fa-xs"></i>
                                    <span class="text-secondary small" title="<?php echo e($customer->address); ?>"><?php echo e(Str::limit($customer->address, 25)); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td>
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                <?php echo e($customer->orders_count ?? 0); ?> Orders
                            </span>
                        </td>

                        <td>
                            <h6 class="mb-0 fw-bold text-dark">৳<?php echo e(number_format($customer->total_spent ?? 0, 2)); ?></h6>
                        </td>

                        <td>
                            <?php
                                // Optimized logic moved inside view for compatibility
                                $lastOrder = \App\Models\Order::whereIn('id', function($query) use ($vendor) {
                                        $query->select('order_id')
                                              ->from('order_details')
                                              ->whereIn('product_id', function($q) use ($vendor) {
                                                  $q->select('id')->from('products')->where('vendor_id', $vendor->id);
                                              });
                                    })
                                    ->where('customer_id', $customer->id)
                                    ->latest()
                                    ->first();
                            ?>

                            <?php if($lastOrder): ?>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium text-dark small"><?php echo e($lastOrder->created_at->format('d M, Y')); ?></span>
                                    <small class="text-muted" style="font-size: 11px;"><?php echo e($lastOrder->created_at->format('h:i A')); ?></small>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">Never</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if($customer->status == 1): ?>
                                <span class="badge-soft badge-soft-success">
                                    <span class="me-1" style="width: 6px; height: 6px; border-radius: 50%; background-color: currentColor;"></span> Active
                                </span>
                            <?php else: ?>
                                <span class="badge-soft badge-soft-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <div class="mb-3 p-3 bg-light rounded-circle">
                                    <i class="fas fa-users-slash fa-2x text-secondary opacity-50"></i>
                                </div>
                                <h6 class="text-secondary fw-bold">No Customers Found</h6>
                                <p class="text-muted small mb-0">It looks like you don't have any customers yet.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

  <?php if($customers->hasPages()): ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-4 border-top bg-white rounded-bottom">
    
    <div class="text-muted small fw-medium mb-3 mb-md-0">
        Showing <span class="fw-bold text-dark"><?php echo e($customers->firstItem()); ?></span> 
        to <span class="fw-bold text-dark"><?php echo e($customers->lastItem()); ?></span> 
        of <span class="fw-bold text-dark"><?php echo e($customers->total()); ?></span> entries
    </div>

    <nav aria-label="Page navigation">
        <ul class="premium-pagination mb-0">
            
            
            <?php if($customers->onFirstPage()): ?>
                <li class="page-item disabled">
                    <span class="page-link icon-box"><i class="fas fa-chevron-left"></i></span>
                </li>
            <?php else: ?>
                <li class="page-item">
                    <a class="page-link icon-box" href="<?php echo e($customers->previousPageUrl()); ?>" rel="prev">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php
                $start = max($customers->currentPage() - 2, 1);
                $end = min($start + 4, $customers->lastPage());
                if($end - $start < 4) {
                    $start = max($end - 4, 1);
                }
            ?>

            <?php if($start > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo e($customers->url(1)); ?>">1</a>
                </li>
                <?php if($start > 2): ?>
                    <li class="page-item disabled"><span class="page-link dots">...</span></li>
                <?php endif; ?>
            <?php endif; ?>

            <?php for($i = $start; $i <= $end; $i++): ?>
                <?php if($i == $customers->currentPage()): ?>
                    <li class="page-item active">
                        <span class="page-link"><?php echo e($i); ?></span>
                    </li>
                <?php else: ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo e($customers->url($i)); ?>"><?php echo e($i); ?></a>
                    </li>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if($end < $customers->lastPage()): ?>
                <?php if($end < $customers->lastPage() - 1): ?>
                    <li class="page-item disabled"><span class="page-link dots">...</span></li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo e($customers->url($customers->lastPage())); ?>"><?php echo e($customers->lastPage()); ?></a>
                </li>
            <?php endif; ?>

            
            <?php if($customers->hasMorePages()): ?>
                <li class="page-item">
                    <a class="page-link icon-box" href="<?php echo e($customers->nextPageUrl()); ?>" rel="next">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link icon-box"><i class="fas fa-chevron-right"></i></span>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<style>
    /* Premium Pagination Styles */
    .premium-pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        gap: 5px;
        align-items: center;
    }
    
    .premium-pagination .page-link {
        border: none;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px; /* Modern Rounded Corners */
        font-weight: 700;
        color: #64748b;
        background: #f8fafc;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        font-size: 0.85rem;
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
    
    .premium-pagination .dots { 
        background: transparent; 
        cursor: default; 
    }
    .premium-pagination .dots:hover { 
        background: transparent; 
        transform: none; 
    }
</style>
<?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('vendor.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/creativedesignbd/ecommerce1.creativedesign.com.bd/resources/views/vendor/customers.blade.php ENDPATH**/ ?>