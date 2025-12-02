@extends('layouts.admin')







@section('title', 'Create Booking | '. (auth()->user()->companySetting->site_name ?? ''))







@section('content')





<!-- partial -->



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                Create Booking



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{route('service-bookings.index')}}">Service Booking</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Create Booking</li>



                </ol>



            </nav>



        </div>



        @if(Session::has('success'))



            <div class="alert alert-success alert-dismissible">



            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



                {{Session::get('success')}}



            </div>



        @endif



        @if(Session::has('error'))



            <div class="alert alert-danger alert-dismissible">



            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



                {{Session::get('error')}}



            </div>



        @endif



        <div class="row">



            <div class="col-12 grid-margin">



                <div class="card">



                    <div class="card-body">



                        <div class="row">



                            <div class="col-md-3">



                            <h4 class="card-title">Bookings Form</h4>



                            </div>



                        <div class="col-md-6"></div>



                        <div class="col-md-3">



                        @can('create customers')
                            <div class="text-center mb-4" style="float: right;">



                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#customerModal">Add New Customer</button>



                            </div>
                        @endcan


                        </div>



                        </div>







                        <form class="forms-sample" action="{{route('service-bookings.store')}}" method="post">



                            @csrf



                            @include('service_bookings.booking_form')



                        </form>



                    </div>



                </div>



            </div>



        </div>



    </div>



</div>







<script>



    // $(document).ready(function(){



    //     var date = '<?php echo $date; ?>';

    //     var url = window.location.pathname;

    //     var urlsplit = url.split("/");

    //     var lastpart = urlsplit[urlsplit.length-1];

    //     if (date) {

    //         if(lastpart == 'success'){

    //         }else {

    //             $("#customerModal").modal('show');

    //         }

    //     }



    // });



    // $(document).ready(function() {
    //     $('#due_time').prop('disabled', true);

    //     function checkUserId() {
    //         var user_id = $('#user_id').val();
    //         if (user_id) {
    //             $('#due_time').prop('disabled', false);
    //         } else {
    //             $('#due_time').prop('disabled', true);
    //             $('#due_time').val('');
    //         }
    //     }

    //     $('#user_id').on('change input', function() {
    //         checkUserId();
    //     });

    //     $('#due_time').on('change', function() {
    //         var user_id = $('#user_id').val();
    //         var due_date = $('#due_date').val();
    //         var due_time = $(this).val();
    //         var _token = "{{ csrf_token() }}";

    //         if (user_id && due_time) {
    //             $.ajax({
    //                 type: "POST",
    //                 url: "{{ url('check-booking-availability') }}",
    //                 data: { user_id: user_id, due_date: due_date, due_time: due_time, _token: _token },
    //                 success: function(response) {
    //                     if (response.error) {
    //                         $("#status-error").show().html(response.error);
    //                         setTimeout(function() {
    //                             $("#status-error").hide();
    //                         }, 3000);

    //                         // Show the modal only if booked slots exist
    //                         if (response.booked_slots.length > 0) {
    //                             var tableBody = $("#bookedSlotsTable tbody");
    //                             tableBody.empty();

    //                             $.each(response.booked_slots, function(index, slot) {
    //                                 var row = `<tr>
    //                                     <td>${slot.due_date}</td>
    //                                     <td>${slot.due_time}</td>
    //                                     <td>${slot.service_time}</td>
    //                                 </tr>`;
    //                                 tableBody.append(row);
    //                             });

    //                             $("#bookedSlotsModal").modal('show');
    //                         }
    //                     }
    //                 }
    //             });
    //         }
    //     });

    //     checkUserId();
    // });
    

    $(document).ready(function() {
        let modalOpened = false; // Prevent repeated modal pops
        let debounceTimer; // Timer for debouncing

        $('#due_time').prop('disabled', true);

        function checkUserId() {
            var user_id = $('#user_id').val();
            if (user_id) {
                $('#due_time').prop('disabled', false);
            } else {
                $('#due_time').prop('disabled', true);
                $('#due_time').val('');
            }
        }

        $('#user_id').on('change input', function() {
            checkUserId();
        });

        // Debounced AJAX request
        function checkAvailability() {
            if (modalOpened) return; // Prevent multiple modal pops

            var user_id = $('#user_id').val();
            var due_date = $('#due_date').val();
            var due_time = $('#due_time').val();
            var _token = "{{ csrf_token() }}";

            if (user_id && due_time) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('check-booking-availability') }}",
                    data: { user_id: user_id, due_date: due_date, due_time: due_time, _token: _token },
                    success: function(response) {
                        if (response.error) {
                            $("#status-error").show().html(response.error);
                            setTimeout(function() {
                                $("#status-error").hide();
                            }, 3000);

                            if (response.booked_slots.length > 0) {
                                var tableBody = $("#bookedSlotsTable tbody");
                                tableBody.empty();

                                $.each(response.booked_slots, function(index, slot) {
                                    var row = `<tr>
                                        <td>${slot.due_date}</td>
                                        <td>${slot.due_time}</td>
                                        <td>${slot.service_time}</td>
                                    </tr>`;
                                    tableBody.append(row);
                                });

                                $('#due_time').val('');
                                
                                $("#bookedSlotsModal").modal('show');
                                modalOpened = true; // Set flag when modal opens
                            }
                        }
                    }
                });
            }
        }

        // Use debounce to prevent excessive AJAX calls
        $('#due_time').on('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(checkAvailability, 8000); // Wait 800ms before calling AJAX
        });

        $('#due_time').on('blur', function() {
            clearTimeout(debounceTimer);
            checkAvailability();
        });

        // Reset modal flag when modal is closed
        $("#bookedSlotsModal").on("hidden.bs.modal", function () {
            modalOpened = false;
        });

        checkUserId();


        $(".close-booked").on("click", function() {
            $("#bookedSlotsModal").modal("hide");
        });
    });


</script>

<!-- Modal -->
<div class="modal fade" id="bookedSlotsModal" tabindex="-1" aria-labelledby="bookedSlotsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bookedSlotsModalLabel">Already Booked Slots</h5>
        
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="bookedSlotsTable">
          <thead>
            <tr>
              <th>Due Date</th>
              <th>Start Time</th>
              <th>Finish Time</th>
            </tr>
          </thead>
          <tbody>
            <!-- Booked slots will be inserted here dynamically -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close-booked" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>






<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">



    <div class="modal-dialog modal-lg" role="document">



      <div class="modal-content">



        <div class="modal-header">



          <h5 class="modal-title" id="customerModalLabel">Adding New Customer</h5>



          <button type="button" class="close" data-dismiss="modal" aria-label="Close">



            <span aria-hidden="true">&times;</span>



          </button>



        </div>



        <div class="modal-body">



            <form class="forms-sample" id="signupForm" action="{{route('customers.store')}}" method="post">



            <div class="row">



            <input type="hidden" name="now_date" value="{{$date}}" class="form-control" />



                <div class="col-md-6">



                    <div class="form-group row">



                        <label class="col-sm-3 col-form-label">First Name</label>



                        <div class="col-sm-9">



                            <input type="text" id="first_name" name="first_name" value="" class="form-control" />



                        </div>



                    </div>



                </div>



                <div class="col-md-6">



                    <div class="form-group row">



                        <label class="col-sm-3 col-form-label">Last Name</label>



                        <div class="col-sm-9">



                            <input type="text" id="last_name" name="last_name" value="" class="form-control" />



                        </div>



                    </div>



                </div>



            </div>



            <div class="row">



                <div class="col-md-6">



                    <div class="form-group row">



                        <label class="col-sm-3 col-form-label">Mobile</label>



                        <div class="col-sm-9">



                        <div class="row">

                        
                            <div class="col-md-3 input-group-prepend">
                                <select id="tags" name="country_code" class="js-example-basic-single w-100">

                                @foreach ($countries as $country)
                                    
                                <option value="{{ $country->phone_code }}" 
                                    {{ old('country_code', $customer->country_code ?? '') == $country->phone_code ? 'selected' : '' }}>
                                    {{ $country->phone_code }}
                                </option>

                                @endforeach

                                </select>
                            </div>
				
                            <div class="col-md-9">
                                <input type="text" id="primary_phone" name="primary_phone" minlength="10"  maxlength="14" value="" class="form-control" maxlength="11"  placeholder=""/>
                            </div>

                        </div>

                        </div>



                    </div>



                </div>



                <div class="col-md-6">



                    <div class="form-group row">



                        <label class="col-sm-3 col-form-label">Date of birth</label>



                        <div class="col-sm-9">



                            <input type="date" name="date_of_birth" value="" class="form-control"   placeholder="" />



                        </div>



                    </div>



                </div>



            </div>

            <input type="hidden" id="latitude" value="" name="latitude">

<input type="hidden" id="longitude" value="" name="longitude">

            <div class="row">
                



                <div class="col-md-6">



                    <div class="form-group row">



                        <label class="col-sm-3 col-form-label">Email</label>



                        <div class="col-sm-9">



                            <input type="email" name="email" value="" class="form-control" />



                        </div>



                    </div>



                </div>



                <div class="col-md-6">



                    <div class="form-group row">



                        <label class="col-sm-3 col-form-label">Address</label>



                        <div class="col-sm-9">



                            <input type="text" name="address" value="" class="form-control" />



                        </div>



                    </div>



                </div>



            </div>



                    <div class="form-group">



                        <label for="exampleTextarea1">Private Notes*</label>



                        <textarea class="form-control" id="exampleTextarea1" name="private_notes" rows="4"></textarea>



                    </div>




                   <div class="form-group">
                    <label for="referred_by" class="col-form-label d-block">Referred By</label>
                    <select id="referred_by" name="referred_by" class="js-example-basic-single form-control" style="width: 100%;">
                        @foreach($referreds as $ref)
                            <option value="{{ $ref->referred_by }}">
                                {{ Ucfirst($ref->referred_by) }}
                            </option>
                        @endforeach
                    </select>
                </div>






            <div class="modal-footer">



                <input type="submit" class="btn btn-primary mr-2" value="Submit">



                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>



            </div>



            @csrf



            </form>



        </div>



        </div>



      </div>



</div>



<!-- <script>

$('#lookup_field').setupPostcodeLookup({

    api_key: 'ak_kr1v46iv0aNNht6IWqYTiNYN2lxLb',

    output_fields: {

        line_1: '#first_line',

        line_2: '#second_line',

        line_3: '#third_line',

        post_town: '#post_town',

        postcode: '#postcode'

    }

});

function updateAddress(elem){

    var optionsText = elem.options[elem.selectedIndex].text;



    $("#addresstextarea").val(optionsText);

    const myArr = elem.value.split(",");

    $('#latitude').val(myArr[0]);

    $('#longitude').val(myArr[1]);

}



</script> -->



@endsection