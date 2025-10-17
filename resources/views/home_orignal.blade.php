
@section('pagescript')
<script src="{{asset('js/calendar.js')}}"></script>
@stop

@extends('layouts.admin')

@section('title', 'Home | Resturant Management System')

@section('content')
<div class="main-panel">
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title">
              Dashboard
            </h3>
          </div>
          <!-- <div class="row grid-margin">
            <div class="col-12">
              <div class="card card-statistics">
                <div class="card-body">
                  <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                      <div class="statistics-item">
                        <p>
                          <i class="icon-sm fas fa-chart-line mr-2"></i>
                          Total Bookings
                        </p>
                        <h2>{{$total_order}}</h2>
                        <label class="badge badge-outline-success badge-pill">{{date('F Y')}}</label>
                      </div>
                      <div class="statistics-item">
                        <p>
                          <i class="icon-sm fas fa-hourglass-half mr-2"></i>
                          Booked Order
                        </p>
                        <h2>{{$total_booked}}</h2>
                        <label class="badge badge-outline-danger badge-pill">{{date('F Y')}}</label>
                      </div>
                      <div class="statistics-item">
                        <p>
                          <i class="icon-sm fas fa-cloud-download-alt mr-2"></i>
                          In-Progress Order
                        </p>
                        <h2>{{$total_progress}}</h2>
                        <label class="badge badge-outline-success badge-pill">{{date('F Y')}}</label>
                      </div>
                      <div class="statistics-item">
                        <p>
                          <i class="icon-sm fas fa-check-circle mr-2"></i>
                          Completed Order
                        </p>
                        <h2>{{$total_completed}}</h2>
                        <label class="badge badge-outline-success badge-pill">{{date('F Y')}}</label>
                      </div>
                      <div class="statistics-item">
                        <p>
                          <i class="icon-sm fas fa-circle-notch mr-2"></i>
                          Cancelled Order
                        </p>
                        <h2>{{$total_cancelled}}</h2>
                        <label class="badge badge-outline-success badge-pill">{{date('F Y')}}</label>
                      </div>
                      <div class="statistics-item">
                        <p>
                          <i class="icon-sm fa fa-user mr-2"></i>
                          New Customers
                        </p>
                        <h2>{{$total_customer}}</h2>
                        <label class="badge badge-outline-danger badge-pill">{{date('F Y')}}</label>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
          
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Booking Calender</h4>
                  <div id="calendar" class="full-calendar"></div>
                </div>
              </div>
            </div>
          </div>
          @if(Auth::user()->role_id == 1)
          <div class="row mt-3">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">
                    <i class="fas fa-table"></i>
                    Latest Orders
                  </h4>
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Serial.#</th>
                          <th>Customer Name</th>
                          <th>Services Name</th>
                          <th>Total Price</th>
                          <th>Booking Date</th>
                          <th>Booking Time</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $num = 1; ?>
                          
                          @foreach($data_array as $key => $book)
                            <tr>
                              <td>{{$num}}</td>
                              <td>{{$book['customer_id']}}</td> <!--  showing customers names -->
                              <td style="max-width:160px;">{{$book['0']}}</td> <!--  showing service names -->
                              <td>{{$book['total_price']}}</td>
                              <td>{{$book['due_date']}}</td>
                              <td>{{$book['due_time']}}</td>
                              <td>
                                <?php $status = array('1' => 'Booked', '2' => 'In-Progress', '3' => 'Completed', '4' => 'Cancel'); ?>
                                <?php $badges = array('1' => 'badge badge-info', '2' => 'In-Progress', '3' => 'badge badge-success', 'badge badge-warning' => 'badge badge-danger'); ?>
                                @foreach($status as $statusKey => $check)
                                <label class="@if($book['service_status'] == 1) badge badge-info badge-pill @endif @if($book['service_status'] == 2) badge badge-warning badge-pill @endif @if($book['service_status'] == 3) badge badge-success badge-pill @endif @if($book['service_status'] == 4) badge badge-danger badge-pill @endif">@if($statusKey == $book['service_status']) {{$check}} @endif</label>
                                @endforeach
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
          @endif

        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- main-panel ends -->
@endsection
