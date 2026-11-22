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







@section('title', 'General Settings | '. (auth()->user()->companySetting->site_name ?? ''))







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




        <div class="card">

                <div class="card-body">

              

                  <div class="row">

                    <div class="col-lg-2 col-md-4">

                      <ul class="nav nav-tabs nav-tabs-vertical-custom" role="tablist">

                      <!-- add permission here  -->

                        <li class="nav-item">

                          <a class="nav-link active" id="home-tab-custom" data-toggle="tab" href="#home-3" role="tab" aria-controls="home-3" aria-selected="true">

                           General Settings <i  class="fas fa-chevron-right"></i>

                          </a>

                        </li>

                        <!-- add permission here  -->

                        <li class="nav-item">

                          <a class="nav-link" id="profile-tab-custom" data-toggle="tab" href="#profile-3" role="tab" aria-controls="profile-3" aria-selected="false">

                            Review SMS<i  class="fas fa-chevron-right"></i>

                          </a>

                        </li>

                        <!-- add permission here  -->

                        <li class="nav-item">

                          <a class="nav-link" id="contact-tab-custom" data-toggle="tab" href="#contact-3" role="tab" aria-controls="contact-3" aria-selected="false">

                            E-Mail Settings <i  class="fas fa-chevron-right"></i>

                          </a>

                        </li>

                        <!-- add permission here  -->

                        <li class="nav-item">

                          <a class="nav-link" id="email-tab-custom" data-toggle="tab" href="#email-3" role="tab" aria-controls="email-3" aria-selected="false">

                            Booking email <i  class="fas fa-chevron-right"></i>

                          </a>

                        </li>

                        <li class="nav-item">

                          <a class="nav-link" id="booking-settings-tab-custom" data-toggle="tab" href="#booking-settings-pane" role="tab" aria-controls="booking-settings-pane" aria-selected="false">

                            Booking Settings <i class="fas fa-chevron-right"></i>

                          </a>

                        </li>



                        <li class="nav-item">

                            <a class="nav-link" id="message-tab-custom" data-toggle="tab" href="#message-3" role="tab" aria-controls="contact-4" aria-selected="false">

                              Message Settings<i  class="fas fa-chevron-right"></i>

                            </a>

                          </li>

                          <!-- add permission here  -->

                          <li class="nav-item">

                            <a class="nav-link" id="tax-tab-custom" data-toggle="tab" href="#tax-3" role="tab" aria-controls="tax-3" aria-selected="false">

                              Tax Settings<i  class="fas fa-chevron-right"></i>

                            </a>

                          </li>

                          

                          <li class="nav-item">

                            <a class="nav-link" id="booking-sms-tab" data-toggle="tab" href="#booking_sms" role="tab" aria-controls="booking_sms" aria-selected="false">

                              SMS Templates<i  class="fas fa-chevron-right"></i>

                            </a>

                          </li>

                          <!-- add permission here  -->

                          <!-- <li class="nav-item">

                            <a class="nav-link" id="postcard" data-toggle="tab" href="#clicksend-tab" role="tab" aria-controls="booking_sms" aria-selected="false">

                             Post Card<i  class="fas fa-chevron-right"></i>

                            </a>

                          </li> -->


                          <li class="nav-item">

                            <a class="nav-link" id="postcard" data-toggle="tab" href="#socialmedia-tab" role="tab" aria-controls="booking_sms" aria-selected="false">

                             Social Media<i  class="fas fa-chevron-right"></i>

                            </a>

                          </li>

                          <li class="nav-item">

                            <a class="nav-link" id="google-calendar-nav-tab" data-toggle="tab" href="#googlecalendar-tab" role="tab" aria-controls="googlecalendar-tab" aria-selected="false">

                             Google Calendar<i  class="fas fa-chevron-right"></i>

                            </a>

                          </li>

                      </ul>

                    </div>

                    <div class="col-lg-10 col-md-8" id="tab-content">



                        <!-- tab-1 starts  -->

                      <div class="tab-content tab-content-vertical tab-content-vertical-custom">

                      <!-- add permission here  -->

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

                                                <!-- ADD THIS NEW BLOCK -->
                                                <div class="form-group">
                                                    <label><i class="fas fa-globe-asia"></i> Company Timezone</label>
                                                    <select name="timezone" class="form-control" required>
                                                        <option value="">Select Timezone</option>
                                                        @php $grouped = collect(DateTimeZone::listIdentifiers())->groupBy(function($tz) {
                                                            return explode('/', $tz)[0];
                                                        }); @endphp

                                                        @foreach($grouped as $region => $timezones)
                                                            <optgroup label="{{ ucfirst(strtolower($region)) }}">
                                                                @foreach($timezones as $tz)
                                                                    <option value="{{ $tz }}" {{ $settings->timezone == $tz ? 'selected' : '' }}>
                                                                        {{ str_replace(['_', '/'], [' ', ' → '], $tz) }}
                                                                        → {{ \Carbon\Carbon::now($tz)->format('h:i A') }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-success">
                                                        Current time in your zone: 
                                                        <strong>{{ now()->setTimezone($settings->timezone ?? 'UTC')->format('d M Y, h:i A') }}</strong>
                                                    </small>
                                                </div>

                                                <a href="https://www.eurochange.co.uk/travel/tips/world-currency-abbreviations-symbols-and-codes-travel-money" 
                                                    target="_blank">
                                                    Click here to view currency symbols and codes
                                                </a>
                                                <div class="form-group">

                                                    <label for="exampleInputUsername1">Currency Code</label>

                                                    <input type="theme" name="currency_code" value="{{$settings->currency_code}}" class="form-control" placeholder="USD" required/>

                                                </div>
                                                <div class="form-group">

                                                    <label for="exampleInputUsername1">Currency Symbol</label>

                                                    <input type="theme" name="currency_symbol" value="{{$settings->currency_symbol}}" class="form-control" placeholder="$" required/>

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

                        <!-- add permission here  -->

                        <div class="tab-pane fade" id="profile-3" role="tabpanel" aria-labelledby="profile-tab-custom">

                            <div class="row">

                                <div class="col-lg-8">

                                    <div class="card">

                                        <div class="card-body">

                                            <div class="row">

                                                <div class="col-lg-6"> <b> Acticvate / Disable review </b></div>

                                                <div class="col-lg-6">

                                    

                                                    <label class="switch" style="float: right;">

                                

                                                        <input type="checkbox" id="user-{{$settings->company_id}}"

                    

                                                            onclick="changeReviewStatus(event.target)"

                    

                                                            {{($settings->review_permissions) ? 'checked' : ''}}>



                                                        <span class="slider round"></span>

                    

                                                    </label>

                                  

                                                </div>

                                            </div>

                                            <div class="row">

                                                <div class="col-lg-12">

                                                    <form class="forms-sample" method="post" id="review_settings">

                        

                                                        @csrf

                        

                                                        <div class="form-group">

                                    

                                                            <label for="exampleTextarea1">Review SMS</label>

                                    

                                                            <textarea class="form-control" id="exampleTextarea1" name="url" rows="4">{{$settings->review_url}}</textarea>

                                    

                                                        </div>

                        

                                                        <button type="submit" class="btn btn-primary mr-2">Update</button>

                    

                        

                                                    </form>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        

                        </div>


                           <!-- tab-3 -->

                        <!-- add permission here  -->

                        <div class="tab-pane fade" id="contact-3" role="tabpanel" aria-labelledby="contact-tab-custom">

                       

                            <div class="card">

                              <div class="card-body">

                                <div class="mt-4">

                                  <div class="accordion accordion-solid-header" id="accordion-4" role="tablist">

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




                                                        <h4 class="card-title">General Email Settings</h4>

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


                             <!-- tab-4 -->

                                                         <!-- add permission here  -->

                        <div class="tab-pane fade" id="email-3" role="tabpanel" aria-labelledby="profile-tab-custom">

                            <div class="card">

                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-lg-6"> 
                                            <b> Booking Email Acticvate / Disable Messages  </b>
                                        </div>
                                        

                                        <div class="col-lg-6">

                                    

                                            <label class="switch" style="float: right;">

                                                

                                                <input type="checkbox" id="email-{{$settings->company_id}}"

            

                                                    onclick="changeEmailStatus(event.target, {{ $settings->company_id }});"

            

                                                    {{($settings->email_permissions) ? 'checked' : ''}}>

        

                                                <span class="slider round"></span>

            

                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="card mt-3">

                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-lg-6"> 
                                            <b> Email Complete Booking Message </b>
                                            <p class="text-muted small mb-0">Send an email notification to the customer when their booking is marked completed.</p>
                                        </div>

                                        <div class="col-lg-6">

                                            <label class="switch" style="float: right;">

                                                <input type="checkbox" id="complete-email-{{$settings->company_id}}"

                                                    onclick="changeCompleteEmailStatus(event.target, {{ $settings->company_id }});"

                                                    {{(isset($settings->complete_booking_email_permission) && $settings->complete_booking_email_permission) ? 'checked' : ''}}>

                                                <span class="slider round"></span>

                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Booking Settings Pane -->
                        <div class="tab-pane fade" id="booking-settings-pane" role="tabpanel" aria-labelledby="booking-settings-tab-custom">

                            <div class="card">

                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-lg-6"> 
                                            <b> Advance Payment </b>
                                            <p class="text-muted small mb-0">Allow taking advance deposit or partial payment when creating a service booking.</p>
                                        </div>

                                        <div class="col-lg-6">

                                            <label class="switch" style="float: right;">

                                                <input type="checkbox" id="advance-pay-{{$settings->company_id}}"

                                                    onclick="changeAdvancePaymentStatus(event.target, {{ $settings->company_id }});"

                                                    {{(isset($settings->advance_payment_permission) && $settings->advance_payment_permission) ? 'checked' : ''}}>

                                                <span class="slider round"></span>

                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="card mt-3">

                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-lg-6"> 
                                            <b> Add Inventory </b>
                                            <p class="text-muted small mb-0">Allow adding retail / inventory products and basket to service bookings.</p>
                                        </div>

                                        <div class="col-lg-6">

                                            <label class="switch" style="float: right;">

                                                <input type="checkbox" id="booking-inv-{{$settings->company_id}}"

                                                    onclick="changeBookingInventoryStatus(event.target, {{ $settings->company_id }});"

                                                    {{(isset($settings->booking_inventory_permission) && $settings->booking_inventory_permission) ? 'checked' : ''}}>

                                                <span class="slider round"></span>

                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                            <!-- add permission here  -->

                        <div class="tab-pane fade" id="message-3" role="tabpanel" aria-labelledby="profile-tab-custom">

                            <div class="card">

                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-lg-6"> <b> Acticvate / Disable Messages  </b></div>

                                        <div class="col-lg-12">

                                    

                                            <label class="switch" style="float: right;">

                        

                                                <input type="checkbox" id="user-{{$settings->company_id}}"

            

                                                    onclick="changeMessageStatus(event.target, {{ $settings->company_id }});"

            

                                                    {{($settings->message_permissions) ? 'checked' : ''}}>

        

                                                <span class="slider round"></span>

            

                                            </label>

                                          




                                                    <form id="voodoosms-settings" class="forms-sample" >

                                        

                                                            @csrf

                                                            <br>

                                                            <div class="form-group">

                                                                <label for="exampleTextarea1">API</label>

                                                            

                                                                <input type="theme" name="voodoosms_api_key" value="{{$settings->voodoosms_api_key}}" class="form-control" placeholder="Enter voodoosms_api_key" required/>


                                                            </div>

                                                            <div class="form-group">

                                                                <label for="exampleTextarea1">SMS Count</label>

                                                            

                                                                <input type="theme" name="sms_count" value="{{$settings->sms_count}}" class="form-control" placeholder="Enter sms count" required/>


                                                            </div>

                                        

                                                    <button type="submit" class="btn btn-primary mr-2">Update API Settings</button>

                                            </form>

                                        </div>

                                    </div>



                                </div>

                            </div>

                        </div>

                                         <!-- tab-5 -->

                        <!-- add permission here  -->

                        <div class="tab-pane fade" id="tax-3" role="tabpanel" aria-labelledby="tax-tab-custom">

                            <div class="row">

                                <div class="col-lg-8">

                                    <div class="card">

                                        <div class="card-body">

                                            <div class="row">

                                                <div class="col-lg-6"> <b> Acticvate / Disable Tax </b></div>

                                                    <div class="col-lg-6">

                                    

                                                        <label class="switch" style="float: right;">

                                    

                                                            <input type="checkbox" id="user-{{$settings->company_id}}"

                        

                                                                onclick="changeTaxStatus(event.target);"

                        

                                                                {{($settings->tax_status) ? 'checked' : ''}}>

                        

                                                            <span class="slider round"></span>

                        

                                                        </label>

                                                        

                                                    </div>

                                                </div>

                                                <div class="row">

                                                    <div class="col-lg-12">

                                                        <form id="tax-settings" class="forms-sample" >

                                        

                                                            @csrf

                                                            <br>

                                                            <div class="form-group">

                                                                <div class="form-check">

                                                                    <label class="form-check-label">

                                                                    <input type="radio" class="form-check-input" name="vat"   id="optionsRadios1" value="0" @if($settings->vat==0) checked @endif >

                                                                    Fixed

                                                                    <i class="input-helper"></i></label>

                                                                </div>

                                                                <br>

                                                                <div class="form-check">

                                                                    

                                                                    <label class="form-check-label">

                                                                    <input type="radio" class="form-check-input" name="vat" id="optionsRadios1" value="1" @if($settings->vat==1)  

                                                                    checked  @endif>

                                                                    Percentage

                                                                    <i class="input-helper"></i></label>

                                                                </div>

                                                                <br>

                                                            

                                                                <label for="exampleTextarea1">Amount</label>

                                                            

                                                                <input type="theme" name="amount" value="{{$settings->amount}}" class="form-control" placeholder="Enter Amount" required/>

                                                                

                                        

                                                            </div>

                                        

                                                            <button type="submit" class="btn btn-primary mr-2">Update Tax Settings</button>

                                    

                                        

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                      


                            <!-- tab-6 -->

                            <div class="tab-pane fade" id="booking_sms" role="tabpanel"aria-labelledby="profile-tab-custom">

                                <div class="row">

                                    <div class="col-lg-8">

                                        <div class="card">

                                            <div class="card-body">

                                                <div class="row">

                                                <div class="alert alert-warning mt-3" role="alert" style="text-align:center">

                                                    In order to send dynamic message please follow the pattern: <strong>{customer name}</strong> | <strong>{services name}</strong> | <strong>{date}</strong> | <strong>{time}</strong>

                                                </div>

                                                    <div class="col-lg-12">

                                                        <form id="booking_sms" class="forms-sample" method="post">



                                                            @csrf

                                        

                                                            <div class="form-group">

                                        

                                                                <label for="exampleTextarea1">New booking SMS <span> (characters limit 150)</span></label>

                                                                <textarea class="form-control" id="booking_message" name="booking_sms" rows="4">{{$settings->booking_sms}}</textarea>

                                                                    <br>

                                                                <label for="exampleTextarea1">Re-Schedule SMS <span> (characters limit 150)</span></label>

                                                                <textarea class="form-control" id="re_shdule" name="re_shdule" rows="4">{{$settings->re_sheduled_sms}}</textarea>
                                                                <br>
                                                                <label for="exampleTextarea1">Survey SMS <span> (characters limit 150)</span></label>

                                                                <textarea class="form-control" id="survey_sms" name="survey_sms" rows="4">{{$settings->survey_sms}}</textarea>
                                        

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

                                    

                            </div>





                            <!------ tab-7  -------->

                            <!-- add permission here  -->

                            <!-- <div class="tab-pane fade" id="clicksend-tab" role="tabpanel"aria-labelledby="profile-tab-custom">

                                <div class="row">

                                    <div class="col-lg-8">

                                        <div class="card">

                                            <div class="card-body">

                                                <div class="row">

                                                    <div class="col-lg-12">

                                                    @if($settings->clicksend_api_key=="" || $settings->clicksend_username=="" || $settings->clicksend_password=="")

                                                        <p class="card-description">You don't have account on clicksend<a target="_blank" href="https://clicksend.com/?u=267536"> Create my account</a></p>

                                                        @endif

                                                        <form id="clicksend-form" class="forms-sample" method="post">



                                                            @csrf

                                                                <div class="form-group">

                                                                    <label for="exampleInputUsername1">Username</label>

                                                                    <input type="text" name="clicksend_username"  class="form-control" value="{{$settings->clicksend_username}}" placeholder="Enter clicksend username" required/>

                                                                </div>

                                                                

                                                                    <div class="form-group">

                                                                    <label for="exampleInputUsername1">Password</label>

                                                                    <input type="text" value="{{$settings->clicksend_password}}" name="clicksend_password" class="form-control" placeholder="Enter clicksend password" required/>

                                                                </div>

                                                                <div class="form-group">

                                                                    <label for="exampleInputUsername1">Api key</label>

                                                                    <input type="text" value="{{$settings->clicksend_api_key}}" name="clicksend_apikey"  class="form-control" placeholder="Enter clicksend ApiKey" required/>

                                                                </div>



                                                                <div class="form-group">

                                                                    <label for="exampleInputUsername1">Return Address Id (To get address Id <a target="_blank" href="https://dashboard.clicksend.com/messaging-settings/post/return-addresses">click here</a>)</label>

                                                                    <input type="text" value="{{$settings->clicksend_returnaddress}}" name="clicksend_returnaddress"  class="form-control" placeholder="Enter clicksend Return AddressId" required/>

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






                            <div class="tab-pane fade" id="socialmedia-tab" role="tabpanel"aria-labelledby="profile-tab-custom">

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

                                                            <button type="submit" class="btn btn-dark mr-2">Update Setting</button>

                                        

                                                            <a class="btn btn-light" href="">Cancel</a>

                                        

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                    

                            </div>

                            <div class="tab-pane fade" id="googlecalendar-tab" role="tabpanel" aria-labelledby="google-calendar-nav-tab">

                                <div class="row">

                                    <div class="col-lg-8">

                                        <div class="card">

                                            <div class="card-body">

                                                <h4 class="card-title">Google Calendar Integration</h4>

                                                <p class="card-description">Connect the salon owner's Google account to automatically sync bookings to your primary Google Calendar and invite customers as guests with Google Calendar RSVP.</p>

                                                @php
                                                    $hasCalendarScope = false;
                                                    if (!empty($settings->google_access_token)) {
                                                        $tokenArr = json_decode($settings->google_access_token, true);
                                                        $hasCalendarScope = str_contains($tokenArr['scope'] ?? '', 'calendar');
                                                    }
                                                    $hasClientCredentials = (!empty($settings->google_client_id) && !empty($settings->google_client_secret))
                                                        || file_exists(storage_path('app/google-calendar/oauth-credentials.json'))
                                                        || (!empty(config('services.google.client_id')) && !empty(config('services.google.client_secret')));
                                                @endphp
                                                @if(!empty($settings->google_access_token) && $hasCalendarScope)
                                                    <div class="alert alert-success d-flex align-items-center justify-content-between p-3 mb-4" role="alert">
                                                        <div>
                                                            <i class="fab fa-google mr-2 text-danger"></i>
                                                            <strong>Connected:</strong> {{ $settings->google_account_email ?? 'Google Account' }}
                                                            <span class="badge badge-success ml-2">Active</span>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" id="disconnect-google-calendar-btn">Disconnect</button>
                                                    </div>
                                                @elseif(!empty($settings->google_access_token) && !$hasCalendarScope)
                                                    <div class="alert alert-warning p-3 mb-4" role="alert">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <div>
                                                                <i class="fab fa-google mr-2 text-danger"></i>
                                                                <strong>Connected:</strong> {{ $settings->google_account_email ?? 'Google Account' }}
                                                                <span class="badge badge-warning ml-2 font-weight-bold">Missing Calendar Permission</span>
                                                            </div>
                                                            <div>
                                                                <a href="{{ route('google.calendar.connect') }}" class="btn btn-sm btn-primary mr-2 font-weight-bold">Reconnect</a>
                                                                <button type="button" class="btn btn-sm btn-outline-danger" id="disconnect-google-calendar-btn">Disconnect</button>
                                                            </div>
                                                        </div>
                                                        <div class="small text-danger">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                                            <strong>Action Required:</strong> Google Calendar permission was not granted. Please click <strong>Reconnect</strong>, and on Google's permission screen, be sure to tick the checkbox <em>"See, edit, share, and permanently delete all the calendars you can access using Google Calendar"</em>.
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="p-4 mb-4 bg-light border rounded text-center">
                                                        <p class="text-muted mb-3">
                                                            @if(!$hasClientCredentials)
                                                                Please enter and save your <strong>Google Client ID</strong> and <strong>Client Secret</strong> below before connecting.
                                                            @else
                                                                No Google account connected. Click below to sign in with your salon's Google account.
                                                            @endif
                                                        </p>
                                                        <a href="{{ route('google.calendar.connect') }}" class="btn btn-danger font-weight-bold {{ !$hasClientCredentials ? 'disabled' : '' }}" {{ !$hasClientCredentials ? 'onclick="alert(\'Please enter and save your Google Client ID and Client Secret below first.\'); return false;"' : '' }}>
                                                            <i class="fab fa-google mr-2"></i> Connect Google Calendar
                                                        </a>
                                                    </div>
                                                @endif

                                                <form action="{{url('google-calendar-settings-update')}}" class="forms-sample" method="post">

                                                    @csrf

                                                    <div class="form-group">
                                                        <label for="google_client_id"><i class="fas fa-key mr-1"></i> Google Client ID</label>
                                                        <input type="text" value="{{ $settings->google_client_id ?? '' }}" name="google_client_id" id="google_client_id" class="form-control" placeholder="e.g. 1234567890-xxx.apps.googleusercontent.com" />
                                                        <small class="form-text text-muted">Created in Google Cloud Console &gt; APIs &amp; Services &gt; Credentials &gt; OAuth 2.0 Client IDs.</small>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="google_client_secret"><i class="fas fa-user-secret mr-1"></i> Google Client Secret</label>
                                                        <input type="password" value="{{ $settings->google_client_secret ?? '' }}" name="google_client_secret" id="google_client_secret" class="form-control" placeholder="e.g. GOCSPX-xxxxxxxxxxxxxxxx" autocomplete="new-password" />
                                                        <small class="form-text text-muted">Your Google OAuth 2.0 Client Secret.</small>
                                                    </div>

                                                    {{-- <div class="form-group">
                                                        <label><i class="fas fa-link mr-1"></i> Authorized Redirect URI <span class="badge badge-info ml-1">Copy into Google Console</span></label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control bg-light font-weight-bold" id="google_redirect_uri" value="{{ route('google.calendar.callback') }}" readonly />
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" id="copy-redirect-uri-btn" onclick="navigator.clipboard.writeText(document.getElementById('google_redirect_uri').value); showSuccessToast('Redirect URI copied to clipboard!');">
                                                                    <i class="far fa-copy mr-1"></i> Copy
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <small class="form-text text-muted">Add this exact URL in Google Cloud Console under <strong>Authorized redirect URIs</strong> for your OAuth client.</small>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="google_calendar_id"><i class="far fa-calendar-alt mr-1"></i> Google Calendar ID</label>
                                                        <input type="text" value="{{ $settings->google_calendar_id ?? 'primary' }}" name="google_calendar_id" id="google_calendar_id" class="form-control" placeholder="primary" />
                                                        <small class="form-text text-muted">Use <code>primary</code> for the connected salon account's main calendar, or enter a specific calendar ID.</small>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="form-check form-check-flat form-check-primary">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" name="google_calendar_sync" value="1" class="form-check-input" {{ (!empty($settings->google_calendar_sync)) ? 'checked' : '' }}>
                                                                Enable Google Calendar Sync
                                                                <i class="input-helper"></i>
                                                            </label>
                                                        </div>
                                                    </div> --}}

                                                    <button type="submit" class="btn btn-dark mr-2">Update Setting</button>

                                                    <a class="btn btn-light" href="">Cancel</a>

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



function changeEmailStatus(_this, id) {



var status = $(_this).prop('checked') == true ? 1 : 0;





$.ajax({

    url: "{{url('email-status')}}",

    type: 'GET',

    data: {

        id: id,

        email_permissions: status

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

function changeCompleteEmailStatus(_this, id) {
    var status = $(_this).prop('checked') == true ? 1 : 0;
    $.ajax({
        url: "{{url('complete-email-status')}}",
        type: 'GET',
        data: {
            id: id,
            complete_booking_email_permission: status
        },
        success: function(result) {
            if(result.success){
                showSuccessToast(result.success);
            } else {
                showDangerToast(result.error);
            }
        }
    });
}

function changeAdvancePaymentStatus(_this, id) {
    var status = $(_this).prop('checked') == true ? 1 : 0;
    $.ajax({
        url: "{{url('advance-payment-status')}}",
        type: 'GET',
        data: {
            id: id,
            advance_payment_permission: status
        },
        success: function(result) {
            if(result.success){
                showSuccessToast(result.success);
            } else {
                showDangerToast(result.error);
            }
        }
    });
}

function changeBookingInventoryStatus(_this, id) {
    var status = $(_this).prop('checked') == true ? 1 : 0;
    $.ajax({
        url: "{{url('booking-inventory-status')}}",
        type: 'GET',
        data: {
            id: id,
            booking_inventory_permission: status
        },
        success: function(result) {
            if(result.success){
                showSuccessToast(result.success);
            } else {
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


//voodoosms settings update
$("#voodoosms-settings").submit(function () {



event.preventDefault();

var formData = new FormData($(this)[0]);

$.ajax({

    url: "{{url('voodoosms-settings-update')}}",

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

$("#googlecalendar-form").submit(function (event) {

    event.preventDefault();

    var formData = new FormData($(this)[0]);

    $.ajax({

        url: "{{url('google-calendar-settings-update')}}",

        type: "POST",

        data: formData,

        processData: false,

        contentType: false,

        success: function (response) {

            if(response.success){

                showSuccessToast(response.success);

            } else {

                showDangerToast(response.error || 'Failed to update Google Calendar settings.');

            }

        },

        error: function(xhr) {

            showDangerToast('An error occurred while updating settings.');

        }

    });

});

$("#disconnect-google-calendar-btn").click(function () {
    if (!confirm('Are you sure you want to disconnect Google Calendar?')) return;
    $.ajax({
        url: "{{ route('google.calendar.disconnect') }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}" },
        success: function (response) {
            showSuccessToast(response.success || 'Google Calendar disconnected.');
            setTimeout(function() { location.reload(); }, 800);
        },
        error: function() {
            showDangerToast('Failed to disconnect Google Calendar.');
        }
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