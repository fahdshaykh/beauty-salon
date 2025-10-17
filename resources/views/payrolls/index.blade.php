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



            @php
                $defaultMonth = now()->subMonth()->month;
                $defaultYear = now()->year;
            @endphp

            <form method="GET" action="{{ route('payrolls.index') }}" class="form-inline mb-3">
                <div class="form-group mr-2">
                    <label for="month" class="mr-2">Select Month:</label>
                    <select name="month" id="month" class="form-control">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}"
                                {{ request('month', $defaultMonth) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-2">
                    <label for="year" class="mr-2">Select Year:</label>
                    <select name="year" id="year" class="form-control">
                        @foreach(range(now()->year, now()->year - 5) as $y)
                            <option value="{{ $y }}"
                                {{ request('year', $defaultYear) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Search</button>
            </form>







                        <div class="row">



                            <div class="col-md-3">



                            <h4 class="card-title">User Payrolls</h4>



                            </div>



                        <div class="col-md-6"></div>



                        <div class="col-md-3">



                            <!-- <div class="text-center" style="float: center;">



                                @if(in_array($user_add, session()->get('all_rp')['0'])) 



                                <a href="{{url('user/user-create')}}" class="btn btn-primary btn-sm">Add New User</a>



                                @endif



                            </div> -->



                        </div>



                        </div>



                <div class="row">



                    <div class="col-12">



                        <div class="table-responsive">



                            <table class="table">



                                <thead>



                                    <tr>



                                        <th>Sr.No #</th>


                                        <th>User Name</th>

                                        <th>User Email</th>

                                        <th>User phone</th>

                                        <th>Month name</th>



                                        <th>Actions</th>



                                    </tr>



                                </thead>



                                <tbody>



                                    <?php $num = 1; ?>



                                    @foreach($users as $user)

                                    



                                    @if($user->role_id !== 1)



                                    <tr>



                                        <td>{{$num}}</td>



                                        <td>{{$user->name}}</td>

                                        <td>{{$user->email}}</td>

                                        <td>{{$user->phone}}</td>

                                        <td>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</td>

                                        



                                        <td>



                                        <a href="{{ url('user/monthly/payslip', $user->id) }}?month={{ request('month') }}&year={{ request('year') }}">



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