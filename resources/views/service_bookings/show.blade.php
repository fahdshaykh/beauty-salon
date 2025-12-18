@extends('layouts.admin')







@section('title', 'Booking Details | '. (auth()->user()->companySetting->site_name ?? ''))







@section('content')







<style>



@media only print {



    #non-printable {



    display: none;



    }



}

.hide

{

   display:none;

}

.loader{

    left:0;

    right:0;

    z-index: 10;

    position: fixed;

    border:none;

    width:100%;

    top:200px;

}

</style>



<!-- partial -->

<center id="loader-modal" class="loader hide" >

                           

  <div class="dot-opacity-loader ">

    <span></span>

    <span></span>

    <span></span>

  </div>

  

</center>

<div class="main-panel">



    <div class="content-wrapper">



        @foreach($booking_detail as $book)



        <?php



            $invoice = $book->invoice;

            $booking_id = $book->id;

            $company_id = $book->company_id;



            $first_name = $book->customer->first_name;



            $last_name = $book->customer->last_name;

            $postcode = $book->customer->zip;

            $primary_phone = $book->customer->primary_phone;
            $secondary_phone = $book->customer->secondary_phone;


            $email = $book->customer->email;



            $address = $book->customer->address;



            $due_date = $book->due_date;



            $total_price = $book->total_price;

            $vat = $book->vat;

            $comments = $book->comment;

            $payment_method = 'Not Available';

            $chequenumber= '';

          if($book->payment_instrument){

            $payment_method = $book->payment_instrument;

            $chequenumber = $book->payment_instrument_number;

          }

          $payment_days = 'Payment Upon Completion';

          if($book->payment_days>0){

            $payment_days = $book->payment_days;

          }



        ?>



        @endforeach







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



            <div class="col-lg-12">



                <div class="card px-2">



                    <div class="card-body">



                        <div class="row">

              

                            <div class="col-md-4" style="margin-bottom: 0rem;">



                                <a class="navbar-brand brand-logo" href="{{url('/home')}}"><img src="@if($company_logo){{asset('email_logo/'.$company_logo)}} @else {{asset('profiles/power_diary.png')}} @endif" width="250" height="50" alt="logo"/></a>



                            </div>



                        <div class="col-md-4"></div>



                        <div class="col-md-4">



                            <div class="text-center" style="margin-top: 0rem; float: center;">



                                <h3 class="text-right my-3">Invoice&nbsp;&nbsp;#{{$invoice}}-{{$postcode}}</h3>



                            </div>



                        </div>



                        </div>



                        <hr>



                        <div class="container-fluid d-flex justify-content-between">



                          <div class="col-lg-6 pl-0">



                            <p class="mt-5 mb-2"><b>{!! $header !!}</b></p>



                          </div>



                          <div class="col-lg-3 pr-0">



                            <p class="mt-5 mb-2 text-right"><b>Invoice to</b></p>



                            <p class="text-right">{{$first_name}} {{$last_name}},<br> @if($primary_phone) {{$primary_phone}} @else {{$secondary_phone}} @endif, {{$email}}<br> {{$address}}.</p>



                          </div>



                        </div>



                        <div class="container-fluid d-flex justify-content-between">



                          <div class="col-lg-3 pl-0">



                            <p class="mb-0 mt-5">Invoice Date : {{date('d-m-Y')}}</p>



                            <p>Booking Date : {{date('d-m-Y', strtotime($due_date))}}</p>



                          </div>

                          <div class="col-lg-3 pr-0">



                          <p class="mb-0 mt-5 text-right">Payment Method: {{ucfirst(trans($payment_method))}}</p>

                          @if($payment_method=='cheque')

                          <p class="text-right">Cheque#: {{$chequenumber}}.</p>

                          @endif

                          <!-- <p class="text-right">Payment Days: {{$payment_days}}</p> -->

                          </div>

                        </div>



                        <div class="container-fluid mt-5 d-flex justify-content-center w-100">



                          <div class="table-responsive w-100">



                              <table class="table">



                                <thead>



                                    <tr class="bg-dark text-white">



                                        <th>Ser.#</th>



                                      <th>Service Name</th>



                                      <th>Price</th>



                                    </tr>



                                </thead>



                                <tbody>



                                    <?php $num = 1; ?>



                                @foreach($services_names[$booking_id] as $key=>$book)

                                  <?php $book = explode(',',$book) ?>

                                  <tr>



                                    <td>{{$num}}</td>



                                    <td>{{$book[0]}}</td>



                                    <td>@if($book[1]>0){{ currency_symbol() }} {{$book[1]}} @endif</td>



                                  </tr>



                                  <?php $num++; ?>



                                @endforeach



                                </tbody>



                              </table>



                            </div>



                        </div>



                        <div class="container-fluid mt-5 w-100">



                          <h6 class="text-right">Sub Total : {{ currency_symbol() }}{{$total_price}}</h6>

                          @if($vat>0)

                          <h6 class="text-right">Vat : {{ currency_symbol() }}{{$vat}}</h6>

                          @endif

                          <h6 class="text-right">Grand Total : {{ currency_symbol() }}{{$total_price+$vat}}</h6>

                          <hr id="non-printable">



                        </div>



                        <div class="container-fluid w-100" id="non-printable">



                          <button class="btn btn-primary float-right mt-4 ml-2" onclick="window.print();"><i class="fa fa-print mr-1"></i>Print</button>

                          <?php if($email!=""){ ?>

                          <a href="javascript:void(0);" onclick="send_mail()" class="btn btn-info float-right mt-4"><i class="fa fa-share mr-1"></i>Send Invoice</a>

                          <?php } ?>

                        </div>



                        <div class="col-lg-6">

                            <p class=""><b>Notes:</b>{{$comments}}</p>

                            <!-- <p><b>Email:</b> {{auth()->user()->email}}</p> -->

                            <p>{!! $footer !!}</p>

                        </div>



                    </div>



                </div>



            </div>



        </div>



    </div>



</div>

<script>

 

function send_mail(){

  $('#loader-modal').removeClass( "hide");

  $.ajax({

      type: "GET",

      url:"{{url('invoice-mail',['id'=> $booking_id])}}",

      success: function(response) {

        $('#loader-modal').addClass("hide");

          if(response.success){

              showSuccessToast(response.success);

          }

          else{

              showDangerToast(response.error);

          }

          

          

      }

  });

}  

</script>

@endsection