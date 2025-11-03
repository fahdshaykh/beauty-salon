

<?php $__env->startSection('pagescript'); ?>

<script src="<?php echo e(asset('js/select2.js')); ?>"></script>

<script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<?php $__env->stopSection(); ?>





<?php $__env->startSection('title', 'Create Expense Reports | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Create Reports

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?php echo e(url('expense-reports')); ?>">Reports</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Expense Report</li>

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

          <div class="col-md-12 grid-margin stretch-card">

            <div class="card">

              <div class="card-body">

                <h4 class="card-title">Expense Report</h4>

                <p class="card-description">

                  In order to get report please select between dates <code>From Date</code> & <code>To Date.</code> You can also specifies with your Expense Type!

                </p>

                <form class="form-inline" action="<?php echo e(url('record/expense')); ?>" method="get" id="expense-filters">

                  <div class="input-group">

                    <div id="datepicker-popup-new" class="input-group date datepicker">

                      <input type="text" name="start_date" class="form-control">

                      <span class="input-group-addon input-group-append border-left">

                        <span class="far fa-calendar input-group-text"></span>

                      </span>

                    </div>

                  </div>

                  <div class="input-group">

                    <div id="datepicker-popup-new2" class="input-group date datepicker">

                      <input type="text" name="end_date" class="form-control">

                      <span class="input-group-addon input-group-append border-left">

                        <span class="far fa-calendar input-group-text"></span>

                      </span>

                    </div>

                  </div>

                  

                    <div class="form-group">

                      <select id="expense_type" name="expense_type" class="form-control js-example-basic-single w-100" style="width:100%">

                        <option value="no" selected>Select Expense Type</option>

                        <?php $__currentLoopData = $expense_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option value="<?php echo e($expense_type->id); ?>"><?php echo e($expense_type->type); ?></option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                      </select>

                    </div>

                    <div class="form-group">

                      <select id="payment_method" name="payment_method" class="form-control js-example-basic-single w-100" style="width:100%">

                        <option value="no" selected>Select Payment Method</option>

                        <?php $__currentLoopData = $payment_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment_method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option value="<?php echo e($payment_method->id); ?>"><?php echo e($payment_method->method); ?></option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                      </select>

                    </div>

                    <button type="submit" value="submit" class="btn btn-primary mb-2">Submit</button>


                </form>

              </div>

            </div>

          </div>

        </div>
        <div class="card" id="report">
            
          </div>
    </div>

</div>



<script>
  $(document).ready(function(){
    $("#expense-filters").submit(function(event){
      event.preventDefault();
      $.ajax({
          url:"<?php echo e(url('record/expense')); ?>",
          type:"GET",
          data: $('form#expense-filters').serialize(),
          success:function(response){
            //console.log(response.view);
            $("#report").empty();
            $("#report").append(response.view);        
          }
        });  
          
    });
});

  

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/reports/expense_index.blade.php ENDPATH**/ ?>