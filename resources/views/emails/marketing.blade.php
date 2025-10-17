<div class="row">

            <div class="col-lg-12">

                <div class="card px-2">

                        <div class="card-body">

                          <div class="row">

                            <div class="col-md-4" style="margin-bottom: 0rem;">

                                <a class="navbar-brand brand-logo" href="#"><img src="@if($company_logo){{asset('email_logo/'.$company_logo)}} @else {{asset('profiles/power_diary.png')}} @endif" width="250" height="50" alt="logo"/></a>

                            </div>

                            <div class="col-md-4"></div>

                            <div class="col-md-4">

                            {!! $header !!}

                            </div>

                          </div>

                        <hr>


                        </div>

                        <div class="container-fluid mt-5 d-flex justify-content-center w-100">

                   
                          {!! $descripton !!}

                        </div>

                        <hr>

                        <div class="col-lg-6" >

                            {!! $footer !!}

                        </div>

                    </div>

                </div>

            </div>

        </div>