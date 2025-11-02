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

                    <li class="breadcrumb-item"><a href="{{url('expenses/index')}}">Expenses</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Expenses Listing</li>

                </ol>

            </nav>

        </div>



        <div class="alert alert-success" style="display:none;" id="status-success"></div>



        @if(Session::has('success'))

        <div class="alert alert-success alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('success')}}

        </div>



        @elseif(Session::has('destroy'))

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('destroy')}}

        </div>

        @endif



        @if(Session::has('update'))

        <div class="alert alert-success alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('update')}}

        </div>

        @endif


        <div style="background: #333; color: white; padding: 10px; font-family: monospace;">
    DEBUG:
    User ID: {{ auth()->id() }} |
    Company ID: {{ auth()->user()->company_id }} |
    Roles: {{ auth()->user()->getRoleNames() }} |
    Permissions: {{ auth()->user()->getAllPermissions()->pluck('name')->implode(', ') }}
</div>

        <div class="card">

            <div class="card-body">

                <h4 class="card-title"></h4>

                
                    <div class="row">
                        <div class="col-md-3">
                            <div class="pagination-dropdown">
                                <label for="per_page">Items per page</label>
                                <form method="GET" action="{{ route('expenses.index') }}">
                                    @foreach(request()->except(['page', 'per_page']) as $key => $value)
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endforeach
                                    <select name="per_page" id="per_page" onchange="this.form.submit()" class="form-control">
                                        @foreach($paginationOptions as $option)
                                            <option value="{{ $option }}" {{ request('per_page', auth()->user()->getPerPagePreference()) == $option ? 'selected' : '' }}>{{ $option }} per page</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>

                <div class="row">

                    <div class="col-12">

                        <div class="table-responsive">

                            <table class="table table-striped">

                                <thead>

                                    <tr>

                                      

                                        <th>Expense Type</th>

                                        <th>Date Paid</th>

                                        <th>Amount</th>

                                        <th>Payment Method</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                   

                                    @foreach($expenses as $expense)

                                   

                                    <tr>

                                      

                                        <td>{{$expense->type}}</td>

                                        <td>{{$expense->payable}}</td>

                                        <td><i class="fas fa-dollar-sign"></i>{{$expense->amount}}</td>

                                        <td>{{$expense->method}}</td>

                                        <td>

                                        
                                            @can('edit expenses')
                                            <a href="{{url('expenses/edit',$expense->id)}}">

                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>

                                            </a>
                                            @endcan
                                            
                                            @can('delete expenses')
                                            |<a onclick="return confirm('Are you sure to delete');" href="{{url('expenses/destroy',$expense->id)}}">

                                            <i class="fas fa-trash text-danger"></i>                          

                                            </a>
                                            @endcan
                                       

                                       

                                        </td>

                                    </tr>

                               

                                    @endforeach

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

