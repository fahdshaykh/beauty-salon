<?php $__env->startSection('pagescript'); ?>

<link rel="stylesheet" href="<?php echo e(asset('vendors/summernote/dist/summernote-bs4.css')); ?>">

<script src="<?php echo e(asset('vendors/summernote/dist/summernote-bs4.min.js')); ?>"></script>

<script src="<?php echo e(asset('js/editorDemo.js')); ?>"></script>

<?php $__env->stopSection(); ?>







<?php $__env->startSection('title', 'Send Quotation | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Send Quote

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?php echo e(url('quote-mail')); ?>">Quote</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Send Quote</li>

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



        <?php if(Session::has('success')): ?>

        <div class="alert alert-success alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            <?php echo e(Session::get('success')); ?>

        </div>

        <?php endif; ?>



        <div class="row">

            <div class="col-12 grid-margin">

                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title">Quote Form</h4>

                        <form class="forms-sample" action="<?php echo e(url('quote-mail')); ?>" method="post">

                            <?php echo csrf_field(); ?>

                            <div class="form-group">

                            <label for="exampleInputUsername1">Email</label>

                            <input type="email" name="email" value="" class="form-control" placeholder="Enter Email Address" required/>

                            </div>

                            <div class="form-group">

                                <textarea id="summernoteExample" name="message"></textarea>

                            </div>

                            <button type="submit" class="btn btn-primary mr-2 mt-3">Send Quote</button>

                            <!-- <a class="btn btn-light" href="#">Cancel</a> -->

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/quotes/create.blade.php ENDPATH**/ ?>