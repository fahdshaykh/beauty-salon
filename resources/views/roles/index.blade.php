@extends('layouts.admin')







@section('title', 'Roles | '.Session::get('site_name'))







@section('content')







<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                All Roles



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{url('/roles')}}">Roles</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Role Listing</li>



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
                                <form method="GET" action="{{ route('roles.index') }}">
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



                            <h4 class="card-title"></h4>



                            </div>



                        <div class="col-md-6"></div>



                        <div class="col-md-3">



                            <div class="text-center">



                                @if(in_array("7,2", session()->get('all_rp')['0']))



                                <a href="{{url('role/role-create')}}" class="btn btn-primary btn-sm">Add New Role</a>



                                @endif



                            </div>



                        </div>



                    </div>



                <div class="row">



                    <div class="col-12 mt-3">



                        <div class="table-responsive">



                            <table class="table">



                                <thead>



                                    <tr>



                                        <th>Sr.No #</th>



                                        <th>Role Name</th>



                                        <th>Created at</th>



                                        <th>Actions</th>



                                    </tr>



                                </thead>



                                <tbody>



                                    <?php $num = 1; ?>



                                    @foreach($role_permissions as $role)



                                    @if($role->role_id !== 1)



                                    <tr>



                                        <td>{{$num}}</td>



                                        <td>{{$role->role->role}}</td>



                                        <td>{{date('m-d-Y', strtotime($role->role->created_at))}}</td>



                                        <td>



                                        @if(in_array("7,3", session()->get('all_rp')['0']))



                                            <a href="{{url('role/edit',$role->role_id)}}">



                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>



                                            </a>



                                        @endif



                                        </td>



                                    </tr>



                                    <?php $num++; ?>



                                    @endif



                                    @endforeach



                                </tbody>



                            </table>



                        </div>



                    </div>



                </div>


                <!-- Pagination Links -->
                <div class="row">
                    <div class="col-12 mt-4">
                        <p>Showing {{ $role_permissions->firstItem() }} to {{ $role_permissions->lastItem() }} of {{ $role_permissions->total() }} entries</p>
                        {{ $role_permissions->links() }}
                    </div>
                </div>


            </div>



        </div>



    </div>



    @endsection







    <script>



    function changeServiceStatus(_this, id) {







        var status = $(_this).prop('checked') == true ? 1 : 0;



        $.ajax({



            url: "{{url('servicestatus')}}",



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