@extends('layouts.admin')

@section('title', 'Booking History | ' . Session::get('site_name'))

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Booking History</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Booking History</li>
                </ol>
            </nav>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Customer details</h4>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Name:</strong> {{ $customer->first_name }} {{ $customer->first_name }}
                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong> {{ $customer->email }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Phone:</strong> {{ $customer->primary_phone }}
                    </div>
                    <div class="col-md-6">
                        <strong>Address:</strong> {{ $customer->address }}
                    </div>
                </div>

            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title">Bookings</h4>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Service Status</th>
                                <th>Services</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $status = array('1' => 'Booked', '2' => 'In-Progress', '3' => 'Completed', '4' => 'Cancel'); ?>
                            @foreach ($service_info as $booking)
                            <tr>
                                <td>{{ $booking->id }}</td>
                                <td>(Total Price: ${{ $booking->bookingdetail->sum(fn($detail) => $detail->service->price) }})</td>
                                <td>{{ $booking->due_date }}</td>
                                <td>{{ $booking->due_time }}</td>
                                <td>{{ $status[$booking->service_status] ?? 'Unknown' }}</td>
                                <td>
                                    <ul>
                                        @foreach ($booking->bookingdetail as $detail)
                                            <li>{{ $detail->service->name }} - ${{ $detail->service->price }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
