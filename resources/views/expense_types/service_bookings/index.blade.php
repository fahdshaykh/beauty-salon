@section('pagescript')

    <script src="{{asset('js/x-editable.js')}}"></script>

@stop

@extends('layouts.admin')



@section('title', 'Service Bookings | '.Session::get('site_name'))



@section('content')



<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Service Bookings

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{route('service-bookings.index')}}">Service Bookings</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Bookings Listing</li>

                </ol>

            </nav>

        </div>



        <div class="alert alert-success" style="display:none;" id="status-success"></div>



        @if(Session::has('success'))

        <div class="alert alert-success alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('success')}}

        </div>



        @elseif(Session::has('destroy'))

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('destroy')}}

        </div>

        @endif



        @if(Session::has('update'))

        <div class="alert alert-success alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('update')}}

        </div>

        @endif



        <?php $booking_edit_array = array(4,3); ?>

        <?php $booking_edit = implode(',', $booking_edit_array); ?>

        <?php $booking_delete_array = array(4,4); ?>

        <?php $booking_delete = implode(',', $booking_delete_array); ?>

        <?php $booking_show_array = array(4,5); ?>

        <?php $booking_show = implode(',', $booking_show_array); ?>

 



        <div class="card">

            <div class="card-body">

                <h4 class="card-title"></h4>

                <div class="row">

                    <div class="col-12">

                        <div class="table-responsive">

                            <table id="order-listing" class="table table-striped">

                                <thead>

                                    <tr>

                                        <th>Sr.No.</th>

                                        <th>User</th>

                                        <th>Customer</th>

                                        <th style="width: 10%;">Services</th>

                                        <th>Price</th>

                                        <th>Pay Days</th>

                                        <th>Book Date</th>

                                        <th>Book Time</th>

                                        <th>Address</th>

                                        <th>Staus</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    

                                    @foreach($data_array as $key => $book)

                            

                                    <tr>

                                        <td>{{$num}}</td>

                                        <td>{{$book['user_name']}}</td> <!--  showing user names -->

                                        <td>{{$book['customer_name']}}</td> <!--  showing customers names -->

                                        <td style="max-width:160px;">{{$book['services']}}</td> <!--  showing service names -->

                                        <td><i class="fas fa-pound-sign"></i>{{$book['total_price']}}</td>

                                        <td>{{$book['payment_days']}}</td>

                                        <td style="width: 15%;">{{$book['due_date']}}</td>

                                        <td style="width: 15%;">{{$book['due_time']}}</td>

                                        <td>{{$book['customer_zip']}}-{{$book['customer_address']}}</td>

                                        <td width="100%">

                                        <?php $status = array('1' => 'Booked', '2' => 'In-Progress', '3' => 'Completed', '4' => 'Cancel'); ?>

                                        <div class="form-group">

                                        <select class="form-control form-control-sm" id="change" data-booking_id="{{$book['id']}}" onchange="changeLevel(this);">

                                            @foreach($status as $statusKey => $check)

                                                <option value="{{$statusKey}}" @if($statusKey == $book['service_status']) selected @endif >{{$check}}</option>

                                            @endforeach

                                            </select>

                                            </div> 

                                        </td>

                                        <td>

                                        @if(in_array($booking_edit, session()->get('all_rp')['0']))

                                            <a href="{{url('service-bookings/edit',$book['id'])}}">

                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>

                                            </a> 

                                        @endif

                                        @if(in_array($booking_delete, session()->get('all_rp')['0']))

                                            |<a onclick="return confirm('Are you sure to delete');" href="{{url('service-bookings/destroy',$book['id'])}}">

                                                <i class="fas fa-trash text-danger"></i>                          

                                            </a>

                                        @endif

                                        @if(in_array($booking_show, session()->get('all_rp')['0']))

                                        |<a href="{{url('view-booking',$book['id'])}}" title="View/Send Invoice">

                                                <i class="fa fa-eye text-primary"></i>

                                            </a>

                                        </td>

                                        @endif

                                    </tr>

                                    <?php $num++; ?>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

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

            <form method="post" id="payment-form">

            <div class="modal-body">

                

                <div class="form-group">

                    <select class="form-control form-control-lg" name="payment_method" id="payment_method" required>

                        <option value="">Select Payment Method</option>

                        <option value="cash">Cash</option>

                        <option value="card">Card</option>

                        <option value="paypal">PayPal</option>

                        <option value="invoice">Invoice</option>

                        <option value="cheque">Cheque</option>

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

            @csrf

        </form>

            </div>

        </div>

    </div>





    @endsection



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>



<script>



$(document).ready(function(){

    

    $("#payment-form").submit(function (event) {

            event.preventDefault();

            $.ajax({

                url: "{{url('change-booking-status')}}",

                type: "POST",

                data: $("#payment-form").serialize(),

                success: function (response) {

                    if(response.success){

                    showSuccessToast(response.success);

                    $("#payment-modal").modal('hide');

                }

                else{

                    showDangerToast(response.error);

                }

                },

            });

        });





    changeLevel = function(level){

    var status_value = level.value;

    var booking_id = $(level).data('booking_id');

    if(status_value == 3)

        {

            $("#payment-modal").modal('show');

            $("#invoice").val(booking_id);

            $("#service_status").val(status_value);

            return false;

        }

        else{



            $.ajax({

            type: "POST",

            url:"{{url('booking-status')}}",

            data: { id: booking_id,service_status: status_value,_token: "{{ csrf_token() }}"  },

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

    $("#payment_method").on('change',function(){

        var value=$(this).val();

        if(value == 'cheque'){

            $(".cheque_number_label").show();

            $("#cheque_number").show();

        }

        else {

            $(".cheque_number_label").hide();

            $("#cheque_number").hide();

        }

    });

});

</script>





