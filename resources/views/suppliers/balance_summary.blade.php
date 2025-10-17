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
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Balance Summary</li>
                </ol>
            </nav>
        </div>

        <!-- Supplier Info -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Supplier Details</h4>

                <div class="row">
                    <div class="col-md-4"><strong>Total Purchases:</strong> {{ number_format($totalPurchases, 2) }}</div>
                    <div class="col-md-4"><strong>Total Paid:</strong> {{ number_format($totalPaid, 2) }}</div>
                    <div class="col-md-4"><strong>Total Pending:</strong> {{ number_format($totalPending, 2) }}</div>
                </div>
                
            </div>
        </div>

        <!-- other payments  -->
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">Payment History</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Amount Paid</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($supplier->payments as $index => $purchase)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                                    <td>{{ number_format($purchase->amount, 2) }}</td>
                                    <td>{{ $purchase->note ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No purchase records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- payment history -->

        <!-- Purchase History -->
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">Purchase History</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Inventory</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Amount Paid</th>
                                <th>Pending</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($supplier->purchases as $index => $purchase)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $purchase->inventory->name ?? 'N/A' }}</td>
                                    <td>{{ $purchase->quantity }}</td>
                                    <td>{{ number_format($purchase->price, 2) }}</td>
                                    <td>{{ number_format($purchase->amount_paid, 2) }}</td>
                                    <td>{{ number_format($purchase->amount_pending, 2) }}</td>
                                    <td>{{ $purchase->description ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No purchase records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
