@section('pagescript')
    <script src="{{asset('js/file-upload.js')}}"></script>
    <script src="{{asset('js/typeahead.js')}}"></script>
    <script src="{{asset('js/select2.js')}}?v=2.1"></script>
    <script src="{{asset('js/formpickers.js')}}"></script>
@stop

@extends('layouts.admin')

@section('title', 'Add Supplier Payment | '. (auth()->user()->companySetting->site_name ?? ''))

@section('content')



<div class="main-panel">        
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Add Supplier Payment
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('suppliers.index')}}">Suppliers</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.balance_summary', $supplier->id) }}">{{ $supplier->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Payment</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12 mx-auto grid-margin stretch-card">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <h4 class="card-title mb-0 font-weight-bold text-dark">Record Supplier Payment</h4>
                            </div>
                            <span class="badge badge-light border text-muted">
                                Payee: <strong class="text-primary">{{ $supplier->name }}</strong>
                            </span>
                        </div>

                        <form class="forms-sample" action="{{ route('supplier_payments.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="supplier_id" value="{{ $supplier->id }}" required>

                            <!-- Amount Field -->
                            <div class="form-group mb-3">
                                <label for="amount" class="font-weight-bold text-dark">
                                    Amount <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary text-white font-weight-bold">{{ currency_symbol() }}</span>
                                    </div>
                                    <input type="number" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" 
                                           name="amount" id="amount" placeholder="Enter payment amount" value="{{ old('amount') }}" required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Method Select -->
                            <div class="form-group mb-3">
                                <label for="payment_method" class="font-weight-bold text-dark">
                                    Payment Method <span class="text-danger">*</span>
                                </label>
                                <select name="payment_method" id="payment_method" class="js-example-basic-single w-100" required>
                                    <option value="" disabled selected>Select Payment Method</option>
                                    @foreach($payment_methods as $method)
                                        <option value="{{ $method->method }}" 
                                                data-description="{{ $method->description ?? '' }}"
                                                data-fields="{{ is_array($method->fields) ? json_encode($method->fields) : ($method->fields ?? '[]') }}"
                                                @if(old('payment_method') == $method->method || (strtolower($method->method) === 'cash' && !old('payment_method'))) selected @endif>
                                            {{ $method->method }}
                                        </option>
                                    @endforeach
                                </select>
                                <small id="payment-method-desc" class="text-muted mt-1 d-block" style="display:none;"></small>
                                @error('payment_method')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dynamic Payment Fields Container -->
                            <div id="dynamic-payment-fields-wrapper" class="card bg-light border p-3 mb-3" style="display: none; border-radius: 6px;">
                                <div class="d-flex align-items-center mb-2 text-primary">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <span class="font-weight-bold small text-uppercase">Payment Details</span>
                                </div>
                                <div id="dynamic-payment-fields-container"></div>
                            </div>

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

                            <!-- Note Field -->
                            <div class="form-group mb-4">
                                <label for="note" class="font-weight-bold text-dark">Note / Reference</label>
                                <textarea class="form-control" id="note" name="note" rows="3" maxlength="160" placeholder="Optional reference notes or remarks for this disbursement...">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex align-items-center">
                                <button type="submit" class="btn btn-primary px-4 mr-2">
                                    Submit Payment
                                </button>
                                <a class="btn btn-light" href="{{ route('suppliers.balance_summary', $supplier->id) }}">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function initSelect2Fields() {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            $('#payment_method:not(.select2-hidden-accessible)').select2({
                width: '100%',
                dropdownAutoWidth: false
            });
        }
    }
    initSelect2Fields();

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

    $('#payment_method').on('change', renderPaymentFields);

    if ($('#payment_method').val()) {
        renderPaymentFields();
    }
});
</script>

@endsection
