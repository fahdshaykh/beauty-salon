@section('pagescript')

    <script src="{{asset('js/x-editable.js')}}"></script>

@stop

@extends('layouts.admin')



@section('title', 'Booking Report Records | '. (auth()->user()->companySetting->site_name ?? ''))



@section('content')

<style>

.switch {

    position: relative;

    display: inline-block;

    width: 60px;

    height: 34px;

}



.switch input {

    opacity: 0;

    width: 0;

    height: 0;

}



.slider {

    position: absolute;

    cursor: pointer;

    top: 0;

    left: 0;

    right: 0;

    bottom: 0;

    background-color: #ccc;

    -webkit-transition: .4s;

    transition: .4s;

}



.slider:before {

    position: absolute;

    content: "";

    height: 26px;

    width: 26px;

    left: 4px;

    bottom: 4px;

    background-color: white;

    -webkit-transition: .4s;

    transition: .4s;

}



input:checked+.slider {

    background-color: #392C70;

}



input:focus+.slider {

    box-shadow: 0 0 1px #2196F3;

}



input:checked+.slider:before {

    -webkit-transform: translateX(26px);

    -ms-transform: translateX(26px);

    transform: translateX(26px);

}



/* Rounded sliders */

.slider.round {

    border-radius: 34px;

}



.slider.round:before {

    border-radius: 50%;

}

</style>

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Reports

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{route('service-bookings.index')}}">Service Bookings</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Bookings Listing</li>

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

        <?php $total_amount = 0; ?>

        @foreach($report_data as $key => $book)



<?php



$start_date = $book['1'];

$end_date = $book['2'];

$total_amount = $total_amount + $book['total_price'];

$service_name = $book['0'];

$booking_status = $book['booking_status'];

$payment_type = $book['payment_type'];

?>

@endforeach

<?php
$export_start_date   = request('start_date') ?: ($start_date ?? 'no');
$export_end_date     = request('end_date') ?: ($end_date ?? 'no');
$export_category_id  = request('category_id') ?: 'no';
$export_service_id   = request('service_id') ?: 'no';
$export_status_id    = request('status_id') ?: 'no';
$export_payment_type = request('payment_instrument') ?: (request('payment_type') ?: ($payment_type ?? 'no'));
?>

        <div class="card">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <h4 class="card-title">Bookings Reports</h4>

                    </div>

                    <div class="col-md-6"></div>

                    <div class="col-md-3">

                        <div class="text-center" style="float: center;">

                            <span data-href="/report-export/{{$export_start_date}}/{{$export_end_date}}/{{$export_category_id}}/{{$export_service_id}}/{{$export_status_id}}/{{$export_payment_type}}" id="export" class="btn btn-primary" onclick="exportReports(event.target);">Export Booking Report</span>

                        </div>

                    </div>

                </div>

                <div class="row grid-margin">

                    <div class="col-12">

                      <div class="alert alert-warning mt-3" role="alert" style="text-align:center">

                          Start Date: <strong>@if($start_date) {{$start_date}} @else No Date Selected @endif</strong>, End Date: <strong>@if($end_date){{$end_date}} @else No Date Selected @endif</strong>

                      </div>

                      <div style="text-align:center">

                          <p>Grand Total: {{ currency_symbol() }}{{$total_amount}}</p>

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

                                        <!-- <th>End Date</th> -->

                                        <th>Status</th>

                                        <th>Payment Method</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    

                                    @foreach($report_data as $key => $book)

                                    <tr>

                                        <td>{{$num}}</td>

                                        <td>{{$book['user_name']}}</td> <!--  showing user names -->

                                        <td>{{$book['customer_names']}}</td> <!--  showing customers names -->

                                        <td style="max-width: 200px;">
                                            @if(!empty($book['services']) && is_array($book['services']))
                                                @forelse($book['services'] as $service)
                                                    @php
                                                        $sName = is_array($service) ? ($service['name'] ?? '') : (is_object($service) ? ($service->name ?? '') : (string)$service);
                                                        $sColor = is_array($service) ? ($service['color'] ?? '#6c757d') : (is_object($service) ? ($service->color ?? $service->service_color ?? '#6c757d') : '#6c757d');
                                                        $isLight = \Illuminate\Support\Str::startsWith($sColor, ['#fff', '#FFF', 'rgb(255']);
                                                    @endphp
                                                    @if(!empty($sName))
                                                        <span class="badge me-1 mb-1 badge-pill"
                                                            style="background-color: {{ $sColor }}; 
                                                                    color: {{ $isLight ? '#000' : '#fff' }};">
                                                            {{ $sName }}
                                                        </span>
                                                    @endif
                                                @empty
                                                    <span class="text-muted fst-italic">No services</span>
                                                @endforelse
                                            @elseif(!empty($book['0']))
                                                @php
                                                    $rawServices = array_filter(array_map('trim', explode(',', $book['0'])));
                                                @endphp
                                                @forelse($rawServices as $sName)
                                                    <span class="badge me-1 mb-1 badge-pill" style="background-color: #6c757d; color: #fff;">
                                                        {{ $sName }}
                                                    </span>
                                                @empty
                                                    <span class="text-muted fst-italic">No services</span>
                                                @endforelse
                                            @else
                                                <span class="text-muted fst-italic">No services</span>
                                            @endif
                                        </td>

                                        <td>{{ currency_symbol() }}{{$book['total_price']}}</td>

                                        

                                        <td>{{$book['due_date']}}</td>

                                        <!-- <td>{{$book['2']}}</td> -->

                                        <td>

                                            <?php $status = array('1' => 'Booked', '2' => 'In-Progress', '3' => 'Completed', '4' => 'Cancel', '5' => 'Survey'); ?>

                                            <?php $badges = array('1' => 'badge badge-primary', '2' => 'badge badge-info', '3' => 'badge badge-success', '4' => 'badge badge-danger', '5' => 'badge badge-warning'); ?>

                                            @foreach($status as $statusKey => $check)

                                            <label class="<?php echo $badges[$book['booking_status']]; ?> badge-pill">@if($statusKey == $book['booking_status']) {{$check}} @endif</label>

                                            @endforeach

                                        </td>

                                        <td>{{$book['payment_type']}}</td>

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
    <!-- content-wrapper ends -->
</div>
<!-- main-panel ends -->

@endsection



<script>

function exportReports(_this) {

    let _url = $(_this).data('href');

    var path = {!! json_encode(url('/')) !!}

    window.location.href = path+_url;

}

</script>