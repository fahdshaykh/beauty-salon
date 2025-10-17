

<?php $__env->startSection('title', 'Expense Types | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                All Expense Types
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(url('expense-types')); ?>">Expense Types</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Expense Type Listing</li>
                </ol>
            </nav>
        </div>

        <div class="alert alert-success" style="display:none;" id="status-success"></div>

        <?php if(Session::has('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?php echo e(Session::get('success')); ?>
        </div>

        <?php elseif(Session::has('destroy')): ?>
        <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?php echo e(Session::get('destroy')); ?>
        </div>
        <?php endif; ?>

        <?php if(Session::has('update')): ?>
        <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?php echo e(Session::get('update')); ?>
        </div>
        <?php endif; ?>

        <?php $expense_types_array = array(8,3); ?>
        <?php $expense_types_edit = implode(',', $expense_types_array); ?>
        <?php $expense_types_array = array(8,4); ?>
        <?php $expense_types_delete = implode(',', $expense_types_array); ?>
        <div class="col-lg-6 grid-margin stretch-card">
      
              <div class="card">
              <!-- <a class="btn btn-primary btn-fw <?php echo e(Request::is('expenses*') ? 'active' : ''); ?>" href="<?php echo e(url('expense-types/creat')); ?>"> Add Expense Type</a> -->
                <div class="card-body">
                  <h4 class="card-title" style="float:right;"><a class="btn btn-primary btn-fw" href="<?php echo e(url('expense-types/create')); ?>"> Add Expense Type</a></h4>
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Sr No#</th>
                          <th>Type</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php $num = 1; ?>
                    
                        <?php $__empty_1 = true; $__currentLoopData = $expense_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($num); ?></td>
                            <td><?php echo e($expense_type->type); ?></td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" id="user-<?php echo e($expense_type->id); ?>" onclick="changeCategoryStatus(event.target, <?php echo e($expense_type->id); ?>);" <?php echo e(($expense_type->is_active) ? 'checked' : ''); ?>>
                                    <span class="slider round"></span>
                                </label></td>
                            <td>
                            <?php if(in_array($expense_types_edit, session()->get('all_rp')['0'])): ?>
                                <a href="<?php echo e(url('expense-types/edit',$expense_type->id)); ?>">
                                    <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>                          
                                </a>
                            <?php endif; ?>
                           
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan='3'> No Data Available</td></tr>
                        <?php $num++; ?>
                        <?php endif; ?>
                 
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
    </div>
    <?php $__env->stopSection(); ?>
    <script>

function changeCategoryStatus(_this, id) {

 

    var status = $(_this).prop('checked') == true ? 1 : 0;

    $.ajax({

        url: "<?php echo e(url('expense-type-status')); ?>",

        type: 'GET',

        data: {

            id: id,

            status: status 

        },

        success: function (result) {

          $("#status-success").show();

          $("#status-success").html(result.success);

          setTimeout(function() {

            $("#status-success").hide()

          }, 3000);

        }

    });

}



</script>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\projects\bsaloon\resources\views/expense_types/index.blade.php ENDPATH**/ ?>