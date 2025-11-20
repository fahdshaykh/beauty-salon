@extends('layouts.admin')



@section('title', 'Expenses | '.Session::get('site_name'))



@section('content')



<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                All Expenses

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('expenses')}}">Expenses</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Expenses Listing</li>

                </ol>

            </nav>

        </div>



        <div class="card">

            <div class="card-body">

                <h4 class="card-title"></h4>

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