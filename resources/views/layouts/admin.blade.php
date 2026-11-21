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
        
        <div class="large mx-3 mt-4 text-white">
            <i class="fas fa-clock"></i> 
            {{ auth()->user()->company->settings->timezone ?? 'UTC' }}
            → {{ now()->format('h:i A') }}
        </div>

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

          @php
            $isDashboard = request()->is('home*') || request()->routeIs('home', 'dashboard');
          @endphp
          <li class="nav-item {{ $isDashboard ? 'active' : '' }}">

            <a class="nav-link {{ $isDashboard ? 'active' : '' }}" href="{{url('/home')}}">

              <i class="fa fa-home menu-icon"></i>

              <span class="menu-title">Dashboard</span>

            </a>

          </li>

          @can('view calendar')
          @php
            $isCalendar = request()->is('calendar*') || request()->routeIs('calendar');
          @endphp
          <li class="nav-item {{ $isCalendar ? 'active' : '' }}">

            <a class="nav-link {{ $isCalendar ? 'active' : '' }}" href="{{url('/calendar')}}">

              <i class="fa fa-calendar menu-icon"></i>

              <span class="menu-title">Calendar</span>

            </a>

          </li>
          @endcan
         

         @can('view categories')
          @php
            $isCategory = request()->is('categories*') || request()->is('category*') || request()->routeIs('categories.*');
            $isCategoryCreate = request()->routeIs('categories.create') || request()->is('categories/*create*') || request()->is('categories/category-create*');
            $isCategoryList = $isCategory && !$isCategoryCreate;
          @endphp
          <li class="nav-item {{ $isCategory ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="{{ $isCategory ? 'true' : 'false' }}" aria-controls="ui-basic">

              <i class="fas fa-briefcase menu-icon"></i>

              <span class="menu-title">Category</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ $isCategory ? 'show' : '' }}" id="ui-basic" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">

                <li class="nav-item"> <a class="nav-link {{ $isCategoryList ? 'active' : '' }}" href="{{ route('categories.index') }}"> Category Listing </a></li>

                @can('create categories')
                <li class="nav-item"> <a class="nav-link {{ $isCategoryCreate ? 'active' : '' }}" href="{{ route('categories.create') }}"> Create Category </a></li>
                @endcan

              </ul>

            </div>

          </li>
          @endcan
          
          @can('view services')
          @php
            $isServices = (request()->is('services*') || request()->is('servicestatus*') || request()->routeIs('services.*')) && !request()->is('service-bookings*');
            $isServicesCreate = $isServices && (request()->routeIs('services.create') || request()->is('services/*create*') || request()->is('services/services-create*'));
            $isServicesList = $isServices && !$isServicesCreate;
          @endphp
          <li class="nav-item {{ $isServices ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#ui-advanced" aria-expanded="{{ $isServices ? 'true' : 'false' }}" aria-controls="ui-advanced">
              <i class="fab fa-trello menu-icon"></i>
              <span class="menu-title">Services</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ $isServices ? 'show' : '' }}" id="ui-advanced" data-parent="#sidebar .nav">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link {{ $isServicesList ? 'active' : '' }}" href="{{ route('services.index') }}">Service Listing</a></li>
                @can('create services')
                <li class="nav-item"> <a class="nav-link {{ $isServicesCreate ? 'active' : '' }}" href="{{ route('services.create') }}">Create Service</a></li>
                @endcan

              </ul>
            </div>
          </li>
          @endcan

          @can('view inventories')
          @php
            $isInventories = request()->is('inventories*') || request()->is('suppliers*') || request()->is('supplier-payments*') || request()->routeIs('inventories.*', 'inventories_purchases.*', 'suppliers.*', 'supplier_payments.*');
            $isInvPurchases = request()->routeIs('inventories_purchases.*') || request()->is('inventories_purchases*');
            $isInvAvailable = request()->routeIs('inventories.available') || request()->is('inventories-available*');
            $isSuppliers = request()->routeIs('suppliers.*', 'supplier_payments.*') || request()->is('suppliers*') || request()->is('supplier-payments*');
            $isInvItems = $isInventories && !$isInvPurchases && !$isInvAvailable && !$isSuppliers;
          @endphp
          <li class="nav-item {{ $isInventories ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#ui-basico" aria-expanded="{{ $isInventories ? 'true' : 'false' }}" aria-controls="ui-basico">

              <i class="fas fa-boxes menu-icon"></i>

              <span class="menu-title">Inventories</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ $isInventories ? 'show' : '' }}" id="ui-basico" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">

              <li class="nav-item"> <a class="nav-link {{ $isInvItems ? 'active' : '' }}" href="{{route('inventories.index')}}"> Inventory Items </a></li>
              
              @can('view inventory purchases')
              <li class="nav-item"> <a class="nav-link {{ $isInvPurchases ? 'active' : '' }}" href="{{route('inventories_purchases.index')}}"> Inventory  Purchases</a></li>
              @endcan

              @can('view available inventories')
              <li class="nav-item"> <a class="nav-link {{ $isInvAvailable ? 'active' : '' }}" href="{{ route('inventories.available') }}"> Inventory Available </a></li>
              @endcan

              @can('view suppliers')
              <li class="nav-item"> <a class="nav-link {{ $isSuppliers ? 'active' : '' }}" href="{{ route('suppliers.index') }}"> Suppliers </a></li>
              @endcan

              </ul>

            </div>

          </li>
          @endcan
          
          @can('view customers')
          @php
            $isCustomers = request()->is('customers*') || request()->is('referred*') || request()->is('bookings-history*') || request()->routeIs('customers.*', 'referred_customers.*');
            $isCustCreate = request()->routeIs('customers.create') || request()->is('customers/*create*');
            $isCustImport = request()->routeIs('customers.import.*', 'customers.import.view') || request()->is('customers-import*') || request()->is('customers/import*');
            $isCustReferred = request()->routeIs('referred_customers.*') || request()->is('referred*');
            $isCustList = $isCustomers && !$isCustCreate && !$isCustImport && !$isCustReferred;
          @endphp
          <li class="nav-item {{ $isCustomers ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#form-elements" aria-expanded="{{ $isCustomers ? 'true' : 'false' }}" aria-controls="form-elements">

              <i class="fa fa-users menu-icon"></i>

              <span class="menu-title">Customers</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ $isCustomers ? 'show' : '' }}" id="form-elements" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">

                <li class="nav-item"><a class="nav-link {{ $isCustList ? 'active' : '' }}" href="{{route('customers.index')}}">Customer Listing</a></li>
         
                @can('create customers')
                <li class="nav-item"><a class="nav-link {{ $isCustCreate ? 'active' : '' }}" href="{{url('customers/customers-create')}}">Add Customer</a></li>
                @endcan
                
                @can('import customers')
                <li class="nav-item"><a class="nav-link {{ $isCustImport ? 'active' : '' }}" href="{{url('customers-import-view')}}">Import Customers</a></li>
                @endcan
                
                @can('view referred customers')
                <li class="nav-item"><a class="nav-link {{ $isCustReferred ? 'active' : '' }}" href="{{route('referred_customers.index')}}">Reffered By</a></li>
                @endcan

              </ul>

            </div>

          </li>
          @endcan
         
          @can('view bookings')
          @php
            $isBookings = request()->is('service-bookings*') || request()->is('service_bookings*') || request()->is('view-booking*') || request()->is('show-job-detail*') || request()->routeIs('service-bookings.*');
            $isBookingsCreate = request()->routeIs('service-bookings.create', 'service-bookings.date', 'service-bookings.date_success') || request()->is('service-bookings/*create*') || request()->is('get-booking-date*');
            $isBookingsList = $isBookings && !$isBookingsCreate;
          @endphp
          <li class="nav-item {{ $isBookings ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#editors" aria-expanded="{{ $isBookings ? 'true' : 'false' }}" aria-controls="editors">

              <i class="fa fa-clipboard-list menu-icon"></i>

              <span class="menu-title">Bookings</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ $isBookings ? 'show' : '' }}" id="editors" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">

                <li class="nav-item"><a class="nav-link {{ $isBookingsList ? 'active' : '' }}" href="{{route('service-bookings.index')}}">Service Bookings</a></li>

                @can('create bookings')
                <li class="nav-item"><a class="nav-link {{ $isBookingsCreate ? 'active' : '' }}" href="{{ url('service-bookings/service-bookings-create') }}">Create Booking</a></li>
                @endcan

              </ul>

            </div>

          </li>
          @endcan
         
          @can('view reports')
          @php
            $isRepServicesGraph = request()->routeIs('graph.report', 'chart.booking', 'chart.heat') || request()->is('graph-report*');
            $isRepBookings = request()->routeIs('reports.index', 'reports.record_find', 'reports.export') || request()->is('reports*') || request()->is('record/find*');
            $isRepEmpGraph = request()->routeIs('employee.graph_report', 'employee.booking_chart') || request()->is('employee-graph-report*') || request()->is('employee-booking-chart*');
            $isRepCustomer = request()->routeIs('customer.reports', 'customer.record', 'customer.report_by_date') || request()->is('customer-reports*');
            $isRepCustGraph = request()->routeIs('customer.graph', 'customer.graph_report', 'customer.country_graph', 'customer.country_graph_report') || request()->is('customer-graph*') || request()->is('customer-country*');
            $isRepInventory = request()->routeIs('inventory-reports', 'inventory.record', 'inventory.report_by_date') || request()->is('inventory-reports*');
            $isRepExpense = request()->routeIs('expense.reports', 'expense.record', 'expense.export') || request()->is('expense-reports*');
            $isRepPayments = request()->routeIs('payment.reports', 'payment.export') || request()->is('payment-reports*');
            $isRepProfitLoss = request()->routeIs('profit_loss.*') || request()->is('profit-loss-report*') || request()->is('zipcode*');
            $isRepLimits = request()->routeIs('company.limits') || request()->is('company/limits*');
            $isReports = $isRepServicesGraph || $isRepBookings || $isRepEmpGraph || $isRepCustomer || $isRepCustGraph || $isRepInventory || $isRepExpense || $isRepPayments || $isRepProfitLoss || $isRepLimits;
          @endphp
          <li class="nav-item {{ $isReports ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#charts" aria-expanded="{{ $isReports ? 'true' : 'false' }}" aria-controls="charts">

              <i class="fas fa-file menu-icon"></i>

              <span class="menu-title">Reports</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ $isReports ? 'show' : '' }}" id="charts" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">

                @can('view services report')
                <li class="nav-item"> <a class="nav-link {{ $isRepServicesGraph ? 'active' : '' }}" href="{{url('graph-report')}}">Services Graph</a></li>
                @endcan

                @can('view bookings report')
                <li class="nav-item"> <a class="nav-link {{ $isRepBookings ? 'active' : '' }}" href="{{url('reports')}}">Bookings Report</a></li>
                @endcan

                @can('view employee report')
                <li class="nav-item"> <a class="nav-link {{ $isRepEmpGraph ? 'active' : '' }}" href="{{url('employee-graph-report')}}">Employee Graph</a></li>
                @endcan

                @can('view customer report')
                <li class="nav-item"> <a class="nav-link {{ $isRepCustomer ? 'active' : '' }}" href="{{url('customer-reports')}}">Customer Report</a></li>
                @endcan

                @can('view customer graph')
                <li class="nav-item"> <a class="nav-link {{ $isRepCustGraph ? 'active' : '' }}" href="{{url('customer-graph')}}">Customer Leads Graph</a></li>
                @endcan
                
                @can('view inventory reports')
                <li class="nav-item"> <a class="nav-link {{ $isRepInventory ? 'active' : '' }}" href="{{ url('inventory-reports') }}"> Inventory Report </a></li>
                @endcan

                @can('view expense report')
                <li class="nav-item"> <a class="nav-link {{ $isRepExpense ? 'active' : '' }}" href="{{url('expense-reports')}}">Expense Report</a></li>
                @endcan

                @canany(['view payment report', 'view reports', 'view expense report', 'view bookings report'])
                <li class="nav-item"> <a class="nav-link {{ $isRepPayments ? 'active' : '' }}" href="{{url('payment-reports')}}">Payment Report</a></li>
                @endcanany

                @can('view profit & loss graph')
                <li class="nav-item"> <a class="nav-link {{ $isRepProfitLoss ? 'active' : '' }}" href="{{url('profit-loss-report')}}">Profit & Loss Graph</a></li>
                @endcan

                <li class="nav-item"> <a class="nav-link {{ $isRepLimits ? 'active' : '' }}" href="{{ route('company.limits') }}">Plan Limits</a></li>

              </ul>

            </div>

          </li>
          @endcan

          @can('view quotations')
          @php
            $isQuotationsCreate = request()->routeIs('quotations.create') || request()->is('send-quotation*');
            $isQuotationsList = (request()->routeIs('quotations.index', 'quotations.show') || request()->is('quotations*') || request()->is('quote*')) && !$isQuotationsCreate;
            $isQuotations = $isQuotationsList || $isQuotationsCreate;
          @endphp
          <li class="nav-item {{ $isQuotations ? 'active' : '' }}">

              <a class="nav-link" data-toggle="collapse" href="#quote" aria-expanded="{{ $isQuotations ? 'true' : 'false' }}" aria-controls="quote">

              <i class="fa fa-question-circle menu-icon" aria-hidden="true"></i>

                <span class="menu-title">Quotation</span>

                <i class="menu-arrow"></i>

              </a>

              <div class="collapse {{ $isQuotations ? 'show' : '' }}" id="quote" data-parent="#sidebar .nav">

                <ul class="nav flex-column sub-menu">

                  <li class="nav-item"> <a class="nav-link {{ $isQuotationsList ? 'active' : '' }}" href="{{url('quotations')}}">Quotations</a></li>

                  @can('send quotations')
                  <li class="nav-item"> <a class="nav-link {{ $isQuotationsCreate ? 'active' : '' }}" href="{{url('send-quotation')}}">Send Quote </a></li>
                  @endcan

                </ul>

              </div>

            </li>
            @endcan

          @role('admin')
          @php
            $isUsersList = request()->routeIs('users.*') || request()->is('users*') || request()->is('user/*');
            $isRolesList = request()->routeIs('roles.*') || request()->is('roles*') || request()->is('role/*');
            $isScheduleList = request()->routeIs('work_schedule.*', 'schedule.status') || request()->is('work-schedule*');
            $isUserSettings = $isUsersList || $isRolesList || $isScheduleList;
          @endphp
          <li class="nav-item {{ $isUserSettings ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#general-pages" aria-expanded="{{ $isUserSettings ? 'true' : 'false' }}" aria-controls="general-pages">

              <i class="fa fa-user-plus menu-icon"></i>

              <span class="menu-title">User Settings</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ $isUserSettings ? 'show' : '' }}" id="general-pages" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">

                <li class="nav-item"> <a class="nav-link {{ $isUsersList ? 'active' : '' }}" href="{{route('users.index')}}"> Users </a></li>

                <li class="nav-item"> <a class="nav-link {{ $isRolesList ? 'active' : '' }}" href="{{url('roles')}}"> Roles </a></li>

                {{-- <li class="nav-item"> <a class="nav-link {{ $isScheduleList ? 'active' : '' }}" href="{{route('work_schedule.index')}}"> working schedule </a></li> --}}

              </ul>

            </div>

          </li>
          @endrole
          
          {{-- @can('view attendance')
          @php
            $isWorkHours = request()->routeIs('work_hours') || request()->is('work-hours*');
            $isPayrolls = request()->routeIs('payrolls.index', 'user.monthly.payslip') || request()->is('user/payrolls*');
            $isAttList = request()->routeIs('attendance', 'employee_attendance') || request()->is('attendance*') || request()->is('employee_attendance*');
            $isAttendance = $isAttList || $isWorkHours || $isPayrolls;
          @endphp
          <li class="nav-item {{ $isAttendance ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#attendance-pages" aria-expanded="{{ $isAttendance ? 'true' : 'false' }}" aria-controls="attendance-pages">

              <i class="fas fa-clipboard-list menu-icon"></i>

              <span class="menu-title">Employee Attendance</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ $isAttendance ? 'show' : '' }}" id="attendance-pages" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">

                <li class="nav-item"> <a class="nav-link {{ $isAttList ? 'active' : '' }}" href="{{ route('attendance') }}">Attendance</a></li>

                @can('view work hours')
                <li class="nav-item"> <a class="nav-link {{ $isWorkHours ? 'active' : '' }}" href="{{ route('work_hours') }}"> Work Hours </a></li>
                @endcan

                @can('view payrolls')
                <li class="nav-item"> <a class="nav-link {{ $isPayrolls ? 'active' : '' }}" href="{{ route('payrolls.index') }}"> Monthly Payrolls </a></li>
                @endcan
                
              </ul>

            </div>

          </li>
          @endcan --}}

          @can('view settings')
          @php
            $isThemeSetting = request()->routeIs('theme.settings', 'theme.edit') || request()->is('theme-settings*');
            $isPaymentMethods = request()->routeIs('payment-methods.*') || request()->is('payment-methods*');
            $isSettings = $isThemeSetting || $isPaymentMethods || request()->is('settings*') || request()->is('message-setting*') || request()->is('email-settings*') || request()->is('review-settings*');
          @endphp
          <li class="nav-item {{ $isSettings ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#icons" aria-expanded="{{ $isSettings ? 'true' : 'false' }}" aria-controls="icons">

              <i class="fa fa-cogs menu-icon"></i>

              <span class="menu-title">Settings</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ $isSettings ? 'show' : '' }}" id="icons" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu"> 
                @can('general settings')
                <li class="nav-item"> <a class="nav-link {{ $isThemeSetting ? 'active' : '' }}" href="{{url('theme-settings')}}"> General Setting</a></li>
                @endcan
                <li class="nav-item"> <a class="nav-link {{ $isPaymentMethods ? 'active' : '' }}" href="{{route('payment-methods.index')}}"> Payment Methods</a></li>
              </ul>

            </div>

          </li>
          @endcan

          @can('view expenses')
          @php
            $isExpCreate = request()->routeIs('expenses.create') || request()->is('expenses/expenses-create*');
            $isExpTypes = request()->routeIs('expense_types.*') || request()->is('expense-types*');
            $isExpList = (request()->routeIs('expenses.index', 'expenses.show', 'expenses.edit') || request()->is('expenses*')) && !$isExpCreate && !$isExpTypes;
            $isExpenses = $isExpList || $isExpCreate || $isExpTypes;
          @endphp
          <li class="nav-item {{ $isExpenses ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#expenses" aria-expanded="{{ $isExpenses ? 'true' : 'false' }}" aria-controls="expenses">

              <i class="fas fa-money-bill menu-icon"></i>

              <span class="menu-title">Other Expenses</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ $isExpenses ? 'show' : '' }}" id="expenses" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">

                <li class="nav-item"> <a class="nav-link {{ $isExpList ? 'active' : '' }}" href="{{url('expenses')}}"> Expenses </a></li>

              </ul>

                <ul class="nav flex-column sub-menu">
                @can('create expenses')
                <li class="nav-item"> <a class="nav-link {{ $isExpCreate ? 'active' : '' }}" href="{{url('expenses/expenses-create')}}">Add Expense </a></li>
                @endcan
                </ul>

                <ul class="nav flex-column sub-menu">
                @can('view expense types')
                <li class="nav-item"> <a class="nav-link {{ $isExpTypes ? 'active' : '' }}" href="{{url('expense-types')}}">List Expense Types </a></li>
                @endcan
                </ul>

            </div>

          </li>
          @endcan

          @can('view templates')
          @php
            $isEmailTemplates = request()->routeIs('email_template.*') || request()->is('email-template*');
            $isSmsTemplates = request()->routeIs('template.*') || (request()->is('template*') && !request()->is('email-template*'));
            $isTemplates = $isEmailTemplates || $isSmsTemplates;
          @endphp
          <li class="nav-item {{ $isTemplates ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#page-layouts" aria-expanded="{{ $isTemplates ? 'true' : 'false' }}" aria-controls="page-layouts">

              <i class="fab fa-wpforms menu-icon"></i>

              <span class="menu-title">Templates</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ $isTemplates ? 'show' : '' }}" id="page-layouts" data-parent="#sidebar .nav">

              <ul class="nav flex-column sub-menu">

                  @can('view email templates')
                  <li class="nav-item"> <a class="nav-link {{ $isEmailTemplates ? 'active' : '' }}" href="{{url('/email-template')}}">Email Templates</a></li>
                  @endcan

                  @can('view sms templates')
                  <li class="nav-item d-none d-lg-block"> <a class="nav-link {{ $isSmsTemplates ? 'active' : '' }}" href="{{url('/template')}}">SMS Templates</a></li>
                  @endcan

                </ul>

            </div>

          </li>
          @endcan

          @can('view marketing')
          @php
            $isSendMails = request()->routeIs('power_mailing.create') || request()->is('mailing');
            $isEmailHistory = request()->routeIs('mailing.history') || request()->is('mailing-history*');
            $isSmsMarketing = request()->routeIs('marketing.index') || request()->is('marketing');
            $isSmsHistory = request()->routeIs('sms.history') || request()->is('sms-history*');
            $isMarketing = $isSendMails || $isEmailHistory || $isSmsMarketing || $isSmsHistory || request()->is('postcard*');
          @endphp
          <li class="nav-item {{ $isMarketing ? 'active' : '' }}">

            <a class="nav-link" data-toggle="collapse" href="#marketing" aria-expanded="{{ $isMarketing ? 'true' : 'false' }}" aria-controls="marketing">

              <i class="fas fa-bullhorn menu-icon"></i>

              <span class="menu-title">Marketing</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse {{ $isMarketing ? 'show' : '' }}" id="marketing" data-parent="#sidebar .nav">

                <ul class="nav flex-column sub-menu">
                  @can('email marketing')
                  <li class="nav-item"> <a class="nav-link {{ $isSendMails ? 'active' : '' }}" href="{{ url('mailing') }}"> Send Mails </a></li>
                  @endcan
                </ul>

                <ul class="nav flex-column sub-menu">
                  @can('email history')
                  <li class="nav-item"> <a class="nav-link {{ $isEmailHistory ? 'active' : '' }}" href="{{ url('mailing-history') }}">Email history </a></li>
                  @endcan
                </ul>

                @can('sms marketing')
                <ul class="nav flex-column sub-menu">

                  <li class="nav-item"> <a class="nav-link {{ $isSmsMarketing ? 'active' : '' }}" href="{{url('/marketing')}}"> SMS Marketing </a></li>

                </ul>
                @endcan

                @can('sms history')
                <ul class="nav flex-column sub-menu">

                  <li class="nav-item"> <a class="nav-link {{ $isSmsHistory ? 'active' : '' }}" href="{{url('sms-history')}}"> SMS History </a></li>

                </ul>
                @endcan

            </div>

          </li>
          @endcan
          
          @can('view awards')
          @php
            $isAwards = request()->is('awards*') || request()->routeIs('awards.*');
          @endphp
          <li class="nav-item {{ $isAwards ? 'active' : '' }}">
            <a class="nav-link {{ $isAwards ? 'active' : '' }}" href="{{ route('awards.index') }}">
              <i class="fas fa-trophy menu-icon"></i>
              <span class="menu-title">Awards</span>
              <i class="menu-arrow"></i>
            </a>
          </li>
          @endcan

          @can('view holidays')
          @php
            $isHolidays = request()->is('add-holidays*') || request()->is('holidays*') || request()->routeIs('holidays.*');
          @endphp
          <li class="nav-item {{ $isHolidays ? 'active' : '' }}">
            <a class="nav-link {{ $isHolidays ? 'active' : '' }}" href="{{url('/add-holidays')}}" >
              <i class="fas fa-umbrella-beach menu-icon"></i>
              <span class="menu-title">Add Holidays</span>
              <i class="menu-arrow"></i>
            </a>
          </li>
          @endcan

          @role('admin')
          @php
            $isTickets = request()->is('tickets*') || request()->routeIs('tickets.*');
          @endphp
          <li class="nav-item {{ $isTickets ? 'active' : '' }}">
            <a class="nav-link {{ $isTickets ? 'active' : '' }}" href="{{ route('tickets.index') }}" >
              <i class="fas fa-ticket-alt menu-icon"></i>
              <span class="menu-title">Support Tickets</span>
              <i class="menu-arrow"></i>
            </a>
          </li>
          @endrole

        </ul>

      </nav>

      <!-- partial -->

      @yield('content')

      <footer class="footer non-printable" id="app-main-footer">

        <div class="d-sm-flex justify-content-center justify-content-sm-between">

          <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2024 <a href="https://www.bSaloon.co.uk/" target="_blank">bSaloon</a>. All rights reserved.</span>

        </div>

      </footer>

      <script>
        (function() {
          function placeAppFooter() {
            var footer = document.getElementById('app-main-footer');
            var mainPanel = document.querySelector('.main-panel');
            if (!mainPanel || !footer) return;

            // Remove any legacy duplicate footers inside the main panel
            var existingFooters = mainPanel.querySelectorAll('footer.footer');
            for (var i = 0; i < existingFooters.length; i++) {
              if (existingFooters[i] !== footer) {
                existingFooters[i].parentNode.removeChild(existingFooters[i]);
              }
            }

            // Ensure footer is placed inside .main-panel as its last child
            if (footer.parentElement !== mainPanel) {
              mainPanel.appendChild(footer);
            }
          }

          placeAppFooter();
          if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', placeAppFooter);
          }
        })();
      </script>

    </div>

    <!-- page-body-wrapper ends -->

  </div>

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

  <script>
    // This makes currency symbol available globally in JS
      window.currencySymbol = "{{ currency_symbol() }}";
      window.currencyCode    = "{{ currency_code() }}";
  </script>

  <script src="{{asset('js/off-canvas.js')}}"></script>

  <script src="{{asset('js/hoverable-collapse.js')}}"></script>

  <script src="{{asset('js/misc.js')}}?v=2.5"></script>

  <script src="{{asset('js/settings.js')}}"></script>

  <script src="{{asset('js/todolist.js')}}"></script>

  <script src="{{asset('js/select2.js')}}?v=2.1"></script>

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

      @if ($errors->any())
          @foreach ($errors->all() as $error)
              iziToast.error({
                  title: 'Validation Error',
                  message: '{!! addslashes($error) !!}',
                  position: 'bottomCenter',
                  icon: 'fas fa-exclamation-triangle',
                  timeout: 6000
              });
          @endforeach
      @endif
  });
  
  // Sidebar active state & accordion management
  $(function() {
      var currentPath = window.location.pathname.replace(/\/+$/, '');
      var currentUrl  = window.location.href.split(/[?#]/)[0].replace(/\/+$/, '');

      // 1) Find the single best matching link in the sidebar for the current URL path
      var $bestSubLink = null;
      var bestSubScore = -1;
      var $bestTopLink = null;
      var bestTopScore = -1;

      $('.sidebar .nav a.nav-link').each(function() {
          var href = $(this).attr('href');
          if (!href || href === '#' || href.indexOf('javascript') === 0) return;

          var linkUrl = href.split(/[?#]/)[0].replace(/\/+$/, '');
          var linkPath = '';
          try {
              linkPath = new URL(linkUrl, window.location.origin).pathname.replace(/\/+$/, '');
          } catch(e) {
              linkPath = linkUrl;
          }

          if (!linkPath) return;

          var isExactMatch = (currentUrl === linkUrl) || (currentPath === linkPath);
          var isPrefixMatch = (linkPath !== '/' && currentPath.indexOf(linkPath + '/') === 0);

          if (isExactMatch || isPrefixMatch) {
              var score = linkPath.length + (isExactMatch ? 1000 : 0);
              if ($(this).closest('.sub-menu').length) {
                  if (score > bestSubScore) {
                      bestSubScore = score;
                      $bestSubLink = $(this);
                  }
              } else if (!$(this).attr('data-toggle')) {
                  if (score > bestTopScore) {
                      bestTopScore = score;
                      $bestTopLink = $(this);
                  }
              }
          }
      });

      // 2) Cleanse any stray active/show classes (e.g., from browser-cached legacy scripts)
      //    and activate ONLY the single true matching module.
      if ($bestSubLink || $bestTopLink) {
          $('.sidebar .nav-item').removeClass('active');
          $('.sidebar .nav-link').removeClass('active');
          $('.sidebar .collapse').removeClass('show');
          $('.sidebar [data-toggle="collapse"]').attr('aria-expanded', 'false');

          if ($bestSubLink) {
              $bestSubLink.addClass('active');
              $bestSubLink.closest('.nav-item').addClass('active');
              var $parentCollapse = $bestSubLink.closest('.collapse');
              if ($parentCollapse.length) {
                  $parentCollapse.addClass('show');
                  var targetId = '#' + $parentCollapse.attr('id');
                  var $parentToggler = $('.sidebar [data-toggle="collapse"][href="' + targetId + '"]');
                  $parentToggler.attr('aria-expanded', 'true');
                  $parentToggler.closest('.nav-item').addClass('active');
              }
          } else if ($bestTopLink) {
              $bestTopLink.addClass('active');
              $bestTopLink.closest('.nav-item').addClass('active');
          }
      } else {
          // If no client match, ensure only one module is open and highlighted from Blade
          var $bladeActiveSub = $('.sidebar .sub-menu a.nav-link.active').first();
          if ($bladeActiveSub.length) {
              var $parentCollapse = $bladeActiveSub.closest('.collapse');
              $('.sidebar .collapse').not($parentCollapse).removeClass('show');
              $('.sidebar [data-toggle="collapse"]').not('[href="#' + $parentCollapse.attr('id') + '"]').attr('aria-expanded', 'false');
          }
      }

      // 3) Accordion toggle behavior on user click: when opening a collapse, close other collapses
      $('.sidebar [data-toggle="collapse"]').on('click', function() {
          var $this = $(this);
          var targetId = $this.attr('href');
          var $targetCollapse = $(targetId);

          if ($targetCollapse.length) {
              $('.sidebar .collapse').not($targetCollapse).collapse('hide');
          }
      });
  });
  </script>
  @endpush

  @stack('scripts')
</body>

</html>

