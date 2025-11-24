@extends('layouts.admin')



@section('title', 'Dashboard | bSaloon')







@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Dashboard
            </h3>
        </div>
        
        @role('admin')
        <div class="row grid-margin">
            <div class="col-12">
                <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                        <div class="statistics-item">
                        <p>
                            <i class="icon-sm fa fa-user mr-2"></i>
                            {{ $monthName }} Customers
                        </p>
                        <h2>{{ number_format($totalCustomers ?? 0) }}</h2>
                        
                        </div>
                        <div class="statistics-item">
                        <p>
                            <i class="icon-sm fas fa-calendar-alt mr-2"></i>
                            {{ $monthName }} Bookings
                        </p>
                        <h2>{{ number_format($totalBookings ?? 0) }}</h2>
                        
                        </div>
                        <div class="statistics-item">
                        <p>
                            <i class="icon-sm fas fa-boxes mr-2"></i>
                            {{ $monthName }} Suppliers
                        </p>
                        <h2>{{ number_format($totalSuppliers ?? 0) }}</h2>
                        
                        </div>
                        <div class="statistics-item">
                        <p>
                            <i class="icon-sm fas fa-money-bill-wave mr-2"></i>
                            {{ $monthName }} Expense
                        </p>
                        <h2>{{ number_format($totalExpense ?? 0) }}</h2>
                        
                        </div>
                        <div class="statistics-item">
                        <p>
                            <i class="icon-sm fas fa-chart-line mr-2"></i>
                            {{ $monthName }} Sales
                        </p>
                        <h2>{{ number_format($totalSales ?? 0) }}</h2>
                        
                        </div>
                        <div class="statistics-item">
                        <p>
                            <i class="icon-sm fas fa-chart-bar mr-2"></i>
                            {{ $monthName }} Profit
                        </p>
                        <h2>{{ number_format($totalProfit ?? 0) }}</h2>
                        
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">
                    <i class="fas fa-gift"></i>
                    Booking Overview
                  </h4>
                  <canvas id="home-orders-chart"></canvas>
                  <div id="home-orders-chart-legend" class="home-orders-chart-legend"></div>                  
                </div>
              </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    Today orders status
                  </h4>
                  <div class="flex-grow-1 d-flex flex-column justify-content-between">
                    <canvas id="home-sales-status-chart" class="mt-3"></canvas>
                    <div class="pt-4">
                      <div id="home-sales-status-chart-legend" class="home-sales-status-chart-legend"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body d-flex flex-column">
                  <h4 class="card-title">
                    <i class="fas fa-tachometer-alt"></i>
                    Today Sales
                  </h4>
                  <div class="flex-grow-1 d-flex flex-column justify-content-between">
                    <canvas id="home-daily-sales-chart" class="mt-3 mb-3 mb-md-0"></canvas>
                    <div id="home-daily-sales-chart-legend" class="home-daily-sales-chart-legend pt-4 border-top"></div>
                  </div>
                </div>
              </div>
            </div>
        </div>



        @else
        <div class="row">
            <div class="col-md-6 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-0">Today Order Assigned</h4>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-inline-block pt-3">
                                <div class="d-md-flex">
                                    <h2 class="mb-0">{{ $bookingsAssign }}</h2>
                                </div>
                            </div>
                            <div class="d-inline-block">
                                <i class="fas fa-calendar-alt text-warning icon-lg"></i>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-0">Today Order Completed</h4>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-inline-block pt-3">
                                <div class="d-md-flex">
                                    <h2 class="mb-0">{{ $bookingsCompleted }}</h2>
                                </div>
                            </div>
                            <div class="d-inline-block">
                                <i class="fas fa-calendar-check text-info icon-lg"></i>                                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <!-- content-wrapper ends -->
    <!-- partial:partials/_footer.html -->
    
    <!-- partial -->
</div>

<!-- main-panel ends -->

@endsection