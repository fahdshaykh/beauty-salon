@section('pagescript')

    <script src="{{asset('js/select2.js')}}"></script>

    <script src="{{asset('js/formpickers.js')}}"></script>

@stop

@extends('layouts.admin')



@section('title', 'Booking Reports by Date | '. (auth()->user()->companySetting->site_name ?? ''))



@section('content')



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Create Reports

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('/home')}}">Reports</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Create Reports</li>

                </ol>

            </nav>

        </div>

        @if(Session::has('error'))

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('error')}}

        </div>

        @endif

          <div class="col-12 grid-margin stretch-card">

            <div class="card">

              <div class="card-body">

                <h4 class="card-title">Booking Reports</h4>

                <p class="card-description">

                  In order to get report please select between dates <code>From Date</code> & <code>To Date.</code> You can also specifies with your category and service!

                </p>

                <form action="{{url('record/find')}}" method="get">
                  <div class="form-row align-items-start">
                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                      <div id="datepicker-popup-new" class="input-group date datepicker">
                        <input type="text" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', request('start_date')) }}" placeholder="From Date" autocomplete="off" required>
                        <span class="input-group-addon input-group-append border-left">
                          <span class="far fa-calendar input-group-text"></span>
                        </span>
                      </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                      <div id="datepicker-popup-new2" class="input-group date datepicker">
                        <input type="text" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', request('end_date')) }}" placeholder="To Date" autocomplete="off" required>
                        <span class="input-group-addon input-group-append border-left">
                          <span class="far fa-calendar input-group-text"></span>
                        </span>
                      </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                      <select class="form-control js-example-basic-single w-100" name="category_id" id="category_id" style="width: 100%;">
                        <option value="no" {{ old('category_id', request('category_id')) == 'no' ? 'selected' : '' }}>All Categories</option>
                        @foreach($categories as $category)
                          <option value="{{$category->id}}" {{ old('category_id', request('category_id')) == $category->id ? 'selected' : '' }}>{{$category->name}}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                      <select class="form-control js-example-basic-single w-100" name="service_id" id="service_id" style="width: 100%;">
                        <option value="no" {{ old('service_id', request('service_id')) == 'no' ? 'selected' : '' }}>All Services</option>
                        @foreach($services as $service)
                          <option value="{{$service->id}}" {{ old('service_id', request('service_id')) == $service->id ? 'selected' : '' }}>{{$service->name}}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                      <select class="form-control js-example-basic-single w-100" name="status_id" id="status_id" style="width: 100%;">
                        <option value="no" {{ old('status_id', request('status_id')) == 'no' ? 'selected' : '' }}>All Statuses</option>
                        <option value="1" {{ old('status_id', request('status_id')) == '1' ? 'selected' : '' }}>Booked</option>
                        <option value="2" {{ old('status_id', request('status_id')) == '2' ? 'selected' : '' }}>In-Progress</option>
                        <option value="3" {{ old('status_id', request('status_id')) == '3' ? 'selected' : '' }}>Completed</option>
                        <option value="4" {{ old('status_id', request('status_id')) == '4' ? 'selected' : '' }}>Cancelled</option>
                      </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                      <select class="form-control js-example-basic-single w-100" name="payment_instrument" id="payment_instrument" style="width: 100%;">
                        <option value="no" {{ old('payment_instrument', request('payment_instrument')) == 'no' ? 'selected' : '' }}>All Payment Methods</option>
                        <option value="cash" {{ old('payment_instrument', request('payment_instrument')) == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="paypal" {{ old('payment_instrument', request('payment_instrument')) == 'paypal' ? 'selected' : '' }}>PayPal</option>
                      </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-auto mb-3">
                      <button type="submit" class="btn btn-primary px-4" style="height: 46px; min-width: 120px;">Submit</button>
                    </div>
                  </div>
                </form>

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

            url:"{{url('get-services-by-category')}}",

            data:{category_id},

            success:function(response){

              $("#service_id").empty();

              console.log(response);          

              if(response){

                $("#service_id").append('<option value="no">All Services</option>');

                $.each(response, function( index, value ) {

                  $("#service_id").append('<option value="'+value['id']+'">'+value['name']+'</option>');

                });

                $("#service_id").trigger('change');

                }else{

                $("#customer-detail").hide();

              }

            }

        });

      }

    });

});

  

</script>

@endsection