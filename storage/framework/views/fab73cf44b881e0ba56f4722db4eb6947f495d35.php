

<?php $__env->startSection('title', 'Create Category | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Create Category
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('categories.index')); ?>">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Category</li>
                </ol>
            </nav>
        </div>
        <?php if(Session::has('error')): ?>
        <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?php echo e(Session::get('error')); ?>
        </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Category Form</h4>
                        <form class="forms-sample" action="<?php echo e(route('categories.store')); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <?php echo $__env->make('categories.category_form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/categories/create.blade.php ENDPATH**/ ?>