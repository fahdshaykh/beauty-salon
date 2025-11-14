@section('pagescript')

    <script src="{{asset('js/select2.js')}}"></script>

    <script src="{{asset('js/formpickers.js')}}"></script>

    <script src="{{asset('js/form-repeater.js')}}"></script>

@stopbSaloon



@extends('admin.layouts.admin')



@section('title', 'Update Company | DiaryMaster ')



@section('content')



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Edit Company

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('companies')}}">Companies</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Edit Company</li>

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

                        <h4 class="card-title">Update Company Information</h4>

                        <form class="forms-sample" action="{{route('admin.companies.update',$company_detail['0']->company_id)}}" method="post">

                            @csrf

                            @method('PATCH')

                            <div class="form-group">

                                <label for="exampleInputUsername1">Company Name</label>

                                <input type="text" name="company_name" value="{{$company_detail['0']->company_name}}" class="form-control" placeholder="Enter Company Name" required/>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputUsername1">Smart Name(11 characters max)</label>

                                <input type="text" name="smart_name" value="{{$company_detail['0']->smart_name}}" class="form-control" placeholder="This name will be used in SMS. Max=11 & Min =2 characters allowed" minlength="2" maxlength="11" required/>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputUsername1">Owner Name</label>

                                <input type="text" name="owner_name" value="{{$company_detail['0']->owner_name}}" class="form-control" placeholder="Enter Owner Name" required/>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputUsername1">Company Phone</label>

                                <input type="text" name="company_phone" value="{{$company_detail['0']->company_phone}}" class="form-control" placeholder="Enter Company Phone" required/>

                            </div>



                            <div class="form-group">

                                <label for="exampleInputUsername1">Company E-mail</label>

                                

                                <input type="email" name="email" value="{{$company_detail['0']->useremail}}" class="form-control" placeholder="Enter Company Phone" required/>

                            </div>



                            <div class="form-group">

                                <label for="exampleInputUsername1">Company Password</label>

                                <input type="password" id="newPassword" name="password" value="" onkeyup="checkPasswordMatch()" class="form-control" placeholder="Enter Password" />

                            </div>

                            <div class="form-group">

                                <label for="exampleInputUsername1">Confirm Password</label>

                                <input type="password" id="confirmPassword" value="" onkeyup="checkPasswordMatch()" class="form-control" placeholder="Confirm Password" />

                                <span id="CheckPasswordMatch" class="text-warning"></span>

                            </div>





                            <!-- <div class="form-group">

                                <label for="exampleInputUsername1">Plan Name</label>

                                <input type="text" name="plan_name" value="{{$company_detail['0']->plan_name}}" class="form-control" placeholder="Enter Plan Name" required/>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputUsername1">Plan Price</label>

                                <input type="text" name="plan_price" value="{{$company_detail['0']->plan_price}}" class="form-control" placeholder="Enter Plan Price" required/>

                            </div>





                            <div class="form-group">

                            <label>Plan End Date</label>

                            <div id="datepicker-popup-booking" class="input-group date datepicker">

                                <input type="text" name="plan_end_date" value="{{$company_detail['0']->plan_end_date}}" class="form-control" required>

                                <span class="input-group-addon input-group-append border-left">

                                <span class="far fa-calendar input-group-text"></span>

                                </span>

                            </div>

                            </div> -->



                            <div class="form-group">

                                <label for="exampleTextarea1">Company Address</label>

                                <textarea class="form-control" id="exampleTextarea1" name="company_address" rows="4" placeholder="Enter Company Address">{{$company_detail['0']->company_address}}</textarea>

                            </div>



                            <!-- <div class="form-group row">

                                <label class="col-sm-3 col-form-label">Site Settings Permissions</label>

                                <div class="col-sm-4 mt-2">

                                    <div class="form-check">

                                        <label class="form-check-label">

                                            <input type="checkbox" name="message_permission" value="1" class="form-check-input" @if($company_detail[0]->message_permissions == 1) checked @endif>

                                            Message Setting

                                        </label>

                                    </div>

                                </div>

                                <div class="col-sm-5 mt-2">

                                    <div class="form-check">

                                        <label class="form-check-label">

                                            <input type="checkbox" name="email_permission" value="1" class="form-check-input" @if($company_detail[0]->email_permissions == 1) checked @endif>

                                            E-Mail Settings

                                        </label>

                                    </div>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-md-4">

                                    <label>SMS Max Limit:</label>

                                    <input class="form-control" type="text" name="sms_count" value="{{$company_detail['0']->sms_count}}" min="0" placeholder="Enter number of SMS"/>

                                </div>

                                <div class="col-md-4">

                                    <label>Monthly Addition:</label>

                                    <input class="form-control" type="text" name="sms_limit" value="0" min="0" placeholder="Enter number of WhatsApp Notification" />

                                </div>

                            </div> -->

                            <button type="submit" id="add-company" class="btn btn-primary mr-2">Update Company</button>

                            <a class="btn btn-light" href="{{route('admin.companies.index')}}">Cancel</a>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection



<script>

function checkPasswordMatch() 

{

    var password = $("#newPassword").val();

    var confirmPassword = $("#confirmPassword").val();

    if (password != confirmPassword){

        $('#add-company').attr('disabled', true);

        $("#CheckPasswordMatch").html("Passwords does not match!");

    }

    else {

        $('#add-company').attr('disabled', false);

        $("#CheckPasswordMatch").html("");

    }

    

}

</script>