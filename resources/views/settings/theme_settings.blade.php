@section('pagescript')

<script src="{{asset('js/typeahead.js')}}"></script>
<script src="{{asset('js/select2.js')}}"></script>
<script src="{{asset('js/formpickers.js')}}"></script>
<link rel="stylesheet" href="{{asset('vendors/summernote/dist/summernote-bs4.css')}}">
<script src="{{asset('vendors/summernote/dist/summernote-bs4.min.js')}}"></script>
<script src="{{asset('js/editorDemo.js')}}"></script>
<script src="{{asset('js/file-upload.js')}}"></script>
<style>

  

    .nav-tabs-vertical-custom li a i{

     float:right;

     margin-top:7px

    }



</style>

@stop







@extends('layouts.admin')







@section('title', ' Settings | '.Session::get('site_name'))







@section('content')







<!-- partial -->



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                General Settings



            </h3>

          

            <div class="alert alert-success" style="display:none;" id="status-success"></div>

            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{url('theme-settings')}}">General Setting</a></li>



              



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



        <div class="card">

                <div class="card-body">

              

                  <div class="row">

                    <div class="col-lg-2 col-md-4">

                      <ul class="nav nav-tabs nav-tabs-vertical-custom" role="tablist">

                        <li class="nav-item">

                          <a class="nav-link active" id="home-tab-custom" data-toggle="tab" href="#home-3" role="tab" aria-controls="home-3" aria-selected="true">

                           General Settings <i  class="fas fa-chevron-right"></i>

                          </a>

                        </li>




                        <li class="nav-item">

                          <a class="nav-link" id="contact-tab-custom" data-toggle="tab" href="#contact-3" role="tab" aria-controls="contact-3" aria-selected="false">

                            E-Mail Settings <i  class="fas fa-chevron-right"></i>

                          </a>

                        </li>





                         


                          <!-- <li class="nav-item">

                            <a class="nav-link" id="postcard" data-toggle="tab" href="#socialmedia-tab" role="tab" aria-controls="booking_sms" aria-selected="false">

                             Social Media<i  class="fas fa-chevron-right"></i>

                            </a>

                          </li> -->

                      </ul>

                    </div>

                    <div class="col-lg-10 col-md-8" id="tab-content">



                        <!-- tab-1 starts  -->

                      <div class="tab-content tab-content-vertical tab-content-vertical-custom">


                        <div class="tab-pane fade show active" id="home-3" role="tabpanel" aria-labelledby="home-tab-custom">

                            <div class="row">

                                <div class="col-lg-8">

                                    <div class="card">

                                        <div class="card-body">

                                            <h4 class="card-title">Company Settings</h4>

                                            <form class="forms-sample" action="{{url('theme-settings-update')}}" method="post" enctype="multipart/form-data" >

                                                @csrf

                                                <div class="form-group">

                                                    <label for="exampleInputUsername1">Company Name</label>    

                                                    <input type="theme" name="site_name" value="{{$settings->site_name}}" class="form-control" placeholder="Enter theme Name" required/>

        

                                                </div>

                                                <div class="form-group">

                                                    <label for="exampleInputUsername1">Smart Name* (max length 11, This name will be used in SMS to customer on booking )</label>

                                                    <input type="theme" name="smart_name" value="{{$settings->smart_name}}" class="form-control" placeholder="Enter theme " required/>

                                                </div>
                                                <div class="form-group">
                                                    <label>Financial Year Start:(Please set only once)</label>
                                                    <div id="financial-year" class="input-group date datepicker">
                                                        
                                                            <input type="text" name="financial_year" value="{{$settings->financial_year}}" class="form-control" required="">
                                                            <span class="input-group-addon input-group-append border-left">
                                                                <span class="far fa-calendar input-group-text"></span>
                                                            </span>
                                                    </div>
                                                </div>

                                                <div class="row">

                                                    <div class="col-lg-6">

                                                        <div class="card">

                                                            <div class="card-body">

                                                                <h4 class="card-title d-flex">Main Logo</h4>

                                                                <input type="file" name="site_logo" class="dropify" />

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-lg-6">

                                                        <div class="card">

                                                            <div class="card-body">

                                                                <h4 class="card-title d-flex">Smart Logo</h4>

                                                                <input type="file" name="smart_logo" class="dropify" />

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                                <button type="submit" class="btn btn-primary mr-2 mt-3">Update Setting</button>

        

                                            </form>  

                                        </div>

                                    </div>       

                                </div>

                                <div class="col-lg-4">

                                    <div class="card">

                                        <div class="card-body">

                                          

                                            <div class="form-group">

                                            <b>Main Logo</b>

                                              <div>

                                                <img class="img-fluid"  src="@if(Session::get('site_logo')){{asset('company_thumbnail/'.Session::get('site_logo'))}} @else {{asset('profiles/power_diary.png')}} @endif" alt="logo"/>

                                              </div>

                                            </div>

                                            <div class="form-group">

                                             <b>Smart Logo</b>

                                              <div class="col-sm-9">

                                              <img width="100" height="100" class="img-fluid rounded-cercular" src="@if(Session::get('smart_logo')){{asset('company_thumbnail/'.Session::get('smart_logo'))}} @else {{asset('profiles/power_d.png')}} @endif" alt="logo"/>

                                              </div>

                                            </div>

                                        </div>

                                      </div>

                                </div> 

                            </div>             

                        </div>






                            <!-- tab-2 -->




                           <!-- tab-3 -->


                        <div class="tab-pane fade" id="contact-3" role="tabpanel" aria-labelledby="contact-tab-custom">

                       

                            <div class="card">

                              <div class="card-body">

                                <div class="mt-4">

                                  <div class="accordion accordion-solid-header" id="accordion-4" role="tablist">

                                    

                                    <div class="card">

                                      <!-- <div class="card-header" role="tab" id="heading-11">

                                        <h6 class="mb-0">

                                          <a class="collapsed" data-toggle="collapse" href="#collapse-11" aria-expanded="false" aria-controls="collapse-11">

                                            Email configrations

                                          </a>

                                        </h6>

                                      </div> -->

                                      <div>

                                        <div class="row">

                                            <div class="col-12 grid-margin">

                                                <div class="card">

                                                    <div class="card-body">

                                                        <h4 class="card-title">Email Settings</h4>

                                                        <form id="email_settings" class="forms-sample" enctype="multipart/form-data" >

                                                            @csrf

                                                            

                                                            <!-- <div class="form-group">

                                                                <label for="exampleInputUsername1">Company Email</label>

                                                                <input type="email" name="email" value="{{$email['0']->email}}" class="form-control" placeholder="Enter Email Address" required/>

                                                            </div> -->

                                                            <div class="form-group row">

                                                                <div class="col">

                                                                    <label>Title:</label>

                                                                    <input type="text" name="title" value="{{$email['0']->title}}" class="form-control" placeholder="Title should be your company name which will display in emails" required/>

                                                                </div>

                                                                <div class="col">

                                                                    <label>Invoice Subject:</label>

                                                                    <input type="text" name="subject" value="{{$email['0']->subject}}" class="form-control" placeholder="This subject will be used in Invoice Only" required/>

                                                                </div>

                                                            </div>

                                                            <div class="form-group row">

                                                                <div class="col">

                                                                    <label>Email Logo:</label>

                                                                    <input type="file" name="image" class="file-upload-default">

                                                                        <div class="input-group col-xs-12">

                                                                            <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">

                                                                            <span class="input-group-append">

                                                                                <button class="file-upload-browse btn btn-primary" type="button">Upload</button>

                                                                            </span>

                                                                        </div>

                                                                </div>

                                                                <div class="col">

                                                                    <img src="@if($email['0']->image){{asset('email_logo/'.$email['0']->image)}} @else {{asset('profiles/power_diary.png')}} @endif" alt="profile" class="img-fluid"/>

                                                                </div>

                                                            </div>

                                                            

                                                            <div class="form-group row">

                                                                <div class="col">

                                                                    <label for="exampleInputUsername1">Invoice Email Header</label>

                                                                    <textarea id="summernoteHeader" name="header" >{{$email['0']->header}}</textarea>

                                                                </div>

                                                            

                                                                <div class="col">

                                                                    <label for="exampleInputUsername1">Invoice Email Footer</label>

                                                                    <textarea id="summernoteFooter" name="footer">{{$email['0']->footer}}</textarea>

                                                                </div>

                                                            </div>


                                                            <div class="form-group row">

                                                                <div class="col">

                                                                    <label for="exampleInputUsername1">Qoutation Email Header</label>

                                                                    <textarea id="summernoteQoutationh" name="quotation_header" >{{$email['0']->quotation_header}}</textarea>

                                                                </div>

                                                            

                                                                <div class="col">

                                                                    <label for="exampleInputUsername1">Qoutation Email Footer</label>

                                                                    <textarea id="summernoteQoutationf" name="quotation_footer">{{$email['0']->quotation_footer}}</textarea>

                                                                </div>

                                                            </div>

                                                            <button type="submit" class="btn btn-primary mr-2 mt-3">Update Setting</button>

                                                    

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                      </div>

                                    </div>



                                  </div>

                                </div>

                              </div>

                            </div>

                       

                        </div>


                             <!-- tab-4 -->

                            

                        

                                         <!-- tab-5 -->


                        

                      


                            <!-- tab-6 -->

                            





                            <!------ tab-7  -------->


                            






                            <!-- <div class="tab-pane fade" id="socialmedia-tab" role="tabpanel"aria-labelledby="profile-tab-custom">

                                <div class="row">

                                    <div class="col-lg-8">

                                        <div class="card">

                                            <div class="card-body">

                                                <div class="row">

                                                    <div class="col-lg-12">

                                                        <form id="socialmedia-form" class="forms-sample" method="post">



                                                            @csrf

                                                                <div class="form-group">

                                                                    <label for="exampleInputUsername1"><i class="fab fa-facebook-f"></i>acebook</label>

                                                                    <input type="text" name="facebook"  class="form-control" value="{{$settings->facebook}}" placeholder="Enter Facebook Page Url" required/>

                                                                </div>

                                                                

                                                                    <div class="form-group">

                                                                    <label for="exampleInputUsername1"><i class="fab fa-linkedin-in"></i>stagram</label>

                                                                    <input type="text" value="{{$settings->instagram}}" name="instagram" class="form-control" placeholder="Enter Instagram Url" required/>

                                                                </div>

                                                                <div class="form-group">

                                                                    <label for="exampleInputUsername1"><i class="fab fa-twitter"></i>Twitter</label>

                                                                    <input type="text" value="{{$settings->twitter}}" name="twitter"  class="form-control" placeholder="Enter Twitter Account Url" required/>

                                                                </div>                                        

                                                            <button type="submit" class="btn btn-primary mr-2">Update </button>

                                        

                                                            <a class="btn btn-light" href="">Cancel</a>

                                        

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                    

                            </div> -->

                        </div>

                                

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

              



<script>

function changeReviewStatus(_this) {

        var status = $(_this).prop('checked') == true ? 1 : 0;

   

        $.ajax({



            url: "{{url('review-status')}}",



            type: 'GET',



            data: {



                review_permissions: status



            },



            success: function(result) {



                if(result.success){

                    showSuccessToast(result.success);

                }

                else{

                    showDangerToast(result.error);

                }



            }



        });



    }



    $("#review_settings").submit(function () {



        event.preventDefault();

        var formData = new FormData($(this)[0]);

      

        $.ajax({

            url: "{{url('review-settings-update')}}",

            type: "POST",

            data: formData,

            processData: false,

            contentType: false,

            success: function (response) {

                if(response.success){

                showSuccessToast(response.success);

            

            }

            else{

                showDangerToast(response.error);

            }

            },

        });

        });



$("#email_settings").submit(function () {



event.preventDefault();

var formData = new FormData($(this)[0]);



$.ajax({

    url: "{{url('email-settings-update')}}",

    type: "POST",

    data: formData,

    processData: false,

    contentType: false,

    success: function (response) {

        if(response.success){

        showSuccessToast(response.success);

    

    }

    else{

        showDangerToast(response.error);

    }

    },

});

});



function changeMessageStatus(_this, id) {



var status = $(_this).prop('checked') == true ? 1 : 0;





$.ajax({

    url: "{{url('message-status')}}",

    type: 'GET',

    data: {

        id: id,

        message_permissions: status

    },

    success: function(result) {

        if(result.success){

                    showSuccessToast(result.success);

                }

                else{

                    showDangerToast(result.error);

                }

    }

});

}





function changeTaxStatus(_this) {



var status = $(_this).prop('checked') == true ? 1 : 0;



$.ajax({

    url: "{{url('tax-status')}}",

    type: 'GET',

    data: {

        tax_status : status

    },

    success: function(result) {

        if(result.success){

                    showSuccessToast(result.success);

                }

                else{

                    showDangerToast(result.error);

                }

    }

});

}





$("#tax-settings").submit(function () {



event.preventDefault();

var formData = new FormData($(this)[0]);

$.ajax({

    url: "{{url('tax-settings-update')}}",

    type: "POST",

    data: formData,

    processData: false,

    contentType: false,

    success: function (response) {

        if(response.success){

        showSuccessToast(response.success);

    

    }

    else{

        showDangerToast(response.error);

    }

    },

});

});



$("#socialmedia-form").submit(function () {



event.preventDefault();

var formData = new FormData($(this)[0]);

$.ajax({

    url: "{{url('socialmedia-update')}}",

    type: "POST",

    data: formData,

    processData: false,

    contentType: false,

    success: function (response) {

        if(response.success){

        showSuccessToast(response.success);

    

    }

    else{

        showDangerToast(response.error);

    }

    },

});

});





$("#clicksend-form").submit(function () {



event.preventDefault();

var formData = new FormData($(this)[0]);

$.ajax({

    url: "{{url('clicksend-update')}}",

    type: "POST",

    data: formData,

    processData: false,

    contentType: false,

    success: function (response) {

        if(response.success){

        showSuccessToast(response.success);

    

    }

    else{

        showDangerToast(response.error);

    }

    },

});

});



$("#booking_sms").submit(function () {



event.preventDefault();

var booking_sms = $("#booking_message").val();

var re_sheduled_sms = $("#re_shdule").val();

var survey_sms = $("#survey_sms").val();

$.ajax({

    url: "{{url('update-booking-message')}}",

    type: "POST",

    data: {

        "_token": "{{ csrf_token() }}",

        "booking_sms": booking_sms,

        "re_sheduled_sms" : re_sheduled_sms,
        "survey_sms" : survey_sms

        },

    success: function (response) {

        if(response.success){

        showSuccessToast(response.success);

    

    }

    else{

        showDangerToast(response.error);

    }

    },

});

});





    </script>

@endsection