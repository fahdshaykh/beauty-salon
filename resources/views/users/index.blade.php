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



                All Users



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{route('users.index')}}">Users</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Users Listing</li>



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



        @if(session('error'))

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{ session('error') }}

        </div>

        @endif



        <?php $user_add_array = array(6,2); ?>



        <?php $user_add = implode(',', $user_add_array); ?>



        <?php $user_edit_array = array(6,3); ?>



        <?php $user_edit = implode(',', $user_edit_array); ?>



        <?php $user_delete_array = array(6,4); ?>



        <?php $user_delete = implode(',', $user_delete_array); ?>











        <div class="card">



            <div class="card-body">



                        <div class="row">



                            <div class="col-md-3">



                            <h4 class="card-title">User Listing</h4>



                            </div>



                        <div class="col-md-6"></div>



                        <div class="col-md-3">



                            <div class="text-center" style="float: center;">



                                @if(in_array($user_add, session()->get('all_rp')['0'])) 



                                <a href="{{url('user/user-create')}}" class="btn btn-primary btn-sm">Add New User</a>



                                @endif



                            </div>



                        </div>



                        </div>


                    <!-- Pagination Dropdown -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="pagination-dropdown">
                            <label for="per_page">Items per page</label>
                            <form method="GET" action="{{ route('users.index') }}">
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



                            <table class="table">



                                <thead>



                                    <tr>



                                        <th>Sr.No #</th>



                                        <th>Role</th>



                                        <th>User Name</th>

                                        <th>User Email</th>

                                        <th>User phone</th>

                                        <th>User address</th>

                                        <th>Status</th>



                                        <th>Actions</th>



                                    </tr>



                                </thead>



                                <tbody>



                                    <?php $num = 1; ?>



                                    @foreach($users as $user)



                                    @if($user->role_id !== 1)



                                    <tr>



                                        <td>{{$num}}</td>



                                        <td>{{$user->role->role}}</td>



                                        <td> {{$user->name}} </td>

                                        <td>{{$user->email}}</td>

                                        <td>{{$user->phone}}</td>

                                        <td>{{$user->address}}</td>

                                        <td>







                                            <label class="switch">



                                                <input type="checkbox" id="user-{{$user->id}}"



                                                    onclick="changeUserStatus(event.target, {{ $user->id }});"



                                                    {{($user->is_active) ? 'checked' : ''}}>



                                                <span class="slider round"></span>



                                            </label>



                                        </td>



                                        <td>



                                        @if(in_array($user_edit, session()->get('all_rp')['0']))



                                            <a href="{{url('user/edit',$user->id)}}">



                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>



                                            </a>



                                        @endif 



                                        @if(in_array($user_delete, session()->get('all_rp')['0']))



                                            |<a onclick="return confirm('Are you sure to delete');" href="{{url('user/destroy',$user->id)}}">



                                                <i class="fas fa-trash text-danger"></i>



                                            </a>



                                        @endif

                                        |<a href="{{url('user/payslip',$user->id)}}">



                                        <i class='fas fa-file-pdf ml-2' style='font-size:14px'></i>



                                        </a>



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
                        <p>Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries</p>
                        {{ $users->links() }}
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