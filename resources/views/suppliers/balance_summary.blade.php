@extends('layouts.admin')

@section('title', 'Supplier Balance Summary | ' . (auth()->user()->companySetting->site_name ?? 'bSaloon'))

@section('content')
<div class="main-panel">
    <div class="content-wrapper">

        <!-- Print-Only Letterhead Header -->
        <div class="d-none d-print-block mb-4">
            <div class="row align-items-center">
                <div class="col-8">
                    <h2 class="font-weight-bold text-dark">{{ auth()->user()->companySetting->site_name ?? 'bSaloon' }}</h2>
                    <p class="mb-0 text-muted"><strong>Supplier Statement of Account</strong></p>
                    <p class="mb-0 text-muted">Generated: {{ date('d M Y, h:i A') }}</p>
                </div>
                <div class="col-4 text-right">
                    <h4 class="font-weight-bold mb-1">{{ $supplier->name }}</h4>
                    <p class="mb-0 small text-muted">{{ $supplier->phone ?? '' }} | {{ $supplier->email ?? '' }}</p>
                    <p class="mb-0 small text-muted">{{ $supplier->address ?? '' }}</p>
                </div>
            </div>
            <hr class="my-3">
        </div>

        <!-- Page Header & Action Bar -->
        <div class="page-header flex-wrap non-printable">
            <div class="d-flex align-items-center">
                <h3 class="page-title mb-0 mr-3">Supplier Balance Summary</h3>
                <span class="badge badge-outline-primary font-weight-bold">{{ $supplier->name }}</span>
            </div>
            <div class="d-flex align-items-center mt-2 mt-md-0">
                <a href="{{ route('supplier_payments.edit', $supplier->id) }}" class="btn btn-primary btn-sm mr-2">
                    <i class="fa fa-plus-circle mr-1"></i> Add Payment
                </a>
                <a href="{{ route('suppliers.balance_summary.export', request()->all() + ['id' => $supplier->id]) }}" class="btn btn-outline-success btn-sm mr-2">
                    <i class="fa fa-file-csv mr-1"></i> Export CSV
                </a>
                <button type="button" onclick="window.print()" class="btn btn-outline-dark btn-sm mr-2">
                    <i class="fa fa-print mr-1"></i> Print Statement
                </button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left mr-1"></i> Back to Suppliers
                </a>
            </div>
        </div>

        <!-- Supplier Details & Contacts Banner -->
        <div class="card mb-4 non-printable shadow-sm border-0">
            <div class="card-body py-3">
                <div class="row text-center text-md-left">
                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <small class="text-muted d-block font-weight-bold text-uppercase">Supplier Name</small>
                        <span class="font-weight-bold text-dark">{{ $supplier->name }}</span>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <small class="text-muted d-block font-weight-bold text-uppercase">Phone</small>
                        <span>{{ $supplier->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <small class="text-muted d-block font-weight-bold text-uppercase">Email</small>
                        <span>{{ $supplier->email ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <small class="text-muted d-block font-weight-bold text-uppercase">Payment Terms</small>
                        <span>{{ $supplier->payment_terms ?? 'Standard (Net 30)' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial KPI Cards -->
        <div class="row mb-4">
            <!-- Opening Balance -->
            <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 font-weight-medium">Opening Balance</p>
                                <h3 class="font-weight-bold mb-0 text-dark">${{ number_format($openingBalance, 2) }}</h3>
                            </div>
                            <div class="icon-box bg-light text-secondary p-3 rounded-circle">
                                <i class="fa fa-history fa-lg"></i>
                            </div>
                        </div>
                        <p class="text-muted mt-3 mb-0 small">
                            <i class="fa fa-calendar-alt mr-1"></i>
                            {{ $hasDateFilter ? 'Before ' . \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Prior to period' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Period Purchases -->
            <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 font-weight-medium">Total Purchases</p>
                                <h3 class="font-weight-bold mb-0 text-primary">${{ number_format($periodPurchases, 2) }}</h3>
                            </div>
                            <div class="icon-box bg-primary-light text-primary p-3 rounded-circle" style="background-color: rgba(57, 44, 112, 0.1);">
                                <i class="fa fa-shopping-cart fa-lg"></i>
                            </div>
                        </div>
                        <p class="text-muted mt-3 mb-0 small">
                            <i class="fa fa-boxes mr-1"></i> Lifetime: ${{ number_format($lifetimePurchases, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Period Paid -->
            <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 font-weight-medium">Total Paid</p>
                                <h3 class="font-weight-bold mb-0 text-success">${{ number_format($periodPaid, 2) }}</h3>
                            </div>
                            <div class="icon-box bg-success-light text-success p-3 rounded-circle" style="background-color: rgba(40, 167, 69, 0.1);">
                                <i class="fa fa-check-circle fa-lg"></i>
                            </div>
                        </div>
                        <p class="text-muted mt-3 mb-0 small">
                            <i class="fa fa-receipt mr-1"></i> Lifetime: ${{ number_format($lifetimePaid, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Net Payable / Balance Due -->
            <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics shadow-sm border-0 {{ $closingBalance > 0 ? 'border-left-danger' : 'border-left-success' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 font-weight-medium">Net Payable</p>
                                <h3 class="font-weight-bold mb-0 {{ $closingBalance > 0 ? 'text-danger' : 'text-success' }}">
                                    ${{ number_format($closingBalance, 2) }}
                                </h3>
                            </div>
                            <div class="icon-box p-3 rounded-circle {{ $closingBalance > 0 ? 'text-danger' : 'text-success' }}" style="background-color: {{ $closingBalance > 0 ? 'rgba(220, 53, 69, 0.1)' : 'rgba(40, 167, 69, 0.1)' }};">
                                <i class="fa {{ $closingBalance > 0 ? 'fa-exclamation-circle' : 'fa-check-double' }} fa-lg"></i>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 small">
                            @if($closingBalance > 0)
                                <span class="badge badge-danger font-weight-bold">Payment Due</span>
                            @elseif($closingBalance < 0)
                                <span class="badge badge-info font-weight-bold">Advance Credit</span>
                            @else
                                <span class="badge badge-success font-weight-bold">Account Settled</span>
                            @endif
                            <span class="text-muted ml-1">Current Balance</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Range Filter & Quick Presets Toolbar -->
        <div class="card mb-4 non-printable shadow-sm border-0">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('suppliers.balance_summary', $supplier->id) }}" id="statementFilterForm" class="row align-items-end">
                    <input type="hidden" name="tab" value="{{ $activeTab }}" id="filterActiveTab">

                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <label class="font-weight-bold small text-muted mb-1">Start Date</label>
                        <input type="date" name="start_date" id="filterStartDate" value="{{ $startDate ?? '' }}" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <label class="font-weight-bold small text-muted mb-1">End Date</label>
                        <input type="date" name="end_date" id="filterEndDate" value="{{ $endDate ?? '' }}" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6 col-sm-12 d-flex flex-wrap align-items-center mt-2 mt-md-0">
                        <button type="submit" class="btn btn-primary btn-sm mr-2 mb-1">
                            <i class="fa fa-filter mr-1"></i> Filter
                        </button>
                        <a href="{{ route('suppliers.balance_summary', $supplier->id) }}" class="btn btn-light btn-sm mr-2 mb-1">
                            <i class="fa fa-undo mr-1"></i> Reset
                        </a>

                        <!-- Quick Presets -->
                        <div class="btn-group btn-group-sm mb-1 ml-auto" role="group">
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('all')">All Time</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('this_month')">This Month</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('last_month')">Last Month</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('this_year')">This Year</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Tabbed Interface -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-3 px-3 non-printable">
                <ul class="nav nav-tabs card-header-tabs" id="supplierSummaryTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold {{ $activeTab === 'ledger' ? 'active' : '' }}" id="ledger-tab" data-toggle="tab" href="#ledger-pane" role="tab" aria-controls="ledger-pane" aria-selected="{{ $activeTab === 'ledger' ? 'true' : 'false' }}">
                            <i class="fa fa-book mr-1"></i> Statement of Account (Ledger)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold {{ $activeTab === 'purchases' ? 'active' : '' }}" id="purchases-tab" data-toggle="tab" href="#purchases-pane" role="tab" aria-controls="purchases-pane" aria-selected="{{ $activeTab === 'purchases' ? 'true' : 'false' }}">
                            <i class="fa fa-shopping-cart mr-1"></i> Purchases ({{ $purchases->total() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold {{ $activeTab === 'payments' ? 'active' : '' }}" id="payments-tab" data-toggle="tab" href="#payments-pane" role="tab" aria-controls="payments-pane" aria-selected="{{ $activeTab === 'payments' ? 'true' : 'false' }}">
                            <i class="fa fa-money-bill-wave mr-1"></i> Payments ({{ $payments->total() }})
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content border-0 p-3" id="supplierSummaryTabContent">

                    <!-- TAB 1: Unified Statement of Account (Ledger) -->
                    <div class="tab-pane fade {{ $activeTab === 'ledger' ? 'show active' : '' }}" id="ledger-pane" role="tabpanel" aria-labelledby="ledger-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0">Unified Supplier Ledger</h5>
                                <small class="text-muted">Chronological transactions with live mathematical running balance</small>
                            </div>
                            <span class="badge badge-light text-muted border">Total Transactions: {{ $ledger->total() }}</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead style="background-color: #392C70; color: #fff;">
                                    <tr>
                                        <th style="width: 120px;">Date</th>
                                        <th style="width: 100px;">Type</th>
                                        <th>Reference</th>
                                        <th>Description / Note</th>
                                        <th class="text-right" style="width: 140px;">Billed (Debit)</th>
                                        <th class="text-right" style="width: 140px;">Paid (Credit)</th>
                                        <th class="text-right" style="width: 150px;">Running Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Opening Balance Row -->
                                    <tr class="table-secondary font-weight-bold">
                                        <td>{{ $hasDateFilter ? \Carbon\Carbon::parse($startDate)->format('Y-m-d') : 'Beginning' }}</td>
                                        <td><span class="badge badge-info text-white">Opening</span></td>
                                        <td>Balance Brought Forward</td>
                                        <td class="text-muted">Cumulative balance prior to selected period</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right text-dark">${{ number_format($openingBalance, 2) }}</td>
                                    </tr>

                                    @forelse ($ledger as $row)
                                        <tr>
                                            <td>{{ $row->txn_date }}</td>
                                            <td>
                                                @if($row->txn_type === 'purchase')
                                                    <span class="badge badge-primary">Bill</span>
                                                @else
                                                    <span class="badge badge-success">Payment</span>
                                                @endif
                                            </td>
                                            <td class="font-weight-medium">{{ $row->reference }}</td>
                                            <td>{{ $row->note ?? '-' }}</td>
                                            <td class="text-right font-weight-bold text-dark">
                                                {{ $row->debit > 0 ? '$' . number_format($row->debit, 2) : '-' }}
                                            </td>
                                            <td class="text-right font-weight-bold text-success">
                                                {{ $row->credit > 0 ? '$' . number_format($row->credit, 2) : '-' }}
                                            </td>
                                            <td class="text-right font-weight-bold {{ $row->running_balance > 0 ? 'text-danger' : 'text-success' }}">
                                                ${{ number_format($row->running_balance, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="fa fa-folder-open fa-2x mb-2 d-block"></i>
                                                No transactions recorded for the selected period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="font-weight-bold bg-light">
                                    <tr>
                                        <td colspan="4" class="text-right text-uppercase">Period Activity & Closing Balance:</td>
                                        <td class="text-right text-dark">${{ number_format($periodPurchases, 2) }}</td>
                                        <td class="text-right text-success">${{ number_format($periodPaid, 2) }}</td>
                                        <td class="text-right {{ $closingBalance > 0 ? 'text-danger' : 'text-success' }} font-weight-bold">
                                            ${{ number_format($closingBalance, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap non-printable">
                            <span class="text-muted small">
                                Showing {{ $ledger->firstItem() ?? 0 }} to {{ $ledger->lastItem() ?? 0 }} of {{ $ledger->total() }} entries
                            </span>
                            <div>
                                {{ $ledger->links() }}
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Purchases List -->
                    <div class="tab-pane fade {{ $activeTab === 'purchases' ? 'show active' : '' }}" id="purchases-pane" role="tabpanel" aria-labelledby="purchases-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0">Purchase Invoices & Orders</h5>
                                <small class="text-muted">Itemized inventory purchases from this supplier</small>
                            </div>
                            <span class="badge badge-light text-muted border">Total Invoices: {{ $purchases->total() }}</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead style="background-color: #392C70; color: #fff;">
                                    <tr>
                                        <th>Date</th>
                                        <th>Item / Inventory</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-right">Price</th>
                                        <th class="text-right">Paid</th>
                                        <th class="text-right">Pending</th>
                                        <th class="text-center">Status</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($purchases as $purchase)
                                        @php
                                            $isPaid = $purchase->amount_pending <= 0;
                                            $isPartial = $purchase->amount_paid > 0 && $purchase->amount_pending > 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $purchase->purchase_date ?? $purchase->created_at->format('Y-m-d') }}</td>
                                            <td class="font-weight-medium">{{ $purchase->inventory->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $purchase->quantity }}</td>
                                            <td class="text-right font-weight-bold">${{ number_format($purchase->price, 2) }}</td>
                                            <td class="text-right text-success">${{ number_format($purchase->amount_paid, 2) }}</td>
                                            <td class="text-right text-danger">${{ number_format($purchase->amount_pending, 2) }}</td>
                                            <td class="text-center">
                                                @if($isPaid)
                                                    <span class="badge badge-success">Paid</span>
                                                @elseif($isPartial)
                                                    <span class="badge badge-warning text-dark">Partial</span>
                                                @else
                                                    <span class="badge badge-danger">Unpaid</span>
                                                @endif
                                            </td>
                                            <td>{{ $purchase->description ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
                                                No purchase records found for this period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap non-printable">
                            <span class="text-muted small">
                                Showing {{ $purchases->firstItem() ?? 0 }} to {{ $purchases->lastItem() ?? 0 }} of {{ $purchases->total() }} entries
                            </span>
                            <div>
                                {{ $purchases->links() }}
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Payments List -->
                    <div class="tab-pane fade {{ $activeTab === 'payments' ? 'show active' : '' }}" id="payments-pane" role="tabpanel" aria-labelledby="payments-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0">Payment Receipts & History</h5>
                                <small class="text-muted">All payments disbursed to this supplier</small>
                            </div>
                            <span class="badge badge-light text-muted border">Total Payments: {{ $payments->total() }}</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead style="background-color: #392C70; color: #fff;">
                                    <tr>
                                        <th>Date</th>
                                        <th>Payment Voucher #</th>
                                        <th>Linked Purchase Bill</th>
                                        <th class="text-right">Amount Paid</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                                            <td class="font-weight-bold text-primary">PAY-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td>
                                                @if($payment->inventory_purchase_id)
                                                    <span class="badge badge-outline-secondary">Bill #{{ $payment->inventory_purchase_id }} ({{ $payment->purchase->inventory->name ?? 'Inventory' }})</span>
                                                @else
                                                    <span class="badge badge-outline-info">General Account Payment</span>
                                                @endif
                                            </td>
                                            <td class="text-right font-weight-bold text-success">${{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ $payment->note ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                No payment records found for this period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap non-printable">
                            <span class="text-muted small">
                                Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} entries
                            </span>
                            <div>
                                {{ $payments->links() }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    .non-printable, .navbar, .sidebar, #app-main-footer, .page-header, .btn, .nav-tabs {
        display: none !important;
    }
    .main-panel {
        width: 100% !important;
        padding: 0 !important;
    }
    .content-wrapper {
        padding: 0 !important;
        background: transparent !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .table-responsive {
        overflow: visible !important;
    }
    .table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    .table th, .table td {
        border: 1px solid #dee2e6 !important;
    }
}
</style>

<script>
function setDateRange(range) {
    var today = new Date();
    var startDateInput = document.getElementById('filterStartDate');
    var endDateInput = document.getElementById('filterEndDate');
    var form = document.getElementById('statementFilterForm');

    function formatDate(d) {
        var month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();
        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        return [year, month, day].join('-');
    }

    if (range === 'all') {
        startDateInput.value = '';
        endDateInput.value = '';
    } else if (range === 'this_month') {
        var start = new Date(today.getFullYear(), today.getMonth(), 1);
        var end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        startDateInput.value = formatDate(start);
        endDateInput.value = formatDate(end);
    } else if (range === 'last_month') {
        var start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        var end = new Date(today.getFullYear(), today.getMonth(), 0);
        startDateInput.value = formatDate(start);
        endDateInput.value = formatDate(end);
    } else if (range === 'this_year') {
        var start = new Date(today.getFullYear(), 0, 1);
        var end = new Date(today.getFullYear(), 11, 31);
        startDateInput.value = formatDate(start);
        endDateInput.value = formatDate(end);
    }

    form.submit();
}

// Keep active tab state when switching tabs
$(document).ready(function() {
    $('#supplierSummaryTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var tabId = $(e.target).attr('id');
        var tabName = 'ledger';
        if (tabId === 'purchases-tab') tabName = 'purchases';
        if (tabId === 'payments-tab') tabName = 'payments';
        $('#filterActiveTab').val(tabName);
    });
});
</script>
@endsection
