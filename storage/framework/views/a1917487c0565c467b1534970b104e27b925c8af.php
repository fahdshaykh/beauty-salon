

<?php $__env->startSection('title', 'Create Role | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Create Role</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('roles.index')); ?>">Roles</a></li>
                    <li class="breadcrumb-item active">Create Role</li>
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
                        <form action="<?php echo e(route('roles.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <div class="form-group">
                                <label>Role Name</label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. Manager">
                            </div>

                            <div class="form-group">
                                <label>Permissions</label>
                                <div class="row">
                                    <?php $__currentLoopData = \Spatie\Permission\Models\Permission::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" name="permissions[]" value="<?php echo e($permission->name); ?>" class="form-check-input">
                                                    <?php echo e(ucfirst(str_replace('_', ' ', $permission->name))); ?>

                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Create Role</button>
                            <a href="<?php echo e(route('roles.index')); ?>" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/roles/create.blade.php ENDPATH**/ ?>