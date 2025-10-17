

<?php $__env->startSection('title', 'Booking History | ' . Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Booking History</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('customers.index')); ?>">Customers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Booking History</li>
                </ol>
            </nav>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Customer details</h4>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Name:</strong> <?php echo e($customer->first_name); ?> <?php echo e($customer->first_name); ?>

                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong> <?php echo e($customer->email); ?>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Phone:</strong> <?php echo e($customer->primary_phone); ?>

                    </div>
                    <div class="col-md-6">
                        <strong>Address:</strong> <?php echo e($customer->address); ?>

                    </div>
                </div>

            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title">Bookings</h4>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Service Status</th>
                                <th>Services</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $status = array('1' => 'Booked', '2' => 'In-Progress', '3' => 'Completed', '4' => 'Cancel'); ?>
                            <?php $__currentLoopData = $service_info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($booking->id); ?></td>
                                <td>(Total Price: $<?php echo e($booking->bookingdetail->sum(fn($detail) => $detail->service->price)); ?>)</td>
                                <td><?php echo e($booking->due_date); ?></td>
                                <td><?php echo e($booking->due_time); ?></td>
                                <td><?php echo e($status[$booking->service_status] ?? 'Unknown'); ?></td>
                                <td>
                                    <ul>
                                        <?php $__currentLoopData = $booking->bookingdetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($detail->service->name); ?> - $<?php echo e($detail->service->price); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-saloon\resources\views/customers/booking_history.blade.php ENDPATH**/ ?>