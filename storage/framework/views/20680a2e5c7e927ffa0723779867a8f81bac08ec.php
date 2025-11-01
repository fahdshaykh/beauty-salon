

<?php $__env->startSection('title', 'Daily Work Hours | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>
<div class="main-panel">          
  <div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title">Daily Work Hours</h3>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Pages</a></li>
          <li class="breadcrumb-item active" aria-current="page">Daily Work Hours</li>
        </ol>
      </nav>
    </div>
    
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Employee Work Hours</h4>
            
            <form action="<?php echo e(route('work_hours')); ?>" method="GET" class="mb-4">
              <div class="form-group">
                <label for="date_from">From:</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>" onchange="this.form.submit()">
              </div>
              <div class="form-group">
                <label for="date_to">To:</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>" onchange="this.form.submit()">
              </div>
              <div class="form-group">
                <label for="user_id">Select Employee:</label>
                <select name="user_id" id="user_id" class="form-control" onchange="this.form.submit()">
                  <option value="">All Employees</option>
                  <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                      <?php echo e($user->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </form>
            
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr class="bg-primary text-white">
                    <th>#</th>
                    <th>Employee Name</th>
                    <th>Date</th>
                    <th>Entry Time</th>
                    <th>Exit Time</th>
                    <th>overtime minutes</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__currentLoopData = $workHours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $workHour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  
                  <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td><?php echo e($workHour->user->name); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($workHour->check_in)->format('Y-m-d')); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($workHour->check_in)->format('H:i')); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($workHour->check_out)->format('H:i')); ?></td>
                    <td><?php echo e($workHour->overtime_minutes); ?></td>
                  </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('pagescript'); ?>
<script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>
<script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>
<script src="<?php echo e(asset('js/select2.js')); ?>"></script>
<script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>
<script>
    document.getElementById('date_from').addEventListener('change', function() {
        this.form.submit();
    });
    document.getElementById('date_to').addEventListener('change', function() {
        this.form.submit();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/employee_attendance/work_hours.blade.php ENDPATH**/ ?>