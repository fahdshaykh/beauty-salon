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

                New User

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?php echo e(url('/users')); ?>">Users</a></li>

                    <li class="breadcrumb-item active" aria-current="page">New User</li>

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

                    <h4 class="card-title">Create User</h4>

                    <form class="forms-sample" action="<?php echo e(route('user.add')); ?>" method="post" enctype="multipart/form-data">

                    <?php echo csrf_field(); ?>

                      <div class="form-group">

                        <label for="name">Name</label>

                        <input type="text" class="form-control" value="<?php echo e(old('name')); ?>" name="name" id="name" placeholder="Name">

                      </div>

                      <div class="form-group">

                        <label for="email">Email</label>

                        <input type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>" id="email" placeholder="Email">

                      </div>

                      <div class="form-group">

                        <label for="password">Password</label>

                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">

                      </div>
                      <div class="form-group">

                        <label for="userphone">User Phone</label>

                        <input type="text" class="form-control" name="phone" id="userphone" placeholder="Enter Phone Number" maxLength="12">

                      </div>
                      <div class="form-group">

                        <label for="Address">User Address</label>

                        <input type="text" class="form-control" name="address" id="address" placeholder="Enter Address">

                      </div>

                      <div class="form-group">

                        <label for="exampleSelectGender">Role</label>

                        <select class="form-control" name="role_id" id="exampleSelectGender">

                        <option value="">Select Role</option>

                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                          <option value="<?php echo e($role->id); ?>"><?php echo e($role->role); ?></option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>

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

                      <a class="btn btn-light" href="<?php echo e(route('users.index')); ?>">Cancel</a>

                    </form>

                  </div>

                </div>

              </div>

        </div>

    </div>



    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/users/create.blade.php ENDPATH**/ ?>