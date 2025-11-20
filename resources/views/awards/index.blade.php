

@extends('layouts.admin')







@section('title', 'Users | '.Session::get('site_name'))







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



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{route('awards.index')}}"> Awards </a></li>



                    <li class="breadcrumb-item active" aria-current="page">Awards Listing</li>



                </ol>



            </nav>



        </div>







        <div class="alert alert-success" style="display:none;" id="status-success"></div>


        <div class="card">



            <div class="card-body">

                <div class="row">



                    <div class="col-md-3">



                    <h4 class="card-title">Supplier Listing</h4>



                    </div>



                    <div class="col-md-6"></div>



                    <div class="col-md-3">



                        <div class="text-center" style="float: center;">



                        @can('create awards')
                            <a href="{{ route('awards.create') }}" class="btn btn-primary btn-sm">Add Award</a>
                        @endcan


                        </div>



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