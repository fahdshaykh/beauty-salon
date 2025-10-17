

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



            <input type="text" name="due_date" value="<?php if(isset($due_date)): ?><?php echo e($due_date); ?><?php else: ?><?php echo e($now_date); ?><?php endif; ?>" class="form-control" required>



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



                <input type="text" name="due_time" value="<?php if(isset($due_time)): ?><?php echo e($due_time); ?><?php else: ?><?php echo e($now_time); ?><?php endif; ?>" class="form-control form-control-sm datetimepicker-input" data-target="#timepicker-example" required/>



                <div class="input-group-addon input-group-append"><i class="far fa-clock input-group-text"></i></div>



              </div>



            </div>



          </div>



          <div class="col-md-4">

          <label>Finish Time:(minutes)</label>

            <div class="input-group date" data-target-input="nearest">



              <div class="input-group">



                <input type="text" name="finish_time" value="<?php if(isset($finish_time)): ?><?php echo e($finish_time); ?><?php else: ?> 45 <?php endif; ?>" class="form-control"  maxlength="4"/>



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

        

        <div class="form-group">



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




<script>







$(document).ready(function(){







  $('#customer_id').on('change',function() {



    var customer_id = $(this).val();



    if(customer_id){



      $.ajax({



          type:"GET",



          url:"<?php echo e(url('customer-detail')); ?>",



          data:{customer_id},



          success:function(response){



            console.log(response);          



            if(response){



              $("#customer-detail").show();



              $.each(response, function( index, value ) {



                $(".customer-name").html(value['first_name']+' '+value['last_name']);



                $(".customer-primary-phone").html(value['primary_phone']);



                $(".customer-secondary-phone").html(value['secondary_phone']);



                $(".customer-email").html(value['email']);



                $(".customer-zip").html(value['zip']);



                $(".customer-address").html(value['address']);



              });



              }else{



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
$(document).ready(function () {
  $('#add_or_submit_basket').on('click', function () {
    var inventorySelect = $('#inventory_id');
    var quantity = $('#quantity').val();
    var inventoryId = inventorySelect.val();
    var inventoryName = inventorySelect.find('option:selected').data('name');

    // Check if basket has rows, submit it if no input
    if (!inventoryId && $('#basket_body').children().length > 0) {
      submitBasket();
      return;
    }

    // Validation
    if (!inventoryId || quantity <= 0) {
      alert('Please select inventory and enter a valid quantity.');
      return;
    }

    // Add row to table
    $('#basket_body').append(`
      <tr>
        <td>
          <input type="hidden" name="inventory_id[]" value="${inventoryId}">${inventoryName}
        </td>
        <td>
          <input type="hidden" name="quantity[]" value="${quantity}">${quantity}
        </td>
        <td>
          <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
        </td>
      </tr>
    `);

    // Show table and prepare for submission
    $('#basket_table').show();

    // Clear form
    inventorySelect.val('');
    $('#quantity').val('');
  });

  // Remove row
  $('#basket_body').on('click', '.remove-row', function () {
    $(this).closest('tr').remove();
    if ($('#basket_body').children().length === 0) {
      $('#basket_table').hide();
    }
  });

  // Submit function
  function submitBasket() {
    var formData = [];
    $('input[name="inventory_id[]"]').each(function (index, element) {
      formData.push({
        inventory_id: $(element).val(),
        quantity: $('input[name="quantity[]"]').eq(index).val()
      });
    });

    console.log('Submitting data:', formData); // Debug: Log data before submission

    // AJAX Call
    $.ajax({
      url: "<?php echo e(route('basket.submit')); ?>",
      type: "POST",
      data: {
        _token: "<?php echo e(csrf_token()); ?>",
        basket: formData
      },
      success: function (response) {
        console.log('Server Response:', response); // Debug: Log response
        alert(response.message);
        location.reload();
      },
      error: function (xhr) {
        console.error('Error Response:', xhr.responseText); // Debug: Log error
        alert('An error occurred while submitting the basket.');
      }
    });
  }
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
  </script><?php /**PATH E:\projects\bsaloon\resources\views/service_bookings/booking_form.blade.php ENDPATH**/ ?>