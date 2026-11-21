@extends('layouts.admin')

@section('title', 'Edit Expense | ' . (auth()->user()->companySetting->site_name ?? ''))

@section('content')

<div class="main-panel">        
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <i class="fas fa-edit text-primary mr-2"></i> Edit Expense
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('expenses') }}">Expenses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Expense</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12 col-md-12 col-lg-12 grid-margin stretch-card mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        

                        <form class="forms-sample" action="{{ route('expenses.update', $expense->id) }}" method="post">
                            @csrf
                            @method('PUT')

                            <!-- Category Selection -->
                            <div class="form-group mb-3">
                                <label for="expense_type" class="font-weight-bold text-dark mb-1">
                                    Expense Category <span class="text-danger">*</span>
                                </label>
                                <select id="expense_type" name="expense_type" 
                                        class="form-control @error('expense_type') is-invalid @enderror js-example-basic-single w-100" 
                                        required>
                                    <option value="">Select Expense Category</option>
                                    @foreach($expense_types as $expense_type)
                                        <option value="{{ $expense_type->id }}" 
                                            @if((string)old('expense_type', $expense->expense_type) === (string)$expense_type->id) selected @endif>
                                            {{ $expense_type->type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('expense_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Method Selection -->
                            <div class="form-group mb-3">
                                <label for="payment_method" class="font-weight-bold text-dark mb-1">
                                    Payment Method <span class="text-danger">*</span>
                                </label>
                                <select id="payment_method" name="payment_method" 
                                        class="form-control @error('payment_method') is-invalid @enderror js-example-basic-single w-100" 
                                        required>
                                    <option value="">Select Payment Method</option>
                                    @foreach($payment_methods as $payment_method)
                                        <option value="{{ $payment_method->id }}" 
                                            data-description="{{ $payment_method->description ?? '' }}"
                                            data-fields="{{ is_array($payment_method->fields) ? json_encode($payment_method->fields) : ($payment_method->fields ?? '[]') }}"
                                            @if((string)old('payment_method', $expense->payment_method) === (string)$payment_method->id) selected @endif>
                                            {{ $payment_method->method }}
                                        </option>
                                    @endforeach
                                </select>
                                <small id="payment-method-desc" class="text-muted mt-1 d-block" style="display:none;"></small>
                                @error('payment_method')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Expense Amount -->
                            <div class="form-group mb-3">
                                <label for="amount" class="font-weight-bold text-dark mb-1">
                                    Amount <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary text-white font-weight-bold">{{ currency_symbol() }}</span>
                                    </div>
                                    <input type="number" step="0.01" min="0.01" name="amount" id="amount" 
                                           value="{{ old('amount', $expense->amount) }}" 
                                           class="form-control @error('amount') is-invalid @enderror" placeholder="0.00" required>
                                </div>
                                @error('amount')
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

                            <!-- Expense Date -->
                            <div class="form-group mb-3">
                                <label for="payable" class="font-weight-bold text-dark mb-1">
                                    Expense Date <span class="text-danger">*</span>
                                </label>
                                <div id="datepicker-popup-new" class="input-group date datepicker">
                                    <input type="text" name="payable" id="payable" 
                                           value="{{ old('payable', $expense->payable) }}" 
                                           class="form-control @error('payable') is-invalid @enderror" 
                                           autocomplete="off" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-white border-left-0">
                                            <i class="far fa-calendar text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                                @error('payable')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="form-group mb-4">
                                <label for="expense_description" class="font-weight-bold text-dark mb-1">
                                    Expense Description / Notes
                                </label>
                                <textarea class="form-control" id="expense_description" name="description" rows="3" 
                                          maxlength="160" placeholder="Optional expense details, invoice references or receipt notes...">{{ old('description', $expense->description) }}</textarea>
                            </div>

                            <div class="d-flex align-items-center">
                                <button type="submit" class="btn btn-dark font-weight-bold px-4 mr-2">
                                    Update Expense
                                </button>
                                <a class="btn btn-light" href="{{ url('expenses') }}">Cancel</a>
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

    // Existing saved payment details
    const existingPaymentDetails = {!! json_encode($expense->payment_details ?? []) !!};

    // Initialize Select2 with 100% width and no auto-expand
    $('.js-example-basic-single').each(function() {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                width: '100%',
                dropdownAutoWidth: false
            });
        }
    });

    // Dynamic Payment Fields Renderer
    function renderPaymentFields() {
        const $selected = $('#payment_method').find('option:selected');
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
                const savedVal = (existingPaymentDetails && existingPaymentDetails[f.name] !== undefined) ? existingPaymentDetails[f.name] : '';
                const inputHtml = `
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold mb-1">${f.label} ${reqStar}</label>
                        <input type="${f.type || 'text'}" 
                               class="form-control form-control-sm" 
                               name="payment_details[${f.name}]" 
                               value="${savedVal}"
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

    // On change & initial trigger
    $('#payment_method').on('change', renderPaymentFields);

    if ($('#payment_method').val()) {
        renderPaymentFields();
    }
});
</script>
@stop
