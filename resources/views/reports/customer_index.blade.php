

@section('pagescript')

<script src="{{asset('js/select2.js')}}"></script>

<script src="{{asset('js/formpickers.js')}}"></script>

@stop

@extends('layouts.admin')



@section('title', 'Booking Reports by Customer | '. (auth()->user()->companySetting->site_name ?? ''))



@section('content')



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Customer Report

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('/home')}}">Reports</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Customer Report</li>

                </ol>

            </nav>

        </div>

        @if(Session::has('error'))

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('error')}}

        </div>

        @endif

        <div class="row">

          <div class="col-md-12 grid-margin stretch-card">

            <div class="card">

              <div class="card-body">

                <h4 class="card-title">Customer Report</h4>

                <p class="card-description">

                  In order to get report please select between dates <code>From Date</code> & <code>To Date.</code> You can also specifies with your Customer!

                </p>

                <form class="form-inline" action="{{url('record/customer')}}" method="get">

                  <div class="input-group">

                    <div id="datepicker-popup-new" class="input-group date datepicker">

                      <input type="text" name="start_date" class="form-control">

                      <span class="input-group-addon input-group-append border-left">

                        <span class="far fa-calendar input-group-text"></span>

                      </span>

                    </div>

                  </div>

                  <div class="input-group">

                    <div id="datepicker-popup-new2" class="input-group date datepicker">

                      <input type="text" name="end_date" class="form-control">

                      <span class="input-group-addon input-group-append border-left">

                        <span class="far fa-calendar input-group-text"></span>

                      </span>

                    </div>

                  </div>

                  

                    <div class="form-group col-md-3">

                    <select id="customer_id" name="customer_id" class="form-control js-example-basic-single w-100">

                    <option disabled>Select a customer</option>

                      @foreach($customers as $customer)

                          <option value="{{$customer->id}}">{{$customer->first_name}} {{$customer->last_name}}</option>

                      @endforeach

                    </select>

                    </div>

                  

                  <input type="submit" value="Submit" class="btn btn-primary ">



                </form>

              </div>

            </div>

          </div>

        </div>

    </div>

</div>



@endsection