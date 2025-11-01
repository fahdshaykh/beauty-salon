

<?php $__env->startSection('title', 'Create Referred Source | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Create Referred Source</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('referred_customers.index')); ?>">Referred Sources</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
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
                        <h4 class="card-title">New Referred Source</h4>
                        <form class="forms-sample" action="<?php echo e(route('referred_customers.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <div class="form-group">
                                <label for="referred_by">Source name</label>
                                <input type="text" class="form-control" id="referred_by" name="referred_by" value="<?php echo e(old('referred_by')); ?>" placeholder="e.g. Google">
                            </div>

                            <div class="form-group">

                                <label for="employees">Status</label>
                                
                                <select name="is_active" class="form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">In-active</option>
                                </select>

                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Create</button>
                            <a class="btn btn-light" href="<?php echo e(route('referred_customers.index')); ?>">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/referred_customers/create.blade.php ENDPATH**/ ?>