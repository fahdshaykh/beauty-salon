@extends('layouts.admin')

@section('title', 'Supplier Balance Summary | ' . Session::get('site_name'))

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Supplier Summary</h3>
            <a href="{{ route('supplier-payments.edit', $supplier->id) }}" class="btn btn-primary btn-sm">Add Payment</a>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Employee Summary</li>
                </ol>
            </nav>
        </div>

        <!-- Supplier Info -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Employee Details</h4>

                <h2>Paystub for {{ $payroll->user->name }}</h2>
                <p>Pay Period: {{ $payroll->pay_period_start }} - {{ $payroll->pay_period_end }}</p>
                <table>
                    <tr><td>Base Salary</td><td>{{ $payroll->base_salary }}</td></tr>
                    <tr><td>Commission</td><td>{{ $payroll->commission }}</td></tr>
                    <tr><td>Overtime</td><td>{{ $payroll->overtime_payment }}</td></tr>
                    <tr><td>Deductions</td><td>{{ $payroll->deductions }}</td></tr>
                    <tr><td><strong>Net Pay</strong></td><td><strong>{{ $payroll->net_pay }}</strong></td></tr>
                </table>
                
            </div>
        </div>

    </div>
</div>
@endsection
