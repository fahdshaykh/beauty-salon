<?php

use App\Http\Controllers\{
    Admin\LoginController,
    Admin\DashboardController,
    Admin\CompanyController,
    Admin\CompanyReportController,
    Admin\AdminTicketController,
    Admin\CompanyLimitController,
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
    CalendarController,
    EmailTemplateController,
    StripeController,
    TicketController,
    WorkScheduleController
};
// use App\Http\Controllers\Admin\CompanyLimitController;
use Illuminate\Support\Facades\Route;

// ──────────────────────────────────────
// 1. ADMIN ROUTES (guest + auth)
// ──────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest Admin (not logged in)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.post');
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

        // Company Reports
        Route::get('company-graph-report', [CompanyReportController::class, 'company_graph_report'])->name('company.graph');
        Route::get('company-booking-chart', [CompanyReportController::class, 'company_booking_chart'])->name('company.booking');
        Route::get('company-expense-reports', [CompanyReportController::class, 'expense_index'])->name('company.expense');
        Route::get('company/record/expense', [CompanyReportController::class, 'expense_record'])->name('company.expense_record');
        Route::get('company-inventory-reports', [CompanyReportController::class, 'inventory_index'])->name('company.inventory');
        Route::get('company/record/inventory', [CompanyReportController::class, 'inventory_record'])->name('company.inventory_record');

        Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [AdminTicketController::class, 'index'])->name('index');
        Route::get('/{ticket}', [AdminTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('reply');
        Route::patch('/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('updateStatus');
        });

        Route::get('companies/{company}/limits', [CompanyLimitController::class, 'edit'])->name('limits.edit');
        Route::post('companies/{company}/limits', [CompanyLimitController::class, 'update'])->name('limits.update');

    });
});

// ──────────────────────────────────────
// 2. UTILITY ROUTES
// ──────────────────────────────────────
Route::get('/clear-config', fn() => \Artisan::call('config:clear') ? \Artisan::output() : 'Config cleared.')->name('clear.config');
Route::get('/clear-cache', fn() => \Artisan::call('cache:clear') ? \Artisan::output() : 'Cache cleared.')->name('clear.cache');
Route::get('/clear-view', fn() => \Artisan::call('view:clear') ? \Artisan::output() : 'Views cleared.')->name('clear.view');

// ──────────────────────────────────────
// 3. PUBLIC / GUEST ROUTES
// ──────────────────────────────────────
Route::get('sms-invoice-mail/{id}/{company}', [MailController::class, 'sms_invoice_mail'])->name('sms.invoice.mail');
Route::get('optout/{companyid}/{customerid}/{opt}', [MarketingController::class, 'emailoptout'])
    ->where(['companyid' => '[0-9]+', 'customerid' => '[0-9]+', 'opt' => '[0-9]+'])
    ->name('email.optout');
Route::get('smsoptout/{companyid}/{customerid}/{opt}', [MarketingController::class, 'smsoptout'])
    ->where(['companyid' => '[0-9]+', 'customerid' => '[0-9]+', 'opt' => '[0-9]+'])
    ->name('sms.optout');

// ──────────────────────────────────────
// 4. COMPANY MIDDLEWARE ROUTES (No Auth Required)
// ──────────────────────────────────────
Route::middleware('company')->group(function () {
    Route::resource('templates', EmailTemplateController::class)->names([
        'index'   => 'templates.index',
        'create'  => 'templates.create',
        'store'   => 'templates.store',
        'show'    => 'templates.show',
        'edit'    => 'templates.edit',
        'update'  => 'templates.update',
        'destroy' => 'templates.destroy',
    ]);
    Route::get('/email-template-status', [EmailTemplateController::class, 'templateStatus'])->name('templates.status');
});

// Authentication Routes (replacing Auth::routes() from laravel/ui)
Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Password Reset Routes
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Email Verification Routes
Route::get('email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');

// ──────────────────────────────────────
// 5. AUTH + COMPANY MIDDLEWARE ROUTES
// ──────────────────────────────────────
Route::middleware(['auth', 'company'])->group(function () {

    // Root Redirect
    Route::get('/', fn() => redirect('/home'))->name('root');

    // Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Home
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');

    // ── Notifications ──
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.all');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // ── Categories ──
    Route::get('categories/category-create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('category-status', [CategoryController::class, 'categoryStatus'])->name('categories.status');
    Route::get('categories/destroy/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy.custom');
    Route::resource('categories', CategoryController::class)->names([
        'index'   => 'categories.index',
        'store'   => 'categories.store',
        'show'    => 'categories.show',
        'edit'    => 'categories.edit',
        'update'  => 'categories.update',
        'destroy' => 'categories.destroy',
    ]);

    // ── Services ──
    Route::get('services/services-create', [ServicesController::class, 'create'])->name('services.create');
    Route::get('servicestatus', [ServicesController::class, 'servicestatus'])->name('services.status');
    Route::get('services/destroy/{id}', [ServicesController::class, 'destroy'])->name('services.destroy.custom');
    Route::get('get-services-by-category', [ServicesController::class, 'get_services'])->name('services.by_category');
    Route::resource('services', ServicesController::class)->names([
        'index'   => 'services.index',
        'store'   => 'services.store',
        'show'    => 'services.show',
        'edit'    => 'services.edit',
        'update'  => 'services.update',
    ]);

    // ── Customers ──
    Route::get('customers/customers-create', [CustomerController::class, 'create'])->name('customers.create');
    Route::get('customerstatus', [CustomerController::class, 'customerstatus'])->name('customers.status');
    Route::get('bookings-history/{id}', [CustomerController::class, 'bookingsHistory'])->name('customers.bookings_history');
    Route::get('/all-customer-export', [CustomerController::class, 'exportCsv'])->name('customers.export');
    Route::get('customers-import-view', [CustomerController::class, 'customer_import_view'])->name('customers.import.view');
    Route::post('import-customer', [CustomerController::class, 'importCsv'])->name('customers.import');
    Route::get('user/profile/{id}', [CustomerController::class, 'profile'])->name('customers.profile');
    Route::post('user/profile-update/{id}', [CustomerController::class, 'profile_update'])->name('customers.profile_update');
    Route::get('/customers/import', [CustomerController::class, 'showImportForm'])->name('customers.import.form');
    Route::post('/customers/import', [CustomerController::class, 'import'])->name('customers.import.post');
    Route::resource('customers', CustomerController::class)->names([
        'index'   => 'customers.index',
        'store'   => 'customers.store',
        'show'    => 'customers.show',
        'edit'    => 'customers.edit',
        'update'  => 'customers.update',
    ]);

    // ── Referred Customers ──
    Route::get('referred-status', [ReferredCustomerController::class, 'referredStatus'])->name('referred_customers.status');
    Route::resource('/referred_customers', ReferredCustomerController::class)->names([
        'index'   => 'referred_customers.index',
        'create'  => 'referred_customers.create',
        'store'   => 'referred_customers.store',
        'show'    => 'referred_customers.show',
        'edit'    => 'referred_customers.edit',
        'update'  => 'referred_customers.update',
        'destroy' => 'referred_customers.destroy',
    ]);

    // ── Service Bookings ──
    Route::get('service-bookings/service-bookings-create', [ServiceBookingController::class, 'create'])->name('service-bookings.create');
    Route::get('service-bookings/edit/{id}', [ServiceBookingController::class, 'edit'])->name('service-bookings.edit');
    Route::post('service-bookings/update/{id}', [ServiceBookingController::class, 'update'])->name('service-bookings.update');
    Route::get('service-bookings/destroy/{id}', [ServiceBookingController::class, 'destroy'])->name('service-bookings.destroy.custom');
    Route::post('/service-bookings/cancel', [ServiceBookingController::class, 'cancel'])->name('service-bookings.cancel');
    Route::get('view-booking/{invoice}', [ServiceBookingController::class, 'show'])->name('service-bookings.show');
    Route::get('show-job-detail/{invoice}', [ServiceBookingController::class, 'show_job_detail'])->name('service-bookings.job_detail');
    Route::post('change-booking-status', [ServiceBookingController::class, 'book_status'])->name('service-bookings.change_status');
    Route::get('fetch-job-notes/{bookingid}', [ServiceBookingController::class, 'fetch_job_notes'])->name('service-bookings.fetch_notes');
    Route::get('delete-notes/{id}', [ServiceBookingController::class, 'delete_notes'])->name('service-bookings.delete_note');
    Route::post('/update-job-notes', [ServiceBookingController::class, 'update_notes'])->name('service-bookings.update_notes');
    Route::post('change-calendar-status', [ServiceBookingController::class, 'calendar_status'])->name('service-bookings.calendar_status');
    Route::post('check-booking-availability', [ServiceBookingController::class, 'checkAvailability'])->name('service-bookings.check_availability');
    Route::get('get-booking-orders', [ServiceBookingController::class, 'get_booking_orders'])->name('service-bookings.orders');
    Route::get('get-booking-date/{date?}', [ServiceBookingController::class, 'create'])->name('service-bookings.date');
    Route::get('get-booking-date/{date?}/success', [ServiceBookingController::class, 'create'])->name('service-bookings.date_success');
    Route::get('get-user-bookings/{user_id}', [ServiceBookingController::class, 'get_user_bookings'])->name('service-bookings.user_bookings');
    Route::get('get-service-status-bookings/{service_status}', [ServiceBookingController::class, 'get_service_status_bookings'])->name('service-bookings.service_status_bookings');
    Route::get('/get-employee-services', [ServiceBookingController::class, 'getEmployeeServices'])->name('getEmployeeServices');
    Route::post('booking-status', [ServiceBookingController::class, 'change_boking_status'])->name('service-bookings.booking_status');
    Route::get('/send_review_sms/{invoice}', [ServiceBookingController::class, 'send_review_sms'])->name('service-bookings.send_review_sms');
    Route::post('invoice-sms', [ServiceBookingController::class, 'invoice_sms'])->name('service-bookings.invoice_sms');
    Route::get('service-price', [ServiceBookingController::class, 'service_price'])->name('service-bookings.service_price');
    Route::get('service-bookings/edit-inventory/{id}', [ServiceBookingController::class, 'editInventory'])->name('service-bookings.edit-inventory');
    Route::post('service-bookings/update-inventory/{id}', [ServiceBookingController::class, 'updateInventory'])->name('service-bookings.update-inventory');
    Route::resource('service-bookings', ServiceBookingController::class)->names([
        'index'   => 'service-bookings.index',
        'store'   => 'service-bookings.store',
    ]);
    Route::get('customer-detail', [CustomerController::class, 'customer_detail'])->name('customers.booking.history');

    // ── Mailing & Marketing ──
    // Route::post('/customer-email-filter', [PowerMailingController::class, 'email_filter'])->name('mailing.email_filter');
    Route::get('mailing-history', [PowerMailingController::class, 'mailing_history'])->name('mailing.history');
    Route::post('/email-history-filter', [PowerMailingController::class, 'mailing_history_filter'])->name('mailing.history_filter');
    Route::get('sms-history', [MarketingController::class, 'sms_history'])->name('sms.history');
    Route::post('/sms-history-filter', [MarketingController::class, 'sms_history_filter'])->name('sms.history_filter');
    Route::get('cancel-schedule', [MarketingController::class, 'cancel_scheduled_sms'])->name('sms.cancel_schedule');
    Route::post('/whatsapp-status', [MarketingController::class, 'whatsapp_status'])->name('whatsapp.status');
    Route::get('/marketing', [MarketingController::class, 'index'])->name('marketing.index');
    Route::get('/postcard', [MarketingController::class, 'postcard'])->name('postcard.index');
    Route::post('/send-message', [MarketingController::class, 'send_message'])->name('marketing.send_message');
    Route::get('/marketing-search', [MarketingController::class, 'find_customers'])->name('marketing.search');
    Route::get('/postcard-search', [MarketingController::class, 'postcard_customers'])->name('postcard.search');
    Route::get('/template-search', [MarketingController::class, 'find_templates'])->name('template.search');
    Route::get('get-template-preview/{id}', [PowerMailingController::class, 'get_template_preview'])->name('template.preview');
    Route::get('mailing', [PowerMailingController::class, 'create'])->name('power_mailing.create');
    Route::post('send-mailing', [PowerMailingController::class, 'send'])->name('power_mailing.send');

    // ── Reports ──
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('record/find', [ReportController::class, 'record'])->name('reports.record_find');
    Route::get('report-export/{start_date}/{end_date}/{category_id}/{service_id}/{status_id}/{payment_type}', [ReportController::class, 'reportCsv'])->name('reports.export');
    Route::get('customer-reports', [ReportController::class, 'customer_index'])->name('customer.reports');
    Route::get('customer-graph-report', [ReportController::class, 'customer_graph_report'])->name('customer.graph_report');
    Route::get('customer-graph', [ReportController::class, 'customer_graph'])->name('customer.graph');
    Route::get('customer-country-graph-report', [ReportController::class, 'customer_country_graph_report'])->name('customer.country_graph_report');
    Route::get('customer-country-graph', [ReportController::class, 'customer_country_graph'])->name('customer.country_graph');
    Route::get('record/customer', [ReportController::class, 'customer_record'])->name('customer.record');
    Route::get('profit-loss-report', [ReportController::class, 'profit_loss_report'])->name('profit_loss.report');
    Route::get('profit-loss-data', [ReportController::class, 'profit_loss_data'])->name('profit_loss.data');
    Route::get('zipcode-report', [ReportController::class, 'zipcode_report'])->name('zipcode.report');
    Route::get('zipcode-data', [ReportController::class, 'zipcode_data'])->name('zipcode.data');
    Route::get('customer-report-by-date/{start_date}/{end_date}/{customer_id}', [ReportController::class, 'reportCustomerCsv'])->name('customer.report_by_date');
    Route::get('expense-reports', [ReportController::class, 'expense_index'])->name('expense.reports');
    Route::get('record/expense', [ReportController::class, 'expense_record'])->name('expense.record');
    Route::get('expense-report/{start_date}/{end_date}/{expense_type}/{method}', [ReportController::class, 'reportExpenseCsv'])->name('expense.export');
    Route::get('graph-report', [ReportController::class, 'graph_report'])->name('graph.report');
    Route::get('get-booking-chart', [ReportController::class, 'get_booking_chart'])->name('chart.booking');
    Route::get('get-heat-chart', [ReportController::class, 'heat_graph_report'])->name('chart.heat');
    Route::post('show-heat-chart', [ReportController::class, 'show_heat_report'])->name('chart.show_heat');

    Route::get('/dashboard/booking-overview', [HomeController::class, 'bookingOverviewData'])->name('dashboard.booking-overview');
    Route::get('/dashboard/sales-status', [HomeController::class, 'salesStatusData'])->name('dashboard.sales-status');
    Route::get('/dashboard/daily-sales', [HomeController::class, 'dailySalesData'])->name('dashboard.daily-sales');
    // ── Users & Roles ──
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('user/user-create', [UserController::class, 'create'])->name('users.create');
    Route::get('users/deleted', [UserController::class, 'deleted_users'])->name('users.deleted');
    Route::post('users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('user-status', [UserController::class, 'user_status'])->name('users.status');
    Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('user/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('user/destroy/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('user/delete_permanent/{id}', [UserController::class, 'delete_permanent'])->name('users.delete_permanent');
    Route::get('user/undelete/{id}', [UserController::class, 'undelete'])->name('users.undelete');
    Route::get('user/payslip/{id}', [UserController::class, 'payslip'])->name('users.payslip');
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('role/role-create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('role/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('role/edit/{role}', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('role/update/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('role/destroy/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // ── Settings ──
    Route::get('message-setting', [SettingController::class, 'message_index'])->name('settings.message');
    Route::get('message-status', [SettingController::class, 'message_status'])->name('settings.message_status');
    Route::get('email-status', [SettingController::class, 'email_status'])->name('settings.email_status');
    Route::get('tax-status', [SettingController::class, 'tax_status'])->name('settings.tax_status');
    Route::post('tax-settings-update', [SettingController::class, 'tax_settings_update'])->name('settings.tax_update');
    Route::post('voodoosms-settings-update', [SettingController::class, 'voodoosms_settings_update'])->name('settings.voodoosms_update');
    Route::post('socialmedia-update', [SettingController::class, 'socialmeida_update'])->name('settings.social_update');
    Route::get('message-setting/edit/{id}', [SettingController::class, 'message_settings_edit'])->name('settings.message_edit');
    Route::post('message-setting/update/{id}', [SettingController::class, 'message_settings_update'])->name('settings.message_update');
    Route::get('review-status', [SettingController::class, 'review_status'])->name('settings.review_status');
    Route::get('review-settings/edit', [SettingController::class, 'review_settings_edit'])->name('settings.review_edit');
    Route::post('review-settings-update', [SettingController::class, 'review_settings_update'])->name('settings.review_update');
    Route::get('email-settings', [SettingController::class, 'email_settings'])->name('settings.email');
    Route::get('email-settings/edit/{id}', [SettingController::class, 'email_settings_edit'])->name('settings.email_edit');
    Route::post('email-settings-update', [SettingController::class, 'email_settings_update'])->name('settings.email_update');
    Route::post('clicksend-update', [SettingController::class, 'clicksend_update'])->name('settings.clicksend_update');
    Route::get('theme-settings', [SettingController::class, 'theme_settings'])->name('theme.settings');
    Route::get('theme-settings/edit/{id}', [SettingController::class, 'theme_settings_edit'])->name('theme.edit');
    Route::post('theme-settings-update', [SettingController::class, 'theme_settings_update'])->name('theme.update');
    Route::post('update-booking-message', [SettingController::class, 'booking_message_update'])->name('settings.booking_message');

    // ── Quotations ──
    Route::get('quotations', [QuoteController::class, 'index'])->name('quotations.index');
    Route::get('quote-view/{id}', [QuoteController::class, 'show'])->name('quotations.show');
    Route::get('send-quotation', [QuoteController::class, 'create'])->name('quotations.create');
    Route::post('quote-mail', [QuoteController::class, 'quote_mail'])->name('quotations.send_mail');
    Route::get('add-holidays', [QuoteController::class, 'add_holidays'])->name('holidays.index');
    Route::post('update-holidays', [QuoteController::class, 'update_holidays'])->name('holidays.update');
    Route::get('delete-holidays/{id}', [QuoteController::class, 'delete_holidays'])->name('holidays.delete');

    // ── Expenses ──
    Route::get('expense-types', [ExpenseTypeController::class, 'index'])->name('expense_types.index');
    Route::get('expense-types/create', [ExpenseTypeController::class, 'create'])->name('expense_types.create');
    Route::get('expense-type-status', [ExpenseTypeController::class, 'expense_type_status'])->name('expense_types.status');
    Route::post('expense-types/store', [ExpenseTypeController::class, 'store'])->name('expense_types.store');
    Route::get('expense-types/edit/{id}', [ExpenseTypeController::class, 'edit'])->name('expense_types.edit');
    Route::post('expense-types/update/{id}', [ExpenseTypeController::class, 'update'])->name('expense_types.update');
    Route::get('expense-types/destroy/{id}', [ExpenseTypeController::class, 'destroy'])->name('expense_types.destroy');
    Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('expenses/expenses-create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('expenses/store', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('expenses/edit/{id}', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::post('expenses/update/{id}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/destroy/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // ── Postcards ──
    Route::get('postcard', [PostCardController::class, 'index'])->name('postcard.index');
    Route::get('postcard-info', [PostCardController::class, 'postcard_info'])->name('postcard.info');
    Route::post('postcard-send', [PostCardController::class, 'sendpost_card'])->name('postcard.send');
    Route::get('postcard-sample', [PostCardController::class, 'postcard_samples'])->name('postcard.samples');
    Route::get('postcard-history', [PostCardController::class, 'postcard_history'])->name('postcard.history');
    Route::get('postcard-download/{id}', [PostCardController::class, 'postcard_download'])->name('postcard.download');

    // ── Email Templates (Custom) ──
    Route::get('/email-template', [TemplateController::class, 'email_templates'])->name('email_template.index');
    Route::get('/email-template-create', [TemplateController::class, 'email_template_create'])->name('email_template.create');
    Route::post('/email-template-store', [TemplateController::class, 'store_email_template'])->name('email_template.store');
    Route::get('/email-template-edit/{id}', [TemplateController::class, 'edit_email_template'])->name('email_template.edit');
    Route::get('/email-template-json/{id}', [TemplateController::class, 'get_email_template_json'])->name('email_template.json');
    Route::post('/email-template-update', [TemplateController::class, 'update_email_template'])->name('email_template.update');
    Route::get('template-status', [TemplateController::class, 'templateStatus'])->name('email_template.status');
    Route::resource('/template', TemplateController::class)->names([
        'index'   => 'template.index',
        'create'  => 'template.create',
        'store'   => 'template.store',
        'show'    => 'template.show',
        'edit'    => 'template.edit',
        'update'  => 'template.update',
        'destroy' => 'template.destroy',
    ]);

    // ── JS Grid (Job Notes) ──
    Route::resource('/add-job-notes', JsGridController::class)->names([
        'index'   => 'add-job-notes.index',
        'create'  => 'add-job-notes.create',
        'store'   => 'add-job-notes.store',
        'show'    => 'add-job-notes.show',
        'edit'    => 'add-job-notes.edit',
        'update'  => 'add-job-notes.update',
        'destroy' => 'add-job-notes.destroy',
    ]);

    // ── Inventory ──
    Route::resource('/inventories', InventoryController::class)->names([
        'index'   => 'inventories.index',
        'create'  => 'inventories.create',
        'store'   => 'inventories.store',
        'show'    => 'inventories.show',
        'edit'    => 'inventories.edit',
        'update'  => 'inventories.update',
        'destroy' => 'inventories.destroy',
    ]);
    Route::get('inventories-available', [InventoryController::class, 'available'])->name('inventories.available');
    Route::resource('/inventories_purchases', InventoryPurchasesController::class)->names([
        'index'   => 'inventories_purchases.index',
        'create'  => 'inventories_purchases.create',
        'store'   => 'inventories_purchases.store',
        'show'    => 'inventories_purchases.show',
        'edit'    => 'inventories_purchases.edit',
        'update'  => 'inventories_purchases.update',
        'destroy' => 'inventories_purchases.destroy',
    ]);
    Route::post('/check-inventory-quantity', [InventoryController::class, 'checkQuantity'])->name('inventories.check_quantity');

    // ── Suppliers ──
    Route::resource('suppliers', SupplierController::class)->names([
        'index'   => 'suppliers.index',
        'create'  => 'suppliers.create',
        'store'   => 'suppliers.store',
        'show'    => 'suppliers.show',
        'edit'    => 'suppliers.edit',
        'update'  => 'suppliers.update',
        'destroy' => 'suppliers.destroy',
    ]);
    Route::resource('supplier-payments', SupplierPaymentController::class)->names([
        'index'   => 'supplier_payments.index',
        'create'  => 'supplier_payments.create',
        'store'   => 'supplier_payments.store',
        'show'    => 'supplier_payments.show',
        'edit'    => 'supplier_payments.edit',
        'update'  => 'supplier_payments.update',
        'destroy' => 'supplier_payments.destroy',
    ]);
    Route::get('/suppliers/{id}/balance-summary', [SupplierController::class, 'balanceSummary'])->name('suppliers.balance_summary');

    // ── Employee Management ──
    Route::get('/employee-graph-report', [EmployeeController::class, 'employee_graph_report'])->name('employee.graph_report');
    Route::get('/employee-booking-chart', [EmployeeController::class, 'employee_booking_chart'])->name('employee.booking_chart');
    Route::get('employee_attendance', [EmployeeAttendanceController::class, 'employee_attendance'])->name('employee_attendance');
    Route::post('/attendance/store', [EmployeeAttendanceController::class, 'store'])->name('attendance.store');
    Route::get('attendance', [EmployeeAttendanceController::class, 'attendance'])->name('attendance');
    Route::post('/update-exit-time', [EmployeeAttendanceController::class, 'updateExitTime'])->name('update.exit.time');
    Route::get('/work-hours', [EmployeeAttendanceController::class, 'work_hours'])->name('work_hours');

    // ── Payroll ──
    Route::get('/user/payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('user/monthly/payslip/{id}', [PayrollController::class, 'payslip'])->name('user.monthly.payslip');

    // ── Awards ──
    Route::resource('awards', AwardController::class)->names([
        'index'   => 'awards.index',
        'create'  => 'awards.create',
        'store'   => 'awards.store',
        'show'    => 'awards.show',
        'edit'    => 'awards.edit',
        'update'  => 'awards.update',
        'destroy' => 'awards.destroy',
    ]);

    // ── Admin Booking Status Change ──
    Route::post('/admin/booking/change-status', [BookingController::class, 'changeStatus'])->name('admin.booking.change-status');

    // ── Payments (Stripe & PayPal) ──
    Route::get('/', [StripeController::class, 'index'])->name('service_bookings.index');
    Route::post('/checkout', [StripeController::class, 'checkout'])->name('checkout');
    Route::get('/success', [StripeController::class, 'success'])->name('success');

    // ── Mail ──
    Route::get('invoice-mail/{id}', [MailController::class, 'invoice_mail'])->name('invoice.mail');

    // ── Work Schedule ──
    Route::get('/work-schedule', [WorkScheduleController::class, 'index'])->name('work_schedule.index');
    Route::patch('/work-schedule/{id}', [WorkScheduleController::class, 'update'])->name('work_schedule.update');
    Route::get('schedule-status', [WorkScheduleController::class, 'categoryStatus'])->name('schedule.status');

    Route::resource('tickets', TicketController::class);

    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');

    Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');

    Route::get('company/limits', [ReportController::class, 'limits'])->name('company.limits');
});