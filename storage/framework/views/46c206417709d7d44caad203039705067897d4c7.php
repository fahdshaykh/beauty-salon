
<?php $__env->startSection('title', 'Create New Supplier | ' . Session::get('site_name')); ?>

<?php $__env->startSection('pagescript'); ?>
    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Create Supplier</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('suppliers.index')); ?>">Suppliers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Supplier</li>
                </ol>
            </nav>
        </div>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Supplier Details</h4>
                        <form class="forms-sample" action="<?php echo e(route('suppliers.store')); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label for="name">Supplier Name</label>
                                <input type="text" class="form-control" name="name" value="<?php echo e(old('name')); ?>" required placeholder="Enter Supplier Name">
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" class="form-control" name="phone" value="<?php echo e(old('phone')); ?>" placeholder="Phone">
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>" placeholder="Email">
                            </div>

                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" name="address" rows="3" placeholder="Address"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Submit</button>
                            <a class="btn btn-light" href="<?php echo e(route('suppliers.index')); ?>">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/suppliers/create.blade.php ENDPATH**/ ?>