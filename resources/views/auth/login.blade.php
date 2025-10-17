@extends('layouts.app')



 <!-- plugins:css -->



 <link rel="stylesheet" href="../../vendors/iconfonts/font-awesome/css/all.min.css">



  <link rel="stylesheet" href="{{ asset('css/style.css') }}">



  <!-- endinject -->



  <link rel="shortcut icon" href="../../images/favicon.png" />



@section('content')



<div class="container-scroller">



    <div class="container-fluid page-body-wrapper full-page-wrapper">



      <div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">



        <div class="row flex-grow">



          <div class="col-lg-6 d-flex align-items-center justify-content-center">



            <div class="auth-form-transparent text-left p-3">



              <div class="brand-logo">



                <img src="../../images/power_diary.png" alt="logo">



              </div>



              <h4>{{ __('Login') }}</h4>



                @error('email')



                    <div class="alert alert-danger" role="alert">



                        {{ $message }}



                        <!-- <h6 class="font-weight-light">Happy to see you again!</h6> -->



                    </div>



                @enderror



                @if(Session::has('inactive'))



                  <div class="alert alert-danger alert-dismissible">



                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



                      {{Session::get('inactive')}}



                  </div>



                @endif



              



              <form class="pt-3" method="POST" action="{{ route('login') }}">



              @csrf



                <div class="form-group">



                  <label for="email">{{ __('E-Mail Address') }}</label>



                  <div class="input-group">



                    <div class="input-group-prepend bg-transparent">



                      <span class="input-group-text bg-transparent border-right-0">



                        <i class="fa fa-user text-primary"></i>



                      </span>



                    </div>



                    <input type="email"  class="form-control form-control-lg border-left-0 @error('password') is-invalid @enderror" id="email" name="email" placeholder="Enter Email" required autocomplete="email" autofocus>



                    



                  </div>



                </div>



                <div class="form-group">



                  <label for="password">{{ __('Password') }}</label>



                  <div class="input-group">



                    <div class="input-group-prepend bg-transparent">



                      <span class="input-group-text bg-transparent border-right-0">



                        <i class="fa fa-lock text-primary"></i>



                      </span>



                    </div>



                    <input type="password" class="form-control form-control-lg border-left-0 @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password" placeholder="Password">



                    @error('password')



                    <div class="alert alert-danger" role="alert">



                        <strong>{{ $message }}</strong>



                    </div>



                    @enderror                        



                  </div>



                </div>



                







                <div class="row">



                  @if (Route::has('password.request'))



                      <a class="btn btn-link" href="{{ route('password.request') }}">



                          {{ __('Forgot Your Password?') }}



                      </a>



                  @endif



                </div>











                <div class="my-3">



                <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">



                LOGIN</button>



                </div>



              </form>



            </div>



          </div>



          <div class="col-lg-6 login-half-bg d-flex flex-row">



            <p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy; 2022  All rights reserved.</p>



          </div>



        </div>



      </div>



    </div>







</div>



@endsection





