<?php $__env->startSection('pagescript'); ?>



    <script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>



    <script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>



    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>



    <script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>



<?php $__env->stopSection(); ?>















<?php $__env->startSection('title', 'Edit Expense | '.Session::get('site_name')); ?>







<?php $__env->startSection('content'); ?>











 <!-- partial -->



 <div class="main-panel">        



        <div class="content-wrapper">



          <div class="page-header">



            <h3 class="page-title">



                Update Expense



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                <li class="breadcrumb-item"><a href="<?php echo e(url('expenses')); ?>">Expenses</a></li>



                <li class="breadcrumb-item active" aria-current="page">Edit Expense</li>



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



          <div class="row">



            <div class="col-md-6 grid-margin stretch-card">



              <div class="card">



                <div class="card-body">



                  <h4 class="card-title">Edit Expense</h4>



                  <form class="forms-sample" action="<?php echo e(url('expenses/update',$expense->id)); ?>" method="post">



                  <?php echo csrf_field(); ?>



                  <div class="form-group">



                      <label>Select Category</label>



                      <select id="expense_type" name="expense_type" class="js-example-basic-single w-100">



                        <?php $__currentLoopData = $expense_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                            <option value="<?php echo e($expense_type->id); ?>" <?php if($expense->expense_type == $expense_type->id): ?> selected <?php endif; ?> ><?php echo e($expense_type->type); ?></option>



                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



                      </select>



                    </div>



                    <div class="form-group">



                      <label>Select Payment Method</label>



                      <select id="payment_method" name="payment_method" class="js-example-basic-single w-100">



                        <?php $__currentLoopData = $payment_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment_method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                            <option value="<?php echo e($payment_method->id); ?>" <?php if($expense->payment_method == $payment_method->id): ?> selected <?php endif; ?> ><?php echo e($payment_method->method); ?></option>



                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



                      </select>



                    </div>



                    <div class="form-group">



                      <div id="datepicker-popup-new" class="input-group date datepicker">



                        <input type="text" name="payable" value="<?php echo e($expense->payable); ?>" class="form-control" required>



                        <span class="input-group-addon input-group-append border-left">



                          <span class="far fa-calendar input-group-text"></span>



                        </span>



                      </div>



                    </div>



                    <div class="form-group">



                      <label for="exampleInputUsername1">Expense Amount:</label>



                      <div class="input-group">



                        <div class="input-group-prepend">



                          <span class="input-group-text bg-primary text-white">$</span>



                        </div>



                        <input type="text"  name="amount" value="<?php echo e($expense->amount); ?>" class="form-control" aria-label="Amount (to the nearest dollar)" required>



                      </div>



                    </div>



                    <div class="form-group">



                        <label for="exampleTextarea1">Expense Description</label>



                        <textarea class="form-control" id="exampleTextarea1" name="description" rows="4"><?php echo e($expense->description); ?></textarea>



                    </div>



                    <button type="submit" class="btn btn-primary mr-2">Submit</button>



                    <a class="btn btn-light" href="<?php echo e(url('expenses')); ?>">Cancel</a>



                  </form>



                </div>



              </div>



            </div>



          </div>



        </div>



      



<?php $__env->stopSection(); ?>








<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/expenses/edit.blade.php ENDPATH**/ ?>