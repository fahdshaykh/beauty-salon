@extends('admin.layouts.admin')

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

            <div class="col-md-6 grid-margin">

                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title mb-0">Active Company</h4>

                        <div class="d-flex justify-content-between align-items-center">

                            <div class="d-inline-block pt-3">

                                <div class="d-md-flex">

                                    <h2 class="mb-0">{{ 2 }}</h2>

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

                                    <h2 class="mb-0">{{ 1 }}</h2>

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



    </div>



    <!-- content-wrapper ends -->



</div>




@endsection