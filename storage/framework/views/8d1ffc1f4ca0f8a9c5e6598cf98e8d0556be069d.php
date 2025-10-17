

<?php $__env->startSection('title', 'Customers | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>

<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                All Customers



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="<?php echo e(route('customers.index')); ?>">Customers</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Customers Listing</li>



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







        <?php $customer_edit_array = array(3,3); ?>



        <?php $customer_edit = implode(',', $customer_edit_array); ?>



        <?php $customer_delete_array = array(3,4); ?>



        <?php $customer_delete = implode(',', $customer_delete_array); ?>







        <div class="card">



            <div class="card-body">



                    <div class="row">



                            <div class="col-md-3">



                            <h4 class="card-title">Customers Listing</h4>



                            </div>



                        <div class="col-md-6"></div>



                        <div class="col-md-3">



                            <div class="text-center" style="float: center;">



                                <span data-href="/all-customer-export" id="export" class="btn btn-primary" onclick="exportTasks(event.target);">Export Customers</span>



                            </div>



                        </div>



                    </div>



                <div class="row">



                    <div class="col-12 mt-2">



                        <div class="table-responsive">



                            <table class="table table-striped">

                                <thead>

                                    <tr>

                                        <th>Name</th>
                                        <th>Country Code</th>
                                        <th>Primary Phone</th>
                                        <th>Date Of Birth</th>
                                        <th>E-Mail</th>

                                        <th>Address</th>

                                        <th>Referred By</th>
                                        <!-- <th>Bookings</th> -->
                                       

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    
                                    <tr>
                                        <td><a href="<?php echo e(url('bookings-history', $customer->id)); ?>"> <?php echo e($customer->first_name); ?> <?php echo e($customer->last_name); ?> </a></td>
                                        <td><?php echo e($customer->country_code); ?></td>


                                        <td><?php echo e($customer->primary_phone); ?></td>
                                        <td><?php echo e($customer->date_of_birth); ?></td>
                                        <td><?php echo e($customer->email); ?></td>

                                        <td><?php echo e($customer->address); ?></td>

                                        <td><?php echo e($customer->referred_by); ?></td>
                                        <!-- <td><?php if($customer->total_bookings>1): ?> Repeat(<?php echo e($customer->total_bookings); ?>) <?php else: ?> Once(<?php echo e($customer->total_bookings); ?>) <?php endif; ?></td> -->
                                        

                                        <td>

                                        <?php if(in_array($customer_edit, session()->get('all_rp')['0'])): ?>

                                            <a href="<?php echo e(route('customers.edit',$customer->id)); ?>">

                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>

                                            </a> 

                                        <?php endif; ?> 
                                        <?php if(in_array($customer_delete, session()->get('all_rp')['0'])): ?>

|<a onclick="return confirm('Are you sure to delete');" href="<?php echo e(url('customers/destroy',$customer->id)); ?>">

    <i class="fas fa-trash text-danger"></i>                          

</a>

<?php endif; ?>
                                        </td>

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



    <?php $__env->stopSection(); ?>



<script>



function changeCustomerStatus(_this, id) {







var status = $(_this).prop('checked') == true ? 1 : 0;



$.ajax({



    url: "<?php echo e(url('customerstatus')); ?>",



    type: 'GET',



    data: {



        id: id,



        status: status



    },



    success: function(result) {



        $("#status-success").show();



        $("#status-success").html(result.success);



        setTimeout(function() {



            $("#status-success").hide()



        }, 3000);



    }



});



}

    



   function exportTasks(_this) {



      let _url = $(_this).data('href');



      window.location.href = _url;



   }



</script>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/customers/index.blade.php ENDPATH**/ ?>