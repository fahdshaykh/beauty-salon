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
                            <div class="row g-3">
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

                            <div class="row g-3 mt-2">
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

                            <div class="row g-3 mt-2 align-items-end">
                                <!-- Customer Name Filter -->
                                <div class="col-md-3">
                                    <label for="customer_name" class="form-label">Customer Name</label>
                                    <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ request('customer_name') }}" placeholder="Enter customer name">
                                </div>

                                <!-- Primary Phone Filter -->
                                <div class="col-md-3">
                                    <label for="primary_phone" class="form-label">Phone</label>
                                    <input type="text" name="primary_phone" id="primary_phone" class="form-control" value="{{ request('primary_phone') }}" placeholder="Enter phone number">
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

                                        <th>User</th>

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

                                        <td>{{ currency_symbol() }}{{$book['total_price']}}</td>

                                        <td style="width: 10%;">{{$book['due_date']}}</td>

                                        <td style="width: 11%;">
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold text-dark">{{ $book['due_time'] }}</span>
                                                <small class="text-muted d-inline-flex align-items-center mt-1">
                                                    <i class="far fa-clock mr-1 text-secondary"></i> {{ $book['service_time'] ?? 30 }}m
                                                </small>
                                            </div>
                                        </td>

                                        <td style="width: 13%;">
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
                                        <td colspan='9'> No Data Available</td>
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
                <h5 class="modal-title" id="ModalLabel">Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="payment-form" action="/checkout" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <select class="form-control form-control-lg" name="payment_method" id="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="paypal">PayPal</option>
                            <option value="stripe">Stripe</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cheque_number" class="cheque_number_label" style="display:none">Cheque Number:</label>
                        <input type="text" class="form-control" id="cheque_number" name="cheque_number" style="display:none">
                    </div>
                    <input type="hidden" id="invoice" name="invoice">
                    <input type="hidden" id="service_status" name="service_status">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
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
            $('#invoice').val(bookingId);
            $('#service_status').val(newStatus);
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

    // 5. PAYMENT MODAL SUBMIT
    $('#payment-form').on('submit', function (e) {
        e.preventDefault();

        const bookingId = $('#invoice').val();
        const $select = $(`select[data-booking_id="${bookingId}"]`);

        $.ajax({
            url: "{{ url('change-booking-status') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    // iziToast.success({
                    //     title: 'Success',
                    //     message: res.success,
                    //     position: 'topRight'
                    // });
                    $('#payment-modal').modal('hide');
                    sendStatusAjax(bookingId, 3, $select);
                } else {
                    iziToast.error({
                        title: 'Failed',
                        message: res.error || 'Payment processing failed.',
                        position: 'topRight'
                    });
                }
            },
            error: function () {
                iziToast.error({
                    title: 'Error',
                    message: 'Payment processing failed.',
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