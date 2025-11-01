<?php $__env->startSection('pagescript'); ?>

    <script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>

    <script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>

    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>

    <script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<?php $__env->stopSection(); ?>







<?php $__env->startSection('title', 'Create New Service | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>





 <!-- partial -->

 <div class="main-panel">        

        <div class="content-wrapper">

          <div class="page-header">

            <h3 class="page-title">

                Add Payment

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="<?php echo e(route('suppliers.index')); ?>">Supplers</a></li>

                <li class="breadcrumb-item active" aria-current="page">Add Payment</li>

                </ol>

            </nav>

          </div>

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



          <?php if(Session::has('error')): ?>

            <div class="alert alert-danger alert-dismissible">

                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                <?php echo e(Session::get('error')); ?>


            </div>

          <?php endif; ?>

          

          <div class="row">

            <div class="col-md-12 grid-margin stretch-card">

              <div class="card">

                <div class="card-body">

                  <h4 class="card-title">Add Pending Payment</h4>

                  <form class="forms-sample" action="<?php echo e(route('supplier-payments.store')); ?>" method="post">

                  <?php echo csrf_field(); ?>

                    <input type="hidden" class="form-control" name="supplier_id" value="<?php echo e($supplier->id); ?>" required>

                    <div class="form-group">

                      <label for="exampleInputUsername1">Amount</label>

                      <input type="number" class="form-control" name="amount" id="exampleInputUsername1" placeholder="Enter Amount" required>

                    </div>


                    <div class="form-group">

                      <div class="form-group mt-2">

                        <label for="exampleInputUsername1">Note</label>

                        <textarea class="form-control" id="exampleTextarea1" name="note" rows="4" maxlength="160"></textarea>

                      </div>

                    </div>


                    <button type="submit" class="btn btn-primary mr-2">Submit</button>

                    <a class="btn btn-light" href="<?php echo e(route('suppliers.balance_summary', $supplier->id)); ?>">Cancel</a>

                  </form>

                </div>

              </div>

            </div>

          </div>

        </div>

</div>

      

<?php $__env->stopSection(); ?>






<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/suppliers/add_payment.blade.php ENDPATH**/ ?>