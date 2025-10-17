

@if ($errors->any())



    <div class="alert alert-danger alert-dismissible">



        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



        <ul>



            @foreach ($errors->all() as $error)



                <li>{{ $error }}</li>



            @endforeach



        </ul>



    </div>



@endif











@if($booking_detail != '')



@foreach($booking_detail as $book)



  <?php

    $booking_id = $book->id;

    $customer_id = $book->customer_id;

    $due_date = $book->due_date;

    $due_time = $book->due_time;

    $finish_time = $book->finish_time;

    $total_price = $book->total_price;

    $comment = $book->comment;

    $pay_days = $book->payment_days;



  ?>



@endforeach

<input type="hidden" name="booking_id" value="{{$booking_id}}">

@endif

<style>

  .hide

{

 display:none;

}

</style>



<div class="row">



<div class="col-md-6 grid-margin stretch-card">



    <div class="card">



      <div class="card-body">



        <div class="form-group">



          <label>Booking Date:</label>



          <?php if(isset($date)) $now_date = substr_replace($date, ':', 13, 0); ?>



          <?php if(isset($date)) $now_date = date('Y-m-d', strtotime($date)); else $now_date = date('Y-m-d'); ?>



          <div id="datepicker-popup-booking" class="input-group date datepicker">



            <input type="text" name="due_date" value="@if(isset($due_date)){{$due_date}}@else{{$now_date}}@endif" class="form-control" required>



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



                <input type="text" name="due_time" value="@if(isset($due_time)){{$due_time}}@else{{$now_time}}@endif" class="form-control form-control-sm datetimepicker-input" data-target="#timepicker-example" required/>



                <div class="input-group-addon input-group-append"><i class="far fa-clock input-group-text"></i></div>



              </div>



            </div>



          </div>



          <div class="col-md-4">

          <label>Finish Time:</label>

            <div class="input-group date" data-target-input="nearest">




              <div class="input-group">



                <input type="text" name="finish_time" value="@if(isset($finish_time)){{$finish_time}}@else 45 @endif" class="form-control"  maxlength="3"/>



                <div class="input-group-addon input-group-append"><i class="far fa-clock input-group-text"></i></div>



              </div>



            </div>



          </div>



        </div>



        <div class="form-group">



          <label>Select Customer</label>



          <select id="customer_id" name="customer_id" class="form-control js-example-basic-single w-100" required>



          @if($booking_detail == '')



                <option value="">Select Customer</option>



            @foreach($customers as $customer)



                <option value="{{$customer->id}}">{{$customer->first_name}} {{$customer->last_name}} ({{$customer->primary_phone}}) </option>



            @endforeach



            



          @else



                <option value="">Select Customer</option>



            @foreach($customers as $customer)



                <option value="{{$customer->id}}" @if($customer_id==$customer->id) selected @endif >{{$customer->first_name}} {{$customer->last_name}}</option>



            @endforeach







          @endif



          </select>



        </div>





        

        <div class="form-group">



          <label>Select Services</label>



          <select id="service_id" name="service_id[]" class="form-control js-example-basic-multiple w-100" multiple="multiple" required>

              @foreach($services as $category)

                  <optgroup label="{{$category->name}}">

                    @if($booking_detail == '')

                      @foreach($category['services_booking'] as $service)

                        <option value="{{$service->id}}">{{$service->name}}</option>



                      @endforeach

                    @else

                    @foreach($category['services_booking'] as $service)

                      <option value="{{$service->id}}" @if(in_array($service->id, $services_ids[$booking_id])) selected @endif >{{$service->name}}</option>

                      @endforeach

                    @endif

                  </optgroup>

              @endforeach              

          </select>



        </div>

        <div class="form-group">



          

          

          <div class="form-check form-check-flat form-check-primary">

                                                     

                   <label class="form-check-label"> Payment upon completion

                    

                   <input id="pay_upon" name="days" type="checkbox" @if(isset($pay_days) && $pay_days==0 || !isset($pay_days) ) checked @endif class="form-check-input custom-check" >

                     <i class="input-helper"></i></label>

                </div>



        </div>

        <div class="form-group " >

      

          <div class="input-group">



              <input type="number" id="num_days" name="num_days" class="form-control @if(isset($pay_days) && $pay_days==0 || !isset($pay_days) ) hide @endif " placeholder="Enter number of days 1 or greater" value="@if(isset($pay_days)){{$pay_days}}@else 1 @endif" min="1">



          </div>

         

        </div>

        <div class="form-group">

      

          <div class="input-group">

             

              <div class="input-group-prepend">



                <span class="input-group-text bg-primary text-white"><i class="fas fa-pound-sign"></i></span>



              </div>



              <input type="text" name="total_price" class="form-control" value="@if(isset($total_price)){{$total_price}}@else{{old('total_price')}}@endif" id="total_price">



          </div>

         

        </div>



        <label style="color:red;">

        @if($tax_info[0]->tax_status==1  && $tax_info[0]->amount!="") VAT {{$tax_info[0]->amount}} @if($tax_info[0]->vat==1)% @elseif($tax_info[0]->vat==0)&#163; @endif will apply. @endif <label>

          

     

        </div>

      



    </div>



  </div>

















  </div>



<div class="form-group">



    <label for="exampleTextarea1">Comment</label>



    <textarea class="form-control" id="exampleTextarea1" name="comment" rows="4">@if(isset($comment)){{$comment}}@else{{old('comment')}}@endif</textarea>



</div>







<button type="submit" class="btn btn-primary mr-2">Submit</button>



<a class="btn btn-light" href="{{route('service-bookings.index')}}">Cancel</a>







<script>







$(document).ready(function(){







  $('#customer_id').on('change',function() {



    var customer_id = $(this).val();



    if(customer_id){



      $.ajax({



          type:"GET",



          url:"{{url('customer-detail')}}",



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



          url:"{{url('service-price')}}",



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