@section('pagescript')
<script src="{{asset('js/select2.js')}}?v=2.1"></script>
<script src="{{asset('js/formpickers.js')}}"></script>
@stop

@extends('layouts.admin')

@section('title', 'Payment Reports | ' . (auth()->user()->companySetting->site_name ?? ''))

@section('content')

<style>
.report-filter-row .input-group.date {
    padding: 0 !important;
}

.report-filter-row .form-control,
.report-filter-row .input-group-text,
.report-filter-row .select2-container--default .select2-selection--single {
    height: 46px !important;
    padding-top: 0.875rem !important;
    padding-bottom: 0.875rem !important;
}

.report-filter-row .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 18px !important;
    padding-left: 0 !important;
}

.report-filter-row .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 44px !important;
}

.report-filter-row .btn {
    height: 46px !important;
}

.stat-widget {
    border-radius: 8px;
    transition: all 0.2s ease-in-out;
    border-left: 4px solid transparent;
    background: #fff;
}

.stat-widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
}

.stat-widget.widget-primary { border-left-color: #4b49ac; }
.stat-widget.widget-success { border-left-color: #28a745; }
.stat-widget.widget-info { border-left-color: #17a2b8; }
.stat-widget.widget-danger { border-left-color: #dc3545; }
.stat-widget.widget-warning { border-left-color: #ffc107; }

.kpi-card {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    transition: transform 0.2s ease;
}

.kpi-card:hover {
    transform: translateY(-2px);
}

.badge-inflow {
    background-color: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
    font-weight: 600;
}

.badge-outflow {
    background-color: #ffebee;
    color: #c62828;
    border: 1px solid #ffcdd2;
    font-weight: 600;
}

.custom-detail-chip {
    display: inline-block;
    padding: 2px 6px;
    margin: 2px 3px 2px 0;
    font-size: 0.78rem;
    background: #f1f3f5;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    color: #495057;
}

.custom-detail-chip strong {
    color: #392C70;
}
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Payment Report
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('payment-reports') }}">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Payment Report</li>
                </ol>
            </nav>
        </div>

        

        @php
            $startDate = $filters['start_date'] ?? date('Y-m-01');
            $endDate   = $filters['end_date'] ?? date('Y-m-d');
            $selectedMethod = $filters['payment_method'] ?? 'all';
            $selectedType   = $filters['transaction_type'] ?? 'all';
            $selectedFlow   = $filters['flow_type'] ?? 'all';

            $records      = $reportData['records'] ?? collect();
            $totalInflow  = $reportData['total_inflow'] ?? 0;
            $totalOutflow = $reportData['total_outflow'] ?? 0;
            $netBalance   = $reportData['net_balance'] ?? 0;
            $byMethod     = $reportData['by_method'] ?? [];
            $counts       = $reportData['counts'] ?? ['total' => 0, 'inflow' => 0, 'outflow' => 0];

            $exportParams = [
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'payment_method'   => $selectedMethod,
                'transaction_type' => $selectedType,
                'flow_type'        => $selectedFlow,
            ];
        @endphp

        <!-- Metric Stat Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                <div class="card stat-widget widget-success shadow-sm border-0 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase font-weight-bold small">Total Inflow</span>
                            <h3 class="font-weight-bold text-success mt-1 mb-0">{{ currency_symbol() }}{{ number_format($totalInflow, 2) }}</h3>
                            <small class="text-muted">{{ number_format($counts['inflow']) }} received payments</small>
                        </div>
                        <div class="p-3 bg-light rounded-circle text-success">
                            <i class="fas fa-arrow-down fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                <div class="card stat-widget widget-danger shadow-sm border-0 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase font-weight-bold small">Total Outflow</span>
                            <h3 class="font-weight-bold text-danger mt-1 mb-0">{{ currency_symbol() }}{{ number_format($totalOutflow, 2) }}</h3>
                            <small class="text-muted">{{ number_format($counts['outflow']) }} paid expenses & bills</small>
                        </div>
                        <div class="p-3 bg-light rounded-circle text-danger">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                <div class="card stat-widget {{ $netBalance >= 0 ? 'widget-primary' : 'widget-danger' }} shadow-sm border-0 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase font-weight-bold small">Net Balance</span>
                            <h3 class="font-weight-bold {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }} mt-1 mb-0">{{ currency_symbol() }}{{ number_format($netBalance, 2) }}</h3>
                            <small class="text-muted">{{ $netBalance >= 0 ? 'Positive cash surplus' : 'Negative cash deficit' }}</small>
                        </div>
                        <div class="p-3 bg-light rounded-circle {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }}">
                            <i class="fas fa-wallet fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                <div class="card stat-widget widget-info shadow-sm border-0 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase font-weight-bold small">Transactions</span>
                            <h3 class="font-weight-bold text-dark mt-1 mb-0">{{ number_format($counts['total']) }}</h3>
                            <small class="text-muted">Total recorded entries</small>
                        </div>
                        <div class="p-3 bg-light rounded-circle text-info">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-filter text-primary mr-2"></i>Filter Transactions
                    </h5>
                    <div>
                        <a href="{{ route('payment.export', $exportParams) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-file-csv mr-1"></i> Export to CSV
                        </a>
                        <a href="{{ url('payment-reports') }}" class="btn btn-outline-secondary btn-sm ml-1" title="Reset Filters">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>

                <form action="{{ url('payment-reports') }}" method="GET">
                    <div class="form-row align-items-center report-filter-row">
                        <!-- Start Date -->
                        <div class="col-12 col-sm-6 col-md-3 col-lg mb-2">
                            <label class="small text-muted mb-1">From Date</label>
                            <div id="datepicker-popup-new" class="input-group date datepicker">
                                <input type="text" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}" placeholder="From Date" autocomplete="off">
                                <span class="input-group-addon input-group-append border-left">
                                    <span class="far fa-calendar input-group-text"></span>
                                </span>
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="col-12 col-sm-6 col-md-3 col-lg mb-2">
                            <label class="small text-muted mb-1">To Date</label>
                            <div id="datepicker-popup-new2" class="input-group date datepicker">
                                <input type="text" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}" placeholder="To Date" autocomplete="off">
                                <span class="input-group-addon input-group-append border-left">
                                    <span class="far fa-calendar input-group-text"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-12 col-sm-6 col-md-3 col-lg mb-2">
                            <label class="small text-muted mb-1">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="form-control js-example-basic-single w-100" style="width:100%">
                                <option value="all" {{ $selectedMethod == 'all' ? 'selected' : '' }}>All Payment Methods</option>
                                @foreach($payment_methods as $pm)
                                    <option value="{{ $pm->id }}" {{ ($selectedMethod == $pm->id || strtolower($selectedMethod) == strtolower($pm->method)) ? 'selected' : '' }}>
                                        {{ $pm->method }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Source Module -->
                        <div class="col-12 col-sm-6 col-md-3 col-lg mb-2">
                            <label class="small text-muted mb-1">Source / Module</label>
                            <select id="transaction_type" name="transaction_type" class="form-control js-example-basic-single w-100" style="width:100%">
                                <option value="all" {{ $selectedType == 'all' ? 'selected' : '' }}>All Sources</option>
                                <option value="booking" {{ $selectedType == 'booking' ? 'selected' : '' }}>Service Bookings (Inflow)</option>
                                <option value="expense" {{ $selectedType == 'expense' ? 'selected' : '' }}>Expenses (Outflow)</option>
                                <option value="inventory_purchase" {{ $selectedType == 'inventory_purchase' ? 'selected' : '' }}>Inventory Purchases (Outflow)</option>
                                <option value="supplier_payment" {{ $selectedType == 'supplier_payment' ? 'selected' : '' }}>Supplier Payments (Outflow)</option>
                            </select>
                        </div>

                        <!-- Flow Type -->
                        <div class="col-12 col-sm-6 col-md-3 col-lg mb-2">
                            <label class="small text-muted mb-1">Flow Type</label>
                            <select id="flow_type" name="flow_type" class="form-control js-example-basic-single w-100" style="width:100%">
                                <option value="all" {{ $selectedFlow == 'all' ? 'selected' : '' }}>All Inflows & Outflows</option>
                                <option value="inflow" {{ $selectedFlow == 'inflow' ? 'selected' : '' }}>Inflow Only (+)</option>
                                <option value="outflow" {{ $selectedFlow == 'outflow' ? 'selected' : '' }}>Outflow Only (-)</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 col-sm-6 col-md-auto mb-2 text-center text-md-left">
                            <label class="small d-none d-md-block mb-1">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block px-4">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment Method Breakdown Pills -->
        @if(!empty($byMethod) && count($byMethod) > 0)
        <div class="card mb-4">
            <div class="card-body py-3 px-4">
                <h6 class="font-weight-bold mb-2 text-dark">
                    <i class="fas fa-credit-card text-muted mr-1"></i> Payment Method Breakdown
                </h6>
                <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
                    @foreach($byMethod as $methodName => $data)
                        <div class="border rounded px-3 py-2 bg-light d-flex align-items-center" style="min-width: 170px;">
                            <div class="mr-2">
                                <span class="badge badge-dark">{{ $data['name'] }}</span>
                            </div>
                            <div>
                                <div class="small font-weight-bold text-dark">
                                    Net: {{ currency_symbol() }}{{ number_format($data['net'], 2) }}
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    In: <span class="text-success">+{{ currency_symbol() }}{{ number_format($data['inflow'], 0) }}</span> | 
                                    Out: <span class="text-danger">-{{ currency_symbol() }}{{ number_format($data['outflow'], 0) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Transactions Listing Card -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title mb-0">Transaction Records</h4>
                        <small class="text-muted">Showing payments between <strong>{{ $startDate }}</strong> and <strong>{{ $endDate }}</strong></small>
                    </div>
                    <div>
                        <span class="badge badge-light border text-muted px-2 py-1">
                            {{ $records->count() }} Records Found
                        </span>
                    </div>
                </div>

                @if($records->isEmpty())
                    <div class="alert alert-info text-center py-4 my-3" role="alert">
                        <i class="fas fa-info-circle fa-2x mb-2 text-info d-block"></i>
                        No payment transactions found matching the selected filter criteria.
                    </div>
                @else
                    <div class="table-responsive">
                        <table id="order-listing" class="table table-hover table-striped">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th style="width: 40px;">#</th>
                                    <th>Date</th>
                                    <th>Source / Module</th>
                                    <th>Ref / Invoice</th>
                                    <th>Party / Payee</th>
                                    <th>Method</th>
                                    <th>Custom Payment Details</th>
                                    <th class="text-right">Inflow (+)</th>
                                    <th class="text-right">Outflow (-)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="font-weight-bold text-dark">{{ $row['date'] }}</span>
                                        @if(!empty($row['time']))
                                            <small class="text-muted d-block">{{ $row['time'] }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $row['source_badge'] }}">
                                            {{ $row['source_label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-primary">{{ $row['reference'] }}</span>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ $row['party'] }}</div>
                                        @if(!empty($row['party_phone']))
                                            <small class="text-muted"><i class="fas fa-phone mr-1"></i>{{ $row['party_phone'] }}</small>
                                        @endif
                                        @if(!empty($row['description']) && $row['description'] !== $row['party'])
                                            <small class="text-muted d-block text-truncate" style="max-width: 220px;" title="{{ $row['description'] }}">
                                                {{ $row['description'] }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-light border font-weight-bold text-dark">
                                            {{ $row['payment_method'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(!empty($row['payment_details']) && is_array($row['payment_details']))
                                            @foreach($row['payment_details'] as $k => $v)
                                                @if($v !== null && $v !== '')
                                                    <span class="custom-detail-chip">
                                                        <strong>{{ ucwords(str_replace('_', ' ', $k)) }}:</strong> {{ $v }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-muted font-italic small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($row['inflow_amount'] > 0)
                                            <span class="text-success font-weight-bold">
                                                +{{ currency_symbol() }}{{ number_format($row['inflow_amount'], 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($row['outflow_amount'] > 0)
                                            <span class="text-danger font-weight-bold">
                                                -{{ currency_symbol() }}{{ number_format($row['outflow_amount'], 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="7" class="text-right">Totals:</td>
                                    <td class="text-right text-success">+{{ currency_symbol() }}{{ number_format($totalInflow, 2) }}</td>
                                    <td class="text-right text-danger">-{{ currency_symbol() }}{{ number_format($totalOutflow, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-right font-weight-bold">Net Cash Flow:</td>
                                    <td colspan="2" class="text-right font-weight-bold {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }}">
                                        {{ currency_symbol() }}{{ number_format($netBalance, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
