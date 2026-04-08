@section('pagescript')

<script src="{{asset('js/select2.js')}}"></script>

<script src="{{asset('js/formpickers.js')}}"></script>

@stop

@extends('layouts.admin')



@section('title', 'Inventory Booking Reports | '. (auth()->user()->companySetting->site_name ?? ''))



@section('content')



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Inventory Usage Report

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('/home')}}">Reports</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Inventory Usage Report</li>

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

                <h4 class="card-title">Inventory Usage Report</h4>

                <p class="card-description">

                  In order to get report please select between dates <code>From Date</code> & <code>To Date.</code> You can also specify an Inventory item!

                </p>

                <form class="form-inline" action="{{url('record/inventory-usage')}}" method="get">

                  <div class="input-group">

                    <div id="datepicker-popup-new" class="input-group date datepicker">

                      <input type="text" name="start_date" class="form-control" placeholder="From Date">

                      <span class="input-group-addon input-group-append border-left">

                        <span class="far fa-calendar input-group-text"></span>

                      </span>

                    </div>

                  </div>

                  <div class="input-group">

                    <div id="datepicker-popup-new2" class="input-group date datepicker">

                      <input type="text" name="end_date" class="form-control" placeholder="To Date">

                      <span class="input-group-addon input-group-append border-left">

                        <span class="far fa-calendar input-group-text"></span>

                      </span>

                    </div>

                  </div>



                    <div class="form-group col-md-3">

                    <select id="inventory_id" name="inventory_id" class="form-control js-example-basic-single w-100">

                      @foreach($inventories as $inventory)

                          <option value="{{$inventory->id}}">{{$inventory->name}}</option>

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



@endsection
