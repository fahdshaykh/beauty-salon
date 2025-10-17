<!DOCTYPE html>

<html lang="en">





<head>

  <!-- Required meta tags -->

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">



  <title><?php echo $__env->yieldContent('title'); ?></title>

  <!-- plugins:css -->

  <link rel="stylesheet" href="<?php echo e(asset('vendors/iconfonts/font-awesome/css/all.min.css')); ?>">

  <link rel="stylesheet" href="<?php echo e(asset('vendors/css/vendor.bundle.base.css')); ?>">

  <link rel="stylesheet" href="<?php echo e(asset('vendors/css/vendor.bundle.addons.css')); ?>">

  <!-- endinject -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <script src="<?php echo e(asset('js/jquery.postcodes.js')); ?>"></script>

  <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">

  <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-datetimepicker.css')); ?>">

  <link rel="shortcut icon" href="../../images/favicon.png" />



</head>

<style>

@media  only print {

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

        <!-- <a class="navbar-brand brand-logo" href="<?php echo e(url('/home')); ?>"><img class="img-fluid" src="<?php if(Session::get('site_logo')): ?><?php echo e(asset('company_thumbnail/'.Session::get('site_logo'))); ?> <?php else: ?> <?php echo e(asset('profiles/power_diary.png')); ?> <?php endif; ?>" alt="logo"/></a> -->

        <a class="navbar-brand brand-logo" href="<?php echo e(url('/home')); ?>"><?php echo e(Session::get('site_name')); ?></a>

        <a class="navbar-brand brand-logo-mini" href="<?php echo e(url('/home')); ?>"><img class="img-fluid" src="<?php if(Session::get('smart_logo')): ?><?php echo e(asset('company_thumbnail/'.Session::get('smart_logo'))); ?> <?php else: ?> <?php echo e(asset('profiles/power_d.png')); ?> <?php endif; ?>" alt="logo"/></a>

      </div>

      <div class="navbar-menu-wrapper d-flex align-items-stretch">

        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">

          <span class="fas fa-bars"></span>

        </button>
       

        <ul class="navbar-nav navbar-nav-right">

        <li class="nav-item dropdown">
              <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                  <i class="fas fa-bell mx-0"></i>
                  <span class="count"><?php echo e(auth()->user()->unreadNotifications->count()); ?></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                  
                  <div class="dropdown-header">
                      <p class="mb-0 font-weight-normal float-left">
                          You have <?php echo e(auth()->user()->unreadNotifications->count()); ?> new notifications
                      </p>
                      <a href="<?php echo e(route('notifications.all')); ?>" class="badge badge-pill badge-warning float-right">View all</a>
                  </div>

                  <div class="dropdown-divider mt-5"></div>

                  <?php $__empty_1 = true; $__currentLoopData = auth()->user()->unreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                      <?php if($notification->data['company_id'] == auth()->user()->company_id): ?>
                          <a href="<?php echo e(route('customers.edit', $notification->data['customer_id'])); ?>" class="dropdown-item preview-item">
                              <div class="preview-thumbnail">
                                  <div class="preview-icon bg-info">
                                      <i class="far fa-envelope mx-0"></i>
                                  </div>
                              </div>
                              <div class="preview-item-content">
                                  <h6 class="preview-subject font-weight-medium"><?php echo e($notification->data['message']); ?></h6>
                                  <p class="font-weight-light small-text">
                                      <?php echo e($notification->created_at->diffForHumans()); ?>

                                  </p>
                              </div>
                          </a>
                      <?php endif; ?>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                      <a class="dropdown-item text-center text-muted">No new notifications</a>
                  <?php endif; ?>
              </div>
          </li>

        

          <li class="nav-item nav-profile dropdown">

            <a class="nav-link dropdown-toggle" href="<?php echo e(url('user/profile')); ?>/<?php echo Auth::user()->id; ?>" data-toggle="dropdown" id="profileDropdown">

            <img src="<?php if(Auth::user()->image): ?><?php echo e(asset('profiles/'.Auth::user()->image)); ?> <?php else: ?> <?php echo e(asset('profiles/default-avatar.jpg')); ?> <?php endif; ?>" alt="Profile"/>

            </a>

            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">

              <a class="dropdown-item" href="<?php echo e(url('user/profile')); ?>/<?php echo Auth::user()->id; ?>">

                <i class="fas fa-cog text-primary"></i>

                Settings

              </a>

              <div class="dropdown-divider"></div>

              <a class="dropdown-item" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                <i class="fas fa-power-off text-primary"></i>

                Logout

              </a>

              <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">

                <?php echo csrf_field(); ?>

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

                <img src="<?php if(Auth::user()->image): ?><?php echo e(asset('profiles/'.Auth::user()->image)); ?> <?php else: ?> <?php echo e(asset('profiles/default-avatar.jpg')); ?> <?php endif; ?>" alt="image"/>

              </div>

              <div class="profile-name">

                <p class="name">

                  Welcome <?php echo e(Auth::user()->name); ?>


                </p>

                <p class="designation">

                  <?php if(auth()->user()->role_id == 1): ?>

                    Admin

                  <?php else: ?> 

                    User

                  <?php endif; ?>

                </p>

              </div>

            </div>

          </li>

          <li class="nav-item">

            <a class="nav-link" href="<?php echo e(url('/home')); ?>">

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

          



          <?php if(auth()->user()->company_id == 1): ?>

            <li class="nav-item <?php echo e(Request::is('companies*') ? 'active' : ''); ?>">

              <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="true" aria-controls="ui-basic">

                <i class="fas fa-industry menu-icon"></i>

                <span class="menu-title">Company</span>

                <i class="menu-arrow"></i>

              </a>

              <div class="collapse" id="ui-basic">

                <ul class="nav flex-column sub-menu">

                  <li class="nav-item"> <a class="nav-link" href="<?php echo e(route('companies.index')); ?>"> Company Listing </a></li>



                  <li class="nav-item"> <a class="nav-link" href="<?php echo e(route('companies.create')); ?>"> Create Company </a></li>

                

                </ul>

              </div>

            </li>

            <li class="nav-item <?php echo e(Request::is('companies*') ? 'active' : ''); ?>">

              <a class="nav-link" data-toggle="collapse" href="#ui-company" aria-expanded="true" aria-controls="ui-company">

                <i class="fas fa-industry menu-icon"></i>

                <span class="menu-title">Reports</span>

                <i class="menu-arrow"></i>

              </a>

              <div class="collapse" id="ui-company">

                <ul class="nav flex-column sub-menu">

                  <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('company-graph-report')); ?>">Company Bookings</a></li>

                  <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('company-expense-reports')); ?>">Expense Report</a></li>

                  <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('company-inventory-reports')); ?>">Inventory Report</a></li>

                </ul>

              </div>

            </li>

          <?php else: ?>
         

         

          <li class="nav-item <?php echo e(Request::is('categories/*') ? 'active' : ''); ?>">

            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="true" aria-controls="ui-basic">

              <i class="fas fa-briefcase menu-icon"></i>

              <span class="menu-title">Category</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse" id="ui-basic">

              <ul class="nav flex-column sub-menu">

              <?php if(in_array("1,1", session()->get('all_rp')['0'])): ?>

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(route('categories.index')); ?>"> Category Listing </a></li>

                <?php endif; ?>

                <?php if(in_array("1,2", session()->get('all_rp')['0'])): ?>

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('categories/category-create')); ?>"> Create Category </a></li>

                <?php endif; ?>

              </ul>

            </div>

          </li>

        
          <li class="nav-item <?php echo e(Request::is('inventories*') || Request::is('inventories/*') || Request::is('inventories_purchases') ? 'active' : ''); ?>">

            <a class="nav-link" data-toggle="collapse" href="#ui-basico" aria-expanded="true" aria-controls="ui-basico">

              <i class="fas fa-briefcase menu-icon"></i>

              <span class="menu-title">Inventories</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse <?php echo e(Request::is('inventories*') || Request::is('inventories_purchases*') ? 'show' : ''); ?>" id="ui-basico">

              <ul class="nav flex-column sub-menu">

              <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('inventories*') ? 'active' : ''); ?>" href="<?php echo e(route('inventories.index')); ?>"> Inventory Items </a></li>
              
              <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('inventories_purchases') ? 'active' : ''); ?>" href="<?php echo e(route('inventories_purchases.index')); ?>"> Inventory  Purchases</a></li>

              <li class="nav-item"> <a class="nav-link" href="<?php echo e(route('inventories.available')); ?>"> Inventory Available </a></li>

              <li class="nav-item"> <a class="nav-link" href="<?php echo e(route('suppliers.index')); ?>"> Suppliers </a></li>

              </ul>

            </div>

          </li>

          

          <li class="nav-item <?php echo e(Request::is('services*') ? 'active' : ''); ?>">

            <a class="nav-link" data-toggle="collapse" href="#ui-advanced" aria-expanded="false" aria-controls="ui-advanced">

              <i class="fab fa-trello menu-icon"></i>

              <span class="menu-title">Services</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse" id="ui-advanced">

              <ul class="nav flex-column sub-menu">

                <?php if(in_array("2,1", session()->get('all_rp')['0'])): ?>

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(route('services.index')); ?>">Service Listing</a></li>

                <?php endif; ?>

                <?php if(in_array("2,2", session()->get('all_rp')['0'])): ?>

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('services/services-create')); ?>">Create Service</a></li>

                <?php endif; ?>

              </ul>

            </div>

          </li>

      

          

          <li class="nav-item <?php echo e(Request::is('customers*') ? 'active' : ''); ?>">

            <a class="nav-link" data-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">

              <i class="fa fa-users menu-icon"></i>

              <span class="menu-title">Customers</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse" id="form-elements">

              <ul class="nav flex-column sub-menu">

                <?php if(in_array("3,1", session()->get('all_rp')['0'])): ?>

                <li class="nav-item"><a class="nav-link  <?php echo e(Request::is('customers*') ? 'active' : ''); ?>" href="<?php echo e(route('customers.index')); ?>">Customer Listing</a></li>

                <?php endif; ?>

                <?php if(in_array("3,2", session()->get('all_rp')['0'])): ?>               

                <li class="nav-item"><a class="nav-link  <?php echo e(Request::is('customers*') ? 'active' : ''); ?>" href="<?php echo e(url('customers/customers-create')); ?>">Add Customer</a></li>

                <!-- <li class="nav-item"><a class="nav-link  <?php echo e(Request::is('customers*') ? 'active' : ''); ?>" href="<?php echo e(url('customers-import-view')); ?>">Import Customers</a></li> -->

                <?php endif; ?>

              </ul>

            </div>

          </li>

         

          

          <li class="nav-item <?php echo e(Request::is('service-bookings*') ? 'active' : ''); ?>">

            <a class="nav-link" data-toggle="collapse" href="#editors" aria-expanded="false" aria-controls="editors">

              <i class="fa fa-truck menu-icon"></i>

              <span class="menu-title">Bookings</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse" id="editors">

              <ul class="nav flex-column sub-menu">

                <?php if(in_array("4,1", session()->get('all_rp')['0'])): ?>

                <li class="nav-item"><a class="nav-link <?php echo e(Request::is('service-bookings*') ? 'active' : ''); ?>" href="<?php echo e(route('service-bookings.index')); ?>">Service Bookings</a></li>

                <?php endif; ?>

                <?php if(in_array("4,2", session()->get('all_rp')['0'])): ?>  

                <li class="nav-item"><a class="nav-link <?php echo e(Request::is('service-bookings/service-bookings-create') ? 'active' : ''); ?>" href="<?php echo e(url('service-bookings/service-bookings-create')); ?>">Create Booking</a></li>

                <?php endif; ?>

              </ul>

            </div>

          </li>
         

          <li class="nav-item <?php echo e(Request::is('reports*') ? 'active' : ''); ?>">

            <a class="nav-link" data-toggle="collapse" href="#charts" aria-expanded="false" aria-controls="charts">

              <i class="fas fa-file menu-icon"></i>

              <span class="menu-title">Reports</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse" id="charts">

              <ul class="nav flex-column sub-menu">

                <?php if(in_array("5,7", session()->get('all_rp')['0'])): ?> 

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('graph-report')); ?>">Services Report</a></li>

                <!-- <?php endif; ?>
                <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('get-heat-chart')); ?>">GeoMap Chart</a></li>
                <?php if(in_array("5,1", session()->get('all_rp')['0'])): ?>   -->

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('reports')); ?>">Bookings Report</a></li>

                <?php endif; ?>

                <?php if(in_array("5,3", session()->get('all_rp')['0'])): ?>  

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('employee-graph-report')); ?>">Employee Report</a></li>

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('customer-reports')); ?>">Customer Report</a></li>
                <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('customer-graph')); ?>">Customer Graph</a></li>
                <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('customer-country-graph')); ?>">Customer Country Graph</a></li>
                <!-- <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('zipcode-report')); ?>">PostCode Graph</a></li> -->

                <?php endif; ?>

                <?php if(in_array("5,5", session()->get('all_rp')['0'])): ?>  

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('expense-reports')); ?>">Expense Report</a></li>
                <!-- <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('profit-loss-report')); ?>">Profit & Loss Graph</a></li> -->

                <?php endif; ?>

              </ul>

            </div>

          </li>
          



        

          <!-- <li class="nav-item <?php echo e(Request::is('send-quotation*') ? 'active' : ''); ?>">

              <a class="nav-link" data-toggle="collapse" href="#quote" aria-expanded="false" aria-controls="quote">

              <i class="fa fa-question-circle menu-icon" aria-hidden="true"></i>

                <span class="menu-title">Quotation</span>

                <i class="menu-arrow"></i>

              </a>

              <div class="collapse <?php echo e(Request::is('send-quotation*') ? 'show' : ''); ?>" id="quote">

                <ul class="nav flex-column sub-menu">

                  <?php if(in_array("10,1", session()->get('all_rp')['0'])): ?>

                  <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('quotations*') ? 'active' : ''); ?>" href="<?php echo e(url('quotations')); ?>">Qutations</a></li>

                  <?php endif; ?>

                  <?php if(in_array("10,2", session()->get('all_rp')['0'])): ?>

                  <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('send-quotation*') ? 'active' : ''); ?>" href="<?php echo e(url('send-quotation')); ?>">Send Quote </a></li>

                  <?php endif; ?>

                </ul>`

              </div>

            </li> -->
          <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('award.index')); ?>">
              <i class="fa fa-puzzle-piece menu-icon"></i>
              <span class="menu-title">Award</span>
            </a>
          </li>

          <!-- <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('award.index')); ?>">
              <i class="fa fa-puzzle-piece menu-icon"></i>
              <span class="menu-title">Notifications</span>
            </a>
          </li> -->

          <li class="nav-item <?php echo e(Request::is('user*') || Request::is('role*') ? 'active' : ''); ?>">

            <a class="nav-link" data-toggle="collapse" href="#general-pages" aria-expanded="false" aria-controls="general-pages">

              <i class="fa fa-user-plus menu-icon"></i>

              <span class="menu-title">User Settings</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse <?php echo e(Request::is('user*') || Request::is('role*') ? 'show' : ''); ?>" id="general-pages">

              <ul class="nav flex-column sub-menu">

                <?php if(in_array("6,1", session()->get('all_rp')['0'])): ?> 

                <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('user*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>"> Users </a></li>

                <?php endif; ?>

                <?php if(in_array("7,1", session()->get('all_rp')['0'])): ?>  

                <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('role*') ? 'active' : ''); ?>" href="<?php echo e(url('roles')); ?>"> Roles </a></li>

                <?php endif; ?>
                
              </ul>

            </div>

          </li>
          

          <li class="nav-item <?php echo e(Request::is('attendance*') || Request::is('work_hours*') ? 'active' : ''); ?>">

            <a class="nav-link" data-toggle="collapse" href="#attendance-pages" aria-expanded="false" aria-controls="attendance-pages">

              <i class="fa fa-cogs menu-icon"></i>

              <span class="menu-title">Employee Attendance</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse <?php echo e(Request::is('attendance*') || Request::is('work_hours*') ? 'show' : ''); ?>" id="attendance-pages">

              <ul class="nav flex-column sub-menu">

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(route('attendance')); ?>">Attendance</a></li>

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(route('work_hours')); ?>"> Work Hours </a></li>

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(route('payrolls.index')); ?>"> Monthly Payrolls </a></li>
                
              </ul>

            </div>

          </li>


         <!-- <?php if(in_array("17,1", session()->get('all_rp')['0'])): ?>

         <li class="nav-item <?php echo e(Request::is('message-setting*') ? 'active' : ''); ?>">

            <a class="nav-link" data-toggle="collapse" href="#icons" aria-expanded="false" aria-controls="icons">

              <i class="fa fa-cogs menu-icon"></i>

              <span class="menu-title">Settings</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse <?php echo e(Request::is('message-setting*') ? 'show' : ''); ?>" id="icons">

              <ul class="nav flex-column sub-menu"> 

                <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('theme*') ? 'active' : ''); ?>" href="<?php echo e(url('theme-settings')); ?>"> General Setting</a></li>

              </ul>

            </div>

          </li>

         <?php endif; ?> -->

        

         <?php if(in_array("9,1", session()->get('all_rp')['0'])): ?>

         <li class="nav-item <?php echo e(Request::is('expenses*') ? 'active' : ''); ?>">

            <a class="nav-link" data-toggle="collapse" href="#expenses" aria-expanded="false" aria-controls="expenses">

              <i class="fa fa-minus-square menu-icon"></i>

              <span class="menu-title">Expenses</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse <?php echo e(Request::is('expenses*') ? 'show' : ''); ?>" id="expenses">

              <ul class="nav flex-column sub-menu">

              

                <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('expenses*') ? 'active' : ''); ?>" href="<?php echo e(url('expenses')); ?>"> Expenses </a></li>

              </ul>

            

                <?php if(in_array("9,2", session()->get('all_rp')['0'])): ?>

                <ul class="nav flex-column sub-menu">

                <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('expenses*') ? 'active' : ''); ?>" href="<?php echo e(url('expenses/expenses-create')); ?>">Add Expense </a></li>

                </ul>

                <?php endif; ?>

                <?php if(in_array("8,1", session()->get('all_rp')['0'])): ?>

                <ul class="nav flex-column sub-menu">

                <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('expense-types*') ? 'active' : ''); ?>" href="<?php echo e(url('expense-types')); ?>">List Expense Types </a></li>

                </ul>

                <?php endif; ?>

              

            </div>

          </li>

         <?php endif; ?>
        

         <!-- <?php if(in_array("15,1", session()->get('all_rp')['0'])): ?>

         <li class="nav-item">

            <a class="nav-link" data-toggle="collapse" href="#page-layouts" aria-expanded="false" aria-controls="page-layouts">

              <i class="fab fa-wpforms menu-icon"></i>

              <span class="menu-title">Templates</span>

              <i class="menu-arrow"></i>

            </a>

            <div class="collapse" id="page-layouts">

              <ul class="nav flex-column sub-menu">

                <li class="nav-item d-none d-lg-block"> <a class="nav-link <?php echo e(Request::is('template*') ? 'active' : ''); ?>" href="<?php echo e(url('/template')); ?>">Sms Templates</a></li>

                <li class="nav-item"> <a class="nav-link" href="<?php echo e(url('/email-template')); ?>">Email Templates</a></li>

              </ul>

            </div>

          </li>

          <?php endif; ?> -->

          

         <!-- <li class="nav-item <?php echo e(Request::is('marketing*') ? 'active' : ''); ?>">

            <a class="nav-link" data-toggle="collapse" href="#marketing" aria-expanded="false" aria-controls="marketing">

              <i class="fa fa-bullhorn menu-icon"></i>

              <span class="menu-title">Marketing</span>

              <i class="menu-arrow"></i>

            </a>

            

            <div class="collapse <?php echo e(Request::is('mailing*') ? 'show' : ''); ?>" id="marketing">

            <?php if(in_array("11,1", session()->get('all_rp')['0'])): ?>

              <ul class="nav flex-column sub-menu">

             

                <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('mailing*') ? 'active' : ''); ?>" href="<?php echo e(url('mailing')); ?>"> Email Marketing </a></li>

              </ul>

              <ul class="nav flex-column sub-menu">

             

                <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('mailing-history') ? 'active' : ''); ?>" href="<?php echo e(url('mailing-history')); ?>"> Email History </a></li>

              </ul>

              <?php endif; ?>

              <?php if(in_array("16,1", session()->get('all_rp')['0'])): ?>

              <ul class="nav flex-column sub-menu">

                <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('marketing*') ? 'active' : ''); ?>" href="<?php echo e(url('/marketing')); ?>"> SMS Marketing </a></li>

              </ul>

              <ul class="nav flex-column sub-menu">

             

                <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('marketing*') ? 'active' : ''); ?>" href="<?php echo e(url('sms-history')); ?>"> SMS History </a></li>

              </ul>

              <?php endif; ?>



              <?php if(in_array("16,3", session()->get('all_rp')['0'])): ?>

              <ul class="nav flex-column sub-menu">

                <li class="nav-item"> <a class="nav-link <?php echo e(Request::is('marketing*') ? 'active' : ''); ?>" href="<?php echo e(url('/postcard')); ?>"> Send PostCard </a></li>

              </ul>

              <?php endif; ?>

            </div>

          </li> -->

        

         

          <!-- <li class="nav-item">

            <a class="nav-link" href="<?php echo e(url('/marketing')); ?>" >

              <i class="fas fa-bell menu-icon"></i>

              <span class="menu-title">Marketing</span>

              <i class="menu-arrow"></i>

            </a>

          </li> -->

          <!-- <li class="nav-item">
            <a class="nav-link" href="<?php echo e(url('/add-holidays')); ?>" >
              <i class="fas fa-bell menu-icon"></i>
              <span class="menu-title">Add Holidays</span>
              <i class="menu-arrow"></i>
            </a>
          </li> -->

          

         <?php endif; ?>

       

        </ul>

      </nav>

      <!-- partial -->

      <?php echo $__env->yieldContent('content'); ?>

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

  <script src="<?php echo e(asset('vendors/js/vendor.bundle.base.js')); ?>"></script>

  <script src="<?php echo e(asset('vendors/js/vendor.bundle.addons.js')); ?>"></script>

  <!-- endinject -->

  <!-- Plugin js for this page-->

  <!-- End plugin js for this page-->

  <!-- inject:js -->

  <script src="<?php echo e(asset('js/off-canvas.js')); ?>"></script>

  <script src="<?php echo e(asset('js/hoverable-collapse.js')); ?>"></script>

  <script src="<?php echo e(asset('js/misc.js')); ?>"></script>

  <script src="<?php echo e(asset('js/settings.js')); ?>"></script>

  <script src="<?php echo e(asset('js/todolist.js')); ?>"></script>

  <script src="<?php echo e(asset('js/select2.js')); ?>"></script>

  <script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

  <script src="<?php echo e(asset('js/custom.js')); ?>"></script>

  <script src="<?php echo e(asset('js/form-validation.js')); ?>"></script>

  <script src="<?php echo e(asset('js/bt-maxLength.js')); ?>"></script>

  <script src="<?php echo e(asset('js/toastDemo.js')); ?>"></script>

  <!-- endinject -->

  <!-- Custom js for this page-->

  <script src="<?php echo e(asset('js/dropify.js')); ?>"></script>

  <script src="<?php echo e(asset('js/dashboard.js')); ?>"></script>

  <script src="<?php echo e(asset('js/company-report.js')); ?>"></script>

  <script src="<?php echo e(asset('js/data-table.js')); ?>"></script>

  <script src='https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js'></script>

  <!-- <script src="<?php echo e(asset('js/bootstrap-datetimepicker.min.js')); ?>"></script> -->

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

  <?php echo $__env->yieldContent('pagescript'); ?>

</body>





</html>

<?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/layouts/admin.blade.php ENDPATH**/ ?>