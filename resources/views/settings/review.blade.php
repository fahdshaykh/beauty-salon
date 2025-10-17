@extends('layouts.admin')



@section('title', 'Review Settings | '.Session::get('site_name'))



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

                Review

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('review-settings')}}">Review Setting</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Review Url</li>

                </ol>

            </nav>

        </div>



        <div class="alert alert-success" style="display:none;" id="status-success"></div>



        <div class="card">

            <div class="card-body">

                    <div class="row">

                            <div class="col-md-3">

                            <h4 class="card-title"></h4>

                            </div>

                        <div class="col-md-6"></div>

                        <div class="col-md-3">

                            <div class="text-center">

                                

                            </div>

                        </div>

                    </div>

                <div class="row">

                    <div class="col-12 mt-3">

                        <div class="table-responsive">

                            <table id="order-listing" class="table">

                                <thead>

                                    <tr>

                                        <!-- <th>API Key</th> -->

                                        <th>Review URL</th>

                                        <th>Status</th>

                                        <th>Action</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach($review as $data)

                                    

                                    <tr>

                                        <!-- <td style="width:50%">$data->api_key</td> -->

                                        <td>{{$data->url}}</td>

                                        <td>

                                            <label class="switch">

                                                <input type="checkbox" id="user-{{$data->id}}"

                                                    onclick="changeReviewStatus(event.target, {{ $data->id }});"

                                                    {{($data->is_active) ? 'checked' : ''}}>

                                                <span class="slider round"></span>

                                            </label>

                                        </td>

                                        <td>

                                            <a href="{{url('review-settings/edit')}}">

                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>

                                            </a>

                                        </td>

                                        

                                    </tr>

                                    

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

    function changeReviewStatus(_this, id) {



        var status = $(_this).prop('checked') == true ? 1 : 0;

        $.ajax({

            url: "{{url('review-status')}}",

            type: 'GET',

            data: {

                id: id,

                is_active: status

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