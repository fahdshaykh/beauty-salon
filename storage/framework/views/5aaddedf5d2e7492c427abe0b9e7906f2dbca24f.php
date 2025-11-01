<?php $__env->startSection('pagescript'); ?>

    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>

    <script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<?php $__env->stopSection(); ?>





<?php $__env->startSection('title', 'Booking Reports by Date | '.Session::get('site_name')); ?>



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

                    <li class="breadcrumb-item"><a href="<?php echo e(url('/home')); ?>">Reports</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Create Reports</li>

                </ol>

            </nav>

        </div>

        <?php if(Session::has('error')): ?>

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            <?php echo e(Session::get('error')); ?>


        </div>

        <?php endif; ?>

          <div class="col-12 grid-margin stretch-card">

            <div class="card">

              <div class="card-body">

                <h4 class="card-title">Booking Reports</h4>

                <p class="card-description">

                  In order to get report please select between dates <code>From Date</code> & <code>To Date.</code> You can also specifies with your category and service!

                </p>

                <form class="form-inline" action="<?php echo e(url('record/find')); ?>" method="get">

                  <div class="col-md-12 row">

                    <div class="input-group mb-2 mr-sm-2">

                      <div id="datepicker-popup-new" class="input-group date datepicker">

                        <input type="text" name="start_date" class="form-control" required>

                        <span class="input-group-addon input-group-append border-left">

                          <span class="far fa-calendar input-group-text"></span>

                        </span>

                      </div>

                    </div>

                    <div class="input-group mb-2 mr-sm-2">

                      <div id="datepicker-popup-new2" class="input-group date datepicker">

                        <input type="text" name="end_date" class="form-control" required>

                        <span class="input-group-addon input-group-append border-left">

                          <span class="far fa-calendar input-group-text"></span>

                        </span>

                      </div>

                    </div>

                  </div>

                  <div class="col-md-12 row">

                    <div class="input-group mb-2 mr-sm-2">

                      <div class="form-group">

                        <select class="form-control form-control-lg" name="category_id" id="category_id">

                          <option value="no">All Categories</option>

                          <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>

                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>

                      </div>

                    </div>

                    <div class="input-group mb-2 mr-sm-2">

                      <div class="form-group">

                        <select class="form-control form-control-lg" name="service_id" id="service_id">

                          <option value="no">All Service</option>

                          <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option value="<?php echo e($service->id); ?>"><?php echo e($service->name); ?></option>

                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>

                      </div>

                    </div>

                  

                    <div class="input-group mb-2 mr-sm-2">

                      <div class="form-group">

                        <select class="form-control form-control-lg" name="status_id" id="status_id">

                          <option value="no">All Status</option>

                          <option value="1">Booked</option>

                          <option value="2">In-Progress</option>

                          <option value="3">Completed</option>

                          <option value="4">Cancelled</option>
                          <!-- <option value="5">Survey</option> -->
                        </select>

                      </div>

                    </div>

                    <div class="input-group mb-2 mr-sm-2">

                      <div class="form-group">

                        <select class="form-control form-control-lg" name="payment_instrument" id="payment_instrument">

                          <option value="no">Payment Method</option>

                          <option value="cash">Cash</option>

                          <option value="paypal">PayPal</option>
<!--                           
                          <option value="card">Card</option>

                          <option value="invoice">Invoice</option>

                          <option value="cheque">Cheque</option>

                          <option value="bacs">BACS</option> -->

                        </select>

                      </div>

                    </div>

                  </div>

                  <input type="submit" value="Submit" class="btn btn-primary mb-2">

                </form>

              </div>

            </div>

          </div>

          <div class="card">

            <div class="card-body">

                <h4 class="card-title"></h4>

                <div class="row">

                    <div class="col-12">

                        <div class="table-responsive">

                            <table id="order-listing" class="table table-striped">

                                <thead>

                                    <tr>

                                    

                                        <th>User</th>

                                        <th>Customer</th>

                                        <th style="width: 10%;">Services</th>

                                        <th>Price</th>

                                        <th>Pay Method</th>

                                        <th>Book Date</th>

                                        <th>Address</th>

                                        <th>Staus</th>

                                     

                                    </tr>

                                </thead>

                                <tbody>

                             

                                    

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



<script>



  $(document).ready(function(){

  

    $('#category_id').on('change',function() {

      var category_id = $(this).val();

      if(category_id){

        $.ajax({

            type:"GET",

            url:"<?php echo e(url('get-services-by-category')); ?>",

            data:{category_id},

            success:function(response){

              $("#service_id").empty();

              console.log(response);          

              if(response){

                $("#service_id").append('<option value="0">All Services</option>');

                $.each(response, function( index, value ) {

                  $("#service_id").append('<option value="'+value['id']+'">'+value['name']+'</option>');

                });

                }else{

                $("#customer-detail").hide();

              }

            }

        });

      }

    });

});

  

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/reports/index.blade.php ENDPATH**/ ?>