@section('pagescript')

<link rel="stylesheet" href="{{asset('vendors/summernote/dist/summernote-bs4.css')}}">

<script src="{{asset('vendors/summernote/dist/summernote-bs4.min.js')}}"></script>

<script src="{{asset('js/editorDemo.js')}}"></script>

<script src="{{asset('js/formpickers.js')}}"></script>

<script src="{{asset('js/form-addons.js')}}"></script>

<script src="{{asset('js/file-upload.js')}}"></script>

@stop



@extends('layouts.admin')



@section('title', 'E-Mail History | '.Session::get('site_name'))



@section('content')

<style>

  .hide

{

 display:none;

}

.loader{

    left:0;

    right:0;

    z-index: 999;

    position: fixed;

    border:none;

    width:100%;

    top:200px;

    

}



</style>

<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Email Marketing History

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item active" aria-current="page">Email Marketing History</li>

                </ol>

            </nav>

        </div>

        @if ($errors->any())

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            <ul>

                @foreach ($errors->all() as $error)

                <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

        @endif



        @if(Session::has('success'))

        <div class="alert alert-success alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('success')}}

        </div>

        @endif



        <div class="row">

            <div class="col-12 grid-margin">

                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title">Email Marketing History </h4>

                        <div class="row">

                        <form class="form-inline" method="post" action="{{url('email-history-filter')}}">

                        @csrf

                            <div class="col-lg-3">

                            <div class="input-group mb-2 mr-sm-2">

                                    <div id="datepicker-popup-new" class="input-group date datepicker">

                                        <input type="text" name="start_date" value="@if($start_date!='') {{$start_date}} @endif" placeholder=" Start Date" class="form-control"/>

                                        <span class="input-group-addon input-group-append border-left">

                                            <span class="far fa-calendar input-group-text"></span>

                                        </span>

                                    </div>

                                </div>

                            </div>

                            <div class="col-lg-3">

                            <div class="input-group mb-2 mr-sm-2">

                                    <div id="datepicker-popup-new2" class="input-group date datepicker">

                                        <input type="text" name="end_date" value="@if($start_date!='') {{$end_date}} @endif"placeholder="End Date" class="form-control"/>

                                        <span class="input-group-addon input-group-append border-left">

                                            <span class="far fa-calendar input-group-text"></span>

                                        </span>

                                    </div>

                                </div>

                            </div>

                         

                            <div class="col-lg-2">

                                <div class="form-group">

                                    <select name="type" class="form-control js-example-basic-multiple w-100">

                                    <option value="all" @if($type=='all') selected @endif>All</option>

                                        <option value="sent" @if($type=='sent') selected @endif>Sent</option>

                                        <!-- <option value="schedule" @if($type=='schedule') selected @endif>Scheduled</option> -->

                                    </select>

                                </div>

                            </div>

                            <div class="col-lg-4">

                                <div class="form-group">

                                    <button type="submit" value="submit" class="btn btn-primary mb-2">Search</button>

                                </div>

                            </div>

                        </form>  

                        </div>

                        <br>

                        <!-- <div   class="form-check form-check-flat form-check-primary mt-2 mr-5" style="float: left;">

                                <label class="form-check-label">

                                <input id="select-all" type="checkbox" class="form-check-input" name="customer-check">

                                    Scheduled

                                <i class="input-helper"></i></label>

                             </div> -->

                        <table id="customer-listing" class="table table-bordered  table-hover">             

                                <thead>

                                    <tr class="bg-primary text-white">

                                      

                                        <th>Email#</th>

                                        <th>Template</th>

                                        <th>Date</th>

                                        <th>Time </th>

                                        <th>Type</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @if($history)

                                        @foreach($history as $data)

                                        

                                        <tr>    

                                            <td>{{$data->email}}</td>

                                            <td>@if($data->template==0) Created @else {{$data->template}} @endif</td>

                                            <td>{{$data->date}}</td>

                                            <td>{{$data->time}}</td>

                                            <td>{{$data->type}}</td>

                                    

                                        </tr>

                                        @endforeach

                                    @else

                                    <tr><td>No data found</td></tr>

                                    @endif

                                </tbody>

                            </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



<script>

$(document).ready(function() {

    $("#message").click(function() {

        if ($('.custom-check').is(":checked"))

        {

            var customers = [];

            $('#message-modal').modal('toggle');

            $("input:checkbox[name=customer-checkbox]:checked").each(function(){

                customers.push($(this).val());

            });

            $("#customers_emails").val(customers);

        }

        else{

            alert("Please Select Customer.");

        }

    });



    $("#select-all").click(function() {

        var checkBoxes = $('.custom-check');

        checkBoxes.prop("checked", !checkBoxes.prop("checked"));

    });



    $("#check_schedule").click(function() {

      if ($('#check_schedule').is(":checked"))

      {

        $('#schedule_time_div').removeClass("hide");

        $('#schedule_t').prop('required',true);

        $('#schedule_d').prop('required',true);

      }

      else{

        $('#schedule_time_div').addClass("hide");

        $('#schedule_t').prop('required',false);

        $('#schedule_d').prop('required',false);

      }

  });

});

</script>

@endsection

