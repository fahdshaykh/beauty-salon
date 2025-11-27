@extends('layouts.admin')

@section('title', 'Customers | '. (auth()->user()->companySetting->site_name ?? ''))

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">All Customers</h3>

            @can('create customers')
                <a href="{{url('customers/create')}}" class="btn btn-primary">New Customer</a>
            @endcan
            
        </div>


        <div class="card">
            <div class="card-body">
                
                <div class="row mb-2">
                    <div class="col-md-3">
                        <h4 class="card-title">Customers Listing</h4>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-3 mb-3">
                        @can('export customers')
                        <div class="text-center" style="float: center;">
                            <span data-href="/all-customer-export" id="export" class="btn btn-primary" onclick="exportTasks(event.target);">Export Customers</span>
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- Filter & Search Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body pt-4">
                        <form method="GET" action="{{ route('customers.index') }}" class="row g-3">
                            <!-- Search Field -->
                            <div class="col-md-4 mt-1">
                                <label for="search" class="form-label">Customer</label>
                                <input type="text" name="search" id="search" class="form-control"
                                    value="{{ request('search') }}"
                                    placeholder="Enter name, phone, email, referred by...">
                            </div>
                            <!-- Start Date -->
                            <div class="col-md-4">
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
                            <div class="col-md-4">
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
                                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary w-100 ml-1">Clear</a>
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
                                        <th>Name</th>
                                        <th>Primary Phone</th>
                                        <th>Date Of Birth</th>
                                        <th>E-Mail</th>
                                        <th>Address</th>
                                        <th>Referred By</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                    <tr>
                                        <td><a href="{{ url('bookings-history', $customer->id) }}"> {{ $customer->first_name }} {{ $customer->last_name }} </a></td>
                                        <td>{{ $customer->country_code }}-{{ $customer->primary_phone }}</td>
                                        <td>{{ $customer->date_of_birth }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->address }}</td>
                                        <td>{{ $customer->referred_by }}</td>
                                        <td>
                                            <label class="switch">
                                                <input type="checkbox"
                                                    id="customer-{{ $customer->id }}"
                                                    data-name="{{ $customer->first_name }}" 
                                                    onclick="changeCustomerStatus(this, {{ $customer->id }})"
                                                    {{ $customer->is_active ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @can('edit customers')
                                                <a href="{{ route('customers.edit', $customer->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @endcan


                                                @can('delete customers')
                                                <a href="javascript:void(0)"
                                                onclick="if(confirm('Are You Sure Want To Delete?')) 
                                                            document.getElementById('delete-cat-{{ $customer->id }}').submit();"
                                                class="btn btn-sm btn-outline-danger ml-1" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>

                                                <form id="delete-cat-{{ $customer->id }}"
                                                    action="{{ route('customers.destroy', $customer->id) }}"
                                                    method="POST" style="display:none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                                @endcan

                                            </div>
                                        </td>
                                    </tr>
                                    @empty

                                    <tr style="text-align: center">
                                        <td colspan='8'> No Data Available</td>
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
                        <p>Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} entries</p>
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rowsSelect = document.getElementById('rows');

    if (rowsSelect) {
        rowsSelect.addEventListener('change', function () {
            window.location = "{{ url('/customers') }}?rows=" + this.value;
        });
    }
});
</script>

<script>

function changeCustomerStatus(_this, id) {
    var $checkbox = $(_this);
    var status    = $checkbox.prop('checked') ? 1 : 0;
    var custName  = $checkbox.data('name'); // Get customer name

    $.ajax({
        url: "{{ url('customerstatus') }}",
        type: 'GET',
        data: { id: id, status: status },
        success: function (result) {
            iziToast.success({
                title: custName + (status ? ' Activated!' : ' Deactivated!'),
                message: result.success || 'Status updated successfully.',
                position: 'topRight',
                icon: status ? 'fas fa-check-circle' : 'fas fa-ban',
                timeout: 5000
            });
        },
        error: function () {
            // Revert checkbox
            $checkbox.prop('checked', !status);

            iziToast.error({
                title: custName + ' – Update Failed!',
                message: 'Could not update status. Please try again.',
                position: 'topRight',
                icon: 'fas fa-exclamation-triangle',
                timeout: 6000
            });
        }
    });
}

function exportTasks(_this) {
    let _url = $(_this).data('href');
    window.location.href = _url;
}
</script>
