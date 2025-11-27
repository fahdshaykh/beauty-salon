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

          @can('create bookings')
          <li class="nav-item d-none d-lg-flex">
            <a class="nav-link" href="{{ url('service-bookings/service-bookings-create') }}">
              <span class="btn btn-primary">+ Create Booking</span>
            </a>
          </li>
          @endcan

        <li class="nav-item dropdown">
          <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-bell"></i>
              @if(auth()->user()->unreadNotifications->count() > 0)
                  <span class="count bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
              @endif
          </a>

          <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown" style="width: 300px;">
              
              <!-- Header -->
              <div class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
                  <span class="font-weight-medium small mb-0">
                      You have {{ auth()->user()->unreadNotifications->count() }} new notifications
                  </span>
                  <a href="{{ route('notifications.all') }}" class="badge badge-pill badge-warning">View all</a>
              </div>
              
              <div class="dropdown-divider"></div>

              <!-- Notifications List -->
              <div class="notifications-scroll" style="max-height: 300px; overflow-y: auto;">
                  @forelse(auth()->user()->unreadNotifications as $notification)
                      @if($notification->data['company_id'] == auth()->user()->company_id)
                          <a href="{{ route('customers.edit', $notification->data['customer_id']) }}" class="dropdown-item preview-item d-flex align-items-start px-3 py-2">
                              <div class="preview-thumbnail mr-2">
                                  <div class="preview-icon bg-info text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                                      <i class="far fa-envelope"></i>
                                  </div>
                              </div>
                              <div class="preview-item-content flex-grow-1">
                                  <h6 class="preview-subject mb-1 font-weight-medium">{{ $notification->data['message'] }}</h6>
                                  <p class="small text-muted mb-0">
                                      {{ $notification->created_at->diffForHumans() }}
                                  </p>
                              </div>
                          </a>
                          <div class="dropdown-divider"></div>
                      @endif
                  @empty
                      <div class="text-center text-muted py-3">No new notifications</div>
                  @endforelse
              </div>
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

          @can('view calendar')
          <li class="nav-item">

            <a class="nav-link" href="{{url('/calendar')}}">

              <i class="fa fa-calendar menu-icon"></i>

              <span class="menu-title">Calendar</span>

            </a>

          </li>
          @endcan
         

         @can('view categories')
          <li class="nav-item {{ request()->routeIs('categories.index', 'categories.create', 'categories.show', 'categories.edit') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="{{ request()->routeIs('categories.index', 'categories.create', 'categories.show', 'categories.edit') ? 'true' : 'false' }}" aria-controls="ui-basic">

              <i class="fas fa-briefcase menu-icon"></i>

              <span class="menu-title">Category</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ request()->routeIs('categories.index', 'categories.create', 'categories.show', 'categories.edit') ? 'show' : '' }}" id="ui-basic" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">



                <li class="nav-item"> <a class="nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }}" href="{{ route('categories.index') }}"> Category Listing </a></li>


                @can('create categories')
                <li class="nav-item"> <a class="nav-link {{ request()->routeIs('categories.create') ? 'active' : '' }}" href="{{ route('categories.create') }}"> Create Category </a></li>
                @endcan

              </ul>

            </div>

          </li>
          @endcan

        
          @can('view inventories')
          <li class="nav-item {{ request()->routeIs('inventories.index', 'inventories.create', 'inventories.show', 'inventories.edit', 'inventories.available', 'inventories_purchases.index', 'inventories_purchases.create', 'inventories_purchases.show', 'inventories_purchases.edit', 'suppliers.index', 'suppliers.create', 'suppliers.show', 'suppliers.edit') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#ui-basico" aria-expanded="{{ request()->routeIs('inventories.index', 'inventories.create', 'inventories.show', 'inventories.edit', 'inventories.available', 'inventories_purchases.index', 'inventories_purchases.create', 'inventories_purchases.show', 'inventories_purchases.edit', 'suppliers.index', 'suppliers.create', 'suppliers.show', 'suppliers.edit') ? 'true' : 'false' }}" aria-controls="ui-basico">

              <i class="fas fa-boxes menu-icon"></i>

              <span class="menu-title">Inventories</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ request()->routeIs('inventories.index', 'inventories.create', 'inventories.show', 'inventories.edit', 'inventories.available', 'inventories_purchases.index', 'inventories_purchases.create', 'inventories_purchases.show', 'inventories_purchases.edit', 'suppliers.index', 'suppliers.create', 'suppliers.show', 'suppliers.edit') ? 'show' : '' }}" id="ui-basico" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">

              <li class="nav-item"> <a class="nav-link {{ request()->routeIs('inventories.index') ? 'active' : '' }}" href="{{route('inventories.index')}}"> Inventory Items </a></li>
              
              @can('view inventory purchases')
              <li class="nav-item"> <a class="nav-link {{ request()->routeIs('inventories_purchases.index') ? 'active' : '' }}" href="{{route('inventories_purchases.index')}}"> Inventory  Purchases</a></li>
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
          <li class="nav-item {{ request()->routeIs('services.index', 'services.create', 'services.show', 'services.edit') ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#ui-advanced" aria-expanded="{{ request()->routeIs('services.index', 'services.create', 'services.show', 'services.edit') ? 'true' : 'false' }}" aria-controls="ui-advanced">
              <i class="fab fa-trello menu-icon"></i>
              <span class="menu-title">Services</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('services.index', 'services.create', 'services.show', 'services.edit') ? 'show' : '' }}" id="ui-advanced" data-parent="#sidebar .nav">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link {{ request()->routeIs('services.index') ? 'active' : '' }}" href="{{ route('services.index') }}">Service Listing</a></li>
                @can('create services')
                <li class="nav-item"> <a class="nav-link {{ request()->routeIs('services.create') ? 'active' : '' }}" href="{{ route('services.create') }}">Create Service</a></li>
                @endcan

              </ul>
            </div>
          </li>
          @endcan
          
          @can('view customers')
          <li class="nav-item {{ request()->routeIs('customers.index', 'customers.create', 'customers.show', 'customers.edit', 'customers.profile', 'customers.bookings_history', 'customers.import.view', 'referred_customers.index', 'referred_customers.create', 'referred_customers.show', 'referred_customers.edit') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#form-elements" aria-expanded="{{ request()->routeIs('customers.index', 'customers.create', 'customers.show', 'customers.edit', 'customers.profile', 'customers.bookings_history', 'customers.import.view', 'referred_customers.index', 'referred_customers.create', 'referred_customers.show', 'referred_customers.edit') ? 'true' : 'false' }}" aria-controls="form-elements">

              <i class="fa fa-users menu-icon"></i>

              <span class="menu-title">Customers</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ request()->routeIs('customers.index', 'customers.create', 'customers.show', 'customers.edit', 'customers.profile', 'customers.bookings_history', 'customers.import.view', 'referred_customers.index', 'referred_customers.create', 'referred_customers.show', 'referred_customers.edit') ? 'show' : '' }}" id="form-elements" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">


                <li class="nav-item"><a class="nav-link {{ request()->routeIs('customers.index') ? 'active' : '' }}" href="{{route('customers.index')}}">Customer Listing</a></li>
         
                
                @can('create customers')
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('customers.create') ? 'active' : '' }}" href="{{url('customers/customers-create')}}">Add Customer</a></li>
                @endcan
                
                @can('import customers')
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('customers.import.view') ? 'active' : '' }}" href="{{url('customers-import-view')}}">Import Customers</a></li>
                @endcan
                
                @can('view referred customers')
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('referred_customers.index') ? 'active' : '' }}" href="{{route('referred_customers.index')}}">Reffered By</a></li>
                @endcan

              </ul>

            </div>

          </li>
          @endcan
         

          

          @can('view bookings')
          <li class="nav-item {{ request()->routeIs('service-bookings.index', 'service-bookings.create', 'service-bookings.show', 'service-bookings.edit', 'service-bookings.job_detail') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#editors" aria-expanded="{{ request()->routeIs('service-bookings.index', 'service-bookings.create', 'service-bookings.show', 'service-bookings.edit', 'service-bookings.job_detail') ? 'true' : 'false' }}" aria-controls="editors">

              <i class="fa fa-clipboard-list menu-icon"></i>

              <span class="menu-title">Bookings</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ request()->routeIs('service-bookings.index', 'service-bookings.create', 'service-bookings.show', 'service-bookings.edit', 'service-bookings.job_detail') ? 'show' : '' }}" id="editors" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">



                <li class="nav-item"><a class="nav-link {{ request()->routeIs('service-bookings.index') ? 'active' : '' }}" href="{{route('service-bookings.index')}}">Service Bookings</a></li>



                @can('create bookings')
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('service-bookings.create') ? 'active' : '' }}" href="{{ url('service-bookings/service-bookings-create') }}">Create Booking</a></li>
                @endcan


              </ul>

            </div>

          </li>
          @endcan
         

          @can('view reports')
          <li class="nav-item {{ request()->routeIs('reports.index', 'customer.reports', 'customer.graph_report', 'customer.graph', 'customer.country_graph_report', 'customer.country_graph', 'expense.reports', 'profit_loss.report', 'zipcode.report', 'graph.report', 'chart.booking', 'chart.heat') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#charts" aria-expanded="{{ request()->routeIs('reports.index', 'customer.reports', 'customer.graph_report', 'customer.graph', 'customer.country_graph_report', 'customer.country_graph', 'expense.reports', 'profit_loss.report', 'zipcode.report', 'graph.report', 'chart.booking', 'chart.heat') ? 'true' : 'false' }}" aria-controls="charts">

              <i class="fas fa-file menu-icon"></i>

              <span class="menu-title">Reports</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ request()->routeIs('reports.index', 'customer.reports', 'customer.graph_report', 'customer.graph', 'customer.country_graph_report', 'customer.country_graph', 'expense.reports', 'profit_loss.report', 'zipcode.report', 'graph.report', 'chart.booking', 'chart.heat') ? 'show' : '' }}" id="charts" data-parent="#sidebar .nav">

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


                <li class="nav-item"> <a class="nav-link" href="{{ route('company.limits') }}">Plan Limits</a></li>


              </ul>

            </div>

          </li>
          @endcan



        
          @can('view quotations')
          <li class="nav-item {{ request()->routeIs('quotations.index', 'quotations.create', 'quotations.show') ? 'active' : '' }}">

              <a class="nav-link" data-toggle="collapse" href="#quote" aria-expanded="{{ request()->routeIs('quotations.index', 'quotations.create', 'quotations.show') ? 'true' : 'false' }}" aria-controls="quote">

              <i class="fa fa-question-circle menu-icon" aria-hidden="true"></i>

                <span class="menu-title">Quotation</span>

                <i class="menu-arrow"></i>

              </a>

              <div class="collapse {{ request()->routeIs('quotations.index', 'quotations.create', 'quotations.show') ? 'show' : '' }}" id="quote" data-parent="#sidebar .nav">

                <ul class="nav flex-column sub-menu">



                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('quotations.index') ? 'active' : '' }}" href="{{url('quotations')}}">Quotations</a></li>



                  @can('send quotations')
                  <li class="nav-item"> <a class="nav-link {{ request()->routeIs('quotations.create') ? 'active' : '' }}" href="{{url('send-quotation')}}">Send Quote </a></li>
                  @endcan


                </ul>`

              </div>

            </li>
            @endcan


          @role('admin')
          <li class="nav-item {{ request()->routeIs('users.index', 'users.create', 'users.edit', 'roles.index', 'roles.create', 'roles.edit', 'work_schedule.index') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#general-pages" aria-expanded="{{ request()->routeIs('users.index', 'users.create', 'users.edit', 'roles.index', 'roles.create', 'roles.edit', 'work_schedule.index') ? 'true' : 'false' }}" aria-controls="general-pages">

              <i class="fa fa-user-plus menu-icon"></i>

              <span class="menu-title">User Settings</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ request()->routeIs('users.index', 'users.create', 'users.edit', 'roles.index', 'roles.create', 'roles.edit', 'work_schedule.index') ? 'show' : '' }}" id="general-pages" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">



                <li class="nav-item"> <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{route('users.index')}}"> Users </a></li>



                <li class="nav-item"> <a class="nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}" href="{{url('roles')}}"> Roles </a></li>


                <li class="nav-item"> <a class="nav-link {{ request()->routeIs('work_schedule.index') ? 'active' : '' }}" href="{{route('work_schedule.index')}}"> working schedule </a></li>

                
              </ul>

            </div>

          </li>
          @endrole
          

          @can('view attendance')
          <li class="nav-item {{ request()->routeIs('attendance', 'work_hours', 'payrolls.index') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#attendance-pages" aria-expanded="{{ request()->routeIs('attendance', 'work_hours', 'payrolls.index') ? 'true' : 'false' }}" aria-controls="attendance-pages">

              <i class="fas fa-clipboard-list menu-icon"></i>

              <span class="menu-title">Employee Attendance</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ request()->routeIs('attendance', 'work_hours', 'payrolls.index') ? 'show' : '' }}" id="attendance-pages" data-parent="#sidebar .nav">

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
         <li class="nav-item {{ request()->routeIs('theme.settings', 'theme.edit') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#icons" aria-expanded="{{ request()->routeIs('theme.settings', 'theme.edit') ? 'true' : 'false' }}" aria-controls="icons">

              <i class="fa fa-cogs menu-icon"></i>

              <span class="menu-title">Settings</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ request()->routeIs('theme.settings', 'theme.edit') ? 'show' : '' }}" id="icons" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu"> 
                @can('general settings')
                <li class="nav-item"> <a class="nav-link {{ request()->routeIs('theme.settings') ? 'active' : '' }}" href="{{url('theme-settings')}}"> General Setting</a></li>
                @endcan
              </ul>

            </div>

          </li>
          @endcan


        
          @can('view expenses')
          <li class="nav-item {{ request()->routeIs('expenses.index', 'expenses.create', 'expenses.edit', 'expense_types.index', 'expense_types.create', 'expense_types.edit') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#expenses" aria-expanded="{{ request()->routeIs('expenses.index', 'expenses.create', 'expenses.edit', 'expense_types.index', 'expense_types.create', 'expense_types.edit') ? 'true' : 'false' }}" aria-controls="expenses">

              <i class="fas fa-money-bill menu-icon"></i>

              <span class="menu-title">Expenses</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ request()->routeIs('expenses.index', 'expenses.create', 'expenses.edit', 'expense_types.index', 'expense_types.create', 'expense_types.edit') ? 'show' : '' }}" id="expenses" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">

              

                <li class="nav-item"> <a class="nav-link {{ request()->routeIs('expenses.index') ? 'active' : '' }}" href="{{url('expenses')}}"> Expenses </a></li>

              </ul>

            


                <ul class="nav flex-column sub-menu">
                @can('create expenses')
                <li class="nav-item"> <a class="nav-link {{ request()->routeIs('expenses.create') ? 'active' : '' }}" href="{{url('expenses/expenses-create')}}">Add Expense </a></li>
                @endcan
                </ul>



                <ul class="nav flex-column sub-menu">
                @can('view expense types')
                <li class="nav-item"> <a class="nav-link {{ request()->routeIs('expense_types.index') ? 'active' : '' }}" href="{{url('expense-types')}}">List Expense Types </a></li>
                @endcan
                </ul>

            </div>

          </li>
          @endcan


          @can('view templates')
         <li class="nav-item {{ request()->routeIs('email_template.index', 'email_template.create', 'email_template.edit') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#page-layouts" aria-expanded="{{ request()->routeIs('email_template.index', 'email_template.create', 'email_template.edit') ? 'true' : 'false' }}" aria-controls="page-layouts">

              <i class="fab fa-wpforms menu-icon"></i>

              <span class="menu-title">Templates</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ request()->routeIs('email_template.index', 'email_template.create', 'email_template.edit') ? 'show' : '' }}" id="page-layouts" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">

                  @can('view email templates')
                  <li class="nav-item"> <a class="nav-link {{ request()->routeIs('email_template.index') ? 'active' : '' }}" href="{{url('/email-template')}}">Email Templates</a></li>
                  @endcan

                  @can('view sms templates')
                  <li class="nav-item d-none d-lg-block"> <a class="nav-link {{ Request::is('template*') ? 'active' : '' }}" href="{{url('/template')}}">SMS Templates</a></li>
                  @endcan

                </ul>

            </div>

          </li>
          @endcan

          @can('view marketing')
          <li class="nav-item {{ request()->routeIs('marketing.index', 'power_mailing.create', 'mailing.history', 'postcard.index') ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#marketing" aria-expanded="{{ request()->routeIs('marketing.index', 'power_mailing.create', 'mailing.history', 'postcard.index') ? 'true' : 'false' }}" aria-controls="marketing">

              <i class="fas fa-bullhorn menu-icon"></i>

              <span class="menu-title">Marketing</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ request()->routeIs('marketing.index', 'power_mailing.create', 'mailing.history', 'postcard.index') ? 'show' : '' }}" id="marketing" data-parent="#sidebar .nav">


                <ul class="nav flex-column sub-menu">
                  @can('email marketing')
                  <li class="nav-item"> <a class="nav-link {{ request()->routeIs('power_mailing.create') ? 'active' : '' }}" href="{{ url('mailing') }}"> Send Mails </a></li>
                  @endcan
                </ul>

                <ul class="nav flex-column sub-menu">
                  @can('email history')
                  <li class="nav-item"> <a class="nav-link {{ request()->routeIs('mailing.history') ? 'active' : '' }}" href="{{ url('mailing-history') }}">Email history </a></li>
                  @endcan
                </ul>

                @can('sms marketing')
                <ul class="nav flex-column sub-menu">

                  <li class="nav-item"> <a class="nav-link {{ Request::is('marketing*') ? 'active' : '' }}" href="{{url('/marketing')}}"> SMS Marketing </a></li>

                </ul>
                @endcan

                @can('sms history')
                <ul class="nav flex-column sub-menu">

                  <li class="nav-item"> <a class="nav-link {{ Request::is('marketing*') ? 'active' : '' }}" href="{{url('sms-history')}}"> SMS History </a></li>

                </ul>
                @endcan

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

          
          <li class="nav-item">
            <a class="nav-link" href="{{ route('tickets.index') }}" >
              <i class="fas fa-ticket-alt menu-icon"></i>
              <span class="menu-title">Support Tickets</span>
              <i class="menu-arrow"></i>
            </a>
          </li>

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

  <script src="{{ asset('js/home.js') }}"></script>

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
  
  // Close all collapsible menus and only open the active one
  $(function() {
      // Close all collapses by default
      $('.sidebar .collapse').collapse('hide');

      // 1) If a top-level nav-item is marked active, open its collapse
      $('.sidebar .nav-item.active').each(function() {
        var $activeItem = $(this);
        var collapseId = $activeItem.find('[data-toggle="collapse"]').attr('href');
        if (collapseId) {
          $(collapseId).collapse('show');
        }
      });

      // 2) If a sub-menu link is active (child anchor), open the closest collapse
      //    and mark its top-level toggler as active so the parent stays highlighted.
      $('.sidebar a.nav-link.active').each(function() {
        var $link = $(this);
        var $collapse = $link.closest('.collapse');
        if ($collapse.length) {
          $collapse.collapse('show');
          var id = '#' + $collapse.attr('id');
          var $toggler = $('.sidebar [data-toggle="collapse"][href="' + id + '"]');
          $toggler.closest('.nav-item').addClass('active');
        }
      });
  });
  </script>
  @endpush

  @stack('scripts')
</body>

</html>

