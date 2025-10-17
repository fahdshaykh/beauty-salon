@section('pagescript')



<script src="{{asset('js/calendar.js')}}"></script>



<script src="{{asset('js/select2.js')}}"></script>

<script src="{{asset('js/js-grid.js')}}"></script>

<script src="{{asset('js/formpickers.js')}}"></script>



@stop

@extends('layouts.admin')



@section('title', 'Dashboard | bSaloon')







@section('content')



<link rel="stylesheet" href="<?php echo asset('css/jobmodal.css')?>" type="text/css"> 

<style>

.popper,

.tooltipevent {

  position: absolute;

  z-index: 9999;

  background: black;

  color: white;

  width: 250px;

  border-radius: 3px;

  box-shadow: 0 0 2px rgba(0,0,0,0.5);

  padding: 10px;

  text-align: center;

}

.style5 .tooltipevent {

  background: #1E252B;

  color: #FFFFFF;

  max-width: 200px;

  width: auto;

  font-size: .8rem;

  padding: .5em 1em;

}

.fc-event{

    cursor: pointer;

}

.holiday {
    background: lightgray;
    pointer-events: none;
}

      

</style>

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Dashboard

            </h3>

              

          @if(Session::has('success'))



<div class="alert alert-success alert-dismissible">



    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



    {{Session::get('success')}}



</div>

@endif

     

        </div>

        @if(auth()->user()->company_id == 1)

        <div class="row">

            <div class="col-md-6 grid-margin">

                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title mb-0">Active Company</h4>

                        <div class="d-flex justify-content-between align-items-center">

                            <div class="d-inline-block pt-3">

                                <div class="d-md-flex">

                                    <h2 class="mb-0">{{$active_company}}</h2>

                                </div>

                            </div>

                            <div class="d-inline-block">

                                <i class="fa fa-user-plus text-info icon-lg"></i>                                    

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-6 grid-margin">

                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title mb-0">In-Active Company</h4>

                        <div class="d-flex justify-content-between align-items-center">

                            <div class="d-inline-block pt-3">

                                <div class="d-md-flex">                                    

                                    <h2 class="mb-0">{{$inactive_company}}</h2>

                                </div>

                            </div>

                            <div class="d-inline-block">

                                <i class="fa fa-user-times text-danger icon-lg"></i>                                    

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        @else



        <div class="row">



            <div class="col-md-12">



                @if(auth()->user()->role_id == 1)



                <!-- <div class="form-group col-md-3">



                    <select id="user_id" name="user_id" class="form-control js-example-basic-single w-100"



                        style="width:100%">



                        <option value="{{auth()->user()->company_id}}">All Bookings</option>



                        @foreach($users as $customer)



                        <option value="{{$customer->id}}">{{$customer->name}}</option>



                        @endforeach



                    </select>



                </div> -->



                @endif



                <div class="card">

                    <?php $permvalue = 'no'; 

                    if(in_array('4,2', session()->get('all_rp')['0']))

                    $permvalue = 'yes'

                    ?>

                    <div class="card-body">

                    <input type="hidden" id="creat-booking-perm" value="{{$permvalue}}">

                        <h4 class="card-title">Booking Calender</h4>



                        <div id="calendar" class="full-calendar"></div>



                    </div>



                </div>



            </div>



        </div>



        @endif



    </div>



    <!-- content-wrapper ends -->



</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

                    

</div>

<!-- main-panel ends -->



@endsection