@extends('layouts.admin')

@section('title', 'Booking History - ' . $customer->first_name . ' ' . $customer->last_name . ' | ' . (auth()->user()->companySetting->site_name ?? 'Beauty Salon'))

@section('content')


<div class="main-panel">
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-header d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h3 class="page-title font-weight-bold text-dark">
                    <i class="fas fa-history text-primary mr-2"></i>Customer Booking History
                </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $customer->first_name }} {{ $customer->last_name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                <a href="{{ route('service-bookings.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary font-weight-bold mr-2">
                    <i class="fas fa-plus-circle mr-1"></i> New Booking
                </a>
                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-outline-primary font-weight-bold mr-2">
                    <i class="fas fa-edit mr-1"></i> Edit Profile
                </a>
                <a href="{{ route('customers.index') }}" class="btn btn-light border font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <!-- Customer Summary Hero Card -->
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-auto text-center text-md-left mb-3 mb-md-0">
                        <div class="customer-avatar mx-auto">
                            {{ strtoupper(substr($customer->first_name ?? 'C', 0, 1)) }}{{ strtoupper(substr($customer->last_name ?? '', 0, 1)) }}
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="d-flex flex-wrap align-items-center mb-1">
                            <h4 class="font-weight-bold mb-0 mr-2 text-dark">{{ $customer->first_name }} {{ $customer->last_name }}</h4>
                            @if($customer->is_active)
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Active</span>
                            @else
                                <span class="badge badge-secondary px-2 py-1">Inactive</span>
                            @endif
                        </div>
                        <div class="row text-muted small mt-2">
                            <div class="col-lg-3 col-md-6 mb-2">
                                <i class="fas fa-phone-alt text-primary mr-1"></i>
                                @if(!empty($customer->primary_phone))
                                    <a href="tel:{{ $customer->primary_phone }}" class="text-dark font-weight-500">
                                        {{ $customer->country_code ? '+' . ltrim($customer->country_code, '+') . ' ' : '' }}{{ $customer->primary_phone }}
                                    </a>
                                @else
                                    <span>Not provided</span>
                                @endif
                            </div>
                            <div class="col-lg-4 col-md-6 mb-2">
                                <i class="fas fa-envelope text-primary mr-1"></i>
                                @if(!empty($customer->email))
                                    <a href="mailto:{{ $customer->email }}" class="text-dark font-weight-500">{{ $customer->email }}</a>
                                @else
                                    <span>Not provided</span>
                                @endif
                            </div>
                            <div class="col-lg-3 col-md-6 mb-2">
                                <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                <span class="text-dark font-weight-500">{{ $customer->address ?: 'No address specified' }}{{ $customer->zip ? ', ' . $customer->zip : '' }}</span>
                            </div>
                            <div class="col-lg-2 col-md-6 mb-2">
                                <i class="fas fa-calendar-alt text-info mr-1"></i>
                                <span class="text-dark">Since {{ $customer->created_at ? $customer->created_at->format('M Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                <div class="card stat-widget widget-primary shadow-sm border-0 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase font-weight-bold small">Total Bookings</span>
                            <h3 class="font-weight-bold text-dark mt-1 mb-0">{{ number_format($stats->total_bookings ?? 0) }}</h3>
                            <small class="text-muted">All-time appointments</small>
                        </div>
                        <div class="p-3 bg-light rounded-circle text-primary">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                <div class="card stat-widget widget-success shadow-sm border-0 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase font-weight-bold small">Completed</span>
                            <h3 class="font-weight-bold text-success mt-1 mb-0">{{ number_format($stats->completed_bookings ?? 0) }}</h3>
                            <small class="text-muted">Services delivered</small>
                        </div>
                        <div class="p-3 bg-light rounded-circle text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                <div class="card stat-widget widget-info shadow-sm border-0 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase font-weight-bold small">Total Spend</span>
                            <h3 class="font-weight-bold text-primary mt-1 mb-0">{{ $currency }}{{ number_format($stats->total_spent ?? 0, 2) }}</h3>
                            <small class="text-muted">Completed bookings</small>
                        </div>
                        <div class="p-3 bg-light rounded-circle text-info">
                            <i class="fas fa-wallet fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                <div class="card stat-widget widget-danger shadow-sm border-0 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase font-weight-bold small">Cancelled</span>
                            <h3 class="font-weight-bold text-danger mt-1 mb-0">{{ number_format($stats->cancelled_bookings ?? 0) }}</h3>
                            <small class="text-muted">Cancelled bookings</small>
                        </div>
                        <div class="p-3 bg-light rounded-circle text-danger">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Listing Card matching customers/index.blade.php -->
        <div class="card">
            <div class="card-body">
                
                <div class="row mb-2">
                    <div class="col-md-6">
                        <h4 class="card-title">Bookings History</h4>
                    </div>
                    
                </div>

                <!-- Filter & Search Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body pt-4">
                        <form method="GET" action="{{ route('customers.bookings_history', $customer->id) }}" class="row g-3">
                            <!-- Search Field -->
                            <div class="col-md-3 mt-1">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" name="search" id="search" class="form-control"
                                    value="{{ request('search') }}"
                                    placeholder="Invoice #, service, notes...">
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="js-example-basic-single w-100">
                                    <option value="">All Statuses</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Booked</option>
                                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>In-Progress</option>
                                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Completed</option>
                                    <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <div id="datepicker-popup-new" class="input-group date datepicker">
                                    <input type="text" name="start_date" id="start-date" class="form-control"
                                        value="{{ request('start_date') }}">
                                    <span class="input-group-addon input-group-append border-left">
                                        <span class="far fa-calendar input-group-text"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <div id="datepicker-popup-new2" class="input-group date datepicker">
                                    <input type="text" name="end_date" id="end-date" class="form-control"
                                        value="{{ request('end_date') }}">
                                    <span class="input-group-addon input-group-append border-left">
                                        <span class="far fa-calendar input-group-text"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-4 mt-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                    <a href="{{ route('customers.bookings_history', $customer->id) }}" class="btn btn-outline-warning btn-icon-text w-100 ml-1">
                                        <i class="fas fa-redo btn-icon-prepend"></i>
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Pagination Dropdown -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="pagination-dropdown">
                            <form class="form-inline">
                                <div class="form-group">
                                    <label for="rows">Records:&nbsp;</label>
                                    <select class="form-control" id="rows" name="rows">
                                        <option value="5" @if($rows == 5) selected @endif > 5 </option>
                                        <option value="10" @if($rows == 10) selected @endif > 10 </option>
                                        <option value="20" @if($rows == 20) selected @endif > 20 </option>
                                        <option value="50" @if($rows == 50) selected @endif > 50 </option>
                                        <option value="100" @if($rows == 100) selected @endif > 100 </option>
                                        <option value="500" @if($rows == 500) selected @endif > 500 </option>                        
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mt-2">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th>Inv #</th>
                                        <th>Book Date</th>
                                        <th>Book Time</th>
                                        <th>Assign To</th>
                                        <th>Services</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($service_info as $booking)
                                    @php
                                        $computedTotal = $booking->total_price ?: $booking->bookingdetail->sum(fn($d) => $d->service->price ?? 0);
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ url('view-booking', $booking->id) }}">
                                                #{{ $booking->invoice ?? ('BK-' . $booking->id) }}
                                            </a>
                                            @if(!empty($booking->is_home_service))
                                                <span class="badge badge-info ml-1" title="Home Service: {{ $booking->booking_address }}">Home</span>
                                            @endif
                                            @if(!empty($booking->google_event_id))
                                                <span class="badge badge-danger ml-1" title="Synced with Google Calendar">G</span>
                                            @endif
                                        </td>
                                        <td>{{ $booking->due_date ? \Carbon\Carbon::parse($booking->due_date)->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            {{ $booking->due_time ?: '-' }}
                                            @if(!empty($booking->service_time))
                                                <small class="text-muted">({{ $booking->service_time }}m)</small>
                                            @endif
                                        </td>
                                        <td>{{ $booking->user->name ?? 'Unassigned' }}</td>
                                        <td style="max-width: 200px;">
                                            @forelse ($booking->bookingdetail as $detail)
                                                @if($detail->service)
                                                    @php
                                                        $color = $detail->service->service_color ?? '#6c757d';
                                                    @endphp
                                                    <span class="badge me-1 mb-1 badge-pill"
                                                        style="background-color: {{ $color }}; 
                                                                color: {{ \Illuminate\Support\Str::startsWith($color, '#fff') || 
                                                                        \Illuminate\Support\Str::startsWith($color, '#FFF') ? '#000' : '#fff' }};">
                                                        {{ $detail->service->name }}
                                                    </span>
                                                @endif
                                            @empty
                                                <span class="text-muted fst-italic">No services</span>
                                            @endforelse
                                        </td>
                                        <td>{{ $currency }}{{ number_format($computedTotal, 2) }}</td>
                                        <td>
                                            @if($booking->service_status == 1)
                                                <span class="badge badge-info">Booked</span>
                                            @elseif($booking->service_status == 2)
                                                <span class="badge badge-warning">In-Progress</span>
                                            @elseif($booking->service_status == 3)
                                                <span class="badge badge-success">Completed</span>
                                            @elseif($booking->service_status == 4)
                                                <span class="badge badge-danger">Cancelled</span>
                                            @else
                                                <span class="badge badge-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @can('view invoices')
                                                <a href="{{ url('view-booking', $booking->id) }}"
                                                class="btn btn-sm btn-outline-info" title="view invoice">
                                                    <i class="fa fa-eye text-primary"></i>
                                                </a>
                                                @endcan

                                                @can('edit bookings')
                                                <a href="{{ url('service-bookings/edit', $booking->id) }}"
                                                class="btn btn-sm btn-outline-primary ml-1" title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @endcan

                                                @can('delete bookings')
                                                <a href="{{ url('service-bookings/destroy', $booking->id) }}"
                                                onclick="return confirm('Are you sure to delete?')"
                                                class="btn btn-sm btn-outline-danger ml-1" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr style="text-align: center">
                                        <td colspan="8"> No Data Available</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination Links -->
                <div class="row">
                    <div class="col-12 mt-4">
                        <p>Showing {{ $service_info->firstItem() ?? 0 }} to {{ $service_info->lastItem() ?? 0 }} of {{ $service_info->total() }} entries</p>
                        {{ $service_info->links() }}
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rowsSelect = document.getElementById('rows');

    if (rowsSelect) {
        rowsSelect.addEventListener('change', function () {
            let url = new URL(window.location.href);
            url.searchParams.set('rows', this.value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    }
});
</script>

@endsection
