

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











<?php if($booking_detail != ''): ?>



<?php $__currentLoopData = $booking_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



  <?php

    $booking_id = $book->id;

    $customer_id = $book->customer_id;

    $user_id = $book->user_id;

    $due_date = $book->due_date;

    $due_time = $book->due_time;

    $finish_time = $book->finish_time;

    $total_price = $book->total_price;

    $comment = $book->comment;

    $pay_days = $book->payment_days;



  ?>



<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<input type="hidden" name="booking_id" value="<?php echo e($booking_id); ?>">

<?php endif; ?>

<style>

  .hide

{

 display:none;

}

</style>



<div class="row">



<div class="col-md-12 grid-margin stretch-card">



    <div class="card">



      <div class="card-body">

      <div class="form-group">



          

          

      <div class="form-group row">
          <!-- <label class="col-sm-2">Survey?</label>
          <div class="col-sm-2">
            <div class="form-check">
              <label class="form-check-label">
                <input type="radio" class="form-check-input" name="survey" id="membershipRadios1" value="no" checked="">
                No
              <i class="input-helper"></i></label>
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-check">
              <label class="form-check-label">
                <input type="radio" class="form-check-input" name="survey" id="membershipRadios2" value="yes">
                Yes
              <i class="input-helper"></i></label>
            </div>
          </div> -->
        </div>
      </div>

        <div class="form-group">



          <label>Booking Date:</label>



          <?php if(isset($date)) $now_date = substr_replace($date, ':', 13, 0); ?>



          <?php if(isset($date)) $now_date = date('Y-m-d', strtotime($date)); else $now_date = date('Y-m-d'); ?>


          <div id="datepicker-popup-booking" class="input-group date datepicker">



            <input type="text" name="due_date" id="due_date" value="<?php if(isset($due_date)): ?><?php echo e($due_date); ?><?php else: ?><?php echo e($now_date); ?><?php endif; ?>" class="form-control" required>



            <span class="input-group-addon input-group-append border-left">



              <span class="far fa-calendar input-group-text"></span>



            </span>



          </div>



        </div>

          

        <div class="form-group row">



          <div class="col">



            <?php if(isset($date)) $now_time = date('H:i', strtotime($date)); else $now_time = date('H:i'); ?>

            <label>Booking Time:</label>

           <div class="input-group date" id="timepicker-example" data-target-input="nearest">



              <div class="input-group" data-target="#timepicker-example" data-toggle="datetimepicker">



                <input type="text" name="due_time" value="<?php if(isset($due_time)): ?><?php echo e($due_time); ?><?php else: ?> '' <?php endif; ?>" id="due_time" class="form-control form-control-sm datetimepicker-input" data-target="#timepicker-example" required/>



                <div class="input-group-addon input-group-append"><i class="far fa-clock input-group-text"></i></div>



              </div>



            </div>



          </div>



          <div class="col-md-4">

          <label>Finish Time:(minutes)</label>

            <div class="input-group date" data-target-input="nearest">



              <div class="input-group">



                <input type="text" name="finish_time" value="<?php if(isset($finish_time)): ?><?php echo e($finish_time); ?><?php else: ?> 30 <?php endif; ?>" class="form-control"  maxlength="4"/>



                <div class="input-group-addon input-group-append"><i class="far fa-clock input-group-text"></i></div>



              </div>



            </div>



          </div>



        </div>



        <div class="form-group">



          <label>Select Customer</label>



          <select id="customer_id" name="customer_id" class="form-control js-example-basic-single w-100" required>



          <?php if($booking_detail == ''): ?>



                <option value="">Select Customer</option>



            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->first_name); ?> <?php echo e($customer->last_name); ?> (<?php echo e($customer->primary_phone); ?>) </option>



            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



            



          <?php else: ?>



                <option value="">Select Customer</option>



            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                <option value="<?php echo e($customer->id); ?>" <?php if($customer_id==$customer->id): ?> selected <?php endif; ?> ><?php echo e($customer->first_name); ?> <?php echo e($customer->last_name); ?></option>



            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>







          <?php endif; ?>



          </select>



        </div>

        <div class="form-group">



          <label>Select Eployee</label>



          <select id="user_id" name="user_id" class="form-control js-example-basic-single w-100" required>



          <?php if($booking_detail == ''): ?>



                <option value="">Select employee</option>



            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> </option>



            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



            



          <?php else: ?>



                <option value="">Select employee</option>



            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                <option value="<?php echo e($user->id); ?>" <?php if($user_id==$user->id): ?> selected <?php endif; ?> ><?php echo e($user->name); ?></option>



            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>







          <?php endif; ?>



          </select>



        </div>

<br>

        

        <!-- <div class="form-group">



          <label>Select Services</label>



          <select id="service_id" name="service_id[]" class="form-control js-example-basic-multiple w-100" multiple="multiple" required>
            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <optgroup label="<?php echo e($category->name); ?>">
                    <?php if($booking_detail == ''): ?>
                        <?php $__currentLoopData = $category['services_booking']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($service->is_active==1): ?>
                            <option value="<?php echo e($service->id); ?>" 
                                    data-price="<?php echo e($service->price); ?>" 
                                    data-price-type="<?php echo e($service->price_type); ?>">
                                <?php echo e($service->name); ?>

                            </option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <?php $__currentLoopData = $category['services_booking']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($service->id); ?>" 
                                    data-price="<?php echo e($service->price); ?>" 
                                    data-price-type="<?php echo e($service->price_type); ?>"
                                    <?php if(in_array($service->id, $services_ids[$booking_id])): ?> selected <?php endif; ?>>
                                <?php echo e($service->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </optgroup>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>              
        </select>



        </div> -->

        <input type="hidden" id="selected_services" value="<?php echo e(json_encode($selectedServices ?? [])); ?>">

        <div class="form-group">

          <label>Select Services</label>
          <select id="service_id" name="service_id[]" class="form-control js-example-basic-multiple w-100" multiple="multiple" required>
              
          </select>

        </div>

        <div class="form-group">



          

          

          <!-- <div class="form-check form-check-flat form-check-primary">

                                                     

                   <label class="form-check-label"> Payment upon completion

                    

                   <input id="pay_upon" name="days" type="checkbox" <?php if(isset($pay_days) && $pay_days==0 || !isset($pay_days) ): ?> checked <?php endif; ?> class="form-check-input custom-check" >

                     <i class="input-helper"></i></label>

          </div> -->

        </div>

        <div class="form-group " >

      

          <div class="input-group">



              <input type="number" id="num_days" name="num_days" class="form-control <?php if(isset($pay_days) && $pay_days==0 || !isset($pay_days) ): ?> hide <?php endif; ?> " placeholder="Enter number of days 1 or greater" value="<?php if(isset($pay_days)): ?><?php echo e($pay_days); ?><?php else: ?> 1 <?php endif; ?>" >



          </div>

         

        </div>
       
        <div class="form-group">

      

          <div class="input-group">

             

              <div class="input-group-prepend">



                <span class="input-group-text bg-primary text-white"><i class="fas fa-dollar-sign"></i></span>



              </div>



             <input type="text" name="total_price" class="form-control" value="<?php if(isset($total_price)): ?><?php echo e($total_price); ?><?php else: ?><?php echo e(old('total_price')); ?><?php endif; ?>" id="total_price">
          



          </div>

         

        </div>
 

        <!-- past inventory code here -->

        <div class="row">
  <!-- Inventory Selection -->
  <div class="col-md-8">
    <label>Select Inventory</label>
    <select id="inventory_id" class="form-control js-example-basic-single w-100" >
      <option value="">Select Inventory</option>
      <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($inventory->id); ?>" data-name="<?php echo e($inventory->name); ?>"><?php echo e($inventory->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>

  <!-- Quantity Input -->
  <!-- <div class="col-md-4">
    <label for="quantity">Quantity</label>
    <input type="number" class="form-control" id="quantity" name="quantity[]" placeholder="Enter quantity" min="1">
  </div> -->

  <!-- Single Button -->
  <div class="col-md-4">
    <label>&nbsp;</label>
    <button type="button" class="btn btn-primary btn-block" id="add_or_submit_basket">Go</button>
  </div>
</div>

<!-- Basket Table -->
<div class="form-group" id="basket_table" style="<?php echo e($booking_detail != '' ? '' : 'display:none;'); ?>">
  <h4>Basket</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Inventory</th>
        <th>Quantity</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="basket_body">

      <?php if($booking_detail !== ''): ?>
        <!-- Rows will be dynamically added here -->
        <?php $__currentLoopData = $booking_detail[0]->inventory_books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td>
            <input type="hidden" name="inventory_id[]" value="<?php echo e($book->inventory_id); ?>"> <?php echo e($book->inventory->name); ?>

          </td>
          <td>
            <input type="number" name="quantity[]"  value="<?php echo e($book->quantity); ?>" class="form-control" min="1" required>
          </td>
          <td>
            <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>  


        <!-- inventory code end here  -->

        <label style="color:red;">

        <?php if($tax_info[0]->tax_status==1  && $tax_info[0]->amount!=""): ?> VAT <?php echo e($tax_info[0]->amount); ?> <?php if($tax_info[0]->vat==1): ?>% <?php elseif($tax_info[0]->vat==0): ?>&#163; <?php endif; ?> will apply. <?php endif; ?> <label>

          

     

        </div>

      



    </div>



  </div>

















  </div>



<div class="form-group">



    <label for="exampleTextarea1">Comment</label>



    <textarea class="form-control" id="exampleTextarea1" name="comment" rows="4"><?php if(isset($comment)): ?><?php echo e($comment); ?><?php else: ?><?php echo e(old('comment')); ?><?php endif; ?></textarea>



</div>







<button type="submit" class="btn btn-primary mr-2">Submit</button>



<a class="btn btn-light" href="<?php echo e(route('service-bookings.index')); ?>">Cancel</a>



<!-- Basket Modal (if it's supposed to be a modal) -->
<div class="modal fade" id="basketModal" tabindex="-1" role="dialog" aria-labelledby="basketModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="basketModalLabel">Basket</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table" id="basket_table" style="display: none;">
          <thead>
            <tr>
              <th>Inventory Name</th>
              <th>Quantity</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="basket_body">
            <!-- Added items will appear here -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="submitBasket">Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- modal for customer bookings history -->

<!-- Customer Details Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Customer Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4>Customer Information</h4>
                <div class="row">
                    <div class="col-md-6"><strong>Name:</strong> <span class="customer-name"></span></div>
                    <div class="col-md-6"><strong>Email:</strong> <span class="customer-email"></span></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><strong>Phone:</strong> <span class="customer-primary-phone"></span></div>
                    <div class="col-md-6"><strong>Private Notes:</strong> <span class="customer-notes"></span></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><strong></strong> <span class="customer-birthday"></span></div>
                </div>

                <h4 class="mt-3">Booking History</h4>
                <div class="table-responsive">
                    <table class="table table-bordered" id="booking-table">
                        <thead>
                            <tr>
                                <th>Total Price</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Services</th>
                                <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- AJAX Data Will Populate Here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        var selectedServices = JSON.parse($('#selected_services').val() || '[]'); // Get selected services
        
        $('#user_id').on('change', function() {
            var employeeId = $(this).val();

            if (employeeId) {
                $.ajax({
                    url: "<?php echo e(route('getEmployeeServices')); ?>", // Fetch employee-specific services
                    type: "GET",
                    data: { employee_id: employeeId },
                    success: function(response) {
                        $('#service_id').empty(); // Clear previous options
                        
                        if (response.length > 0) {
                            $.each(response, function(key, service) {
                                var isSelected = selectedServices.includes(service.id) ? 'selected' : ''; // Check if service is selected

                                $('#service_id').append(
                                    `<option value="${service.id}" data-price="${service.price}" data-price-type="${service.price_type}" ${isSelected}>
                                        ${service.name}
                                    </option>`
                                );
                            });
                        } else {
                            $('#service_id').append('<option value="">No services available</option>');
                        }
                    }
                });
            } else {
                $('#service_id').empty().append('<option value="">Select an employee first</option>');
            }
        });

        // Trigger change event if editing form
        if (selectedServices.length > 0) {
            $('#user_id').trigger('change');
        }
    });
</script>




<script>







$(document).ready(function(){






  //this is old code, but it still works now comment it out for modal popup history
  // $('#customer_id').on('change',function() {



  //   var customer_id = $(this).val();



  //   if(customer_id){



  //     $.ajax({



  //         type:"GET",



  //         url:"<?php echo e(url('customer-detail')); ?>",



  //         data:{customer_id},



  //         success:function(response){



  //           console.log(response);          



  //           if(response){



  //             $("#customer-detail").show();



  //             $.each(response, function( index, value ) {



  //               $(".customer-name").html(value['first_name']+' '+value['last_name']);



  //               $(".customer-primary-phone").html(value['primary_phone']);



  //               $(".customer-secondary-phone").html(value['secondary_phone']);



  //               $(".customer-email").html(value['email']);



  //               $(".customer-zip").html(value['zip']);



  //               $(".customer-address").html(value['address']);



  //             });



  //             }else{



  //             $("#customer-detail").hide();



  //           }



  //         }



  //     });



  //   }



  // });

  $('#customer_id').on('change', function () {
        var customer_id = $(this).val();

        if (customer_id) {
            $.ajax({
                type: "GET",
                url: "<?php echo e(url('customer-detail')); ?>",
                data: { customer_id },
                success: function (response) {
                    console.log(response);

                    if (response) {
                        $("#customer-detail").show();

                        let today = new Date().toISOString().slice(5, 10); // Get MM-DD format
                        let customerBirthday = response.customer.date_of_birth ? response.customer.date_of_birth.slice(5, 10) : "";

                        $(".customer-name").html(response.customer.first_name + ' ' + response.customer.last_name);
                        $(".customer-primary-phone").html(response.customer.primary_phone);

                        // Highlight Birthday if Today
                        if (customerBirthday === today) {
                            $(".customer-birthday").html(
                                `<span class="badge bg-warning text-dark">🎉 Birthday Today: ${response.customer.date_of_birth}</span>`
                            );
                        } else {
                            $(".customer-birthday").html("");
                        }

                        $(".customer-secondary-phone").html(response.customer.secondary_phone);
                        $(".customer-email").html(response.customer.email);
                        $(".customer-zip").html(response.customer.zip);
                        $(".customer-notes").html(response.customer.private_notes);

                        var bookingsHtml = '';
                        if (response.service_info.length > 0) {
                            $.each(response.service_info, function (index, booking) {
                                bookingsHtml += `
                                    <tr>
                                        <td>(Total Price: $${booking.bookingdetail.reduce((sum, detail) => sum + detail.service.price, 0)})</td>
                                        <td>${booking.due_date}</td>
                                        <td>${booking.due_time}</td>
                                        <td>
                                            <ul>
                                                ${booking.bookingdetail.map(detail => `<li>${detail.service.name} - $${detail.service.price}</li>`).join('')}
                                            </ul>
                                        </td>
                                        <td>${booking.comment}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            bookingsHtml = `<tr><td colspan="6">No Bookings Found</td></tr>`;
                        }

                        $("#booking-table tbody").html(bookingsHtml);

                        // Show the modal popup
                        $("#customerModal").modal('show');
                    } else {
                        $("#customer-detail").hide();
                    }
                }
            });
        }
    });







  $('#service_id').on('change',function() {



    var service_id = $(this).val();



    if(service_id){



      $.ajax({



          type:"GET",



          url:"<?php echo e(url('service-price')); ?>",



          data:{service_id},



          success:function(res){              



            if(res){



              $("#total_price").val(res);



              }else{



                $("#total_price").empty();



            }



          }



      });



    }



  });







  



});

$(document).ready(function() {

  $("#pay_upon").click(function() {

      if ($('#pay_upon').is(":checked"))

      {

        

          $('#num_days').addClass("hide");

          $('#num_days').prop('required',false);

      }

      else{

        

      $('#num_days').removeClass( "hide");

      $('#num_days').prop('required',true);

      }

  });



});







</script>
<script>
// $(document).ready(function () {

//   $('#add_or_submit_basket').on('click', function () {
//     var inventorySelect = $('#inventory_id');
//     var inventoryId = inventorySelect.val();
//     var inventoryName = inventorySelect.find('option:selected').text();

//     // Validation
//     if (!inventoryId) {
//       alert('Please select inventory.');
//       return;
//     }

//     // Prevent duplicate entries
//     if ($(`#basket_body input[value="${inventoryId}"]`).length > 0) {
//       alert('This inventory is already added.');
//       return;
//     }

//     // Append row with editable quantity input
//     $('#basket_body').append(`
//       <tr>
//         <td>
//           <input type="hidden" name="inventory_id[]" value="${inventoryId}">
//           ${inventoryName}
//         </td>
//         <td>
//           <input type="number" class="form-control" name="quantity[]" value="" min="1" required>
//         </td>
//         <td>
//           <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
//         </td>
//       </tr>
//     `);

//     $('#basket_table').show();

//     // Clear select
//     inventorySelect.val('');
//   });

//   // Remove row
//   $('#basket_body').on('click', '.remove-row', function () {
//     $(this).closest('tr').remove();
//     if ($('#basket_body').children().length === 0) {
//       $('#basket_table').hide();
//     }
//   });


// });



$(document).ready(function () {

$('#add_or_submit_basket').on('click', function () {
  var inventorySelect = $('#inventory_id');
  var inventoryId = inventorySelect.val();
  var inventoryName = inventorySelect.find('option:selected').text();
  var _token = "<?php echo e(csrf_token()); ?>";

  // Validation
  if (!inventoryId) {
    alert('Please select inventory.');
    return;
  }

  // Prevent duplicate entries
  if ($(`#basket_body input[value="${inventoryId}"]`).length > 0) {
    alert('This inventory is already added.');
    return;
  }

  // Check quantity via AJAX
  $.ajax({
    url: '/check-inventory-quantity',
    type: 'POST',
    data: {
      _token: _token,
      inventory_id: inventoryId
    },
    success: function (response) {
      if (response.quantity <= 0) {
        alert(`Inventory "${inventoryName}" is out of stock (${response.quantity})!`);
        return;
      }

      let rowClass = "";
      if (response.quantity < response.low_alert) {
        alert(`Warning: Inventory "${inventoryName}" quantity is low (${response.quantity})!`);
        rowClass = "table-danger";
      }

      // Append the row to basket
      $('#basket_body').append(`
        <tr class="${rowClass}">
          <td>
            <input type="hidden" name="inventory_id[]" value="${inventoryId}">
            ${inventoryName}
          </td>
          <td>
            <input type="number" class="form-control" name="quantity[]" value="" min="1" required>
          </td>
          <td>
            <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
          </td>
        </tr>
      `);

      $('#basket_table').show();
      inventorySelect.val('');
    },
    error: function (xhr) {
      alert('Failed to check inventory. Please try again.');
      console.error(xhr.responseText);
    }
  });
});

// Remove row
$('#basket_body').on('click', '.remove-row', function () {
  $(this).closest('tr').remove();
  if ($('#basket_body').children().length === 0) {
    $('#basket_table').hide();
  }
});

});



</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const serviceDropdown = document.getElementById('service_id'); // Dropdown element
    const totalPriceInput = document.getElementById('total_price'); // Input field for price

    serviceDropdown.addEventListener('change', function () {
        const selectedOptions = Array.from(serviceDropdown.selectedOptions); // Get selected options

        if (selectedOptions.length === 1) {
            const selectedOption = selectedOptions[0];
            const price = selectedOption.getAttribute('data-price');
            const priceType = selectedOption.getAttribute('data-price-type');

            totalPriceInput.value = price;

            if (priceType === 'fixed') {
                totalPriceInput.readOnly = true;
            } else if (priceType === 'modify_able') {
                totalPriceInput.readOnly = false;
            }
        } else {
            totalPriceInput.value = '';
            totalPriceInput.readOnly = false;
        }
    });
});

</script>
<script>
  $(document).ready(function() {
      $('#service_id').on('change', function() {
          var service_id = $(this).val();
          var totalPrice = 0;
          var isFixed = false;
  
          // Loop through all selected options
          $(this).find('option:selected').each(function() {
              var price = parseFloat($(this).data('price'));
              var priceType = $(this).data('price-type');
              
              totalPrice += price;
              
              if (priceType === 'fixed') {
                  isFixed = true;
              }
          });
  
          // Update the total price
          $('#total_price').val(totalPrice.toFixed(2));
  
          $('#total_price').prop('readonly', isFixed);
      });
  });
  </script><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/service_bookings/booking_form.blade.php ENDPATH**/ ?>