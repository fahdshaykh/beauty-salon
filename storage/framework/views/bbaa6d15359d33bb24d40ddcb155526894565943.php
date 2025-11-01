<?php $__env->startSection('pagescript'); ?>
<script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>
<script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>
<script src="<?php echo e(asset('js/select2.js')); ?>"></script>
<script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('title', 'Edit Profile | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>


<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Profile
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(url('/home')); ?>">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
                </ol>
            </nav>
        </div>
        <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo e($error); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title"><?php echo e($user->name); ?></h4>
                    <form class="forms-sample" action="<?php echo e(url('user/profile-update', Auth::user()->id)); ?>" method="post" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                      <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" value="<?php echo e($user->name); ?>" id="name" placeholder="Name">
                      </div>
                      <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo e($user->email); ?>" id="email" placeholder="Email">
                      </div>
                      <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                      </div>
                      <div class="form-group">
                        <label>File upload</label>
                        <input type="file" name="image" class="file-upload-default">
                        <div class="input-group col-xs-12">
                          <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                          <span class="input-group-append">
                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                          </span>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-primary mr-2">Submit</button>
                      <button class="btn btn-light">Cancel</button>
                    </form>
                  </div>
                </div>
              </div>
        </div>
    </div>

    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/users/profile.blade.php ENDPATH**/ ?>