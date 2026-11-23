@extends('layouts.admin')

@section('title', 'Payment Methods | ' . (auth()->user()->companySetting->site_name ?? ''))

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <i class="fas fa-money-check-alt text-primary mr-2"></i> Payment Methods Management
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('theme-settings') }}">Settings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Payment Methods</li>
                </ol>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <!-- Dynamic Form Section -->
            <div class="col-lg-6 col-12 grid-margin stretch-card">
                <div class="card shadow-sm border">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0 font-weight-bold text-dark" id="form-card-title">
                            <i class="fas fa-plus-circle text-primary mr-2"></i><span id="form-action-label">Add New Payment Method</span>
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-reset-form" style="display:none;">
                            <i class="fas fa-undo mr-1"></i> New Method
                        </button>
                    </div>
                    <div class="card-body">
                        <form id="payment-method-form" method="POST" action="{{ route('payment-methods.store') }}">
                            @csrf
                            <input type="hidden" name="_method" id="form-method-spoof" value="POST">
                            <input type="hidden" id="edit-method-id" value="">

                            <!-- 1. Required First Input: Method Name -->
                            <div class="form-group mb-3">
                                <label for="pm_method" class="font-weight-bold text-dark">
                                    Payment Method Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('method') is-invalid @enderror" 
                                       id="pm_method" name="method" 
                                       placeholder="e.g. Cheque, Bank Transfer, POS Card, Gift Card" 
                                       required maxlength="50" autocomplete="off">
                                <small class="text-muted">The primary label displayed when customers or staff choose a payment type.</small>
                            </div>

                            <!-- Description Textarea with Toggle -->
                            <div class="form-group mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="pm_description" class="font-weight-bold text-dark mb-0">
                                        Description / Instructions
                                    </label>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-primary font-weight-bold" id="btn-toggle-description">
                                        <i class="fas fa-chevron-down mr-1" id="desc-toggle-icon"></i> <span id="desc-toggle-text">Show Description</span>
                                    </button>
                                </div>
                                <div id="description-collapse-box" style="display: none;" class="mt-2">
                                    <textarea class="form-control" id="pm_description" name="description" rows="3" 
                                              placeholder="Enter optional payment terms, account details, or instructions shown during checkout..."></textarea>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Dynamic Related Custom Inputs Section -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">
                                        <i class="fas fa-stream text-primary mr-1"></i> Transaction Custom Fields
                                    </h6>
                                    <small class="text-muted">Dynamic inputs captured upon booking completion (e.g., Cheque #, Bank Name).</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold" id="btn-add-field-row">
                                    <i class="fas fa-plus mr-1"></i> Add Field Row
                                </button>
                            </div>

                            <div id="dynamic-fields-container" class="mt-3">
                                <!-- Dynamic rows are injected here -->
                            </div>

                            <!-- Status Toggle -->
                            <div class="form-group mt-3 pt-3 border-top">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="pm_is_active" name="is_active" value="1" checked>
                                    <label class="custom-control-label font-weight-bold text-dark" for="pm_is_active">
                                        Active in Checkout &amp; Booking Flows
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end align-items-center mt-4">
                                <button type="submit" class="btn btn-primary px-4 font-weight-bold" id="btn-submit-pm">
                                    <i class="fas fa-save mr-1"></i> Save Payment Method
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Methods List Section -->
            <div class="col-lg-6 col-12 grid-margin stretch-card">
                <div class="card shadow-sm border">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 font-weight-bold text-dark">
                            <i class="fas fa-list text-primary mr-2"></i> Configured Payment Methods ({{ $methods->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" id="payment-methods-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="font-weight-bold">Method</th>
                                        <th class="font-weight-bold">Custom Fields</th>
                                        <th class="font-weight-bold">Status</th>
                                        <th class="font-weight-bold text-center" style="width: 80px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($methods as $method)
                                        <tr data-id="{{ $method->id }}" 
                                            data-method="{{ $method->method }}" 
                                            data-description="{{ $method->description }}"
                                            data-is-active="{{ $method->is_active ? '1' : '0' }}"
                                            data-fields="{{ json_encode($method->fields ?? []) }}">
                                            <td>
                                                <div class="font-weight-bold text-dark">{{ $method->method }}</div>
                                                @if($method->description)
                                                    <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                                        {{ $method->description }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($method->fields) && count($method->fields) > 0)
                                                    <div class="d-flex flex-wrap" style="gap: 4px;">
                                                        @foreach($method->fields as $f)
                                                            <span class="badge badge-primary py-1 px-2" title="{{ $f['name'] ?? '' }} ({{ $f['type'] ?? 'text' }})">
                                                                {{ $f['label'] ?? $f['name'] }}
                                                                @if(!empty($f['required']))
                                                                    <span class="text-warning">*</span>
                                                                @endif
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted small fst-italic">Standard (No custom fields)</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($method->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm action-dots-btn no-caret" 
                                                            type="button" 
                                                            id="actionDropdown_{{ $method->id }}" 
                                                            data-toggle="dropdown" 
                                                            aria-haspopup="true" 
                                                            aria-expanded="false" 
                                                            title="Actions">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right py-2" aria-labelledby="actionDropdown_{{ $method->id }}" style="min-width: 160px;">
                                                        <button type="button" class="dropdown-item btn-edit-pm d-flex align-items-center">
                                                            <i class="fas fa-edit fa-fw text-primary mr-2"></i> Edit
                                                        </button>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item d-flex align-items-center text-danger" 
                                                           href="javascript:void(0)"
                                                           onclick="if(confirm('Are you sure you want to delete {{ $method->method }}?')) document.getElementById('delete-pm-{{ $method->id }}').submit();">
                                                            <i class="fas fa-trash-alt fa-fw text-danger mr-2"></i> Delete
                                                        </a>
                                                        <form id="delete-pm-{{ $method->id }}" action="{{ route('payment-methods.destroy', $method->id) }}" method="POST" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="fas fa-info-circle mr-1"></i> No payment methods configured yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic Row Template -->
<template id="dynamic-field-template">
    <div class="dynamic-field-row card border bg-light p-3 mb-2 rounded position-relative">
        <div class="row align-items-center">
            <div class="col-md-5 col-12 mb-2 mb-md-0">
                <label class="small font-weight-bold text-dark mb-1">Field Label <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm field-label-input" 
                       placeholder="e.g. Cheque Number, Bank Name" required>
            </div>
            <div class="col-md-3 col-6 mb-2 mb-md-0">
                <label class="small font-weight-bold text-dark mb-1">Type</label>
                <select class="form-control form-control-sm field-type-select">
                    <option value="text">Text</option>
                    <option value="number">Number</option>
                    <option value="date">Date</option>
                </select>
            </div>
            <div class="col-md-3 col-4 d-flex align-items-center pt-md-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input field-required-checkbox" id="field_req_{INDEX}" value="1" checked>
                    <label class="custom-control-label small font-weight-bold" for="field_req_{INDEX}">Required</label>
                </div>
            </div>
            <div class="col-md-1 col-2 text-right pt-md-3">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-field" title="Remove Field">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <input type="hidden" class="field-name-input">
    </div>
</template>

<script>
(function($) {
    'use strict';
    let rowIndex = 0;

    // Toggle description textarea
    $('#btn-toggle-description').on('click', function() {
        const $box = $('#description-collapse-box');
        $box.slideToggle(200, function() {
            if ($box.is(':visible')) {
                $('#desc-toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                $('#desc-toggle-text').text('Hide Description');
            } else {
                $('#desc-toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                $('#desc-toggle-text').text('Show Description');
            }
        });
    });

    // Add a field row
    function addFieldRow(data = {}) {
        const templateHtml = $('#dynamic-field-template').html().replace(/{INDEX}/g, rowIndex);
        const $row = $(templateHtml);

        $row.find('.field-label-input')
            .attr('name', `fields[${rowIndex}][label]`)
            .val(data.label || '');

        $row.find('.field-name-input')
            .attr('name', `fields[${rowIndex}][name]`)
            .val(data.name || '');

        $row.find('.field-type-select')
            .attr('name', `fields[${rowIndex}][type]`)
            .val(data.type || 'text');

        $row.find('.field-required-checkbox')
            .attr('name', `fields[${rowIndex}][required]`)
            .prop('checked', data.required !== false);

        // Auto-slugify label into name
        $row.find('.field-label-input').on('keyup change', function() {
            const labelVal = $(this).val();
            const slug = labelVal.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
            $row.find('.field-name-input').val(slug);
        });

        $('#dynamic-fields-container').append($row);
        rowIndex++;
    }

    $('#btn-add-field-row').on('click', function() {
        addFieldRow();
    });

    // Remove row
    $(document).on('click', '.btn-remove-field', function() {
        $(this).closest('.dynamic-field-row').slideUp(150, function() {
            $(this).remove();
        });
    });

    // Reset Form to Create mode
    function resetForm() {
        $('#payment-method-form').attr('action', "{{ route('payment-methods.store') }}");
        $('#form-method-spoof').val('POST');
        $('#edit-method-id').val('');
        $('#form-action-label').text('Add New Payment Method');
        $('#btn-submit-pm').html('<i class="fas fa-save mr-1"></i> Save Payment Method');
        $('#btn-reset-form').hide();
        $('#pm_method').val('');
        $('#pm_description').val('');
        $('#description-collapse-box').hide();
        $('#desc-toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        $('#desc-toggle-text').text('Show Description');
        $('#pm_is_active').prop('checked', true);
        $('#dynamic-fields-container').empty();
        rowIndex = 0;
    }

    $('#btn-reset-form').on('click', function() {
        resetForm();
    });

    // Edit Button Handler: Populate form
    $('.btn-edit-pm').on('click', function() {
        const $tr = $(this).closest('tr');
        const id = $tr.data('id');
        const method = $tr.data('method');
        const description = $tr.data('description') || '';
        const isActive = $tr.data('is-active') == '1';
        let fields = $tr.data('fields');

        if (typeof fields === 'string') {
            try { fields = JSON.parse(fields); } catch(e) { fields = []; }
        }

        // Switch to Update mode
        $('#payment-method-form').attr('action', `/payment-methods/${id}`);
        $('#form-method-spoof').val('PUT');
        $('#edit-method-id').val(id);
        $('#form-action-label').text(`Edit Payment Method: ${method}`);
        $('#btn-submit-pm').html('<i class="fas fa-check-circle mr-1"></i> Update Payment Method');
        $('#btn-reset-form').show();

        $('#pm_method').val(method);
        $('#pm_description').val(description);
        if (description) {
            $('#description-collapse-box').show();
            $('#desc-toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $('#desc-toggle-text').text('Hide Description');
        } else {
            $('#description-collapse-box').hide();
            $('#desc-toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $('#desc-toggle-text').text('Show Description');
        }

        $('#pm_is_active').prop('checked', isActive);
        $('#dynamic-fields-container').empty();
        rowIndex = 0;

        if (Array.isArray(fields) && fields.length > 0) {
            fields.forEach(function(f) {
                addFieldRow(f);
            });
        }

        // Scroll to form on mobile/tablet
        $('html, body').animate({
            scrollTop: $('#payment-method-form').offset().top - 80
        }, 300);
    });

})(jQuery);
</script>
@endsection
