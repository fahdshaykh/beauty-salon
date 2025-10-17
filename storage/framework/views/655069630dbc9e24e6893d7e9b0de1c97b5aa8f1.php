<?php $__env->startSection('pagescript'); ?>

    <script src="<?php echo e(asset('js/x-editable.js')); ?>"></script>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('title', 'Service Bookings | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Service Bookings

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?php echo e(route('service-bookings.index')); ?>">Service Bookings</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Bookings Listing</li>

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

        <?php $booking_edit_array = array(4,3); ?>

        <?php $booking_edit = implode(',', $booking_edit_array); ?>

        <?php $booking_delete_array = array(4,4); ?>

        <?php $booking_delete = implode(',', $booking_delete_array); ?>

        <?php $booking_show_array = array(4,5); ?>

        <?php $booking_show = implode(',', $booking_show_array); ?>

        <div class="card">

            <div class="card-body">


            <!-- Filter Form -->
        <!-- Filter Form -->
<div class="row mb-3">
    <div class="col-md-12">
        <form method="GET" action="<?php echo e(route('service-bookings.index')); ?>">
            <div class="row">
                <!-- Service Filter -->
                <div class="col-md-3">
                    <label for="service_id">Service</label>
                    <select name="service_id" id="service_id" class="form-control">
                        <option value="">All Services</option>
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($service->id); ?>" <?php echo e(request('service_id') == $service->id ? 'selected' : ''); ?>>
                                <?php echo e($service->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="col-md-3">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
                </div>

                <!-- Status Filter -->
                <div class="col-md-3">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All Statuses</option>
                        <?php $__currentLoopData = ['1' => 'Booked', '2' => 'In-Progress', '3' => 'Completed', '4' => 'Cancel']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>>
                                <?php echo e($label); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <!-- Customer Name Filter -->
                <div class="col-md-3">
                    <label for="customer_name">Customer Name</label>
                    <input type="text" name="customer_name" id="customer_name" class="form-control" value="<?php echo e(request('customer_name')); ?>" placeholder="Enter customer name">
                </div>
                <!-- Primary Phone Filter -->
                <div class="col-md-3">
                    <label for="primary_phone">Phone</label>
                    <input type="text" name="primary_phone" id="primary_phone" class="form-control" value="<?php echo e(request('primary_phone')); ?>" placeholder="Enter phone number">
                </div>
                <div class="col-md-6">
                    <label class="d-none d-md-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="<?php echo e(route('service-bookings.index')); ?>" class="btn btn-secondary">Clear Filters</a>
                </div>
            </div>
            <!-- Preserve per_page in form -->
            <input type="hidden" name="per_page" value="<?php echo e(request('per_page', auth()->user()->getPerPagePreference())); ?>">
        </form>
    </div>
</div>

                <h4 class="card-title"></h4>

                <!-- Pagination Dropdown -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="pagination-dropdown">
                            <label for="per_page">Items per page</label>
                            <form method="GET" action="<?php echo e(route('service-bookings.index')); ?>">
                                <?php $__currentLoopData = request()->except(['page', 'per_page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <select name="per_page" id="per_page" onchange="this.form.submit()" class="form-control">
                                    <?php $__currentLoopData = $paginationOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>" <?php echo e(request('per_page', auth()->user()->getPerPagePreference()) == $option ? 'selected' : ''); ?>><?php echo e($option); ?> per page</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-12">

                        <div class="table-responsive">

                            <table class="table table-striped">

                                <thead>

                                    <tr>

                                        <th>User</th>

                                        <th>Customer</th>

                                        <th>Phone</th>

                                        <th style="width: 10%;">Services</th>

                                        <th>Price</th>

                                        <th>Book Date</th>

                                        <th>Book Time</th>

                                        <th>Status</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    <?php $__currentLoopData = $data_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <tr>

                                        <td><?php echo e($book['user_name']); ?></td>

                                        <td><?php echo e($book['customer_name']); ?></td>

                                        <td><?php echo e($book['primary_phone']); ?></td>

                                        <td style="max-width:160px;"><?php echo e($book['services']); ?></td>

                                        <td><i class="fas fa-dollar-sign"></i><?php echo e($book['total_price']); ?></td>

                                        <td style="width: 15%;"><?php echo e($book['due_date']); ?></td>

                                        <td style="width: 15%;"><?php echo e($book['due_time']); ?></td>

                                        <td style="width: 15%;">

                                        <?php $status = array('1' => 'Booked', '2' => 'In-Progress', '3' => 'Completed', '4' => 'Cancel'); ?>

                                        <div class="form-group">

                                        <select class="form-control form-control-sm" id="change" data-booking_id="<?php echo e($book['id']); ?>" onchange="changeLevel(this);"  <?php if($book['service_status']==5): ?> disabled <?php endif; ?>>

                                            <?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusKey => $check): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                <option value="<?php echo e($statusKey); ?>" <?php if($statusKey == $book['service_status']): ?> selected <?php endif; ?> ><?php echo e($check); ?></option>

                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                            </select>

                                            </div> 

                                        </td>
                                        

                                        <td>
                                        <?php if(in_array($booking_edit, session()->get('all_rp')['0'])): ?>
                                            <?php if($book['service_status']!=5): ?> 
                                            <a href="<?php echo e(url('service-bookings/edit',$book['id'])); ?>">
                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>
                                            </a> |
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if(in_array($booking_delete, session()->get('all_rp')['0'])): ?>
                                            <a onclick="return confirm('Are you sure to delete');" href="<?php echo e(url('service-bookings/destroy',$book['id'])); ?>">
                                                <i class="fas fa-trash text-danger"></i>                          
                                            </a>
                                        <?php endif; ?>

                                        <?php if(in_array($booking_show, session()->get('all_rp')['0'])): ?>
                                        <?php if($book['service_status']!=5): ?> 
                                        |<a href="<?php echo e(url('view-booking',$book['id'])); ?>" title="View/Send Invoice">
                                                <i class="fa fa-eye text-primary"></i>
                                            </a>
                                        </td>
                                        <?php endif; ?>
                                        <?php endif; ?>

                                    </tr>

                                    <?php $num++; ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <!-- Pagination Links -->
                <div class="row">
                    <div class="col-12 mt-4">
                        <p>Showing <?php echo e($bookings->firstItem()); ?> to <?php echo e($bookings->lastItem()); ?> of <?php echo e($bookings->total()); ?> entries</p>
                        <?php echo e($bookings->links()); ?>

                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancel-modal" tabindex="-1" role="dialog" aria-labelledby="CancelModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CancelModalLabel">Cancel Booking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="cancel-form" method="POST" action="<?php echo e(route('service-bookings.cancel')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cancel-reason">Reason for Cancellation</label>
                            <textarea class="form-control" id="cancel-reason" name="reason" required></textarea>
                        </div>
                        <input type="hidden" id="cancel-booking-id" name="booking_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-danger" value="Submit Cancellation">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Payment Method</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/checkout"  method="post" id="payment-form">
            <div class="modal-body">
               
                <div class="form-group">
                    <select class="form-control form-control-lg" name="payment_method" id="payment_method" required>
                        <option value="">Select Payment Method</option>
                        <option value="cash">Cash</option>
                        <option value="paypal">PayPal</option>
                        <option value="stripe">Stripe</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message-text" class="col-form-label cheque_number_label" style="display: none">Cheque Number:</label>
                    <input type="text" class="form-control" id="cheque_number" name="cheque_number" style="display: none">
                </div>
                <input type="hidden" class="form-control" id="invoice" name="invoice">
                <input type="hidden" class="form-control" id="service_status" name="service_status">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
            <?php echo csrf_field(); ?>
        </form>
            </div>
        </div>
    </div>

   

    <?php $__env->stopSection(); ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function(){
        $('.changestatus').change(function() {
            var status = $(this).val();
            var id = $(this).data('id');
            
            if (status === 'cancelled') {
                $('#cancel-booking-id').val(id);
                $('#cancel-modal').modal('show');
                return false;
            } else if (status === 'completed') {
                $("#payment-modal").modal('show');
                $("#invoice").val(id);
                $("#service_status").val(status);
                return false;
            } else {
                $.ajax({
                    url: "<?php echo e(route('admin.booking.change-status')); ?>",
                    method: "POST",
                    data: {
                        status: status,
                        id: id,
                        _token: "<?php echo e(csrf_token()); ?>"
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });

        
        // $('#cancel-form').submit(function(e) {
        //     e.preventDefault();
        //     var formData = $(this).serialize();
            
        //     $.ajax({
        //         url: "<?php echo e(route('service-bookings.cancel')); ?>", // Update this route
        //         method: "POST",
        //         data: formData,
        //         success: function(response) {
        //             if (response.success) {
        //                 $('#cancel-modal').modal('hide');
        //                 toastr.success(response.message);
        //                 location.reload();
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             console.error(error);
        //             toastr.error('An error occurred while cancelling the booking.');
        //         }
        //     });
        // });

        // $("#payment_method").change(function() {
        //     var paymentMethod = $(this).val();
        //     if (paymentMethod === 'paypal') {
        //         var bookingId = $("#invoice").val(); // Get the booking ID
        //         window.location.href = "<?php echo e(route('redirect.to.paypal')); ?>?booking_id=" + bookingId;
        //         return false; // Prevent form submission
        //     }
        // });
    
        $("#payment-form").submit(function (event) {
            var paymentMethod = $("#payment_method").val();
    
            // if (paymentMethod === 'stripe') {
            //     // Allow normal form submission to redirect to Stripe
            //     return true;  
            // }
    
            event.preventDefault(); // Prevent form submission for other methods
            $.ajax({
                url: "<?php echo e(url('change-booking-status')); ?>",
                type: "POST",
                data: $("#payment-form").serialize(),
                success: function (response) {
                    if(response.success){
                        showSuccessToast(response.success);
                        $("#payment-modal").modal('hide');
                    } else {
                        showDangerToast(response.error);
                    }
                },
            });
        });

    
        // $('.changestatus').change(function() {
        //     var status = $(this).val();
        //     var id = $(this).data('id');

        //     console.log(status, id);
            
            
        //     if (status === 'completed') {
        //         $("#payment-modal").modal('show');
        //         $("#invoice").val(id);
        //         $("#service_status").val(status);
        //         return false;
        //     } else {
        //         $.ajax({
        //             url: "<?php echo e(url('change-booking-status')); ?>",
        //             method: "POST",
        //             data: {
        //                 status: status,
        //                 id: id,
        //                 _token: "<?php echo e(csrf_token()); ?>"
        //             },
        //             success: function(response) {
        //                 if (response.success) {
        //                     toastr.success(response.message);
        //                 }
        //             },
        //             error: function(xhr, status, error) {
        //                 console.error(error);
        //             }
        //         });
        //     }
        // });


        changeLevel = function(level){

            var status_value = level.value;

            var booking_id = $(level).data('booking_id');

            if(status_value == 3)
            {

                    $("#payment-modal").modal('show');

                    $("#invoice").val(booking_id);

                    $("#service_status").val(status_value);

                    return false;

            } else if(status_value == 4) {
                    $("#cancel-modal").modal('show');
                    $('#cancel-booking-id').val(booking_id);
                }

                else{



                    $.ajax({

                    type: "POST",

                    url:"<?php echo e(url('booking-status')); ?>",

                    data: { id: booking_id,service_status: status_value,_token: "<?php echo e(csrf_token()); ?>"  },

                    success: function(response) {

                        

                        $("#status-success").show();

                        $("#status-success").html(response.success);

                        setTimeout(function() {

                            $("#status-success").hide()

                        }, 3000);

                    }

                });



            }



        }

    });
    </script>
    
    


    

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-saloon\resources\views/service_bookings/index.blade.php ENDPATH**/ ?>