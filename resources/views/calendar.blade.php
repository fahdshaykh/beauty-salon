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

            background: #1E252B;

            color: #FFFFFF;

            max-width: min(280px, calc(100vw - 30px)) !important;

            width: auto;

            border-radius: 4px;

            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);

            padding: 8px 12px;

            text-align: center;

            font-size: 0.825rem;

            word-break: break-word;

            pointer-events: none;

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

            font-size: 0.825rem;

        }

        .holiday {
            background: lightgray;
            pointer-events: none;
        }

        .calendar-filter-row .input-group.date {
            padding: 0 !important;
        }

        .calendar-filter-row .form-group {
            margin-bottom: 15px;
        }

        .calendar-filter-row .form-control,
        .calendar-filter-row .input-group-text,
        .calendar-filter-row .select2-container--default .select2-selection--single {
            height: 45px !important;
            padding-top: 0.8rem !important;
            padding-bottom: 0.8rem !important;
        }

        .calendar-filter-row .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 18px !important;
        }

        .calendar-filter-row .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 43px !important;
        }

        .calendar-filter-row #clear_calendar_dates {
            height: 45px;
        }

        .calendar-filter-row .select2-container {
            width: 100% !important;
        }

        /* Calendar Viewport & Scroll Container */
        .fc-view-container {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }

        /* Responsive Layout for FullCalendar */
        @media (max-width: 767.98px) {
            .calendar-card-body {
                padding: 1rem 0.5rem !important;
            }

            .fc-toolbar {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                gap: 10px !important;
                margin-bottom: 1rem !important;
            }

            .fc-toolbar .fc-center {
                order: 1;
                float: none !important;
                display: flex !important;
                justify-content: center !important;
                width: 100% !important;
                text-align: center !important;
                margin: 0 !important;
            }

            .fc-toolbar .fc-left {
                order: 2;
                float: none !important;
                display: flex !important;
                justify-content: center !important;
                width: 100% !important;
                text-align: center !important;
                margin: 0 !important;
            }

            .fc-toolbar .fc-right {
                order: 3;
                float: none !important;
                display: flex !important;
                justify-content: center !important;
                width: 100% !important;
                text-align: center !important;
                margin: 0 !important;
            }

            .fc-toolbar .fc-center h2 {
                font-size: 1.2rem !important;
                margin: 0 !important;
            }

            .fc button {
                padding: 0.35rem 0.65rem !important;
                font-size: 0.825rem !important;
                height: 36px !important;
                line-height: 1 !important;
            }

            .fc-month-view > table,
            .fc-basicWeek-view > table,
            .fc-agendaWeek-view > table {
                min-width: 650px !important;
            }

            .fc-agendaDay-view > table,
            .fc-basicDay-view > table {
                min-width: 100% !important;
            }

            .fc-event {
                font-size: 0.75rem !important;
                line-height: 1.2 !important;
            }

            .turnover-day {
                font-size: 0.725rem !important;
            }

            #myModal .modal-dialog {
                max-width: 96% !important;
                margin: 0.5rem auto !important;
            }

            #myModal .modal-body {
                padding: 0.75rem 0.5rem !important;
            }

            #payment-modal,
            #cancel-modal {
                width: 92% !important;
                left: 4% !important;
                right: 4% !important;
            }
        }

        @media (max-width: 575.98px) {
            .calendar-filter-row .form-group {
                margin-bottom: 10px;
            }

            .fc-toolbar .fc-center h2 {
                font-size: 1.05rem !important;
            }

            .fc button {
                padding: 0.3rem 0.5rem !important;
                font-size: 0.775rem !important;
            }
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

                        <div class="row calendar-filter-row">
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
                        <div class="row calendar-filter-row">
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
                            <div class="card-body calendar-card-body">

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
