{{-- resources/views/service_bookings/edit_inventory.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Inventory – Booking #' . $booking->id . ' | ' . Session::get('site_name'))

{{-- ------------------------------------------------- --}}
{{--  Load Select2 ONLY ONCE – remove the duplicate   --}}
{{-- ------------------------------------------------- --}}
@section('pagescript')
    <script src="{{ asset('js/select2.js') }}"></script>
@stop

@section('content')
<div class="main-panel">
    <div class="content-wrapper">

        {{-- Breadcrumb ------------------------------------------------- --}}
        <div class="page-header">
            <h3 class="page-title">Edit Inventory – Booking #{{ $booking->id }}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('service-bookings.index') }}">Bookings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Inventory</li>
                </ol>
            </nav>
        </div>

        {{-- Validation errors ------------------------------------------------- --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Main card ------------------------------------------------- --}}
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Inventory Basket</h4>

                        <form class="forms-sample"
                              action="{{ route('service-bookings.update-inventory', $booking->id) }}"
                              method="POST">
                            @csrf

                            {{-- ==== 1. Inventory selector + Add button ==== --}}
                            <div class="form-group row align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label">Select Inventory</label>
                                    <select id="inventory_id"
                                            class="js-example-basic-single w-100"
                                            style="width:100%">
                                        <option value="">-- Choose --</option>
                                        @foreach($inventories as $inv)
                                            <option value="{{ $inv->id }}" data-name="{{ $inv->name }}">
                                                {{ $inv->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="button"
                                            id="add_inventory"
                                            class="btn btn-primary w-100">Add</button>
                                </div>
                            </div>

                            {{-- ==== 2. Basket table ==== --}}
                            <div id="basket_container"
                                 style="{{ $booking->inventory_books->isEmpty() ? 'display:none' : '' }}">
                                <h5 class="mt-3 mb-2">Basket</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Inventory</th>
                                            <th>
                                                Quantity
                                                <span class="text-primary ms-1" 
                                                    style="cursor: help;" 
                                                    title="You can enter decimal quantities! Examples: 1, 0.25, 0.5, 1.75, etc.">
                                                    <i class="fas fa-info-circle"></i>
                                                </span>
                                            </th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="basket_body">
                                        @foreach($booking->inventory_books as $ib)
                                            <tr>
                                                <td>
                                                    <input type="hidden"
                                                           name="inventory_id[]"
                                                           value="{{ $ib->inventory_id }}">
                                                    {{ $ib->inventory->name }}
                                                </td>
                                                <td>
                                                    <input type="number"
                                                           name="quantity[]"
                                                           value="{{ $ib->quantity }}"
                                                           class="form-control"
                                                           min="0.01" step="0.01"
                                                           required>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm remove-row">
                                                        Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <hr class="my-4">

                            {{-- ==== 3. Submit / Cancel ==== --}}
                            <button type="submit" class="btn btn-success mr-2">Save Inventory</button>
                            <a href="{{ route('service-bookings.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- ============================================================== --}}
{{--  ONE SINGLE script block – no duplicate initialisation       --}}
{{-- ============================================================== --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
$(function () {

    /* --------------------------------------------------------------
       Initialise Select2 – ONLY ONCE
       -------------------------------------------------------------- */
    if ($.fn.select2) {
        $('.js-example-basic-single').select2({
            placeholder: "Select inventory",
            width: '100%'
        });
    }

    /* --------------------------------------------------------------
       Add button – identical to the one in the booking form
       -------------------------------------------------------------- */
    $('#add_inventory').on('click', function () {
        const $sel    = $('#inventory_id');
        const invId   = $sel.val();
        const invName = $sel.find('option:selected').data('name') || $sel.find('option:selected').text();

        if (!invId) return alert('Please select an inventory.');

        // No duplicates
        if ($(`#basket_body input[value="${invId}"]`).length) {
            return alert('This inventory is already in the basket.');
        }

        $.post('{{ url('/check-inventory-quantity') }}', {
            _token: '{{ csrf_token() }}',
            inventory_id: invId
        })
        .done(function (res) {
            let rowClass = '';

            if (res.quantity <= 0) {
                swal({
                    title: "Out of Stock!",
                    text: `"${invName}" is currently out of stock (${res.quantity} remaining).`,
                    icon: "error",
                    button: "OK",
                });
                return;
            }

            if (res.quantity < res.low_alert) {
                swal({
                    title: "Low Stock Warning",
                    text: `"${invName}" has low stock: only ${res.quantity} left!`,
                    icon: "warning",
                    button: "OK",
                });
                rowClass = 'table-danger';
            }

            // Add to basket
            $('#basket_body').append(`
                <tr class="${rowClass}">
                    <td>
                        <input type="hidden" name="inventory_id[]" value="${invId}">
                        ${invName}
                    </td>
                    <td>
                        <input type="number" name="quantity[]" class="form-control" min="0.01" step="0.01" value="1" max="${res.quantity}" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                    </td>
                </tr>
            `);

            $('#basket_container').show();
            $select.val(null).trigger('change');
        })
        .fail(function (xhr) {
            alert('Failed to check inventory.');
            console.error(xhr.responseText);
        });
    });

    /* --------------------------------------------------------------
       Remove row
       -------------------------------------------------------------- */
    $('#basket_body').on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
        if ($('#basket_body tr').length === 0) {
            $('#basket_container').hide();
        }
    });
});
</script>
