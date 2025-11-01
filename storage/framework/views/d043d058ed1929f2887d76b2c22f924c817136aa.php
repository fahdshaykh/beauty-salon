

<?php $__env->startSection('title', 'Add Employee Attendance | '.Session::get('site_name')); ?>

<?php $__env->startSection('pagescript'); ?>
    <script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>
    <script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>
    <script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            $('#status').change(function() {
                if ($(this).val() === 'A') {
                    $('#reasonField').show();
                } else {
                    $('#reasonField').hide();
                    $('#reason').val('');
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="page-header">
            <h3 class="page-title">Add Employee Attendance</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(url('attendance')); ?>">Attendance</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Attendance</li>
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
                        <form class="forms-sample" action="<?php echo e(url('attendance/store')); ?>" method="post">
                            <?php echo csrf_field(); ?>

                            
                            <!-- User Dropdown -->
                             
                             <div class="form-group">
                                <label>Select Employee</label>
                                <select id="user_id" name="user_id" class="js-example-basic-single w-100" required>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            

                            <!-- Attendance Status Dropdown -->
                            <div class="form-group">
                                <label>Status</label>
                                <select id="status" name="status" class="js-example-basic-single w-100" required>
                                    <option value="P">Present</option>
                                    <option value="A">Absent</option>
                                </select>
                            </div>

                            <!-- Conditional Reason Field (for Absent only) -->
                            <div class="form-group" id="reasonField" style="display: none;">
                                <label>Reason (Required if Absent)</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" maxlength="160"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary mr-2">Submit</button>
                            <a class="btn btn-light" href="<?php echo e(url('attendance')); ?>">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/employee_attendance/create.blade.php ENDPATH**/ ?>