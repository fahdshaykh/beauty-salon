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

  <link rel="stylesheet" href="{{ asset('vendors/izitoast/css/iziToast.min.css') }}">

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

        <li class="nav-item dropdown">
              <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                  <i class="fas fa-bell mx-0"></i>
                  <span class="count">{{ auth()->user()->unreadNotifications->count() }}</span>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                  
                  <div class="dropdown-header">
                      <p class="mb-0 font-weight-normal float-left">
                          You have {{ auth()->user()->unreadNotifications->count() }} new notifications
                      </p>
                      <a href="{{ route('notifications.all') }}" class="badge badge-pill badge-warning float-right">View all</a>
                  </div>

                  <div class="dropdown-divider mt-5"></div>

                  @forelse(auth()->user()->unreadNotifications as $notification)
                      @if($notification->data['company_id'] == auth()->user()->company_id)
                          <a href="{{ route('customers.edit', $notification->data['customer_id']) }}" class="dropdown-item preview-item">
                              <div class="preview-thumbnail">
                                  <div class="preview-icon bg-info">
                                      <i class="far fa-envelope mx-0"></i>
                                  </div>
                              </div>
                              <div class="preview-item-content">
                                  <h6 class="preview-subject font-weight-medium">{{ $notification->data['message'] }}</h6>
                                  <p class="font-weight-light small-text">
                                      {{ $notification->created_at->diffForHumans() }}
                                  </p>
                              </div>
                          </a>
                      @endif
                  @empty
                      <a class="dropdown-item text-center text-muted">No new notifications</a>
                  @endforelse
              </div>
          </li>

        

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

              <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                <i class="fas fa-power-off text-primary"></i>

                Logout

              </a>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">

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

                <img src="@if(Auth::user()->image){{asset('profiles/'.Auth::user()->image)}} @else {{asset('profiles/default-avatar.jpg')}} @endif" alt="image"/>

              </div>

              <div class="profile-name">

                <p class="name">

                  Welcome {{ Auth::user()->name }}

                </p>

                <p class="designation">

                  {{ ucfirst(auth()->user()->getRoleNames()->first()) }}

                </p>

              </div>

            </div>

          </li>

          <li class="nav-item">

            <a class="nav-link" href="{{url('/home')}}">

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
         

         @can('view categories')
          <li class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="true" aria-controls="ui-basic">

              <i class="fas fa-briefcase menu-icon"></i>

              <span class="menu-title">Category</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse" id="ui-basic">

              <ul class="nav flex-column sub-menu">



                <li class="nav-item"> <a class="nav-link" href="{{ route('categories.index') }}"> Category Listing </a></li>


                @can('create categories')
                <li class="nav-item"> <a class="nav-link" href="{{ route('categories.create') }}"> Create Category </a></li>
                @endcan

              </ul>

            </div>

          </li>
          @endcan

        
          @can('view inventories')
          <li class="nav-item {{ request()->routeIs('inventories.*') || request()->routeIs('inventories_purchases.*') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#ui-basico" aria-expanded="true" aria-controls="ui-basico">

              <i class="fas fa-boxes menu-icon"></i>

              <span class="menu-title">Inventories</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ Request::is('inventories.*') || Request::is('inventories_purchases.*') ? 'show' : '' }}" id="ui-basico">

              <ul class="nav flex-column sub-menu">

              <li class="nav-item"> <a class="nav-link {{ Request::is('inventories.*') ? 'active' : '' }}" href="{{route('inventories.index')}}"> Inventory Items </a></li>
              
              @can('view inventory purchases')
              <li class="nav-item"> <a class="nav-link {{ Request::is('inventories_purchases') ? 'active' : '' }}" href="{{route('inventories_purchases.index')}}"> Inventory  Purchases</a></li>
              @endcan

              @can('view available inventories')
              <li class="nav-item"> <a class="nav-link" href="{{ route('inventories.available') }}"> Inventory Available </a></li>
              @endcan

              @can('view suppliers')
              <li class="nav-item"> <a class="nav-link" href="{{ route('suppliers.index') }}"> Suppliers </a></li>
              @endcan

              </ul>

            </div>

          </li>
          @endcan

          
          @can('view services')
          <li class="nav-item {{ request()->routeIs('services.*') ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#ui-advanced" aria-expanded="false" aria-controls="ui-advanced">
              <i class="fab fa-trello menu-icon"></i>
              <span class="menu-title">Services</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-advanced">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="{{ route('services.index') }}">Service Listing</a></li>
                @can('create services')
                <li class="nav-item"> <a class="nav-link" href="{{ route('services.create') }}">Create Service</a></li>
                @endcan

              </ul>
            </div>
          </li>
          @endcan
          
          @can('view customers')
          <li class="nav-item {{ request()->routeIs('customers.*') || request()->routeIs('referred_customers.*') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">

              <i class="fa fa-users menu-icon"></i>

              <span class="menu-title">Customers</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse" id="form-elements">

              <ul class="nav flex-column sub-menu">


                <li class="nav-item"><a class="nav-link  {{ Request::is('customers.*') ? 'active' : '' }}" href="{{route('customers.index')}}">Customer Listing</a></li>
         
                
                @can('create customers')
                <li class="nav-item"><a class="nav-link  {{ Request::is('customers.*') ? 'active' : '' }}" href="{{url('customers/customers-create')}}">Add Customer</a></li>
                @endcan
                
                @can('import customers')
                <li class="nav-item"><a class="nav-link  {{ Request::is('customers.*') ? 'active' : '' }}" href="{{url('customers-import-view')}}">Import Customers</a></li>
                @endcan
                
                @can('view referred customers')
                <li class="nav-item"><a class="nav-link  {{ Request::is('referred_customers.*') ? 'active' : '' }}" href="{{route('referred_customers.index')}}">Reffered By</a></li>
                @endcan

              </ul>

            </div>

          </li>
          @endcan
         

          

          @can('view bookings')
          <li class="nav-item {{ request()->routeIs('service-bookings.*') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#editors" aria-expanded="false" aria-controls="editors">

              <i class="fa fa-truck menu-icon"></i>

              <span class="menu-title">Bookings</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse" id="editors">

              <ul class="nav flex-column sub-menu">



                <li class="nav-item"><a class="nav-link {{ Request::is('service-bookings.*') ? 'active' : '' }}" href="{{route('service-bookings.index')}}">Service Bookings</a></li>



                @can('create bookings')
                <li class="nav-item"><a class="nav-link {{ Request::is('service-bookings/service-bookings-create') ? 'active' : '' }}" href="{{url('service-bookings/service-bookings-create')}}">Create Booking</a></li>
                @endcan


              </ul>

            </div>

          </li>
          @endcan
         

          @can('view reports')
          <li class="nav-item {{ request()->routeIs('reports.*') || request()->routeIs('customer.*') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#charts" aria-expanded="false" aria-controls="charts">

              <i class="fas fa-file menu-icon"></i>

              <span class="menu-title">Reports</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse" id="charts">

              <ul class="nav flex-column sub-menu">


                @can('view services report')
                <li class="nav-item"> <a class="nav-link" href="{{url('graph-report')}}">Services Report</a></li>
                @endcan



                @can('view bookings report')
                <li class="nav-item"> <a class="nav-link" href="{{url('reports')}}">Bookings Report</a></li>
                @endcan


                <!-- reports permissions -->

                @can('view employee report')
                <li class="nav-item"> <a class="nav-link" href="{{url('employee-graph-report')}}">Employee Report</a></li>
                @endcan

                @can('view customer report')
                <li class="nav-item"> <a class="nav-link" href="{{url('customer-reports')}}">Customer Report</a></li>
                @endcan

                @can('view customer graph')
                <li class="nav-item"> <a class="nav-link" href="{{url('customer-graph')}}">Customer Graph</a></li>
                @endcan

                @can('view customer country graph')
                <li class="nav-item"> <a class="nav-link" href="{{url('customer-country-graph')}}">Customer Country Graph</a></li>
                @endcan

                <!-- reports permissions -->


                @can('view expense report')
                <li class="nav-item"> <a class="nav-link" href="{{url('expense-reports')}}">Expense Report</a></li>
                @endcan

                @can('view profit & loss graph')
                <li class="nav-item"> <a class="nav-link" href="{{url('profit-loss-report')}}">Profit & Loss Graph</a></li>
                @endcan



              </ul>

            </div>

          </li>
          @endcan



        
          @can('view quotations')
          <li class="nav-item {{ Request::is('send-quotation.*') ? 'active' : '' }}">

              <a class="nav-link" data-toggle="collapse" href="#quote" aria-expanded="false" aria-controls="quote">

              <i class="fa fa-question-circle menu-icon" aria-hidden="true"></i>

                <span class="menu-title">Quotation</span>

                <i class="menu-arrow"></i>

              </a>

              <div class="collapse {{ Request::is('send-quotation.*') ? 'show' : '' }}" id="quote">

                <ul class="nav flex-column sub-menu">



                  <li class="nav-item"> <a class="nav-link {{ Request::is('quotations.*') ? 'active' : '' }}" href="{{url('quotations')}}">Qutations</a></li>



                  @can('send quotations')
                  <li class="nav-item"> <a class="nav-link {{ Request::is('send-quotation.*') ? 'active' : '' }}" href="{{url('send-quotation')}}">Send Quote </a></li>
                  @endcan


                </ul>`

              </div>

            </li>
            @endcan


          @role('admin')
          <li class="nav-item {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#general-pages" aria-expanded="false" aria-controls="general-pages">

              <i class="fa fa-user-plus menu-icon"></i>

              <span class="menu-title">User Settings</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ Request::is('users*') || Request::is('role.*') ? 'show' : '' }}" id="general-pages">

              <ul class="nav flex-column sub-menu">



                <li class="nav-item"> <a class="nav-link {{ Request::is('users.*') ? 'active' : '' }}" href="{{route('users.index')}}"> Users </a></li>



                <li class="nav-item"> <a class="nav-link {{ Request::is('role*') ? 'active' : '' }}" href="{{url('roles')}}"> Roles </a></li>


                <li class="nav-item"> <a class="nav-link {{ Request::is('work_schedule*') ? 'active' : '' }}" href="{{route('work_schedule.index')}}"> working schedule </a></li>

                
              </ul>

            </div>

          </li>
          @endrole
          

          @can('view attendance')
          <li class="nav-item {{ Request::is('attendance.*') || Request::is('work_hours.*') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#attendance-pages" aria-expanded="false" aria-controls="attendance-pages">

              <i class="fas fa-clipboard-list menu-icon"></i>

              <span class="menu-title">Employee Attendance</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ Request::is('attendance.*') || Request::is('work_hours.*') ? 'show' : '' }}" id="attendance-pages">

              <ul class="nav flex-column sub-menu">

                <li class="nav-item"> <a class="nav-link" href="{{ route('attendance') }}">Attendance</a></li>

                @can('view work hours')
                <li class="nav-item"> <a class="nav-link" href="{{ route('work_hours') }}"> Work Hours </a></li>
                @endcan

                @can('view payrolls')
                <li class="nav-item"> <a class="nav-link" href="{{ route('payrolls.index') }}"> Monthly Payrolls </a></li>
                @endcan
                
              </ul>

            </div>

          </li>
          @endcan



          @can('view settings')
         <li class="nav-item {{ Request::is('message-setting*') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#icons" aria-expanded="false" aria-controls="icons">

              <i class="fa fa-cogs menu-icon"></i>

              <span class="menu-title">Settings</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ Request::is('message-setting*') ? 'show' : '' }}" id="icons">

              <ul class="nav flex-column sub-menu"> 
                @can('general settings')
                <li class="nav-item"> <a class="nav-link {{ Request::is('theme*') ? 'active' : '' }}" href="{{url('theme-settings')}}"> General Setting</a></li>
                @endcan
              </ul>

            </div>

          </li>
          @endcan


        
          @can('view expenses')
          <li class="nav-item {{ request()->routeIs('expenses.*') || request()->routeIs('expense_types.*') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#expenses" aria-expanded="false" aria-controls="expenses">

              <i class="fas fa-money-bill menu-icon"></i>

              <span class="menu-title">Expenses</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ Request::is('expenses.*') ? 'show' : '' }}" id="expenses">

              <ul class="nav flex-column sub-menu">

              

                <li class="nav-item"> <a class="nav-link {{ Request::is('expenses.*') ? 'active' : '' }}" href="{{url('expenses')}}"> Expenses </a></li>

              </ul>

            


                <ul class="nav flex-column sub-menu">
                @can('create expenses')
                <li class="nav-item"> <a class="nav-link {{ Request::is('expenses.*') ? 'active' : '' }}" href="{{url('expenses/expenses-create')}}">Add Expense </a></li>
                @endcan
                </ul>



                <ul class="nav flex-column sub-menu">
                @can('view expense types')
                <li class="nav-item"> <a class="nav-link {{ Request::is('expense-types.*') ? 'active' : '' }}" href="{{url('expense-types')}}">List Expense Types </a></li>
                @endcan
                </ul>

            </div>

          </li>
          @endcan


          @can('view templates')
         <li class="nav-item {{ request()->routeIs('email_template.*') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#page-layouts" aria-expanded="false" aria-controls="page-layouts">

              <i class="fab fa-wpforms menu-icon"></i>

              <span class="menu-title">Templates</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse" id="page-layouts">

              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="{{url('/email-template')}}">Email Templates</a></li>
              </ul>

            </div>

          </li>
          @endcan

          @can('view marketing')
          <li class="nav-item {{ request()->routeIs('marketing.*') || request()->routeIs('power_mailing.*') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#marketing" aria-expanded="false" aria-controls="marketing">

              <i class="fas fa-bullhorn menu-icon"></i>

              <span class="menu-title">Marketing</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ Request::is('marketing*') ? 'show' : '' }}" id="marketing">


                <ul class="nav flex-column sub-menu">
                  @can('email marketing')
                  <li class="nav-item"> <a class="nav-link {{ Request::is('marketing*') ? 'active' : '' }}" href="{{ url('mailing') }}"> Send Mails </a></li>
                  @endcan
                </ul>

                <ul class="nav flex-column sub-menu">
                  @can('email history')
                  <li class="nav-item"> <a class="nav-link {{ Request::is('mailing-history') ? 'active' : '' }}" href="{{ url('mailing-history') }}">Email history </a></li>
                  @endcan
                </ul>

            </div>

          </li>
          @endcan
          

          @can('view awards')
          <li class="nav-item">
            <a class="nav-link" href="{{ route('awards.index') }}">
              <i class="fas fa-trophy menu-icon"></i>
              <span class="menu-title">Awards</span>
              <i class="menu-arrow"></i>
            </a>
          </li>
          @endcan

          @can('view holidays')
          <li class="nav-item">
            <a class="nav-link" href="{{url('/add-holidays')}}" >
              <i class="fas fa-umbrella-beach menu-icon"></i>
              <span class="menu-title">Add Holidays</span>
              <i class="menu-arrow"></i>
            </a>
          </li>
          @endcan

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

  <!-- JS Libraies -->
  <script src="{{asset('vendors/izitoast/js/iziToast.min.js')}}"></script>
  <!-- Page Specific JS File -->
  <script src="{{asset('js/toastr.js')}}"></script>

  <script src="{{asset('vendors/sweetalert/sweetalert.min.js')}}"></script>
  <!-- <script src="{{asset('js/alerts.js')}}"></script> -->
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

  @push('scripts')
  <script>
  // Show Laravel Session Messages via iziToast (works on ALL pages)
  document.addEventListener('DOMContentLoaded', function () {
      @if(Session::has('success'))
          iziToast.success({
              title: 'Success!',
              message: '{!! Session::get('success') !!}',
              position: 'bottomCenter',
              icon: 'fas fa-check-circle',   // This will now SHOW
              timeout: 5000
          });
      @endif

      @if(Session::has('update'))
          iziToast.success({
              title: 'Updated!',
              message: '{!! Session::get('update') !!}',
              position: 'bottomCenter',
              icon: 'fas fa-check-circle',   // This will now SHOW
              timeout: 5000
          });
      @endif

      @if(Session::has('destroy'))
          iziToast.error({
              title: 'Deleted?',
              message: '{!! Session::get('destroy') !!}',
              position: 'bottomCenter',
              icon: 'fas fa-trash-alt',
              timeout: 5000
          });
      @endif

      @if(Session::has('error'))
          iziToast.error({
              title: 'Error|',
              message: '{!! Session::get('error') !!}',
              position: 'bottomCenter',
              icon: 'fas fa-exclamation-triangle',
              timeout: 6000
          });
      @endif

      @if(Session::has('warning'))
          iziToast.warning({
              title: 'Warning',
              message: '{!! Session::get('warning') !!}',
              position: 'bottomCenter',
              icon: 'fas fa-exclamation-circle',
              timeout: 6000
          });
      @endif
  });
  </script>
  @endpush

  @stack('scripts')
</body>

</html>

