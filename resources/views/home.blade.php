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


        <div class="row">



            <div class="col-md-12">



            @role('admin')
                <div class="form-group col-md-3">



                    <select id="user_id" name="user_id" class="form-control js-example-basic-single w-100"



                        style="width:100%">



                        <option value="0">All Bookings</option>



                        @foreach($users as $user)



                        <option value="{{$user->id}}">{{$user->name}}</option>



                        @endforeach



                    </select>



                </div>
            @endrole



                <div class="card">


                    <?php 
                        $permvalue = 'no'; 
                    ?>

                    @can('calendar create booking')
                    <?php 
                        $permvalue = 'yes'; 
                    ?>
                    @endcan

                    @can('view calendar')
                    <div class="card-body">

                    <input type="hidden" id="creat-booking-perm" value="{{$permvalue}}">

                        <h4 class="card-title">Booking Calender</h4>



                        <div id="calendar" class="full-calendar"></div>



                    </div>
                    @endcan



                </div>



            </div>



        </div>



    </div>



    <!-- content-wrapper ends -->



</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

                    

</div>

<!-- main-panel ends -->



@endsection