@extends('layouts.pages')


  @section('content')
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center text-center error-page bg-primary">
        <div class="row flex-grow">
          <div class="col-lg-7 mx-auto text-white">
            <div class="row align-items-center d-flex flex-row">
              <div class="col-lg-6 text-lg-right pr-lg-4">
                <h1 class="display-1 mb-0">@if($company_settings!="")<img class="img-fluid" src="{{asset('company_thumbnail/'.$company_settings[0]->site_logo)}}" alt="logo"> @else 404 @endif</h1>
              </div>
              <div class="col-lg-6 error-page-divider text-lg-left pl-lg-4">
                <h2>@if($company_settings!=""){{$company_settings[0]->site_name}}@else Sorry! @endif</h2>
                <h3 class="font-weight-light">@if($customer!="") you are successfully opted out @else The page you are looking is not found @endif</h3>
              </div>
            </div>
            <div class="row mt-5">
              <div class="col-12 text-center mt-xl-2">
                <!-- <a class="text-white font-weight-medium" href="../../index-2.html">Back to home</a> -->
              </div>
            </div>
            <div class="row mt-5">
              <div class="col-12 mt-xl-2">
                <p class="text-white font-weight-medium text-center">Copyright &copy; 2018  All rights reserved.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  @endsection



