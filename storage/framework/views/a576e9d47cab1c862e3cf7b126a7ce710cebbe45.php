

<?php $__env->startSection('title', 'Edit Booking | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Update Booking
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('service-bookings.index')); ?>">Service Bookings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Update Booking</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Customer Form</h4>
                            <?php $__currentLoopData = $booking_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                            $invoice = $book->invoice;
                            ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <form class="forms-sample" action="<?php echo e(url('service-bookings/update',$invoice)); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <?php echo $__env->make('service_bookings.booking_form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/service_bookings/edit.blade.php ENDPATH**/ ?>