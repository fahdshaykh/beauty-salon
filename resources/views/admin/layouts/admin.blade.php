<!DOCTYPE html>

<html lang="en">





<head>

  <!-- Required meta tags -->

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">



  <title>@yield('title')</title>

  <!-- plugins:css -->

  <link rel="stylesheet" href="{{asset('vendors/iconfonts/font-awesome/css/all.min.css')}}">

  <link rel="stylesheet" href="{{asset('vendors/css/vendor.bundle.base.css')}}">

  <link rel="stylesheet" href="{{asset('vendors/css/vendor.bundle.addons.css')}}">

  <!-- endinject -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <script src="{{asset('js/jquery.postcodes.js')}}"></script>

  <link rel="stylesheet" href="{{asset('css/style.css')}}">

  <link rel="stylesheet" href="{{asset('css/bootstrap-datetimepicker.css')}}">

  <link rel="shortcut icon" href="../../images/favicon.png" />



</head>

<style>

@media only print {

    .non-printable {

    display: none !important;

    }



}

</style>

<body>



<div class="container-scroller">

    <!-- partial:partials/_navbar.html -->

    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row default-layout-navbar non-printable">

      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">

        <!-- <a class="navbar-brand brand-logo" href="{{url('/home')}}"><img class="img-fluid" src="@if(Session::get('site_logo')){{asset('company_thumbnail/'.Session::get('site_logo'))}} @else {{asset('profiles/power_diary.png')}} @endif" alt="logo"/></a> -->

        <a class="navbar-brand brand-logo" href="{{url('/home')}}">{{ Session::get('site_name') }}</a>

        <a class="navbar-brand brand-logo-mini" href="{{url('/home')}}"><img class="img-fluid" src="@if(Session::get('smart_logo')){{asset('company_thumbnail/'.Session::get('smart_logo'))}} @else {{asset('profiles/power_d.png')}} @endif" alt="logo"/></a>

      </div>

      <div class="navbar-menu-wrapper d-flex align-items-stretch">

        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">

          <span class="fas fa-bars"></span>

        </button>
       

        <ul class="navbar-nav navbar-nav-right">

        

          <li class="nav-item nav-profile dropdown">

            <a class="nav-link dropdown-toggle" href="{{ url('user/profile') }}/<?php echo Auth::user()->id; ?>" data-toggle="dropdown" id="profileDropdown">

            <img src="@if(Auth::user()->image){{asset('profiles/'.Auth::user()->image)}} @else {{asset('profiles/default-avatar.jpg')}} @endif" alt="Profile"/>

            </a>

            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">

              <a class="dropdown-item" href="{{ url('user/profile') }}/<?php echo Auth::user()->id; ?>">

                <i class="fas fa-cog text-primary"></i>

                Settings

              </a>

              <div class="dropdown-divider"></div>

              <a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                <i class="fas fa-power-off text-primary"></i>

                Logout

              </a>

              <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">

                @csrf

              </form>

            </div>

          </li>

        </ul> 

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">

          <span class="fas fa-bars"></span>

        </button>

      </div>

    </nav>

    <!-- partial -->

    <div class="container-fluid page-body-wrapper">

      <!-- partial:partials/_settings-panel.html -->

      <div class="theme-setting-wrapper">

        <!-- <div id="settings-trigger"><i class="fas fa-fill-drip"></i></div> -->

        <div id="theme-settings" class="settings-panel">

          <i class="settings-close fa fa-times"></i>

          <p class="settings-heading">SIDEBAR SKINS</p>

          <div class="sidebar-bg-options selected" id="sidebar-light-theme"><div class="img-ss rounded-circle bg-light border mr-3"></div>Light</div>

          <div class="sidebar-bg-options" id="sidebar-dark-theme"><div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark</div>

          <p class="settings-heading mt-2">HEADER SKINS</p>

          <div class="color-tiles mx-0 px-4">

            <div class="tiles primary"></div>

            <div class="tiles success"></div>

            <div class="tiles warning"></div>

            <div class="tiles danger"></div>

            <div class="tiles info"></div>

            <div class="tiles dark"></div>

            <div class="tiles default"></div>

          </div>

        </div>

      </div>

      

      <!-- partial -->

      <!-- partial:partials/_sidebar.html -->

      <nav class="sidebar sidebar-offcanvas non-printable" id="sidebar">

        <ul class="nav">

          <li class="nav-item nav-profile">

            <div class="nav-link">

              <div class="profile-image">

                <img src="{{ asset('profiles/default-avatar.jpg') }}" alt="image"/>

              </div>

              <div class="profile-name">

                <p class="name">

                  Welcome {{ Auth::user()->name }}

                </p>

                <p class="designation">

Admin

                </p>

              </div>

            </div>

          </li>

          <li class="nav-item">

            <a class="nav-link" href="{{url('/admin/dashboard')}}">

              <i class="fa fa-home menu-icon"></i>

              <span class="menu-title">Dashboard</span>

            </a>

          </li>

          <!-- <li class="nav-item">

            <a class="nav-link" href="pages/widgets.html">

              <i class="fa fa-puzzle-piece menu-icon"></i>

              <span class="menu-title">Widgets</span>

            </a>

          </li> -->

          



          <!-- if admin login  -->

            <li class="nav-item {{ Request::is('companies*') ? 'active' : '' }}">

              <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="true" aria-controls="ui-basic">

                <i class="fas fa-industry menu-icon"></i>

                <span class="menu-title">Company</span>

                <i class="menu-arrow"></i>

              </a>

              <div class="collapse" id="ui-basic">

                <ul class="nav flex-column sub-menu">

                  <li class="nav-item"> <a class="nav-link" href="{{route('admin.companies.index')}}"> Company Listing </a></li>



                  <li class="nav-item"> <a class="nav-link" href="{{route('admin.companies.create')}}"> Create Company </a></li>

                

                </ul>

              </div>

            </li>

            <li class="nav-item {{ Request::is('admin/companies*') ? 'active' : '' }}">

              <a class="nav-link" data-toggle="collapse" href="#ui-company" aria-expanded="true" aria-controls="ui-company">

                <i class="fas fa-industry menu-icon"></i>

                <span class="menu-title">Reports</span>

                <i class="menu-arrow"></i>

              </a>

              <div class="collapse" id="ui-company">

                <ul class="nav flex-column sub-menu">

                  <li class="nav-item"> <a class="nav-link" href="{{url('admin/company-graph-report')}}">Company Bookings</a></li>

                  <li class="nav-item"> <a class="nav-link" href="{{url('admin/company-expense-reports')}}">Expense Report</a></li>

                  <li class="nav-item"> <a class="nav-link" href="{{url('admin/company-inventory-reports')}}">Inventory Report</a></li>

                </ul>

              </div>

            </li>

          <!-- else admin not loggin  -->

        </ul>

      </nav>

      <!-- partial -->

      @yield('content')

    </div>

    <!-- page-body-wrapper ends -->

  </div>

      <!-- content-wrapper ends -->

        <!-- partial:../../partials/_footer.html -->

        <footer class="footer non-printable">

          <div class="d-sm-flex justify-content-center justify-content-sm-between">

            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2024 <a href="https://www.bSaloon.co.uk/" target="_blank">bSaloon</a>. All rights reserved.</span>

          </div>

        </footer>

        <!-- partial -->

      </div>

      <!-- main-panel ends -->    

        

        <!-- /page content -->



        <!-- container-scroller -->

<div id="calendarModal" class="modal fade">

  <div class="modal-dialog">

      <div class="modal-content">

          <div class="modal-header">

              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>

              <h4 id="modalTitle" class="modal-title"></h4>

          </div>

          <div id="modalBody" class="modal-body"> </div>

          <div class="modal-footer">

              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

          </div>

      </div>

  </div>

</div>

  <!-- plugins:js -->

  <script src="{{asset('vendors/js/vendor.bundle.base.js')}}"></script>

  <script src="{{asset('vendors/js/vendor.bundle.addons.js')}}"></script>

  <!-- endinject -->

  <!-- Plugin js for this page-->

  <!-- End plugin js for this page-->

  <!-- inject:js -->

  <script src="{{asset('js/off-canvas.js')}}"></script>

  <script src="{{asset('js/hoverable-collapse.js')}}"></script>

  <script src="{{asset('js/misc.js')}}"></script>

  <script src="{{asset('js/settings.js')}}"></script>

  <script src="{{asset('js/todolist.js')}}"></script>

  <script src="{{asset('js/select2.js')}}"></script>

  <script src="{{asset('js/formpickers.js')}}"></script>

  <script src="{{asset('js/custom.js')}}"></script>

  <script src="{{asset('js/form-validation.js')}}"></script>

  <script src="{{asset('js/bt-maxLength.js')}}"></script>

  <script src="{{asset('js/toastDemo.js')}}"></script>

  <!-- endinject -->

  <!-- Custom js for this page-->

  <script src="{{asset('js/dropify.js')}}"></script>

  <script src="{{asset('js/dashboard.js')}}"></script>

  <script src="{{asset('js/company-report.js')}}"></script>

  <script src="{{asset('js/data-table.js')}}"></script>

  <script src='https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js'></script>

  <!-- <script src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script> -->

  <script>

    $(document).on('click', '.fc-day-number', function () {

      $("td .fc-day-grid-event .fc-title").css("font-size","20px");

    });

  </script>

    <!-- <script>

      $('#datepicker-popup2').datetimepicker({

        format: 'YYYY-MM-DD'

      });

      $('#datepicker-popup3').datetimepicker({

        format: 'YYYY-MM-DD'

      });

    </script> -->

  <!-- End custom js for this page-->

  @yield('pagescript')

</body>





</html>

