



<?php $__env->startSection('title', 'Expense Type Create | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Create Expense Type

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?php echo e(url('expense-types.index')); ?>">Expense Types</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Create Type</li>

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

            <div class="col-md-6 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title">Expense Type Form</h4>

                        <form class="forms-sample" action="<?php echo e(url('expense-types/store')); ?>" method="post">

                            <?php echo csrf_field(); ?>

                            <?php echo $__env->make('expense_types.expense_type_form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/expense_types/create.blade.php ENDPATH**/ ?>