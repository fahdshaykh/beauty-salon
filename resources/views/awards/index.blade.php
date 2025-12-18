

@extends('layouts.admin')







@section('title', 'Awards | '. (auth()->user()->companySetting->site_name ?? ''))







@section('content')



<style>



.switch {



    position: relative;



    display: inline-block;



    width: 60px;



    height: 34px;



}







.switch input {



    opacity: 0;



    width: 0;



    height: 0;



}







.slider {



    position: absolute;



    cursor: pointer;



    top: 0;



    left: 0;



    right: 0;



    bottom: 0;



    background-color: #ccc;



    -webkit-transition: .4s;



    transition: .4s;



}







.slider:before {



    position: absolute;



    content: "";



    height: 26px;



    width: 26px;



    left: 4px;



    bottom: 4px;



    background-color: white;



    -webkit-transition: .4s;



    transition: .4s;



}







input:checked+.slider {



    background-color: #392C70;



}







input:focus+.slider {



    box-shadow: 0 0 1px #2196F3;



}







input:checked+.slider:before {



    -webkit-transform: translateX(26px);



    -ms-transform: translateX(26px);



    transform: translateX(26px);



}







/* Rounded sliders */



.slider.round {



    border-radius: 34px;



}







.slider.round:before {



    border-radius: 50%;



}



</style>



<div class="main-panel">



    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                All Awards

            </h3>


            @can('create awards')
                <a href="{{ route('awards.create') }}" class="btn btn-primary">Add New Award</a>
            @endcan


        </div>

        <div class="card">

            <div class="card-body">

                <!-- Filter & Search Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body pt-4">
                        <form method="GET" action="{{ route('awards.index') }}" class="row g-3">
                            <!-- Employee Filter -->
                            <div class="col-md-4 mt-1">
                                <label for="user_id">All Employees</label>
                                <select class="js-example-basic-single w-100" id="user_id" name="user_id">
                                    <option value="">Select Employee</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
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
                                    <a href="{{ route('awards.index') }}" class="btn btn-outline-warning btn-icon-text w-100 ml-1">
                                        <i class="fas fa-redo btn-icon-prepend"></i>                                                    
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Pagination Dropdown -->
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



                            <table class="table">



                                <thead>
                                  <tr class="bg-primary text-white">
                                    <th>#</th>
                                    <th>Awardee Name</th>
                                    <th>Award</th>
                                    <th>Gift</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                  </tr>
                                </thead>



                                <tbody>
                          
                                @forelse($awards as $award)
                                  <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $award->user->name }}</td>


                                    <td>{{ $award->award }}</td>
                                    <td>{{ $award->gift }}</td>
                                    <td>{{ $award->date }}</td>
                                    
                                    <td>
                                        <div class="d-flex gap-1">
                                            @can('edit awards')
                                            <a href="{{ route('awards.edit', $award->id) }}"
                                            class="btn btn-sm btn-outline-primary" title="Edit Award">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            @endcan

                                            @can('delete awards')
                                            <a href="javascript:void(0)"
                                            onclick="if(confirm('Are You Sure Want To Delete?'))
                                                        document.getElementById('delete-award-{{ $award->id }}').submit();"
                                            class="btn btn-sm btn-outline-danger ml-1" title="Delete Award">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>

                                            <form id="delete-award-{{ $award->id }}"
                                                action="{{ route('awards.destroy', $award->id) }}"
                                                method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                  </tr>
                                
                                @empty

                                <tr style="text-align: right">
                                    <td colspan='3'> No Data Available</td>
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
                        <p>Showing {{ $awards->firstItem() }} to {{ $awards->lastItem() }} of {{ $awards->total() }} entries</p>
                        {{ $awards->links() }}
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
            window.location = "{{ url('/awards') }}?rows=" + this.value;
        });
    }
});
</script>