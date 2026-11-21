@section('pagescript')

    <script src="{{asset('js/x-editable.js')}}"></script>

@stop

@extends('layouts.admin')

@section('title', 'Service Bookings | '. (auth()->user()->companySetting->site_name ?? ''))

@section('content')

<style>
    /* Overdue row warning highlight */
    .booking-row-overdue td {
        background-color: #fff6f6 !important;
        border-top: 1px solid #ffdede !important;
        position: relative;
    }
    .booking-row-overdue:hover td {
        background-color: #feebeb !important;
    }
    .booking-row-overdue td:first-child {
        border-left: 4px solid #dc3545 !important;
    }

    /* Overdue modern alert badge */
    .badge-overdue-container {
        display: block;
    }
    .badge-overdue-alert {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
        color: #ffffff !important;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.35);
        letter-spacing: 0.2px;
        animation: overdueSoftPulse 2s infinite ease-in-out;
        white-space: nowrap;
    }
    .badge-overdue-alert:hover {
        background: linear-gradient(135deg, #b02a37 0%, #8b1e28 100%);
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.5);
    }

    /* Pulsing white dot */
    .pulse-ring {
        display: inline-block;
        width: 7px;
        height: 7px;
        background-color: #ffffff;
        border-radius: 50%;
        margin-right: 6px;
        animation: pulseDot 1.5s infinite cubic-bezier(0.4, 0, 0.6, 1);
        flex-shrink: 0;
    }

    @keyframes pulseDot {
        0% {
            transform: scale(0.9);
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.85);
        }
        70% {
            transform: scale(1.1);
            box-shadow: 0 0 0 6px rgba(255, 255, 255, 0);
        }
        100% {
            transform: scale(0.9);
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
        }
    }

    @keyframes overdueSoftPulse {
        0%, 100% {
            box-shadow: 0 2px 6px rgba(220, 53, 69, 0.35);
        }
        50% {
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.65);
        }
    }

    /* Top banner alert */
    .overdue-banner {
        background: linear-gradient(135deg, #fff5f5 0%, #fef2f2 100%);
        border: 1px solid #fecaca !important;
        border-left: 5px solid #dc3545 !important;
        border-radius: 8px;
        padding: 12px 18px;
    }
    .overdue-banner-icon {
        width: 42px;
        height: 42px;
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
</style>

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Service Bookings

            </h3>

            @can('create bookings')
                <a href="{{ url('service-bookings/service-bookings-create') }}" class="btn btn-primary">New Booking</a>
            @endcan

        </div>


        <div class="card">

            <div class="card-body">

                @php
                    $overdueCount = collect($data_array)->where('is_overdue', true)->count();
                @endphp

                @if($overdueCount > 0)
                    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center justify-content-between mb-3 overdue-banner" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="overdue-banner-icon mr-3">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 font-weight-bold text-danger">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Overdue Service Attention Required
                                </h6>
                                <p class="mb-0 text-muted small">
                                    There {{ $overdueCount === 1 ? 'is' : 'are' }} <strong class="text-danger">{{ $overdueCount }}</strong> in-progress {{ $overdueCount === 1 ? 'booking' : 'bookings' }} exceeding allocated service time. Please review stylist progress or mark completed.
                                </p>
                            </div>
                        </div>
                        <span class="badge badge-pill badge-danger px-3 py-2 text-uppercase font-weight-bold small shadow-sm">
                            <i class="fas fa-clock mr-1"></i> {{ $overdueCount }} Overdue
                        </span>
                    </div>
                @endif

                <!-- Filter Form -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('service-bookings.index') }}">
                            
                            <div class="row">
                                <!-- Date Range Filter -->
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <div id="datepicker-popup-new" class="input-group date datepicker">
                                        <input type="text" name="start_date" id="start-date" class="form-control" value="{{ request('start_date') }}">
                                        <span class="input-group-addon input-group-append border-left">
                                        <span class="far fa-calendar input-group-text"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <div id="datepicker-popup-new2" class="input-group date datepicker">
                                        <input type="text" name="end_date" id="end-date" class="form-control" value="{{ request('end_date') }}">
                                        <span class="input-group-addon input-group-append border-left">
                                        <span class="far fa-calendar input-group-text"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <!-- Employee Filter -->
                                <div class="col-md-4">
                                    <label for="user_id" class="form-label">Employee</label>
                                    <select name="user_id" id="user_id" class="js-example-basic-single w-100">
                                        <option value="">All Employees</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Service Filter -->
                                <div class="col-md-4">
                                    <label for="service_id" class="form-label">Service</label>
                                    <select name="service_id" id="service_id" class="js-example-basic-single w-100">
                                        <option value="">All Services</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status Filter -->
                                <div class="col-md-4">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="js-example-basic-single w-100">
                                        <option value="">All Statuses</option>
                                        @foreach(['1' => 'Booked', '2' => 'In-Progress', '3' => 'Completed', '4' => 'Cancel'] as $key => $label)
                                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            

                            <div class="row g-3 mt-2 align-items-end">
                                <!-- Customer Name or Phone Filter -->
                                <div class="col-md-3">
                                    <label for="customer_search" class="form-label">Customer Name or Phone</label>
                                    <input type="text" name="customer_search" id="customer_search" class="form-control" value="{{ request('customer_search') ?? request('customer_name') }}" placeholder="Enter customer name or phone">
                                </div>

                                <!-- Invoice Filter -->
                                <div class="col-md-3">
                                    <label for="filter_invoice" class="form-label">Invoice</label>
                                    <input type="text" name="invoice" id="filter_invoice" class="form-control" value="{{ request('invoice') }}" placeholder="Enter invoice number">
                                </div>

                                <!-- Buttons -->
                                <div class="col-md-6 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-50">Apply</button>
                                    <a href="{{ route('service-bookings.index') }}" class="btn btn-outline-warning btn-icon-text w-100 ml-1">
                                        <i class="fas fa-redo btn-icon-prepend"></i>
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <h4 class="card-title"></h4>

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

                    <div class="col-12">

                        <div class="table-responsive">

                            <table class="table table-striped">

                                <thead>

                                    <tr class="bg-primary text-white">

                                        <th>Inv</th>

                                        <th>Assign To</th>

                                        <th>Customer</th>

                                        <th>Phone</th>

                                        <th style="width: 10%;">Services</th>

                                        <th>Price</th>

                                        <th>Book Date</th>

                                        <th>Book Time</th>

                                        <th>Status</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    @forelse($data_array as $key => $book)

                                    <tr class="{{ !empty($book['is_overdue']) ? 'booking-row-overdue' : '' }}">

                                        <td>{{$book['invoice']}}</td>

                                        <td>{{$book['user_name']}}</td>

                                        <td>{{$book['customer_name']}}</td>

                                        <td>{{$book['primary_phone']}}</td>

                                        <td style="max-width: 200px;">
                                            @forelse($book['services'] as $service)
                                                <span class="badge me-1 mb-1 badge-pill"
                                                    style="background-color: {{ $service['color'] }}; 
                                                            color: {{ \Illuminate\Support\Str::startsWith($service['color'], '#fff') || 
                                                                    \Illuminate\Support\Str::startsWith($service['color'], '#FFF') ? '#000' : '#fff' }};">
                                                    {{ $service['name'] }}
                                                </span>
                                            @empty
                                                <span class="text-muted fst-italic">No services</span>
                                            @endforelse
                                        </td>

                                        <td>
                                            <div class="font-weight-bold text-dark">{{ currency_symbol() }}{{ number_format($book['total_with_vat'] ?? $book['total_price'], 2) }}</div>
                                            @php
                                                $pStatus = $book['payment_status'] ?? 'unpaid';
                                                $pPaid = (float)($book['amount_paid'] ?? 0);
                                                $pPending = (float)($book['amount_pending'] ?? 0);
                                            @endphp
                                            @if($pStatus === 'paid')
                                                <span class="badge badge-success text-white" style="font-size: 10px; padding: 2px 5px;" title="Fully Paid ({{ currency_symbol() }}{{ number_format($pPaid, 2) }})">
                                                    <i class="fas fa-check-circle mr-1"></i>Paid
                                                </span>
                                            @elseif($pStatus === 'partial')
                                                <span class="badge badge-warning text-dark font-weight-bold" style="font-size: 10px; padding: 2px 5px;" title="Paid: {{ currency_symbol() }}{{ number_format($pPaid, 2) }} | Due: {{ currency_symbol() }}{{ number_format($pPending, 2) }}">
                                                    <i class="fas fa-hourglass-half mr-1"></i>Partial ({{ currency_symbol() }}{{ number_format($pPending, 2) }} due)
                                                </span>
                                            @else
                                                <span class="badge badge-danger" style="font-size: 10px; padding: 2px 5px;" title="Unpaid: {{ currency_symbol() }}{{ number_format($pPending, 2) }} due">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>Unpaid
                                                </span>
                                            @endif
                                        </td>

                                        <td style="width: 10%;">{{$book['due_date']}}</td>

                                        <td style="width: 11%;">
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold text-dark">{{ $book['due_time'] }}</span>
                                                <small class="text-muted d-inline-flex align-items-center mt-1">
                                                    <i class="far fa-clock mr-1 text-secondary"></i> {{ $book['service_time'] ?? 30 }}m
                                                </small>
                                            </div>
                                        </td>

                                        <td style="width: 10%;">
                                            @php
                                                $statuses = [
                                                    1 => 'Booked',
                                                    2 => 'In-Progress',
                                                    3 => 'Completed',
                                                    4 => 'Cancel'
                                                ];

                                                $currentStatus = $book['service_status'];
                                                $isFinal       = in_array($currentStatus, [3, 4]);   // Completed or Cancel
                                                $isAdmin       = auth()->user()->hasRole('admin'); // <-- change to your role name
                                            @endphp

                                            <div class="form-group mb-1">

                                                {{-- 1. ADMIN → always see the dropdown  --}}
                                                @if($isAdmin)
                                                    <select class="form-control form-control-sm {{ !empty($book['is_overdue']) ? 'border-danger text-danger font-weight-bold' : '' }}"
                                                            id="change"
                                                            data-booking_id="{{ $book['id'] }}"
                                                            data-total="{{ $book['total_with_vat'] ?? $book['total_price'] }}"
                                                            data-paid="{{ $book['amount_paid'] ?? 0 }}"
                                                            data-pending="{{ $book['amount_pending'] ?? 0 }}"
                                                            data-payment_status="{{ $book['payment_status'] ?? 'unpaid' }}"
                                                            data-invoice="{{ $book['invoice'] }}"
                                                            data-customer="{{ $book['customer_name'] }}"
                                                            onchange="changeLevel(this);"
                                                            @if($currentStatus == 5) disabled @endif>
                                                        @foreach($statuses as $key => $label)
                                                            <option value="{{ $key }}" {{ $key == $currentStatus ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                {{-- 2. NON-ADMIN → final status → show badge only  --}}
                                                @elseif($isFinal)
                                                    <button type="button" class="btn btn-{{ $currentStatus == 3 ? 'success' : 'danger' }} p-2">
                                                        {{ $statuses[$currentStatus] }}
                                                    </button>

                                                {{-- 3. NON-ADMIN → editable status → show dropdown  --}}
                                                @else
                                                    <select class="form-control form-control-sm {{ !empty($book['is_overdue']) ? 'border-danger text-danger font-weight-bold' : '' }}"
                                                            id="change"
                                                            data-booking_id="{{ $book['id'] }}"
                                                            data-total="{{ $book['total_with_vat'] ?? $book['total_price'] }}"
                                                            data-paid="{{ $book['amount_paid'] ?? 0 }}"
                                                            data-pending="{{ $book['amount_pending'] ?? 0 }}"
                                                            data-payment_status="{{ $book['payment_status'] ?? 'unpaid' }}"
                                                            data-invoice="{{ $book['invoice'] }}"
                                                            data-customer="{{ $book['customer_name'] }}"
                                                            onchange="changeLevel(this);"
                                                            @if($currentStatus == 5) disabled @endif>
                                                        @foreach($statuses as $key => $label)
                                                            <option value="{{ $key }}" {{ $key == $currentStatus ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif

                                            </div>

                                            @if(!empty($book['is_overdue']))
                                                <div class="badge-overdue-container mt-1">
                                                    <span class="badge-overdue-alert" title="Service duration of {{ $book['service_time'] ?? 30 }}m exceeded by {{ $book['overdue_text'] }}">
                                                        <span class="pulse-ring"></span>
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        <span>Overdue +{{ $book['overdue_text'] }}</span>
                                                    </span>
                                                </div>
                                            @elseif((int)$book['service_status'] === 2)
                                                <div class="mt-1 in-service-badge">
                                                    <span class="badge text-primary border border-info" style="font-size: 11px; padding: 2px 7px; border-radius: 12px; background: rgba(0, 123, 255, 0.08);">
                                                        <i class="fas fa-spinner fa-spin mr-1"></i> In Service
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        

                                        <td>
                                            <div class="d-flex gap-1">

                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success btn-view-payment-history" 
                                                        data-booking_id="{{ $book['id'] }}" 
                                                        data-invoice="{{ $book['invoice'] }}"
                                                        data-customer="{{ $book['customer_name'] }}"
                                                        data-total="{{ $book['total_with_vat'] ?? $book['total_price'] }}"
                                                        data-paid="{{ $book['amount_paid'] ?? 0 }}"
                                                        data-pending="{{ $book['amount_pending'] ?? 0 }}"
                                                        data-payment_status="{{ $book['payment_status'] ?? 'unpaid' }}"
                                                        title="Payment History & Ledger">
                                                    <i class="fas fa-receipt"></i>
                                                </button>

                                                @can('booking inventory')
                                                <a href="{{ route('service-bookings.edit-inventory', $book['id']) }}"
                                                class="btn btn-sm btn-outline-info" title="Edit Inventory">
                                                    <i class="fa fa-box"></i>
                                                </a>
                                                @endcan

                                                @can('edit bookings')
                                                <a href="{{ url('service-bookings/edit', $book['id']) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit Booking">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @endcan

                                                @can('delete bookings')
                                                <a href="{{ url('service-bookings/destroy', $book['id']) }}"
                                                onclick="return confirm('Are you sure to delete?')"
                                                class="btn btn-sm btn-outline-danger" title="Delete Booking">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                @endcan

                                                @can('view invoices')
                                                <a href="{{ url('view-booking', $book['id']) }}"
                                                class="btn btn-sm btn-outline-dark" title="View / Send Invoice">
                                                <i class="fa fa-eye text-success"></i>
                                                </a>
                                                @endcan

                                            </div>

                                        </td>


                                    </tr>

                                    <?php $num++; ?>

                                    @empty

                                    <tr style="text-align: center">
                                        <td colspan='10'> No Data Available</td>
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
                        <p>Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} entries</p>
                        {{ $bookings->links() }}
                    </div>
                </div>

            </div>

        </div>

    </div>

{{-- ==================== CANCEL MODAL ==================== --}}
<div class="modal fade" id="cancel-modal" tabindex="-1" aria-labelledby="CancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="CancelModalLabel">Cancel Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cancel-form" method="POST" action="{{ route('service-bookings.cancel') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cancel-reason">Reason for Cancellation</label>
                        <textarea class="form-control" id="cancel-reason" name="reason" required></textarea>
                    </div>
                    <input type="hidden" id="cancel-booking-id" name="booking_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Submit Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== PAYMENT MODAL ==================== --}}
<div class="modal fade" id="payment-modal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-dark" id="ModalLabel">
                    <i class="fas fa-money-check-alt text-primary mr-2"></i>Collect Payment & Complete Booking
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="payment-form" action="{{ url('change-booking-status') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Financial Summary Card -->
                    <div class="card bg-light border p-3 mb-3" style="border-radius: 6px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-weight-bold text-dark" id="modal-booking-ref">Booking</span>
                            <span class="badge badge-info" id="modal-payment-context-badge">Settlement</span>
                        </div>
                        <div class="row text-center">
                            <div class="col-4 border-right">
                                <small class="text-muted d-block">Total</small>
                                <span class="font-weight-bold text-dark">{{ currency_symbol() }}<span id="modal-total-display">0.00</span></span>
                            </div>
                            <div class="col-4 border-right">
                                <small class="text-muted d-block">Advance Paid</small>
                                <span class="font-weight-bold text-success">{{ currency_symbol() }}<span id="modal-paid-display">0.00</span></span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Balance Due</small>
                                <span class="font-weight-bold text-danger">{{ currency_symbol() }}<span id="modal-pending-display">0.00</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Amount -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark mb-1">
                            Amount to Collect <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">{{ currency_symbol() }}</span>
                            <input type="number" step="0.01" min="0.01" 
                                   class="form-control font-weight-bold" 
                                   id="modal_payment_amount" name="payment_amount" 
                                   required>
                        </div>
                        <small class="text-muted">Defaults to remaining balance due.</small>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark mb-1">Select Payment Method <span class="text-danger">*</span></label>
                        <select class="form-control" name="payment_method" id="payment_method" required>
                            <option value="">-- Choose Payment Method --</option>
                            @if(isset($payment_methods) && $payment_methods->count() > 0)
                                @foreach($payment_methods as $pm)
                                    <option value="{{ strtolower($pm->method) }}" 
                                            data-fields="{{ json_encode($pm->fields ?? []) }}"
                                            data-description="{{ $pm->description ?? '' }}">
                                        {{ $pm->method }}
                                    </option>
                                @endforeach
                            @else
                                <option value="cash">Cash</option>
                                <option value="card" data-fields='[{"name":"last_4_digits","label":"Card Last 4 Digits","type":"number","required":false}]'>Card</option>
                                <option value="cheque" data-fields='[{"name":"cheque_number","label":"Cheque Number","type":"text","required":true}]'>Cheque</option>
                                <option value="bank transfer" data-fields='[{"name":"transaction_ref","label":"Transaction Reference #","type":"text","required":true}]'>Bank Transfer</option>
                            @endif
                        </select>
                        <small id="payment-method-desc" class="form-text text-muted mt-1" style="display:none;"></small>
                    </div>

                    <!-- Dynamic Transaction Inputs -->
                    <div id="dynamic-payment-fields-wrapper" class="card bg-light border p-3 mb-3" style="display:none;">
                        <h6 class="font-weight-bold text-dark small mb-2">
                            <i class="fas fa-list-alt text-primary mr-1"></i> Transaction Details
                        </h6>
                        <div id="dynamic-payment-fields-container"></div>
                    </div>

                    <!-- Payment Proof Document (Max 2MB) -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark mb-1">Payment Proof / Receipt (Optional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="completion_payment_file" name="payment_file" accept=".jpg,.jpeg,.png,.gif,.svg,.pdf,.doc,.docx">
                            <label class="custom-file-label text-truncate" for="completion_payment_file" id="completion_payment_file_label">Choose receipt or proof file...</label>
                        </div>
                        <small class="text-muted d-block mt-1">Accepted: PDF, JPG, PNG, DOCX (Max 2MB)</small>
                        <div id="completion-file-size-error" class="text-danger small mt-1 font-weight-bold" style="display:none;">
                            <i class="fas fa-exclamation-circle mr-1"></i>File size exceeds 2MB. Please choose a smaller file.
                        </div>
                    </div>

                    <input type="hidden" id="invoice" name="invoice">
                    <input type="hidden" id="service_status" name="service_status" value="3">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success font-weight-bold" id="btn-submit-payment">
                        <i class="fas fa-check mr-1"></i> Settle & Complete Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== PAYMENT HISTORY & ADD PAYMENT MODAL ==================== --}}
<div class="modal fade" id="payment-history-modal" tabindex="-1" aria-labelledby="PaymentHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-dark" id="PaymentHistoryModalLabel">
                    <i class="fas fa-receipt text-success mr-2"></i>Booking Payment Ledger & History
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Booking Header Summary -->
                <div class="card bg-light border p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="font-weight-bold text-primary mb-0" id="hist-invoice-customer">Booking</h6>
                        <div class="d-flex align-items-center">
                            <span id="hist-discount-badge" class="mr-2" style="display:none;"></span>
                            <span id="hist-status-badge"></span>
                        </div>
                    </div>
                    <div class="row text-center mt-2" id="hist-summary-row">
                        <div class="col-4 border-right" id="hist-box-total">
                            <small class="text-muted d-block">Total Price</small>
                            <span class="font-weight-bold text-dark h6 mb-0">{{ currency_symbol() }}<span id="hist-total">0.00</span></span>
                        </div>
                        <div class="col-4 border-right" id="hist-box-paid">
                            <small class="text-muted d-block">Total Paid</small>
                            <span class="font-weight-bold text-success h6 mb-0">{{ currency_symbol() }}<span id="hist-paid">0.00</span></span>
                        </div>
                        <div class="col-4" id="hist-box-pending">
                            <small class="text-muted d-block">Remaining Balance</small>
                            <span class="font-weight-bold text-danger h6 mb-0">{{ currency_symbol() }}<span id="hist-pending">0.00</span></span>
                        </div>
                        <div class="col-3" id="hist-box-discount" style="display:none;">
                            <small class="text-muted d-block">Discount Given</small>
                            <span class="font-weight-bold text-warning h6 mb-0">{{ currency_symbol() }}<span id="hist-discount">0.00</span></span>
                        </div>
                    </div>
                </div>

                <!-- Past Payments Table -->
                <h6 class="font-weight-bold text-dark mb-2">
                    <i class="fas fa-history text-secondary mr-1"></i> Payment Transactions
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered" id="hist-payments-table">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Details / Ref</th>
                                <th>Proof</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody id="hist-payments-tbody">
                            <!-- Injected by AJAX -->
                        </tbody>
                    </table>
                </div>

                <!-- Add Payment Section (if balance remains) -->
                <div id="hist-add-payment-section" class="border rounded p-3 bg-white shadow-sm" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <h6 class="font-weight-bold text-success mb-0">
                            <i class="fas fa-plus-circle mr-1"></i> Record Payment / Installment
                        </h6>
                        <small class="text-muted">Collect payment without changing booking status</small>
                    </div>

                    <form id="hist-add-payment-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="hist-add-booking-id" name="booking_id">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="small font-weight-bold text-dark">Amount <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-primary text-white">{{ currency_symbol() }}</span>
                                    <input type="number" step="0.01" min="0.01" class="form-control" name="payment_amount" id="hist_add_amount" required>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small font-weight-bold text-dark">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" name="payment_method" id="hist_add_payment_method" required>
                                    <option value="">-- Method --</option>
                                    @if(isset($payment_methods) && $payment_methods->count() > 0)
                                        @foreach($payment_methods as $pm)
                                            <option value="{{ strtolower($pm->method) }}" 
                                                    data-fields="{{ json_encode($pm->fields ?? []) }}">
                                                {{ $pm->method }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="bank transfer">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small font-weight-bold text-dark">Payment Date</label>
                                <input type="date" class="form-control form-control-sm" name="payment_date" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <!-- Dynamic fields for Add Payment -->
                        <div id="hist-add-dynamic-fields-wrapper" class="card bg-light border p-2 mb-2" style="display:none;">
                            <div id="hist-add-dynamic-fields-container" class="row"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="small font-weight-bold text-dark">Proof Document (Optional, &le;2MB)</label>
                                <input type="file" class="form-control form-control-sm" id="hist_add_file" name="payment_file" accept=".jpg,.jpeg,.png,.gif,.svg,.pdf,.doc,.docx">
                                <div id="hist-add-file-error" class="text-danger small font-weight-bold mt-1" style="display:none;">
                                    File exceeds 2MB!
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small font-weight-bold text-dark">Notes (Optional)</label>
                                <input type="text" class="form-control form-control-sm" name="notes" placeholder="e.g. Advance or installment">
                            </div>
                        </div>

                        <div class="text-end mt-2">
                            <button type="submit" class="btn btn-sm btn-success font-weight-bold" id="btn-submit-hist-payment">
                                <i class="fas fa-check mr-1"></i> Save Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

   

    @endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    if (typeof $.fn.tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    }
    let originalStatus = {};

    // 1. Capture original value on focus
    $(document).on('focus', 'select#change', function () {
        const $select = $(this);
        const bookingId = $select.data('booking_id');
        originalStatus[bookingId] = $select.val();
    });

    // 2. Change handler
    window.changeLevel = function (level) {
        const $select = $(level);
        const bookingId = $select.data('booking_id');
        const newStatus = $select.val();

        if (newStatus == 3) {
            const pending = parseFloat($select.data('pending')) || 0;
            const paid = parseFloat($select.data('paid')) || 0;
            const total = parseFloat($select.data('total')) || 0;
            const invoice = $select.data('invoice') || bookingId;
            const customer = $select.data('customer') || '';

            // If already fully paid upfront (pending <= 0)
            if (pending <= 0) {
                // Bypass payment modal and complete immediately
                sendStatusAjax(bookingId, 3, $select, function() {
                    iziToast.info({
                        title: 'Completed',
                        message: 'Booking was already fully paid in advance. Marked as Completed.',
                        position: 'topRight'
                    });
                });
                return;
            }

            // Populate payment modal with financial context
            $('#invoice').val(bookingId);
            $('#service_status').val(newStatus);
            $('#modal-booking-ref').text('Booking #' + invoice + (customer ? ' - ' + customer : ''));
            $('#modal-total-display').text(total.toFixed(2));
            $('#modal-paid-display').text(paid.toFixed(2));
            $('#modal-pending-display').text(pending.toFixed(2));
            $('#modal_payment_amount').val(pending.toFixed(2)).attr('max', pending.toFixed(2));
            $('#modal-payment-context-badge').text(paid > 0 ? 'Remaining Balance' : 'Full Settlement');

            $('#payment-modal').modal('show');
            return;
        }

        if (newStatus == 4) {
            $('#cancel-booking-id').val(bookingId);
            $('#cancel-modal').modal('show');
            return;
        }

        // For Booked / In-Progress → direct update
        sendStatusAjax(bookingId, newStatus, $select);
    };

    // 3. AJAX to update status
    function sendStatusAjax(bookingId, status, $select, callback) {
        $.ajax({
            type: "POST",
            url: "{{ url('booking-status') }}",
            data: {
                id: bookingId,
                service_status: status,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                iziToast.success({
                    title: 'Updated',
                    message: response.success || 'Status updated successfully.',
                    position: 'topRight'
                });

                $select.val(status);
                delete originalStatus[bookingId];

                // If status changed away from In-Progress (2), clear overdue styles and alerts
                if (status != 2) {
                    const $row = $select.closest('tr');
                    $row.removeClass('booking-row-overdue');
                    $select.removeClass('border-danger text-danger font-weight-bold');
                    $row.find('.badge-overdue-container, .in-service-badge').remove();
                }

                if (typeof callback === 'function') callback();
            },
            error: function (xhr) {
                const orig = originalStatus[bookingId] || 1;
                $select.val(orig);

                iziToast.error({
                    title: 'Failed',
                    message: xhr.responseJSON?.message || 'Failed to update status.',
                    position: 'topRight'
                });
            }
        });
    }

    // 4. CANCEL MODAL SUBMIT
    $('#cancel-form').on('submit', function (e) {
        e.preventDefault();

        const bookingId = $('#cancel-booking-id').val();
        const $select = $(`select[data-booking_id="${bookingId}"]`);

        $.ajax({
            url: "{{ route('service-bookings.cancel') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    // iziToast.success({
                    //     title: 'Success',
                    //     message: res.message,
                    //     position: 'topRight'
                    // });
                    $('#cancel-modal').modal('hide');
                    sendStatusAjax(bookingId, 4, $select);
                } else {
                    iziToast.error({
                        title: 'Failed',
                        message: res.message || 'Cancellation failed.',
                        position: 'topRight'
                    });
                }
            },
            error: function () {
                iziToast.error({
                    title: 'Error',
                    message: 'Cancellation failed.',
                    position: 'topRight'
                });
            }
        });
    });

    // 4.5 DYNAMIC PAYMENT METHOD FIELDS
    $('#payment_method').on('change', function () {
        const $selected = $(this).find('option:selected');
        const description = $selected.data('description') || '';
        let fields = $selected.data('fields') || [];

        if (typeof fields === 'string') {
            try { fields = JSON.parse(fields); } catch(e) { fields = []; }
        }

        const $wrapper = $('#dynamic-payment-fields-wrapper');
        const $container = $('#dynamic-payment-fields-container');
        const $desc = $('#payment-method-desc');

        if (description) {
            $desc.text(description).show();
        } else {
            $desc.hide();
        }

        $container.empty();

        if (Array.isArray(fields) && fields.length > 0) {
            fields.forEach(function (f) {
                const reqAttr = f.required ? 'required' : '';
                const reqStar = f.required ? '<span class="text-danger">*</span>' : '';
                const inputHtml = `
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold mb-1">${f.label} ${reqStar}</label>
                        <input type="${f.type || 'text'}" 
                               class="form-control form-control-sm" 
                               name="payment_details[${f.name}]" 
                               placeholder="Enter ${f.label}" 
                               ${reqAttr}>
                    </div>
                `;
                $container.append(inputHtml);
            });
            $wrapper.slideDown(200);
        } else {
            $wrapper.slideUp(200);
        }
    });

    // Reset payment fields when modal hidden
    $('#payment-modal').on('hidden.bs.modal', function () {
        $('#payment_method').val('');
        $('#dynamic-payment-fields-wrapper').hide();
        $('#dynamic-payment-fields-container').empty();
        $('#payment-method-desc').hide();
        $('#completion_payment_file').val('');
        $('#completion_payment_file_label').text('Choose receipt or proof file...');
        $('#completion-file-size-error').hide();
    });

    // File validation for completion payment modal
    $('#completion_payment_file').on('change', function() {
        const file = this.files[0];
        const $label = $('#completion_payment_file_label');
        const $err = $('#completion-file-size-error');

        if (!file) {
            $label.text('Choose receipt or proof file...');
            $err.hide();
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            $err.show();
            $(this).val('');
            $label.text('Choose receipt or proof file...');
        } else {
            $err.hide();
            $label.text(file.name);
        }
    });

    // 5. PAYMENT MODAL SUBMIT
    $('#payment-form').on('submit', function (e) {
        e.preventDefault();

        const bookingId = $('#invoice').val();
        const $select = $(`select[data-booking_id="${bookingId}"]`);
        const formData = new FormData(this);
        const $btn = $('#btn-submit-payment');

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...');

        $.ajax({
            url: "{{ url('change-booking-status') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Settle & Complete Booking');
                if (res.success) {
                    $('#payment-modal').modal('hide');
                    iziToast.success({
                        title: 'Success',
                        message: res.success || 'Booking completed and payment recorded.',
                        position: 'topRight'
                    });
                    setTimeout(function() {
                        location.reload();
                    }, 800);
                } else {
                    iziToast.error({
                        title: 'Failed',
                        message: res.error || 'Payment processing failed.',
                        position: 'topRight'
                    });
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Settle & Complete Booking');
                iziToast.error({
                    title: 'Error',
                    message: xhr.responseJSON?.message || 'Payment processing failed.',
                    position: 'topRight'
                });
            }
        });
    });

    // ==================== PAYMENT HISTORY & DIRECT ADD PAYMENT ====================
    let historyChanged = false;

    function loadPaymentHistory(bookingId) {
        const $tbody = $('#hist-payments-tbody');
        $tbody.html('<tr><td colspan="7" class="text-center py-3"><i class="fas fa-spinner fa-spin text-primary"></i> Loading payments...</td></tr>');

        $.ajax({
            url: `{{ url('service-bookings') }}/${bookingId}/payments`,
            type: "GET",
            dataType: "json",
            success: function (res) {
                const b = res.booking;
                const payments = res.payments || [];

                $('#hist-invoice-customer').text(`Booking #${b.invoice || b.id} - ${b.customer_name || 'Customer'}`);
                $('#hist-total').text(Number(b.total_with_vat).toFixed(2));
                $('#hist-paid').text(Number(b.amount_paid).toFixed(2));
                $('#hist-pending').text(Number(b.amount_pending).toFixed(2));
                $('#hist-add-booking-id').val(b.id);
                $('#hist_add_amount').val(Number(b.amount_pending).toFixed(2)).attr('max', Number(b.amount_pending).toFixed(2));

                let badgeHtml = '';
                if (b.payment_status === 'paid') {
                    badgeHtml = '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Fully Paid</span>';
                } else if (b.payment_status === 'partial') {
                    badgeHtml = '<span class="badge badge-warning text-dark font-weight-bold"><i class="fas fa-hourglass-half mr-1"></i>Partial</span>';
                } else {
                    badgeHtml = '<span class="badge badge-danger"><i class="fas fa-exclamation-circle mr-1"></i>Unpaid</span>';
                }
                $('#hist-status-badge').html(badgeHtml);

                // Discount Indicator & Box
                const hasDiscount = Boolean(b.is_discount);
                const discountAmt = Number(b.discount_amount || 0);

                if (hasDiscount) {
                    $('#hist-discount-badge').html(`<span class="badge badge-warning text-dark font-weight-bold"><i class="fas fa-tag mr-1"></i>Discount: Yes ({{ currency_symbol() }}${discountAmt.toFixed(2)})</span>`).show();
                    $('#hist-discount').text(discountAmt.toFixed(2));
                    $('#hist-box-discount').show();
                    $('#hist-box-total, #hist-box-paid, #hist-box-pending').removeClass('col-4').addClass('col-3 border-right');
                } else {
                    $('#hist-discount-badge').html(`<span class="badge badge-light border text-muted">Discount: No</span>`).show();
                    $('#hist-box-discount').hide();
                    $('#hist-box-total, #hist-box-paid, #hist-box-pending').removeClass('col-3 border-right').addClass('col-4 border-right');
                    $('#hist-box-pending').removeClass('border-right');
                }

                // Populate Payments Table
                $tbody.empty();
                if (payments.length > 0) {
                    payments.forEach(function (p) {
                        let proofHtml = '<span class="text-muted">-</span>';
                        if (p.payment_file && p.file_url) {
                            proofHtml = `<a href="${p.file_url}" target="_blank" class="btn btn-xs btn-outline-primary" title="View Document Proof">
                                <i class="fas fa-paperclip"></i> View
                            </a>`;
                        }

                        let detailsHtml = '';
                        if (p.payment_details && typeof p.payment_details === 'object') {
                            const detailsArr = [];
                            for (let key in p.payment_details) {
                                detailsArr.push(`<span class="badge badge-light border text-dark">${key.replace(/_/g, ' ')}: ${p.payment_details[key]}</span>`);
                            }
                            detailsHtml = detailsArr.join(' ');
                        } else if (p.payment_details) {
                            detailsHtml = `<span class="badge badge-light border text-dark">${p.payment_details}</span>`;
                        }
                        if (p.notes) {
                            detailsHtml += (detailsHtml ? '<br>' : '') + `<small class="text-muted fst-italic">${p.notes}</small>`;
                        }

                        let typeBadge = (p.payment_type.toLowerCase() === 'advance') 
                            ? '<span class="badge badge-info">Advance</span>' 
                            : (p.payment_type.toLowerCase() === 'installment' ? '<span class="badge badge-warning text-dark">Installment</span>' : '<span class="badge badge-success">Final</span>');

                        const rowHtml = `
                            <tr>
                                <td><span class="font-weight-bold text-dark">${p.payment_date}</span></td>
                                <td>${typeBadge}</td>
                                <td><span class="font-weight-bold text-success">{{ currency_symbol() }}${Number(p.amount).toFixed(2)}</span></td>
                                <td><span class="badge badge-light border text-primary font-weight-bold">${p.payment_method}</span></td>
                                <td>${detailsHtml || '<span class="text-muted">-</span>'}</td>
                                <td class="text-center">${proofHtml}</td>
                                <td><small class="text-muted">${p.created_by}</small></td>
                            </tr>
                        `;
                        $tbody.append(rowHtml);
                    });
                } else {
                    $tbody.html('<tr><td colspan="7" class="text-center text-muted py-3">No payments recorded yet for this booking.</td></tr>');
                }

                // Show/hide Add Payment section based on pending balance
                if (Number(b.amount_pending) > 0) {
                    $('#hist-add-payment-section').show();
                } else {
                    $('#hist-add-payment-section').hide();
                }
            },
            error: function () {
                $tbody.html('<tr><td colspan="7" class="text-center text-danger py-3">Failed to load payment history.</td></tr>');
            }
        });
    }

    $(document).on('click', '.btn-view-payment-history', function () {
        const bookingId = $(this).data('booking_id');
        historyChanged = false;
        loadPaymentHistory(bookingId);
        $('#payment-history-modal').modal('show');
    });

    $('#payment-history-modal').on('hidden.bs.modal', function () {
        if (historyChanged) {
            location.reload();
        }
    });

    // Dynamic fields for History Add Payment
    $('#hist_add_payment_method').on('change', function () {
        const $selected = $(this).find('option:selected');
        let fields = $selected.data('fields') || [];

        if (typeof fields === 'string') {
            try { fields = JSON.parse(fields); } catch(e) { fields = []; }
        }

        const $wrapper = $('#hist-add-dynamic-fields-wrapper');
        const $container = $('#hist-add-dynamic-fields-container');
        $container.empty();

        if (Array.isArray(fields) && fields.length > 0) {
            fields.forEach(function (f) {
                const reqAttr = f.required ? 'required' : '';
                const reqStar = f.required ? '<span class="text-danger">*</span>' : '';
                const inputHtml = `
                    <div class="col-md-6 mb-2">
                        <label class="small font-weight-bold mb-1">${f.label} ${reqStar}</label>
                        <input type="${f.type || 'text'}" 
                               class="form-control form-control-sm" 
                               name="payment_details[${f.name}]" 
                               placeholder="Enter ${f.label}" 
                               ${reqAttr}>
                    </div>
                `;
                $container.append(inputHtml);
            });
            $wrapper.slideDown(200);
        } else {
            $wrapper.slideUp(200);
        }
    });

    // File validation for History Add Payment
    $('#hist_add_file').on('change', function () {
        const file = this.files[0];
        const $err = $('#hist-add-file-error');
        if (file && file.size > 2 * 1024 * 1024) {
            $err.show();
            $(this).val('');
        } else {
            $err.hide();
        }
    });

    // Submit Add Payment Form
    $('#hist-add-payment-form').on('submit', function (e) {
        e.preventDefault();

        const bookingId = $('#hist-add-booking-id').val();
        const formData = new FormData(this);
        const $btn = $('#btn-submit-hist-payment');

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: `{{ url('service-bookings') }}/${bookingId}/add-payment`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Save Payment');
                if (res.success) {
                    historyChanged = true;
                    iziToast.success({
                        title: 'Success',
                        message: res.success || 'Payment recorded successfully.',
                        position: 'topRight'
                    });
                    // Reset add form
                    $('#hist-add-payment-form')[0].reset();
                    $('#hist-add-dynamic-fields-wrapper').hide();
                    $('#hist-add-dynamic-fields-container').empty();
                    // Reload modal history
                    loadPaymentHistory(bookingId);
                } else {
                    iziToast.error({
                        title: 'Failed',
                        message: res.error || 'Failed to record payment.',
                        position: 'topRight'
                    });
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Save Payment');
                iziToast.error({
                    title: 'Error',
                    message: xhr.responseJSON?.message || 'Failed to record payment.',
                    position: 'topRight'
                });
            }
        });
    });

    // 6. REVERT ONLY IF MODAL CLOSED WITHOUT SUBMIT
    function setupRevertOnHide(modalId, idField) {
        $(modalId).on('hidden.bs.modal', function () {
            const bookingId = $(idField).val();
            if (!bookingId) return;

            const $select = $(`select[data-booking_id="${bookingId}"]`);
            const orig = originalStatus[bookingId];

            if (orig !== undefined) {
                $select.val(orig); // Revert UI
                delete originalStatus[bookingId];
            }

            // Clear fields
            $(idField).val('');
        });
    }

    setupRevertOnHide('#cancel-modal', '#cancel-booking-id');
    setupRevertOnHide('#payment-modal', '#invoice');

    // 7. Pagination (unchanged)
    $('#rows').on('change', function () {
        const rows = $(this).val();
        const url = new URL(window.location);
        url.searchParams.set('rows', rows);
        window.location = url;
    });

    // REMOVE THIS DUPLICATE HANDLER THAT RELOADS PAGE
    // It was causing revert + reload race condition
    /*
    $('#cancel-modal, #payment-modal').on('hidden.bs.modal', function () {
        location.reload();
    });
    */
});
</script>