

@section('pagescript')

<script src="{{asset('js/select2.js')}}"></script>

<script src="{{asset('js/formpickers.js')}}"></script>

@stop

@extends('layouts.admin')



@section('title', 'Booking Report by Customer | '.Session::get('site_name'))



@section('content')



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Graph Report

            </h3>



            <div class="col-md-12">
                <form>
                    <div class="row">   
                        <div class="col-4">
                            <div id="datepicker-popup-new" class="input-group date datepicker">
                                <input type="text" name="start_date" id="start-date" class="form-control" required>
                                <span class="input-group-addon input-group-append border-left">
                                <span class="far fa-calendar input-group-text"></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-4">

                            <div id="datepicker-popup-new2" class="input-group date datepicker">
                                <input type="text" name="end_date" id="end-date" class="form-control" required>
                                <span class="input-group-addon input-group-append border-left">
                                <span class="far fa-calendar input-group-text"></span>
                                </span>
                            </div>
                        </div>
                        <input id="company-graph-button" type="button" value="Submit" class="btn btn-primary mb-2">

                    </div>

                </form>
            </div>

            

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item active" aria-current="page">Graph Report</li>

                </ol>

            </nav>

        </div>



        <div class="col-md-12 grid-margin stretch-card mt-4">

            <div class="card">
                

            <div class="card-body" id="chartBar">

                <h4 class="card-title">

                <i class="fa fa-flag"></i>

                Bookings Chart

                </h4>

                <canvas id="company-orders-chart"></canvas>

                <div id="company-orders-chart-legend" class="company-orders-chart-legend"></div>                  

            </div>

            </div>

        </div>

    </div>

</div>



<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>

<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

@endsection