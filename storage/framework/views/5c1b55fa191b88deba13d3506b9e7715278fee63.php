<?php $__env->startSection('pagescript'); ?>
    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>
<?php $__env->stopSection(); ?>




<?php $__env->startSection('title', 'Import Customer | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Import Customer

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?php echo e(route('customers.index')); ?>">Customers</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Import Customer</li>

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
        <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Import Customers</h4>
                  <form class="forms-sample" action="<?php echo e(url('import-customer')); ?>" method="POST" name="importform" enctype="multipart/form-data">
                  <?php echo csrf_field(); ?>
                  <div class="card" id="upload_pdf">
                            <div class="card-body">
                            <h4 class="card-title d-flex">Upload CSV File</h4>
                        
                            <input type="file" name="import_file" id="post_card" class="dropify" required />
                            </div>
                            
                        </div>
                        <p class="card-description">
                                Download Example file and arrange data in that file <code><a href="<?php echo e(asset('Customers.csv')); ?>" target="_blank"><i class="fas fa-info-circle"></i>Download Exampel Import File </a></code>
                            </p>
                            <button type="submit" style="float:right;" class="btn btn-primary mr-2">Submit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/customers/csvimport.blade.php ENDPATH**/ ?>