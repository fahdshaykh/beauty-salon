@extends('admin.layouts.admin')



@section('title', 'Companies | Company Management')



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

                Companies

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{route('admin.companies.index')}}">Companies</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Company Listing</li>

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

                <h4 class="card-title"></h4>

                <div class="row">

                    <div class="col-12">

                        <div class="table-responsive"> <!-- style="overflow: hidden !important;" -->

                            <table id="order-listing" class="table">

                                <thead>

                                    <tr>

                                        <th>Company Name</th>

                                        <th>Owner Name</th>

                                        <th>Admin User</th>

                                        <th>Company Phone</th>

                                        <!-- <th>Plan price</th> -->

                                        <!-- <th>SMS Count</th> -->

                                        <!-- <th>WhatsApp Count</th> -->

                                        <!-- <th>End Date</th> -->

                                        <th>Status</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    @foreach($companies as $company)

                                    <tr>

                                        <td>{{$company->company_name}}</td>

                                        <td>{{$company->owner_name}}</td>

                                        <td style="max-width:150px;">{{$company->email}}</td>

                                        <td>{{$company->company_phone}}</td>

                                        <!-- <td>{{$company->email}}</td> -->

                                        

                                        <!-- <td><i class="fas fa-dollar-sign"></i>{{$company->plan_price}}</td> -->

                                        <!-- <td>{{$company->sms_count}}</td> -->

                                        <!-- <td>{{date('m-d-Y', strtotime($company->plan_end_date))}}</td> -->

                                        <td>

                                            <label class="switch">

                                                <input type="checkbox" id="user-{{$company->id}}"

                                                    onclick="changeCompanyStatus(event.target, {{ $company->id }});"

                                                    {{($company->is_active) ? 'checked' : ''}}>

                                                <span class="slider round"></span>

                                            </label>

                                        </td>

                                        <td>

                                            <a href="{{route('admin.companies.edit',$company->id)}}">

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

    function changeCompanyStatus(_this, id) {



        var status = $(_this).prop('checked') == true ? 1 : 0;

        $.ajax({

            url: "{{url('company-status')}}",

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