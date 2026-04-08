@extends('layouts.admin')



@section('title', 'Inventory Usage Report Records | '. (auth()->user()->companySetting->site_name ?? ''))



@section('content')



<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Reports

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('inventory-reports')}}">Reports</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Inventory Usage Report</li>

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



        <div class="card">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <h4 class="card-title">Inventory Usage Reports</h4>

                    </div>

                    <div class="col-md-6"></div>

                    <div class="col-md-3">

                        <div class="text-center" style="float: center;">



                        <span data-href="/inventory-report-by-date/{{$start_date}}/{{$end_date}}/{{$inventory_id}}" id="export" class="btn btn-primary" onclick="exportReports(event.target);">Export Inventory Report</span>


                        </div>

                    </div>

                </div>

                <div class="row grid-margin">

                    <div class="col-12">

                      <div style="text-align:center">

                          <p>Total Inventory Used: <strong>{{ $total_quantity_used }}</strong></p>

                      </div>

                    </div>

                    <div class="col-12">

                        <div class="table-responsive">

                            <table id="inventory-listing" class="table table-bordered">

                                <thead>

                                    <tr class="bg-primary text-white">

                                        <th>Sr.No #</th>

                                        <th>Inventory Name</th>

                                        <th>Booking ID</th>

                                        <th>Customer Name</th>

                                        <th>Employee Name</th>

                                        <th>Quantity Used</th>

                                        <th>Booking Date</th>

                                        <th>Booking Time</th>

                                        <th>Total Price</th>

                                        <th>Status</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>



                                    @foreach($data_array as $key => $record)

                                    <tr>

                                        <td>{{$num}}</td>

                                        <td>{{$record['inventory_name']}}</td>

                                        <td>#{{$record['booking_id']}}</td>

                                        <td>{{$record['customer_name']}}</td>

                                        <td>{{$record['employee_name']}}</td>

                                        <td style="text-align: center;">{{$record['quantity_used']}}</td>

                                        <td>{{$record['booking_date']}}</td>

                                        <td>{{$record['booking_time']}}</td>

                                        <td>{{ currency_symbol() }}{{$record['total_price']}}</td>

                                        <td>

                                            <?php

                                                $statusColors = [
                                                    '1' => 'badge badge-info',
                                                    '2' => 'badge badge-warning',
                                                    '3' => 'badge badge-success',
                                                    '4' => 'badge badge-danger'
                                                ];

                                                $statusLabels = [
                                                    '1' => 'Booked',
                                                    '2' => 'In-Progress',
                                                    '3' => 'Completed',
                                                    '4' => 'Cancelled'
                                                ];
                                            ?>

                                            <label class="{{$statusColors[$record['service_status']] ?? 'badge badge-secondary'}}">

                                                {{$statusLabels[$record['service_status']] ?? 'Unknown'}}

                                            </label>

                                        </td>

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

@endsection



<script>

function exportReports(_this) {

    let _url = $(_this).data('href');

    var path = {!! json_encode(url('/')) !!}

    window.location.href = path+_url;

}

</script>
