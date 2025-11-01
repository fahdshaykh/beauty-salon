

<?php $__env->startSection('title', 'Roles | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">All Roles</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('roles.index')); ?>">Roles</a></li>
                    <li class="breadcrumb-item active">Role Listing</li>
                </ol>
            </nav>
        </div>

        <!-- Success Messages -->
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('destroy')): ?>
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <?php echo e(session('destroy')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('update')): ?>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <?php echo e(session('update')); ?>

            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <!-- Pagination Dropdown -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <form method="GET" action="<?php echo e(route('roles.index')); ?>">
                            <?php $__currentLoopData = request()->except(['page', 'per_page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <select name="per_page" onchange="this.form.submit()" class="form-control">
                                <?php $__currentLoopData = [10, 25, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($option); ?>" <?php echo e(request('per_page', 10) == $option ? 'selected' : ''); ?>>
                                        <?php echo e($option); ?> per page
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </form>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-3 text-right">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create roles')): ?>
                            <a href="<?php echo e(route('roles.create')); ?>" class="btn btn-primary btn-sm">
                                Add New Role
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Roles Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sr.No #</th>
                                <th>Role Name</th>
                                <th>Permissions</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php if($role->name !== 'admin'): ?> <!-- Optional: hide admin -->
                                <tr>
                                    <td><?php echo e($roles->firstItem() + $index); ?></td>
                                    <td><strong><?php echo e(ucfirst($role->name)); ?></strong></td>
                                    <td>
                                        <?php $__currentLoopData = $role->permissions->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge badge-info mr-1">
                                                <?php echo e(ucfirst(str_replace('_', ' ', $perm->name))); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($role->permissions->count() > 3): ?>
                                            <span class="text-muted">+<?php echo e($role->permissions->count() - 3); ?> more</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($role->created_at->format('m-d-Y')); ?></td>
                                    <td>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit roles')): ?>
                                            <a href="<?php echo e(route('roles.edit', $role)); ?>" class="text-warning mr-2">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete roles')): ?>
                                            <?php if($role->name !== 'admin'): ?>
                                                <form action="<?php echo e(route('roles.destroy', $role)); ?>" method="POST" style="display:inline">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="border-0 bg-transparent text-danger"
                                                            onclick="return confirm('Delete <?php echo e($role->name); ?> role?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No roles found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="row mt-4">
                    <div class="col-12">
                        <p class="text-muted">
                            Showing <?php echo e($roles->firstItem()); ?> to <?php echo e($roles->lastItem()); ?> of <?php echo e($roles->total()); ?> entries
                        </p>
                        <?php echo e($roles->appends(request()->query())->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/roles/index.blade.php ENDPATH**/ ?>