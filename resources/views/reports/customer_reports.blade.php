

@extends('layouts.admin')



@section('title', 'Customer Report Records | '. (auth()->user()->companySetting->site_name ?? ''))



@section('content')



<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Reports

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('customer-reports')}}">Reports</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Customer Report</li>

                </ol>

            </nav>

        </div>

        <?php $customer_report_array = array(5,4); ?>

        <?php $customer_report = implode(',', $customer_report_array); ?>

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

                        <h4 class="card-title">Customer Reports</h4>

                    </div>

                    <div class="col-md-6"></div>

                    <div class="col-md-3">

                        <div class="text-center" style="float: center;">

  

                        <span data-href="/customer-report-by-date/{{$start_date}}/{{$end_date}}/{{$customer_id}}" id="export" class="btn btn-primary" onclick="exportReports(event.target);">Export Customer Report</span>



                        </div>

                    </div>

                </div>

                <div class="row grid-margin">

                    <div class="col-12">

                      <div style="text-align:center">

                          <p>Grand Total: <i class="fas fa-dollar-sign"></i>{{$total_amount}}</p>

                      </div>

                    </div>

                    <div class="col-12">

                        <div class="table-responsive">

                            <table id="order-listing" class="table">

                                <thead>

                                    <tr class="bg-primary text-white">

                                        <th>Sr.No #</th>

                                        <th>Booking User</th>

                                        <th>Customer Name</th>

                                        <th>Services Name</th>

                                        <th>Total Price</th>

                                        <th>Due Date</th>

                                        <th>Due Time</th>

                                        <th>Status</th>

                                        <th>Payment Method</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    

                                    @foreach($data_array as $key => $book)

                                    <tr>

                                        <td>{{$num}}</td>

                                        <td>{{$book['user_name']}}</td> <!--  showing user names -->

                                        <td>{{$book['customer_name']}}</td> <!--  showing customers names -->

                                        <td style="max-width:160px;">{{$book['services']}}</td> <!--  showing service names -->

                                        <td><i class="fas fa-dollar-sign"></i>{{$book['total_price']}}</td>

                                        <td>{{$book['due_date']}}</td>

                                        <td>{{$book['due_time']}}</td>

                                        <td>

                                            <?php $status = array('1' => 'Booked', '2' => 'In-Progress', '3' => 'Completed', '4' => 'Cancel'); ?>

                                            <?php $badges = array('1' => 'badge badge-info', '2' => 'In-Progress', '3' => 'badge badge-success', 'badge badge-warning' => 'badge badge-danger'); ?>

                                            @foreach($status as $statusKey => $check)

                                            <label class="@if($book['service_status'] == 1) badge badge-info @endif @if($book['service_status'] == 2) badge badge-warning @endif @if($book['service_status'] == 3) badge badge-success @endif @if($book['service_status'] == 4) badge badge-danger @endif">@if($statusKey == $book['service_status']) {{$check}} @endif</label>

                                            @endforeach

                                        </td>

                                        <td>{{$book['payment_instrument']}}</td>

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