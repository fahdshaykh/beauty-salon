
@section('pagescript')
<script src="{{asset('js/select2.js')}}"></script>
<script src="{{asset('js/formpickers.js')}}"></script>
@stop
@extends('layouts.admin')

@section('title', 'Customers Graph | '. (auth()->user()->companySetting->site_name ?? ''))

@section('content')

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
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
                        <input id="graph-button" type="button" value="Submit" class="btn btn-primary mb-2">

                    </div>

                </form>
            </div>
            
        </div>

        <div class="col-md-10 grid-margin stretch-card mt-4">
            <div class="card">
            <div class="card-body" id="chartBar">
                <h4 class="card-title">
                <i class="fas fa-chart-pie"></i>
               Customers Graph
                </h4>
                <canvas id="country-status-chart" class="mt-3"></canvas>
                <div id="sales-status-chart-legend" class="sales-status-chart-legend"></div>                  
            </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
@endsection