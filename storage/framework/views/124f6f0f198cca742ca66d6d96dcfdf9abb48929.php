

<?php $__env->startSection('title', 'Notifications | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">All Notifications</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notifications Listing</li>
                </ol>
            </nav>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Notifications</h4>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>Received</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($notification->data['message']); ?></td>
                                    <td><?php echo e($notification->created_at->diffForHumans()); ?></td>
                                    <td>
                                        <?php if(is_null($notification->read_at)): ?>
                                            <form method="POST" action="<?php echo e(route('notifications.markAsRead', $notification->id)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn btn-sm btn-success">Mark as Read</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Read</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No new notifications</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    <?php echo e($notifications->links()); ?>

                </div>

            </div>
        </div>

    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/notifications.blade.php ENDPATH**/ ?>