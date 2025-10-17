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

                Create Services

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="<?php echo e(route('services.index')); ?>">Services</a></li>

                <li class="breadcrumb-item active" aria-current="page">Create Service</li>

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

            <div class="col-md-6 grid-margin stretch-card">

              <div class="card">

                <div class="card-body">

                  <h4 class="card-title">Create Service</h4>

                  <form class="forms-sample" action="<?php echo e(route('services.store')); ?>" method="post">

                  <?php echo csrf_field(); ?>

                    <div class="form-group">

                      <label>Select Category</label>

                      <select id="category_id" name="category_id" class="js-example-basic-single w-100" required>

                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                      </select>

                    </div>

                    <div class="form-group">

                      <label for="exampleInputUsername1">Service Name</label>

                      <input type="text" class="form-control" name="name" id="exampleInputUsername1" placeholder="Enter Service Name" required>

                    </div>
                    <label>Price Type</label>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="price_type" value="fixed" id="fixed">
                            Fixed
                        </label>
                        <label>
                            <input type="radio" name="price_type" value="modify_able" id="modify_able">
                            Modify Able
                        </label>
                    </div>

                    <div class="form-group">

                      <div class="input-group">

                        <div class="input-group-prepend">

                          <span class="input-group-text bg-primary text-white"><i class="fas fa-pound-sign"></i></span>

                        </div>

                        <input type="text" name="price" value="0" class="form-control" aria-label="Amount (to the nearest dollar)">

                      </div>

                    </div>

                    <div class="form-group">

                      <div class="col" style="margin-left: -8px;">

                          <input type='text' class="color-picker" name="service_color" value="#ffe74c" />

                      </div>

                    <!-- </div>

                    <div class="form-group">

                        <label for="exampleTextarea1">Customer Message (characters limit 150)</label>

                        <textarea class="form-control" id="exampleTextarea1" name="customer_message" rows="4" maxlength="150" placeholder="e.g. Dear {customer name}, Your {services} services on {date} has been booked at {time}. For further information please contact us."></textarea>

                    </div> -->

                    <br>

                    <button type="submit" class="btn btn-primary mr-2">Submit</button>

                    <a class="btn btn-light" href="<?php echo e(route('services.index')); ?>">Cancel</a>

                  </form>

                </div>

              </div>

            </div>

          </div>

        </div>

</div>

      

<?php $__env->stopSection(); ?>






<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\projects\bsaloon\resources\views/services/create.blade.php ENDPATH**/ ?>