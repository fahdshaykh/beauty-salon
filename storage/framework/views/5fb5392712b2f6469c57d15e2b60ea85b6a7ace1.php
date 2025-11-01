





<?php $__env->startSection('title', 'Customer Report Records | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>



<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Reports

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="<?php echo e(url('customer-reports')); ?>">Reports</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Customer Report</li>

                </ol>

            </nav>

        </div>

        <?php $customer_report_array = array(5,4); ?>

        <?php $customer_report = implode(',', $customer_report_array); ?>

        <div class="alert alert-success" style="display:none;" id="status-success"></div>



        <?php if(Session::has('success')): ?>

        <div class="alert alert-success alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            <?php echo e(Session::get('success')); ?>


        </div>



        <?php elseif(Session::has('destroy')): ?>

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            <?php echo e(Session::get('destroy')); ?>


        </div>

        <?php endif; ?>



        <?php if(Session::has('update')): ?>

        <div class="alert alert-success alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            <?php echo e(Session::get('update')); ?>


        </div>

        <?php endif; ?>



        <div class="card">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <h4 class="card-title">Customer Reports</h4>

                    </div>

                    <div class="col-md-6"></div>

                    <div class="col-md-3">

                        <div class="text-center" style="float: center;">

                        <?php if(in_array($customer_report, session()->get('all_rp')['0'])): ?>

                        <span data-href="/customer-report-by-date/<?php echo e($start_date); ?>/<?php echo e($end_date); ?>/<?php echo e($customer_id); ?>" id="export" class="btn btn-primary" onclick="exportReports(event.target);">Export Customer Report</span>

                        <?php endif; ?>

                        </div>

                    </div>

                </div>

                <div class="row grid-margin">

                    <div class="col-12">

                      <div style="text-align:center">

                          <p>Grand Total: <i class="fas fa-dollar-sign"></i><?php echo e($total_amount); ?></p>

                      </div>

                    </div>

                    <div class="col-12">

                        <div class="table-responsive">

                            <table id="order-listing" class="table">

                                <thead>

                                    <tr class="bg-primary text-white">

                                        <th>Sr.No #</th>

                                        <th>Booking User</th>

                                        <th>Customer Name</th>

                                        <th>Services Name</th>

                                        <th>Total Price</th>

                                        <th>Due Date</th>

                                        <th>Due Time</th>

                                        <th>Status</th>

                                        <th>Payment Method</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    

                                    <?php $__currentLoopData = $data_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <tr>

                                        <td><?php echo e($num); ?></td>

                                        <td><?php echo e($book['user_name']); ?></td> <!--  showing user names -->

                                        <td><?php echo e($book['customer_name']); ?></td> <!--  showing customers names -->

                                        <td style="max-width:160px;"><?php echo e($book['services']); ?></td> <!--  showing service names -->

                                        <td><i class="fas fa-dollar-sign"></i><?php echo e($book['total_price']); ?></td>

                                        <td><?php echo e($book['due_date']); ?></td>

                                        <td><?php echo e($book['due_time']); ?></td>

                                        <td>

                                            <?php $status = array('1' => 'Booked', '2' => 'In-Progress', '3' => 'Completed', '4' => 'Cancel'); ?>

                                            <?php $badges = array('1' => 'badge badge-info', '2' => 'In-Progress', '3' => 'badge badge-success', 'badge badge-warning' => 'badge badge-danger'); ?>

                                            <?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusKey => $check): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                            <label class="<?php if($book['service_status'] == 1): ?> badge badge-info <?php endif; ?> <?php if($book['service_status'] == 2): ?> badge badge-warning <?php endif; ?> <?php if($book['service_status'] == 3): ?> badge badge-success <?php endif; ?> <?php if($book['service_status'] == 4): ?> badge badge-danger <?php endif; ?>"><?php if($statusKey == $book['service_status']): ?> <?php echo e($check); ?> <?php endif; ?></label>

                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        </td>

                                        <td><?php echo e($book['payment_instrument']); ?></td>

                                    </tr>

                                    <?php $num++; ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <?php $__env->stopSection(); ?>



<script>

function exportReports(_this) {

    let _url = $(_this).data('href');

    var path = <?php echo json_encode(url('/')); ?>


    window.location.href = path+_url;

}

</script>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/reports/customer_reports.blade.php ENDPATH**/ ?>