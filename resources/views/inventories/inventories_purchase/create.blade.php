@extends('layouts.admin')

@section('title', 'Create Inventory Purchase | ' . (auth()->user()->companySetting->site_name ?? ''))

@section('content')


<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <i class="fas fa-boxes text-primary mr-2"></i> Purchase Inventory
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventories_purchases.index') }}">Inventories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Purchase Inventory</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12 col-md-12 col-lg-12 grid-margin stretch-card mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <h4 class="card-title mb-0 font-weight-bold text-dark">Record New Inventory Purchase</h4>
                            </div>
                        </div>

                        <form class="forms-sample mt-4" action="{{ route('inventories_purchases.store') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <!-- Item Selection -->
                            <div class="form-group mb-3">
                                <label for="inventory_id" class="font-weight-bold text-dark mb-1">
                                    Select Item <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('inventory_id') is-invalid @enderror js-example-basic-single w-100" id="inventory_id" name="inventory_id" required>
                                    <option value="">Select Inventory Item</option>
                                    @foreach($inventories as $inventory)
                                        <option value="{{ $inventory->id }}" @if(old('inventory_id') == $inventory->id) selected @endif>
                                            {{ $inventory->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('inventory_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Supplier Selection -->
                            <div class="form-group mb-3">
                                <label for="supplier_id" class="font-weight-bold text-dark mb-1">
                                    Select Supplier <span class="text-danger">*</span>
                                </label>
                                <select class="@error('supplier_id') is-invalid @enderror js-example-basic-single w-100" id="supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @if(old('supplier_id') == $supplier->id) selected @endif>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Quantity & Price Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="quantity" class="font-weight-bold text-dark mb-1">
                                            Quantity <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step="any" min="0.01" class="form-control @error('quantity') is-invalid @enderror" 
                                               name="quantity" value="{{ old('quantity', 1) }}" id="quantity" placeholder="Enter quantity" required>
                                        @error('quantity')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="price" class="font-weight-bold text-dark mb-1">
                                            Total Price <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary text-white font-weight-bold">{{ currency_symbol() }}</span>
                                            </div>
                                            <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" 
                                                   name="price" id="price" value="{{ old('price') }}" placeholder="0.00" required>
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method Selection -->
                            <div class="form-group mb-3">
                                <label for="payment_type" class="font-weight-bold text-dark mb-1">
                                    Payment Method <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('payment_type') is-invalid @enderror @error('paypemnt_type') is-invalid @enderror js-example-basic-single w-100" 
                                        id="payment_type" name="payment_type" required>
                                    <option value="">Select Payment Method</option>
                                    @foreach($payment_methods as $payment_method)
                                        <option value="{{ $payment_method->method }}" 
                                            data-description="{{ $payment_method->description ?? '' }}"
                                            data-fields="{{ is_array($payment_method->fields) ? json_encode($payment_method->fields) : ($payment_method->fields ?? '[]') }}"
                                            @if(old('payment_type', old('paypemnt_type')) == $payment_method->method || (strtolower($payment_method->method) === 'cash' && !old('payment_type'))) selected @endif>
                                            {{ $payment_method->method }}
                                        </option>
                                    @endforeach
                                </select>
                                <small id="payment-method-desc" class="text-muted mt-1 d-block" style="display:none;"></small>
                                @error('payment_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('paypemnt_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dynamic Payment Fields Container -->
                            <div id="dynamic-payment-fields-wrapper" class="card bg-light border p-3 mb-3" style="display:none; border-radius: 6px;">
                                <div class="d-flex align-items-center mb-2 text-primary">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <span class="font-weight-bold small text-uppercase">Payment Method Details</span>
                                </div>
                                <div id="dynamic-payment-fields-container"></div>
                            </div>

                            <!-- Amount Paid & Pending Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="amount_paid" class="font-weight-bold text-dark mb-1">
                                            Amount Paid <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary text-white font-weight-bold">{{ currency_symbol() }}</span>
                                            </div>
                                            <input type="number" step="0.01" min="0" class="form-control @error('amount_paid') is-invalid @enderror" 
                                                   name="amount_paid" id="amount_paid" value="{{ old('amount_paid', 0) }}" placeholder="0.00" required>
                                        </div>
                                        @error('amount_paid')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="amount_pending" class="font-weight-bold text-dark mb-1">
                                            Amount Pending
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-secondary text-white font-weight-bold">{{ currency_symbol() }}</span>
                                            </div>
                                            <input type="number" step="0.01" class="form-control" name="amount_pending" id="amount_pending" value="{{ old('amount_pending', 0) }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Proof Document -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark mb-1">Payment Proof / Document</label>
                                <input type="file" name="payment_file" class="file-upload-default" accept=".jpg,.jpeg,.png,.gif,.svg,.pdf,.doc,.docx">
                                <div class="input-group">
                                    <input type="text" class="form-control file-upload-info @error('payment_file') is-invalid @enderror" disabled placeholder="Upload Payment Proof (Max 2MB)">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                    </span>
                                </div>
                                <small class="text-muted d-block mt-1">Accepted formats: JPG, PNG, PDF, DOC (Max: 2MB)</small>
                                @error('payment_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="form-group mb-4">
                                <label for="description" class="font-weight-bold text-dark mb-1">Description / Notes</label>
                                <textarea class="form-control" id="description" name="description" rows="3" maxlength="160" 
                                          placeholder="Optional purchase details or supplier reference...">{{ old('description') }}</textarea>
                            </div>

                            <div class="d-flex align-items-center">
                                <button type="submit" class="btn btn-primary font-weight-bold px-4 mr-2">
                                    Add Purchase
                                </button>
                                <a class="btn btn-light" href="{{ route('inventories_purchases.index') }}">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script src="{{ asset('js/file-upload.js') }}"></script>
<script src="{{ asset('js/typeahead.js') }}"></script>

<script>
$(document).ready(function() {
    'use strict';

    // Initialize Select2 safely
    $('.js-example-basic-single').each(function() {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                width: '100%',
                dropdownAutoWidth: false
            });
        }
    });

    // Update pending amount live
    function updatePendingAmount() {
        let totalPrice = parseFloat($('#price').val()) || 0;
        let amountPaid = parseFloat($('#amount_paid').val()) || 0;
        let amountPending = totalPrice - amountPaid;
        $('#amount_pending').val(amountPending >= 0 ? amountPending.toFixed(2) : '0.00');
    }

    $('#price, #amount_paid').on('input change', updatePendingAmount);
    updatePendingAmount();

    // Dynamic Payment Fields Renderer
    function renderPaymentFields() {
        const $selected = $('#payment_type').find('option:selected');
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
            fields.forEach(function(f) {
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
    }

    $('#payment_type').on('change', renderPaymentFields);

    if ($('#payment_type').val()) {
        renderPaymentFields();
    }
});
</script>
@stop
