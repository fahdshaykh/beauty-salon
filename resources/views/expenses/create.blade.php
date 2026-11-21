@extends('layouts.admin')

@section('title', 'Add New Expense | ' . (auth()->user()->companySetting->site_name ?? ''))

@section('content')

<div class="main-panel">        
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <i class="fas fa-money-bill-wave text-primary mr-2"></i> Add Expense
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('expenses') }}">Expenses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Expense</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12 col-md-12 col-lg-12 grid-margin stretch-card">
                <div class="card shadow-sm border">
                    
                    <div class="card-body p-4">
                        <form class="forms-sample" action="{{ url('expenses/store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Expense Type -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark mb-1">
                                    Select Expense Type <span class="text-danger">*</span>
                                </label>
                                <select id="expense_type" name="expense_type" class="form-control @error('expense_type') is-invalid @enderror js-example-basic-single w-100" required>
                                    <option value="">Select Expense Type</option>
                                    @foreach($expense_types as $expense_type)
                                        <option value="{{ $expense_type->id }}" @if(old('expense_type') == $expense_type->id) selected @endif>
                                            {{ $expense_type->type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('expense_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark mb-1">
                                    Select Payment Method <span class="text-danger">*</span>
                                </label>
                                <select id="payment_method" name="payment_method" class="form-control @error('payment_method') is-invalid @enderror js-example-basic-single w-100" required>
                                    <option value="">Select Payment Method</option>
                                    @foreach($payment_methods as $pm)
                                        <option value="{{ $pm->id }}" 
                                                data-fields="{{ json_encode($pm->fields ?? []) }}" 
                                                data-description="{{ $pm->description ?? '' }}"
                                                @if(old('payment_method') == $pm->id) selected @endif>
                                            {{ $pm->method }}
                                        </option>
                                    @endforeach
                                </select>
                                <small id="payment-method-desc" class="form-text text-muted mt-1" style="display:none;"></small>
                                @error('payment_method')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark mb-1">
                                    Amount <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary text-white font-weight-bold">{{ currency_symbol() }}</span>
                                    </div>
                                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" 
                                           class="form-control @error('amount') is-invalid @enderror" placeholder="0.00" required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dynamic Custom Fields Container (for Cheque #, Bank Name, Ref #, etc.) -->
                            <div id="dynamic-payment-fields-wrapper" class="card bg-light border p-3 mb-3" style="display:none;">
                                <h6 class="font-weight-bold text-dark small mb-2">
                                    <i class="fas fa-list-alt text-primary mr-1"></i> Transaction Details
                                </h6>
                                <div id="dynamic-payment-fields-container"></div>
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
                                <label for="expense_description" class="font-weight-bold text-dark mb-1">
                                    Expense Description / Notes
                                </label>
                                <textarea class="form-control" id="expense_description" name="description" rows="3" 
                                          maxlength="160" placeholder="Optional expense details, invoice references or receipt notes...">{{ old('description') }}</textarea>
                            </div>

                            <div class="d-flex align-items-center">
                                <button type="submit" class="btn btn-primary font-weight-bold px-4 mr-2">
                                    Create Expense
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

    // On change & initial trigger
    $('#payment_method').on('change', renderPaymentFields);

    if ($('#payment_method').val()) {
        renderPaymentFields();
    }
});
</script>
@stop
