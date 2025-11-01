<?php $__env->startSection('pagescript'); ?>



<script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>



<script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>



<script src="<?php echo e(asset('js/select2.js')); ?>"></script>



<script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>



<?php $__env->stopSection(); ?>















<?php $__env->startSection('title', 'Create User | '.Session::get('site_name')); ?>







<?php $__env->startSection('content'); ?>











<!-- partial -->



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



               New Inventory Purchase



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="<?php echo e(url('/users')); ?>">Inventories</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Inventory Purchase</li>



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



            <div class="col-12 grid-margin stretch-card">



                <div class="card">



                  <div class="card-body">



                    <h4 class="card-title">Inventory</h4>



                    <form class="forms-sample" action="<?php echo e(route('inventories.store')); ?>" method="post">



                    <?php echo csrf_field(); ?>



                      <div class="form-group">



                        <label for="name">Name</label>



                        <input type="text" class="form-control" value="<?php echo e(old('name')); ?>" name="name" id="name" placeholder="Name">



                      </div>



                      <div class="form-group">



                      <label for="name">Low Alert</label>



                      <input type="text" class="form-control" value="<?php echo e(old('low_alert')); ?>" name="low_alert" id="low_alert" placeholder="Minimum Alert">



                      </div>

                      <div class="form-group">



                      <label for="name">Max Alert</label>



                      <input type="text" class="form-control" value="<?php echo e(old('max_alert')); ?>" name="max_alert" id="max_alert" placeholder="Maximum Alert">



                      </div>
                      

                      

                      <div class="form-group">



                            <label for="exampleTextarea1">Description</label>



                            <textarea class="form-control" id="exampleTextarea1" name="description" rows="4" maxlength="160"></textarea>



                      </div>



                      <button type="submit" class="btn btn-primary mr-2">Submit</button>



                      <a class="btn btn-light" href="<?php echo e(route('inventories.index')); ?>">Cancel</a>



                    </form>



                  </div>



                </div>



              </div>



        </div>



    </div>







    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/inventories/create.blade.php ENDPATH**/ ?>