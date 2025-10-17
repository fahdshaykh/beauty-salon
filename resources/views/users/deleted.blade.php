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

        <div class="card">

            <div class="card-body">

                        <div class="row">

                            <div class="col-md-3">

                            <h4 class="card-title">User Listing</h4>

                            </div>
                            <div class="col-md-6"></div>

                        </div>

                <div class="row">

                    <div class="col-12">

                        <div class="table-responsive">

                            <table id="order-listing" class="table">

                                <thead>

                                    <tr>

                                        <th>Sr.No #</th>

                                        <th>Role</th>

                                        <th>User Name</th>
                                        <th>User Email</th>
                                        <th>User phone</th>
                                        <th>User address</th>
                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    @foreach($users as $user)


                                    <tr>

                                        <td>{{$num}}</td>

                                        <td>{{$user->role->role}}</td>

                                        <td>{{$user->name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->phone}}</td>
                                        <td>{{$user->address}}</td>

                                        <td>
                                            <a href="{{url('user/undelete',$user->id)}}">

                                                Undo

                                            </a>
                                            |<a onclick="return confirm('Are you sure to delete');" href="{{url('user/delete_permanent',$user->id)}}">

                                                Destroy

                                            </a>
                                        </td>

                                    </tr>

                                    <?php $num++; ?>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

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