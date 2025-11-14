<?php

use App\Http\Controllers\{
    Admin\LoginController,
    Admin\DashboardController,
    Admin\CompanyController,
    Admin\CompanyReportController,
    HomeController,
    CategoryController,
    ServicesController,
    CustomerController,
    ServiceBookingController,
    NotificationController,
    PowerMailingController,
    MarketingController,
    ReportController,
    UserController,
    RoleController,
    SettingController,
    MailController,
    QuoteController,
    ExpenseTypeController,
    ExpenseController,
    PostCardController,
    TemplateController,
    JsGridController,
    InventoryController,
    InventoryPurchasesController,
    ReferredCustomerController,
    SupplierController,
    SupplierPaymentController,
    EmployeeController,
    EmployeeAttendanceController,
    PayrollController,
    AwardController,
    BookingController,
    EmailTemplateController,
    StripeController
};
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ──────────────────────────────────────
// 1. ADMIN ROUTES (guest + auth)
// ──────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest Admin (not logged in)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);
    });

    // Authenticated Admin
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Companies CRUD
        Route::resource('companies', CompanyController::class)->names([
            'index'   => 'companies.index',
            'create'  => 'companies.create',
            'store'   => 'companies.store',
            'show'    => 'companies.show',
            'edit'    => 'companies.edit',
            'update'  => 'companies.update',
            'destroy' => 'companies.destroy',
        ]);

        Route::get('company-status', [CompanyController::class, 'company_status'])->name('companies.status');

        // ── Company Reports ──
        Route::get('company-graph-report', [CompanyReportController::class, 'company_graph_report'])->name('company.graph');
        Route::get('company-booking-chart', [CompanyReportController::class, 'company_booking_chart'])->name('company.booking');
        Route::get('company-expense-reports', [CompanyReportController::class, 'expense_index'])->name('company.expense');
        Route::get('company/record/expense', [CompanyReportController::class, 'expense_record'])->name('company.expense_record');
        Route::get('company-inventory-reports', [CompanyReportController::class, 'inventory_index'])->name('company.inventory');
        Route::get('company/record/inventory', [CompanyReportController::class, 'inventory_record'])->name('company.inventory_record');
    });
});

// ──────────────────────────────────────
// 2. UTILITY ROUTES (Artisan Commands)
// ──────────────────────────────────────
Route::get('/clear-config', fn() => \Artisan::call('config:clear') ? \Artisan::output() : 'Config cleared.');
Route::get('/clear-cache', fn() => \Artisan::call('cache:clear') ? \Artisan::output() : 'Cache cleared.');
Route::get('/clear-view', fn() => \Artisan::call('view:clear') ? \Artisan::output() : 'Views cleared.');

// ──────────────────────────────────────
// 3. PUBLIC / GUEST ROUTES
// ──────────────────────────────────────
Route::get('sms-invoice-mail/{id}/{company}', [MailController::class, 'sms_invoice_mail']);
Route::get('optout/{companyid}/{customerid}/{opt}', [MarketingController::class, 'emailoptout'])
    ->where(['companyid' => '[0-9]+', 'customerid' => '[0-9]+', 'opt' => '[0-9]+']);
Route::get('smsoptout/{companyid}/{customerid}/{opt}', [MarketingController::class, 'smsoptout'])
    ->where(['companyid' => '[0-9]+', 'customerid' => '[0-9]+', 'opt' => '[0-9]+']);


// ──────────────────────────────────────
// 4. COMPANY MIDDLEWARE ROUTES (No Auth Required)
// ──────────────────────────────────────
Route::middleware('company')->group(function () {
    Route::resource('templates', EmailTemplateController::class);
    Route::get('/email-template-status', [EmailTemplateController::class, 'templateStatus'])->name('templates.status');
});

Auth::routes();

// ──────────────────────────────────────
// 5. AUTH + COMPANY MIDDLEWARE ROUTES
// ──────────────────────────────────────
Route::middleware(['auth', 'company'])->group(function () {

    // Root Redirect
    Route::get('/', fn() => redirect('/home'));

    // Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // ── Notifications ──
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.all');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // ── Categories ──
    Route::get('categories/category-create', [CategoryController::class, 'create']);
    Route::get('category-status', [CategoryController::class, 'categoryStatus'])->name('categories.status');
    Route::get('categories/destroy/{id}', [CategoryController::class, 'destroy']);
    Route::resource('categories', CategoryController::class);

    // ── Services ──
    Route::get('services/services-create', [ServicesController::class, 'create']);
    Route::get('servicestatus', [ServicesController::class, 'servicestatus'])->name('services.status');
    Route::get('services/destroy/{id}', [ServicesController::class, 'destroy']);
    Route::get('get-services-by-category', [ServicesController::class, 'get_services']);
    Route::resource('services', ServicesController::class);

    // ── Customers ──
    Route::get('customers/customers-create', [CustomerController::class, 'create']);
    Route::get('customerstatus', [CustomerController::class, 'customerstatus'])->name('customers.status');
    Route::get('customers/destroy/{id}', [CustomerController::class, 'destroy']);
    Route::get('bookings-history/{id}', [CustomerController::class, 'bookingsHistory']);
    Route::get('/all-customer-export', [CustomerController::class, 'exportCsv'])->name('customers.export');
    Route::get('customers-import-view', [CustomerController::class, 'customer_import_view'])->name('customers.import.view');
    Route::post('import-customer', [CustomerController::class, 'importCsv'])->name('customers.import');
    Route::get('user/profile/{id}', [CustomerController::class, 'profile']);
    Route::post('user/profile-update/{id}', [CustomerController::class, 'profile_update']);
    Route::get('/customers/import', [CustomerController::class, 'showImportForm'])->name('customers.import.form');
    Route::post('/customers/import', [CustomerController::class, 'import'])->name('customers.import');
    Route::resource('customers', CustomerController::class);

    // ── Service Bookings ──
    Route::get('service-bookings/service-bookings-create', [ServiceBookingController::class, 'create']);
    Route::get('service-bookings/edit/{id}', [ServiceBookingController::class, 'edit']);
    Route::post('service-bookings/update/{id}', [ServiceBookingController::class, 'update']);
    Route::get('service-bookings/destroy/{id}', [ServiceBookingController::class, 'destroy']);
    Route::post('/service-bookings/cancel', [ServiceBookingController::class, 'cancel'])->name('service-bookings.cancel');
    Route::get('view-booking/{invoice}', [ServiceBookingController::class, 'show'])->name('service-bookings.show');
    Route::get('show-job-detail/{invoice}', [ServiceBookingController::class, 'show_job_detail']);
    Route::post('change-booking-status', [ServiceBookingController::class, 'book_status']);
    Route::get('fetch-job-notes/{bookingid}', [ServiceBookingController::class, 'fetch_job_notes']);
    Route::get('delete-notes/{id}', [ServiceBookingController::class, 'delete_notes']);
    Route::post('/update-job-notes', [ServiceBookingController::class, 'update_notes']);
    Route::post('change-calendar-status', [ServiceBookingController::class, 'calendar_status']);
    Route::post('check-booking-availability', [ServiceBookingController::class, 'checkAvailability']);
    Route::get('get-booking-orders', [ServiceBookingController::class, 'get_booking_orders']);
    Route::get('get-booking-date/{date?}', [ServiceBookingController::class, 'create']);
    Route::get('get-booking-date/{date?}/success', [ServiceBookingController::class, 'create']);
    Route::get('get-user-bookings/{user_id}', [ServiceBookingController::class, 'get_user_bookings']);
    Route::get('/get-employee-services', [ServiceBookingController::class, 'getEmployeeServices'])->name('getEmployeeServices');
    Route::post('booking-status', [ServiceBookingController::class, 'change_boking_status']);
    Route::get('/send_review_sms/{invoice}', [ServiceBookingController::class, 'send_review_sms']);
    Route::post('invoice-sms', [ServiceBookingController::class, 'invoice_sms']);
    Route::get('service-price', [ServiceBookingController::class, 'service_price']);
    Route::resource('service-bookings', ServiceBookingController::class);

    // ── Mailing & Marketing ──
    Route::post('/customer-email-filter', [PowerMailingController::class, 'email_filter']);
    Route::get('mailing-history', [PowerMailingController::class, 'mailing_history']);
    Route::post('/email-history-filter', [PowerMailingController::class, 'mailing_history_filter']);
    Route::get('sms-history', [MarketingController::class, 'sms_history']);
    Route::post('/sms-history-filter', [MarketingController::class, 'sms_history_filter']);
    Route::get('cancel-schedule', [MarketingController::class, 'cancel_scheduled_sms']);
    Route::post('/whatsapp-status', [MarketingController::class, 'whatsapp_status']);
    Route::get('/marketing', [MarketingController::class, 'index']);
    Route::get('/postcard', [MarketingController::class, 'postcard']);
    Route::post('/send-message', [MarketingController::class, 'send_message']);
    Route::get('/marketing-search', [MarketingController::class, 'find_customers']);
    Route::get('/postcard-search', [MarketingController::class, 'postcard_customers']);
    Route::get('/template-search', [MarketingController::class, 'find_templates']);
    Route::get('get-template-preview/{id}', [PowerMailingController::class, 'get_template_preview']);
    Route::get('mailing', [PowerMailingController::class, 'create'])->name('power_mailing.create');
    Route::post('send-mailing', [PowerMailingController::class, 'send']);

    // ── Reports ──
    Route::get('reports', [ReportController::class, 'index']);
    Route::get('record/find', [ReportController::class, 'record']);
    Route::get('report-export/{start_date}/{end_date}/{category_id}/{service_id}/{status_id}/{payment_type}', [ReportController::class, 'reportCsv']);
    Route::get('customer-reports', [ReportController::class, 'customer_index']);
    Route::get('customer-graph-report', [ReportController::class, 'customer_graph_report']);
    Route::get('customer-graph', [ReportController::class, 'customer_graph']);
    Route::get('customer-country-graph-report', [ReportController::class, 'customer_country_graph_report']);
    Route::get('customer-country-graph', [ReportController::class, 'customer_country_graph']);
    Route::get('record/customer', [ReportController::class, 'customer_record']);
    Route::get('profit-loss-report', [ReportController::class, 'profit_loss_report']);
    Route::get('profit-loss-data', [ReportController::class, 'profit_loss_data']);
    Route::get('zipcode-report', [ReportController::class, 'zipcode_report']);
    Route::get('zipcode-data', [ReportController::class, 'zipcode_data']);
    Route::get('customer-report-by-date/{start_date}/{end_date}/{customer_id}', [ReportController::class, 'reportCustomerCsv']);
    Route::get('expense-reports', [ReportController::class, 'expense_index']);
    Route::get('record/expense', [ReportController::class, 'expense_record']);
    Route::get('expense-report/{start_date}/{end_date}/{expense_type}/{method}', [ReportController::class, 'reportExpenseCsv']);
    Route::get('graph-report', [ReportController::class, 'graph_report']);
    Route::get('get-booking-chart', [ReportController::class, 'get_booking_chart']);
    Route::get('get-heat-chart', [ReportController::class, 'heat_graph_report']);
    Route::post('show-heat-chart', [ReportController::class, 'show_heat_report']);

    // ── Users & Roles ──
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('user/user-create', [UserController::class, 'create']);
    Route::get('users/deleted', [UserController::class, 'deleted_users']);
    Route::post('users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('user-status', [UserController::class, 'user_status']);
    Route::get('user/edit/{id}', [UserController::class, 'edit']);
    Route::put('user/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('user/destroy/{id}', [UserController::class, 'destroy']);
    Route::get('user/delete_permanent/{id}', [UserController::class, 'delete_permanent']);
    Route::get('user/undelete/{id}', [UserController::class, 'undelete']);
    Route::get('user/payslip/{id}', [UserController::class, 'payslip']);

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('role/role-create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('role/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('role/edit/{role}', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('role/update/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('role/destroy/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // ── Settings ──
    Route::get('message-setting', [SettingController::class, 'message_index']);
    Route::get('message-status', [SettingController::class, 'message_status']);
    Route::get('tax-status', [SettingController::class, 'tax_status']);
    Route::post('tax-settings-update', [SettingController::class, 'tax_settings_update']);
    Route::post('socialmedia-update', [SettingController::class, 'socialmeida_update']);
    Route::get('message-setting/edit/{id}', [SettingController::class, 'message_settings_edit']);
    Route::post('message-setting/update/{id}', [SettingController::class, 'message_settings_update']);
    Route::get('review-status', [SettingController::class, 'review_status']);
    Route::get('review-settings/edit', [SettingController::class, 'review_settings_edit']);
    Route::post('review-settings-update', [SettingController::class, 'review_settings_update']);
    Route::get('email-settings', [SettingController::class, 'email_settings']);
    Route::get('email-settings/edit/{id}', [SettingController::class, 'email_settings_edit']);
    Route::post('email-settings-update', [SettingController::class, 'email_settings_update']);
    Route::post('clicksend-update', [SettingController::class, 'clicksend_update']);
    Route::get('theme-settings', [SettingController::class, 'theme_settings']);
    Route::get('theme-settings/edit/{id}', [SettingController::class, 'theme_settings_edit']);
    Route::post('theme-settings-update', [SettingController::class, 'theme_settings_update']);
    Route::post('update-booking-message', [SettingController::class, 'booking_message_update']);

    // ── Quotations ──
    Route::get('quotations', [QuoteController::class, 'index']);
    Route::get('quote-view/{id}', [QuoteController::class, 'show']);
    Route::get('send-quotation', [QuoteController::class, 'create']);
    Route::post('quote-mail', [QuoteController::class, 'quote_mail']);
    Route::get('add-holidays', [QuoteController::class, 'add_holidays']);
    Route::post('update-holidays', [QuoteController::class, 'update_holidays']);
    Route::get('delete-holidays/{id}', [QuoteController::class, 'delete_holidays']);

    // ── Expenses ──
    Route::get('expense-types', [ExpenseTypeController::class, 'index']);
    Route::get('expense-types/create', [ExpenseTypeController::class, 'create']);
    Route::get('expense-type-status', [ExpenseTypeController::class, 'expense_type_status']);
    Route::post('expense-types/store', [ExpenseTypeController::class, 'store']);
    Route::get('expense-types/edit/{id}', [ExpenseTypeController::class, 'edit']);
    Route::post('expense-types/update/{id}', [ExpenseTypeController::class, 'update']);
    Route::get('expense-types/destroy/{id}', [ExpenseTypeController::class, 'destroy']);

    Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('expenses/expenses-create', [ExpenseController::class, 'create']);
    Route::post('expenses/store', [ExpenseController::class, 'store']);
    Route::get('expenses/edit/{id}', [ExpenseController::class, 'edit']);
    Route::post('expenses/update/{id}', [ExpenseController::class, 'update']);
    Route::get('expenses/destroy/{id}', [ExpenseController::class, 'destroy']);

    // ── Postcards ──
    Route::get('postcard', [PostCardController::class, 'index']);
    Route::get('postcard-info', [PostCardController::class, 'postcard_info']);
    Route::post('postcard-send', [PostCardController::class, 'sendpost_card']);
    Route::get('postcard-sample', [PostCardController::class, 'postcard_samples']);
    Route::get('postcard-history', [PostCardController::class, 'postcard_history']);
    Route::get('postcard-download/{id}', [PostCardController::class, 'postcard_download']);

    // ── Email Templates (Custom) ──
    Route::get('/email-template', [TemplateController::class, 'email_templates']);
    Route::get('/email-template-create', [TemplateController::class, 'email_template_create']);
    Route::post('/email-template-store', [TemplateController::class, 'store_email_template']);
    Route::get('/email-template-edit/{id}', [TemplateController::class, 'edit_email_template']);
    Route::get('/email-template-json/{id}', [TemplateController::class, 'get_email_template_json']);
    Route::post('/email-template-update', [TemplateController::class, 'update_email_template']);
    Route::get('template-status', [TemplateController::class, 'templateStatus']);
    Route::resource('/template', TemplateController::class);

    // ── JS Grid (Job Notes) ──
    Route::resource('/add-job-notes', JsGridController::class);

    // ── Inventory ──
    Route::resource('/inventories', InventoryController::class);
    Route::get('inventories-available', [InventoryController::class, 'available'])->name('inventories.available');
    Route::resource('/inventories_purchases', InventoryPurchasesController::class);
    Route::post('/check-inventory-quantity', [InventoryController::class, 'checkQuantity']);

    // ── Suppliers ──
    Route::resource('suppliers', SupplierController::class);
    Route::resource('supplier-payments', SupplierPaymentController::class);
    Route::get('/suppliers/{id}/balance-summary', [SupplierController::class, 'balanceSummary'])->name('suppliers.balance_summary');

    // ── Referred Customers ──
    Route::resource('/referred_customers', ReferredCustomerController::class);

    // ── Employee Management ──
    Route::get('/employee-graph-report', [EmployeeController::class, 'employee_graph_report']);
    Route::get('/employee-booking-chart', [EmployeeController::class, 'employee_booking_chart']);
    Route::get('employee_attendance', [EmployeeAttendanceController::class, 'employee_attendance'])->name('employee_attendance');
    Route::post('/attendance/store', [EmployeeAttendanceController::class, 'store'])->name('attendance.store');
    Route::get('attendance', [EmployeeAttendanceController::class, 'attendance'])->name('attendance');
    Route::post('/update-exit-time', [EmployeeAttendanceController::class, 'updateExitTime'])->name('update.exit.time');
    Route::get('/work-hours', [EmployeeAttendanceController::class, 'work_hours'])->name('work_hours');

    // ── Payroll ──
    Route::get('/user/payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('user/monthly/payslip/{id}', [PayrollController::class, 'payslip'])->name('user.monthly.payslip');

    // ── Awards ──
    Route::resource('awards', AwardController::class);

    // ── Admin Booking Status Change ──
    Route::post('/admin/booking/change-status', [BookingController::class, 'changeStatus'])->name('admin.booking.change-status');

    // ── Payments (Stripe & PayPal) ──
    Route::get('/', [StripeController::class, 'index'])->name('service_bookings.index');
    Route::post('/checkout', [StripeController::class, 'checkout'])->name('checkout');
    Route::get('/success', [StripeController::class, 'success'])->name('success');

    Route::get('/redirect-to-paypal', function (Request $request) {
        $bookingId = $request->query('booking_id');
        $booking = \App\Models\ServiceBooking::findOrFail($bookingId);
        $paypalUrl = "https://www.paypal.com/checkout?amount={$booking->total_price}&currency=USD";
        return redirect()->to($paypalUrl);
    })->name('redirect.to.paypal');

    // ── Mail ──
    Route::get('invoice-mail/{id}', [MailController::class, 'invoice_mail']);
});