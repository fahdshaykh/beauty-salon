


<?php $__env->startSection('title', 'Edit Role | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Edit Role: <?php echo e(ucfirst($role->name)); ?></h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('roles.index')); ?>">Roles</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="<?php echo e(route('roles.update', $role)); ?>" method="POST">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                            <div class="form-group">
                                <label>Role Name</label>
                                <input type="text" name="name" value="<?php echo e(old('name', $role->name)); ?>" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Permissions</label>
                                <div class="row">
                                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module => $perms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-3">
                                            <?php $__currentLoopData = $perms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="permissions[]" value="<?php echo e($perm->name); ?>"
                                                               <?php echo e(in_array($perm->name, $rolePermissions) ? 'checked' : ''); ?>

                                                               class="form-check-input">
                                                        <?php echo e(ucfirst(str_replace('_', ' ', $perm->name))); ?>

                                                    </label>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Update Role</button>
                            <a href="<?php echo e(route('roles.index')); ?>" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/roles/edit.blade.php ENDPATH**/ ?>