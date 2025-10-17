

<?php $__env->startSection('pagescript'); ?>

<script src="<?php echo e(asset('js/select2.js')); ?>"></script>

<script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<?php $__env->stopSection(); ?>





<?php $__env->startSection('title', 'Add Holidays | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Add Holidays

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="#">Holidays</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Add Holidays</li>

                </ol>

            </nav>

        </div>

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

        <div class="row">

          <div class="col-md-12 grid-margin stretch-card">

            <div class="card">

              <div class="card-body">

                <h4 class="card-title">Holidays Setup</h4>

                <p class="card-description">

                  Select <code>Starting date</code> & <code>Ending Date.</code> to apply holidays in calendar!

                </p>

                <form class="form-inline" action="<?php echo e(url('update-holidays')); ?>" method="post" id="expense-filters">
                <?php echo csrf_field(); ?>
                  <div class="input-group">

                    <div id="datepicker-popup-new" class="input-group date datepicker">

                      <input type="text" name="start_date" class="form-control">

                      <span class="input-group-addon input-group-append border-left">

                        <span class="far fa-calendar input-group-text"></span>

                      </span>

                    </div>

                  </div>

                  <div class="input-group">

                    <div id="datepicker-popup-new2" class="input-group date datepicker">

                      <input type="text" name="end_date" class="form-control">

                      <span class="input-group-addon input-group-append border-left">

                        <span class="far fa-calendar input-group-text"></span>

                      </span>

                    </div>

                  </div>

                  

                   

                   

                    <button type="submit" value="submit" class="btn btn-primary mb-2">Update</button>


                </form>

              </div>

            </div>

          </div>

        </div>
        <div class="card" id="report">
        <div class="card">
            <div class="card-body">
                <div class="row grid-margin">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="order-listing" class="table">
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th>Sr </th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Date range</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $num = 1; ?>
                                    <?php $__currentLoopData = $holidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holiday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                    <td><?php echo e($num); ?></td>
                                    <td><?php echo e($holiday->start_date); ?></td>
                                    <td><?php echo e($holiday->end_date); ?></td>
                                    <td><?php echo e($holiday->days); ?></td>
                                    <td><a href="<?php echo e(url('delete-holidays',$holiday->id)); ?>">
                                    <i class="fas fa-trash-alt text-danger" aria-hidden="true"></i>                          
                                </a></td>
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
    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-saloon\resources\views/holidays/index.blade.php ENDPATH**/ ?>