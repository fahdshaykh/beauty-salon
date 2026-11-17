

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

                <form action="{{url('record/customer')}}" method="get">
                  <div class="form-row align-items-start">
                    <div class="col-12 col-sm-6 col-md-3 mb-2">
                      <div id="datepicker-popup-new" class="input-group date datepicker">
                        <input type="text" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', request('start_date')) }}" placeholder="From Date" autocomplete="off">
                        <span class="input-group-addon input-group-append border-left">
                          <span class="far fa-calendar input-group-text"></span>
                        </span>
                      </div>
                      @error('start_date')
                        <div class="text-danger small mt-1 font-weight-normal">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 mb-2">
                      <div id="datepicker-popup-new2" class="input-group date datepicker">
                        <input type="text" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', request('end_date')) }}" placeholder="To Date" autocomplete="off">
                        <span class="input-group-addon input-group-append border-left">
                          <span class="far fa-calendar input-group-text"></span>
                        </span>
                      </div>
                      @error('end_date')
                        <div class="text-danger small mt-1 font-weight-normal">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 mb-2">
                      <select id="customer_id" name="customer_id" class="form-control js-example-basic-single w-100" style="width: 100%;">
                        <option value="" disabled selected>Select a customer</option>
                        @foreach($customers as $customer)
                            <option value="{{$customer->id}}" {{ (old('customer_id', request('customer_id')) == $customer->id) ? 'selected' : '' }}>{{$customer->first_name}} {{$customer->last_name}}</option>
                        @endforeach
                      </select>
                      @error('customer_id')
                        <div class="text-danger small mt-1 font-weight-normal">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-12 col-sm-6 col-md-auto mb-2">
                      <button type="submit" class="btn btn-primary" style="height: 46px; min-width: 100px;">Submit</button>
                    </div>
                  </div>
                </form>

              </div>

            </div>

          </div>

        </div>

    </div>

</div>



@endsection