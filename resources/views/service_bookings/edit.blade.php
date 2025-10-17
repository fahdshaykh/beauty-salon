@extends('layouts.admin')

@section('title', 'Edit Booking | '.Session::get('site_name'))

@section('content')

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Update Booking
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('service-bookings.index')}}">Service Bookings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Update Booking</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Customer Form</h4>
                            @foreach($booking_detail as $book)
                            <?php
                            $invoice = $book->invoice;
                            ?>
                            @endforeach
                        <form class="forms-sample" action="{{url('service-bookings/update',$invoice)}}" method="post">
                            @csrf
                            @include('service_bookings.booking_form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection