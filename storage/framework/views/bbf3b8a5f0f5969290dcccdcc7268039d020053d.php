<?php $__env->startSection('pagescript'); ?>

    <script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>

    <script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>

    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>

    <script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<?php $__env->stopSection(); ?>







<?php $__env->startSection('title', 'Edit Service | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>





 <!-- partial -->

 <div class="main-panel">        

        <div class="content-wrapper">

          <div class="page-header">

            <h3 class="page-title">

                Update Services

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="<?php echo e(route('services.index')); ?>">Services</a></li>

                <li class="breadcrumb-item active" aria-current="page">Edit Service</li>

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

                  <h4 class="card-title">Update Service</h4>

                  <form class="forms-sample" action="<?php echo e(route('services.update',$services->id)); ?>" method="post">

                  <?php echo csrf_field(); ?>

                  <?php echo method_field('PUT'); ?>

                  <div class="form-group">

                      <label>Select Category</label>

                      <select id="category_id" name="category_id" class="js-example-basic-single w-100">

                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option value="<?php echo e($category->id); ?>" <?php if($services->category_id == $category->id): ?> selected <?php endif; ?> ><?php echo e($category->name); ?></option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                      </select>

                    </div>

                    <div class="form-group">

                      <label for="exampleInputUsername1">Service Name:</label>

                      <input type="text" class="form-control" value="<?php echo e($services->name); ?>" name="name" id="exampleInputUsername1" placeholder="Enter Service Name" required>

                    </div>

                    <div class="form-group">

                    <label for="employees">Assign Employees</label>

                    <select name="employees[]" class="form-control js-example-basic-multiple w-100" multiple="multiple" required>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($employee->id); ?>" 
                            <?php echo e(in_array($employee->id, $services->employees->pluck('id')->toArray()) ? 'selected' : ''); ?>>
                            <?php echo e($employee->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    </div>

                    <div class="form-group">

                      <label for="exampleInputUsername1">Service Price:</label>

                      <div class="input-group">

                        <div class="input-group-prepend">

                          <span class="input-group-text bg-primary text-white"><i class="fas fa-dollar-sign"></i></span>

                        </div>

                        <input type="text"  name="price" value="<?php echo e($services->price); ?>" class="form-control" aria-label="Amount (to the nearest dollar)">

                      </div>

                    </div>

                    <div class="form-group">

                      <div class="col" style="margin-left: -8px;">

                          <input type='text' class="color-picker" name="service_color" value="<?php echo e($services->service_color); ?>" />

                      </div>

                    </div>

                    <div class="form-group">

                        <div class="form-group mt-2">

                          <label for="exampleInputUsername1">Description</label>

                          <textarea class="form-control" id="exampleTextarea1" name="description" rows="4" maxlength="160"> <?php echo e($services->description); ?> </textarea>

                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary mr-2">Submit</button>

                    <a class="btn btn-light" href="<?php echo e(route('services.index')); ?>">Cancel</a>

                  </form>

                </div>

              </div>

            </div>

          </div>

        </div>

      

<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/services/edit.blade.php ENDPATH**/ ?>