

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


        <div class="card">



            <div class="card-body">



                <!-- Pagination Dropdown -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="pagination-dropdown">
                            <label for="per_page">Items per page</label>
                            <form method="GET" action="{{ route('awards.index') }}">
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
                          
                                @foreach($awards as $award)
                                  <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $award->user->name }}</td>


                                    <td>{{ $award->award }}</td>
                                    <td>{{ $award->gift }}</td>
                                    <td>{{ $award->date }}</td>
                                    
                                    <td>
                                        <div class="btn-group align-top">

                                        

                                            @can('edit awards')
                                            <a href="{{route('awards.edit',$award->id)}}" class="btn btn-default">
                                                    <i class="fas fa-pen" style="font-size:15px;"></i>
                                            </a>
                                            @endcan

                                            @can('delete awards')
                                            <form method="post" action="{{ route('awards.destroy', $award->id) }}" onsubmit="return confirm('Are You Sure Want To Delete?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-trash-alt" style="font-size:15px; color:red;"></i>
                                                </button>
                                            </form>
                                            @endcan

                                        

                                        </div>
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
                        <p>Showing {{ $awards->firstItem() }} to {{ $awards->lastItem() }} of {{ $awards->total() }} entries</p>
                        {{ $awards->links() }}
                    </div>
                </div>


            </div>



        </div>



    </div>



    @endsection







<script>



function changeUserStatus(_this, id) {







    var status = $(_this).prop('checked') == true ? 1 : 0;



    $.ajax({



        url: "{{url('user-status')}}",



        type: 'GET',



        data: {



            id: id,



            status: status



        },



        success: function(result) {



            $("#status-success").show();



            $("#status-success").html(result.success);



            setTimeout(function() {



                $("#status-success").hide()



            }, 3000);



        }



    });



}



</script>