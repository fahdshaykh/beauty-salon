

<?php $__env->startSection('title', 'Supplier Balance Summary | ' . Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Supplier Summary</h3>
            <a href="<?php echo e(route('supplier-payments.edit', $supplier->id)); ?>" class="btn btn-primary btn-sm">Add Payment</a>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('suppliers.index')); ?>">Suppliers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Balance Summary</li>
                </ol>
            </nav>
        </div>

        <!-- Supplier Info -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Supplier Details</h4>

                <div class="row">
                    <div class="col-md-4"><strong>Total Purchases:</strong> <?php echo e(number_format($totalPurchases, 2)); ?></div>
                    <div class="col-md-4"><strong>Total Paid:</strong> <?php echo e(number_format($totalPaid, 2)); ?></div>
                    <div class="col-md-4"><strong>Total Pending:</strong> <?php echo e(number_format($totalPending, 2)); ?></div>
                </div>
                
            </div>
        </div>

        <!-- other payments  -->
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">Payment History</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Amount Paid</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $supplier->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($purchase->created_at->format('Y-m-d')); ?></td>
                                    <td><?php echo e(number_format($purchase->amount, 2)); ?></td>
                                    <td><?php echo e($purchase->note ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No purchase records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- payment history -->

        <!-- Purchase History -->
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">Purchase History</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Inventory</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Amount Paid</th>
                                <th>Pending</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $supplier->purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($purchase->created_at->format('Y-m-d')); ?></td>
                                    <td><?php echo e($purchase->inventory->name ?? 'N/A'); ?></td>
                                    <td><?php echo e($purchase->quantity); ?></td>
                                    <td><?php echo e(number_format($purchase->price, 2)); ?></td>
                                    <td><?php echo e(number_format($purchase->amount_paid, 2)); ?></td>
                                    <td><?php echo e(number_format($purchase->amount_pending, 2)); ?></td>
                                    <td><?php echo e($purchase->description ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No purchase records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/suppliers/balance_summary.blade.php ENDPATH**/ ?>