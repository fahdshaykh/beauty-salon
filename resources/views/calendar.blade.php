@section('pagescript')



    <script src="{{ asset('js/calendar.js') }}?v={{ filemtime(public_path('js/calendar.js')) }}"></script>



    <script src="{{ asset('js/select2.js') }}"></script>

    <script src="{{ asset('js/js-grid.js') }}"></script>

    <script src="{{ asset('js/formpickers.js') }}"></script>



@stop

@extends('layouts.admin')



@section('title', 'Dashboard | bSaloon')







@section('content')



    <link rel="stylesheet" href="<?php echo asset('css/jobmodal.css'); ?>" type="text/css">

    <style>
        .popper,

        .tooltipevent {

            position: absolute;

            z-index: 9999;

            background: black;

            color: white;

            width: 250px;

            border-radius: 3px;

            box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);

            padding: 10px;

            text-align: center;

        }

        .style5 .tooltipevent {

            background: #1E252B;

            color: #FFFFFF;

            max-width: 200px;

            width: auto;

            font-size: .8rem;

            padding: .5em 1em;

        }

        .fc-event {

            cursor: pointer;

        }

        .holiday {
            background: lightgray;
            pointer-events: none;
        }
    </style>

    <div class="main-panel">

        <div class="content-wrapper">

            <div class="page-header">

                <h3 class="page-title">

                    Dashboard

                </h3>

            </div>


            <div class="row">



                <div class="col-md-12">



                    @role('admin')

                        <div class="row">
                            <div class="form-group col-12 col-sm-6 col-md-3">
                                <select id="user_id" name="user_id" class="form-control js-example-basic-single w-100"
                                    style="width:100%">
                                    <option value="0">All Bookings</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-3">
                                <select id="service_status_admin" name="service_status_admin"
                                    class="form-control js-example-basic-single w-100" style="width:100%">
                                    <option value="0">All Orders</option>
                                    <option value="1">Booked</option>
                                    <option value="2">In-progress</option>
                                    <option value="3">Completed</option>
                                </select>
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-3">
                                <div id="datepicker-popup-new" class="input-group date datepicker">
                                    <input type="text" name="start_date" id="calendar_start_date" class="form-control" placeholder="From Date" autocomplete="off">
                                    <span class="input-group-addon input-group-append border-left">
                                        <span class="far fa-calendar input-group-text"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-3">
                                <div class="d-flex">
                                    <div id="datepicker-popup-new2" class="input-group date datepicker flex-grow-1">
                                        <input type="text" name="end_date" id="calendar_end_date" class="form-control" placeholder="To Date" autocomplete="off">
                                        <span class="input-group-addon input-group-append border-left">
                                            <span class="far fa-calendar input-group-text"></span>
                                        </span>
                                    </div>
                                    <button type="button" id="clear_calendar_dates" class="btn btn-outline-danger ml-1 px-2" title="Clear Date Filter" style="display:none;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="form-group col-12 col-sm-6 col-md-4">
                                <select id="service_status" name="service_status" class="form-control js-example-basic-single w-100"
                                    style="width:100%">
                                    <option value="0">All Orders</option>
                                    <option value="1">Booked</option>
                                    <option value="2">In-progress</option>
                                    <option value="3">Completed</option>
                                </select>
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-4">
                                <div id="datepicker-popup-new" class="input-group date datepicker">
                                    <input type="text" name="start_date" id="calendar_start_date" class="form-control" placeholder="From Date" autocomplete="off">
                                    <span class="input-group-addon input-group-append border-left">
                                        <span class="far fa-calendar input-group-text"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-4">
                                <div class="d-flex">
                                    <div id="datepicker-popup-new2" class="input-group date datepicker flex-grow-1">
                                        <input type="text" name="end_date" id="calendar_end_date" class="form-control" placeholder="To Date" autocomplete="off">
                                        <span class="input-group-addon input-group-append border-left">
                                            <span class="far fa-calendar input-group-text"></span>
                                        </span>
                                    </div>
                                    <button type="button" id="clear_calendar_dates" class="btn btn-outline-danger ml-1 px-2" title="Clear Date Filter" style="display:none;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endrole



                    <div class="card">


                        <?php
                        $permvalue = 'no';
                        ?>

                        @can('calendar create booking')
                            <?php
                            $permvalue = 'yes';
                            ?>
                        @endcan

                        @can('view calendar')
                            <div class="card-body">

                                <input type="hidden" id="creat-booking-perm" value="{{ $permvalue }}">

                                <h4 class="card-title">Booking Calender</h4>



                                <div id="calendar" class="full-calendar"></div>



                            </div>
                        @endcan



                    </div>



                </div>



            </div>



        </div>



        <!-- content-wrapper ends -->



    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">



    </div>

    <!-- main-panel ends -->



@endsection
