@extends('layouts.admin')



@section('title', 'Expenses | '.Session::get('site_name'))



@section('content')



<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                All Expenses

            </h3>

            @can('create expenses')
                <a href="{{ route('expenses.create') }}" class="btn btn-primary">New Expense</a>
            @endcan

        </div>



        <div class="card">

            <div class="card-body">

                <!-- Filter & Search Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body pt-4">
                        <form method="GET" action="{{ route('expenses.index') }}" class="row g-3">
                            <!-- Employee Filter -->
                            <div class="col-md-4 mt-1">
                                <label for="name">Expense Types</label>
                                <select class="js-example-basic-single w-100" id="name" name="expense_type_id">
                                    <option value="">All Expense Types</option>
                                    @foreach($expense_types as $expense_type)
                                    <option value="{{ $expense_type->id }}" {{ request('expense_type_id') == $expense_type->id ? 'selected' : '' }}>
                                        {{ $expense_type->type }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <div id="datepicker-popup-new" class="input-group date datepicker">
                                    <input type="text" name="start_date" id="start-date" class="form-control"
                                        value="{{ request('start_date') }}">
                                    <span class="input-group-addon input-group-append border-left">
                                        <span class="far fa-calendar input-group-text"></span>
                                    </span>
                                </div>
                            </div>
                            <!-- End Date -->
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <div id="datepicker-popup-new2" class="input-group date datepicker">
                                    <input type="text" name="end_date" id="end-date" class="form-control"
                                        value="{{ request('end_date') }}">
                                    <span class="input-group-addon input-group-append border-left">
                                        <span class="far fa-calendar input-group-text"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-4 mt-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary w-100 ml-1">Clear</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- paginations per page  -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="pagination-dropdown">
                                <form class="form-inline">
                                    <div class="form-group">
                                        <label for="rows">Records:&nbsp;</label>
                                        <select class="form-control" id="rows" name="rows">
                                            <option value="5" @if($rows == 5) selected @endif > 5 </option>
                                            <option value="10" @if($rows == 10) selected @endif > 10 </option>
                                            <option value="20" @if($rows == 20) selected @endif > 20 </option>
                                            <option value="50" @if($rows == 50) selected @endif > 50 </option>
                                            <option value="100" @if($rows == 100) selected @endif > 100 </option>
                                            <option value="500" @if($rows == 500) selected @endif > 500 </option>                        
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                <div class="row">

                    <div class="col-12">

                        <div class="table-responsive">

                            <table class="table table-striped">

                                <thead>

                                    <tr class="bg-primary text-white">

                                        <th>Expense Type</th>

                                        <th>Date Paid</th>

                                        <th>Amount</th>

                                        <th>Payment Method</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                   

                                    @forelse($expenses as $expense)

                                   

                                    <tr>

                                      

                                        <td>{{$expense->type}}</td>

                                        <td>{{$expense->payable}}</td>

                                        <td><i class="fas fa-dollar-sign"></i>{{$expense->amount}}</td>

                                        <td>{{$expense->method}}</td>


                                        <td>
                                            <div class="d-flex gap-1">
                                                @can('edit expenses')
                                                <a href="{{ route('expenses.edit', $expense->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @endcan


                                                @can('delete expenses')
                                                <a href="javascript:void(0)"
                                                onclick="if(confirm('Are You Sure Want To Delete?')) 
                                                            document.getElementById('delete-cat-{{ $expense->id }}').submit();"
                                                class="btn btn-sm btn-outline-danger ml-1" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>

                                                <form id="delete-cat-{{ $expense->id }}"
                                                    action="{{ route('expenses.destroy', $expense->id) }}"
                                                    method="POST" style="display:none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                                @endcan

                                            </div>
                                        </td>

                                    </tr>

                               

                                    @empty

                                    <tr style="text-align: center">
                                        <td colspan='5'> No Data Available</td>
                                    </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <!-- Pagination Links -->
                <div class="row">
                    <div class="col-12 mt-4">
                        <p>Showing {{ $expenses->firstItem() }} to {{ $expenses->lastItem() }} of {{ $expenses->total() }} entries</p>
                        {{ $expenses->links() }}
                    </div>
                </div>

            </div>

        </div>

    </div>

    @endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rowsSelect = document.getElementById('rows');

    if (rowsSelect) {
        rowsSelect.addEventListener('change', function () {
            window.location = "{{ url('/expenses') }}?rows=" + this.value;
        });
    }
});
</script>