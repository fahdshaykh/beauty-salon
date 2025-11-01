<?php $__env->startSection('pagescript'); ?>

    <script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>

    <script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>

    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>

    <script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<?php $__env->stopSection(); ?>







<?php $__env->startSection('title', 'Create New Service | '.Session::get('site_name')); ?>

<style>
/* reusable pill‑style radios */
.radio-toggle{display:none;}                              /* hide native input   */
.radio-pill{
    display:inline-block;
    padding:.45rem 1.25rem;
    margin:0 .35rem .5rem 0;
    border:2px solid #ced4da;
    border-radius:.5rem;
    font-weight:500;
    cursor:pointer;
    transition:all .15s ease-in-out;
}
.radio-toggle:checked + .radio-pill{
    background:#0d6efd;                                   /* Bootstrap “primary” */
    color:#fff;
    border-color:#0d6efd;
    box-shadow:0 0 0 .25rem rgba(13,110,253,.25);
}
.radio-pill:hover{border-color:#0d6efd;}
</style>

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

                    <div class="form-group">

                      <label for="employees">Assign Employees</label>
                      
                      <select name="employees[]" class="form-control js-example-basic-multiple w-100" multiple="multiple" required>
                          <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option value="<?php echo e($employee->id); ?>"><?php echo e($employee->name); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>

                    </div>

                    <div class="form-group">
    <label class="d-block mb-2 font-weight-bold">Service Type</label>

    <input type="radio" name="risk_status" value="risky" id="risky"
        class="radio-toggle">
    <label for="risky" class="radio-pill">Risky</label>

    <input type="radio" name="risk_status" value="not_risky" id="not_risky"
        class="radio-toggle" checked>
    <label for="not_risky" class="radio-pill">Not Risky</label>
</div>

<div class="form-group">
    <label class="d-block mb-2 font-weight-bold">Price Type</label>

    <input type="radio" name="price_type" value="fixed" id="fixed"
        class="radio-toggle" checked>
    <label for="fixed" class="radio-pill">Fixed</label>

    <input type="radio" name="price_type" value="modify" id="modify"
        class="radio-toggle">
    <label for="modify" class="radio-pill">Modify Able</label>
</div>

                    <div class="form-group">

                      <div class="input-group">

                        <div class="input-group-prepend">

                          <span class="input-group-text bg-primary text-white"><i class="fas fa-dollar-sign"></i></span>

                        </div>

                        <input type="text" name="price" value="0" class="form-control" aria-label="Amount (to the nearest dollar)">

                      </div>

                    </div>

                    <div class="form-group">

                      <div class="col" style="margin-left: -8px;">

                          <input type='text' class="color-picker" name="service_color" value="#ffe74c" />

                      </div>

                    </div>


                    <div class="form-group">

                      <div class="form-group mt-2">

                        <label for="exampleInputUsername1">Description</label>

                        <textarea class="form-control" id="exampleTextarea1" name="description" rows="4" maxlength="160"></textarea>

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

</div>

      

<?php $__env->stopSection(); ?>






<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/services/create.blade.php ENDPATH**/ ?>