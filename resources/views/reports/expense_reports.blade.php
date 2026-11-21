@extends('layouts.admin')

@section('title', 'Expense Report Records | ' . (auth()->user()->companySetting->site_name ?? ''))

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Reports
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('expense-reports') }}">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Expense Report</li>
                </ol>
            </nav>
        </div>

        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ Session::get('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title mb-0">Expense Report</h4>
                    </div>
                    <div class="col-md-6 text-md-right mt-2 mt-md-0">
                        <a href="{{ url('expense-reports') }}" class="btn btn-light btn-fw btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Filters
                        </a>
                        @php
                            $exportStart  = (!empty($start_date) && $start_date !== 'no_start_date') ? $start_date : 'no_start_date';
                            $exportEnd    = (!empty($end_date) && $end_date !== 'no_end_date') ? $end_date : 'no_end_date';
                            $exportType   = (!empty($type)) ? $type : 'no';
                            $exportMethod = (!empty($method) && $method !== 'no') ? $method : 'no';
                        @endphp
                        <a href="{{ route('expense.export', ['start_date' => $exportStart, 'end_date' => $exportEnd, 'expense_type' => $exportType, 'method' => $exportMethod]) }}" class="btn btn-primary btn-sm ml-2">
                            <i class="fas fa-download"></i> Export Expense Report
                        </a>
                    </div>
                </div>

                <div class="row grid-margin">
                    <div class="col-12">
                        <div class="alert alert-warning mt-2 mb-3 text-center" role="alert">
                            Start Date: <strong>{{ ($start_date == 'no_start_date' || empty($start_date)) ? 'No Date Selected' : $start_date }}</strong>, 
                            End Date: <strong>{{ ($end_date == 'no_end_date' || empty($end_date)) ? 'No Date Selected' : $end_date }}</strong>
                        </div>
                        <div class="text-center mb-4">
                            <h5 class="mb-0">Grand Total: <span class="text-primary font-weight-bold">{{ currency_symbol() }}{{ number_format($expense_report->sum('amount'), 2) }}</span></h5>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="order-listing" class="table">
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th>#</th>
                                        <th>Expense Type</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Payable Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expense_report as $index => $expense)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $expense->type }}</td>
                                        <td>{{ currency_symbol() }}{{ number_format($expense->amount, 2) }}</td>
                                        <td><span class="badge badge-light border">{{ $expense->method }}</span></td>
                                        <td>{{ $expense->payable }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
