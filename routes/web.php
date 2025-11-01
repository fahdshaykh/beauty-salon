<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SbookingController;
use App\Http\Controllers\ServiceBookingController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\EquipmentsController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\Client_detailController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/





Route::get('/clear-config', function() {
    \Artisan::call('config:clear');
      return \Artisan::output();
  });
  Route::get('/clear-cache', function() {
   \Artisan::call('cache:clear');
      return \Artisan::output();
  });
  Route::get('/clear-view', function() {
   \Artisan::call('view:clear');
    return \Artisan::output();
  });
  Route::get('sms-invoice-mail/{id}/{company}', 'MailController@sms_invoice_mail');
Auth::routes();
Route::get('optout/{companyid}/{customerid}/{opt}','MarketingController@emailoptout')->where('id', '[0-9]+','opt', '[0-9]+');
Route::get('smsoptout/{companyid}/{customerid}/{opt}','MarketingController@smsoptout')->where('id', '[0-9]+','opt', '[0-9]+');
Route::middleware(['auth', 'company'])->group(function () {

    Route::get('/', function () {

        return redirect('/home');

    });

    Route::get('categories/category-create','CategoryController@create');

    Route::get('services/services-create','ServicesController@create');

    Route::get('customers/customers-create','CustomerController@create');

    Route::get('service-bookings/service-bookings-create','ServiceBookingController@create');



    // Route::resource('companies', 'CompanyController');

    // Route::get('company-status', 'CompanyController@company_status');

    // Route::get('company/destroy/{id}', 'CompanyController@destroy');

    // Show all notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.all');

    // Mark notification as read
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');  

    Route::get('user/profile/{id}','UserController@profile');

    Route::post('user/profile-update/{id}','UserController@profile_update');

    Route::get('services/destroy/{id}','ServicesController@destroy');

    Route::get('/home', 'HomeController@index')->name('home');

    Route::resource('services', 'ServicesController');

    Route::get('servicestatus', 'ServicesController@servicestatus');

    Route::get('category-status', 'CategoryController@categoryStatus');

    Route::get('categories/destroy/{id}','CategoryController@destroy');

    Route::get('customers/destroy/{id}','CustomerController@destroy');
    Route::get('customerstatus', 'CustomerController@customerstatus');
    Route::get('bookings-history/{id}', 'CustomerController@bookingsHistory');
    Route::get('/all-customer-export', 'CustomerController@exportCsv');
    Route::get('customers-import-view','CustomerController@customer_import_view');
    Route::POST('import-customer','CustomerController@importCsv');
    Route::resource('categories', 'CategoryController');

    Route::resource('customers', 'CustomerController');

    Route::get('service-bookings/edit/{id}','ServiceBookingController@edit');

    Route::post('service-bookings/update/{id}','ServiceBookingController@update');

    Route::get('service-bookings/destroy/{id}','ServiceBookingController@destroy');
    Route::post('/service-bookings/cancel', 'ServiceBookingController@cancel')->name('service-bookings.cancel');
    Route::resource('service-bookings', 'ServiceBookingController');

    Route::get('customer-detail', 'CustomerController@customer_detail');

    // Route::post('service-price', 'ServiceBookingController@service_price');
  
    Route::get('service-price', 'ServiceBookingController@service_price');
    Route::get('show-job-detail/{invoice}', 'ServiceBookingController@show_job_detail');
    Route::post('change-booking-status', 'ServiceBookingController@book_status');
    Route::get('fetch-job-notes/{bookingid}', 'ServiceBookingController@fetch_job_notes');
    Route::get('delete-notes/{id}', 'ServiceBookingController@delete_notes');
    Route::post('/update-job-notes', 'ServiceBookingController@update_notes');
    //Route::put('/user/{id}', 'ServiceBookingController@fetch_job_notes');
    Route::post('change-calendar-status', 'ServiceBookingController@calendar_status');

    Route::post('check-booking-availability', 'ServiceBookingController@checkAvailability');

    Route::get('get-services-by-category', 'ServicesController@get_services');

    Route::get('get-booking-orders', 'ServiceBookingController@get_booking_orders');

    Route::get('get-booking-date/{date?}', 'ServiceBookingController@create');

    Route::get('get-booking-date/{date?}/success', 'ServiceBookingController@create');

    Route::get('get-user-bookings/{user_id}', 'ServiceBookingController@get_user_bookings');

    //new route for getting employee information
    Route::get('/get-employee-services', 'ServiceBookingController@getEmployeeServices')->name('getEmployeeServices');
  
    Route::post('/customer-email-filter', 'PowerMailingController@email_filter');
    Route::get('mailing-history', 'PowerMailingController@mailing_history');
    Route::post('/email-history-filter', 'PowerMailingController@mailing_history_filter');
    Route::get('sms-history', 'MarketingController@sms_history');
    Route::post('/sms-history-filter', 'MarketingController@sms_history_filter');
    Route::get('cancel-schedule','MarketingController@cancel_scheduled_sms');
    Route::get('reports', 'ReportController@index');

    Route::get('record/find','ReportController@record');

    Route::get('report-export/{start_date}/{end_date}/{category_id}/{service_id}/{status_id}/{payment_type}','ReportController@reportCsv');

    Route::get('customer-reports', 'ReportController@customer_index');
    Route::get('customer-graph-report', 'ReportController@customer_graph_report');
    Route::get('customer-graph', 'ReportController@customer_graph');
    Route::get('customer-country-graph-report', 'ReportController@customer_country_graph_report');
    Route::get('customer-country-graph', 'ReportController@customer_country_graph');
    Route::get('record/customer','ReportController@customer_record');
    Route::get('profit-loss-report','ReportController@profit_loss_report');
    Route::get('profit-loss-data','ReportController@profit_loss_data');
    Route::get('zipcode-report','ReportController@zipcode_report');
    Route::get('zipcode-data','ReportController@zipcode_data');
    Route::get('customer-report-by-date/{start_date}/{end_date}/{customer_id}','ReportController@reportCustomerCsv');

   

    Route::get('expense-reports', 'ReportController@expense_index');

    Route::get('record/expense','ReportController@expense_record');

    Route::get('expense-report/{start_date}/{end_date}/{expense_type}/{method}','ReportController@reportExpenseCsv');



    Route::get('users', 'UserController@index')->name('users.index');

    Route::get('user/user-create', 'UserController@create');
    Route::get('users/deleted', 'UserController@deleted_users');

    Route::post('users/store', 'UserController@store')->name('users.store');

    Route::get('user-status', 'UserController@user_status');

    Route::get('user/edit/{id}', 'UserController@edit');

    Route::put('user/update/{id}', 'UserController@update')->name('users.update');

    Route::get('user/destroy/{id}', 'UserController@destroy');
    Route::get('user/delete_permanent/{id}', 'UserController@delete_permanent');
    Route::get('user/undelete/{id}', 'UserController@undelete');


    Route::get('roles', 'RoleController@index')->name('roles.index');

    Route::get('role/role-create', 'RoleController@create')->name('roles.create');

    Route::post('role/store', 'RoleController@store')->name('roles.store');

    Route::get('role/edit/{role}', 'RoleController@edit')->name('roles.edit');

    Route::PUT('role/update/{role}', 'RoleController@update')->name('roles.update');

    Route::delete('role/destroy/{role}', 'RoleController@destroy')->name('roles.destroy');



    Route::get('message-setting', 'SettingController@message_index');
    // Route::get('invoice-setting', 'SettingController@invoice_settings');
    Route::get('message-status', 'SettingController@message_status');

    Route::get('tax-status', 'SettingController@tax_status');
    Route::POST('tax-settings-update', 'SettingController@tax_settings_update');
    Route::POST('socialmedia-update', 'SettingController@socialmeida_update');
    Route::get('message-setting/edit/{id}', 'SettingController@message_settings_edit');

    Route::post('message-setting/update/{id}', 'SettingController@message_settings_update');

    Route::get('review-status', 'SettingController@review_status');

    Route::get('review-settings/edit', 'SettingController@review_settings_edit');

    Route::post('review-settings-update', 'SettingController@review_settings_update');



    Route::get('email-settings', 'SettingController@email_settings');

    Route::get('email-settings/edit/{id}', 'SettingController@email_settings_edit');

    Route::POST('email-settings-update', 'SettingController@email_settings_update');
    Route::POST('clicksend-update', 'SettingController@clicksend_update');

   
    Route::get('theme-settings', 'SettingController@theme_settings');

    Route::get('theme-settings/edit/{id}', 'SettingController@theme_settings_edit');

    Route::post('theme-settings-update', 'SettingController@theme_settings_update');



    Route::get('view-booking/{invoice}', 'ServiceBookingController@show');

    Route::get('invoice-mail/{id}', 'MailController@invoice_mail');

    Route::post('invoice-sms/', 'ServiceBookingController@invoice_sms');

    Route::get('quotations', 'QuoteController@index');

    Route::get('quote-view/{id}', 'QuoteController@show');

    Route::get('send-quotation', 'QuoteController@create');

    Route::post('quote-mail', 'QuoteController@quote_mail');



    Route::get('expense-types', 'ExpenseTypeController@index');

    Route::get('expense-types/create', 'ExpenseTypeController@create');
    Route::get('expense-type-status', 'ExpenseTypeController@expense_type_status');
    Route::post('expense-types/store', 'ExpenseTypeController@store');

    Route::get('expense-types/edit/{id}', 'ExpenseTypeController@edit');

    Route::post('expense-types/update/{id}', 'ExpenseTypeController@update');

    Route::get('expense-types/destroy/{id}', 'ExpenseTypeController@destroy');



    Route::get('expenses', 'ExpenseController@index')->name('expenses.index');

    Route::get('expenses/expenses-create', 'ExpenseController@create');

    Route::post('expenses/store', 'ExpenseController@store');

    Route::get('expenses/edit/{id}', 'ExpenseController@edit');

    Route::post('expenses/update/{id}', 'ExpenseController@update');

    Route::get('expenses/destroy/{id}', 'ExpenseController@destroy');

    Route::get('mailing', 'PowerMailingController@create')->name('power_mailing.create');
    
    Route::post('send-mailing', 'PowerMailingController@send');
  
    Route::get('graph-report', 'ReportController@graph_report');
    
    Route::get('get-booking-chart', 'ReportController@get_booking_chart');
    Route::get('get-heat-chart', 'ReportController@heat_graph_report');
    Route::post('show-heat-chart', 'ReportController@show_heat_report');
    //Post card Api
    Route::get('postcard','PostCardController@index');
    Route::get('postcard-info','PostCardController@postcard_info');
    Route::post('postcard-send','PostCardController@sendpost_card');
    Route::get('postcard-sample','PostCardController@postcard_samples');
    Route::get('postcard-history','PostCardController@postcard_history');
    Route::get('postcard-download/{id}','PostCardController@postcard_download');
    //  Routes Created By Abdul Wahid
    Route::get('/email-template','TemplateController@email_templates');
    Route::get('/email-template-create','TemplateController@email_template_create');
    Route::post('/email-template-store','TemplateController@store_email_template');
    Route::get('/email-template-edit/{id}','TemplateController@edit_email_template');
    Route::get('/email-template-json/{id}','TemplateController@get_email_template_json');
    Route::post('/email-template-update','TemplateController@update_email_template');
Route::resource('/template','TemplateController');
Route::get('template-status', 'TemplateController@templateStatus');
Route::resource('/add-job-notes', 'JsGridController');
Route::post('/whatsapp-status', 'MarketingController@whatsapp_status');
Route::get('/marketing','MarketingController@index');
Route::get('/postcard','MarketingController@postcard');
Route::post('/send-message', 'MarketingController@send_message');
Route::get('/marketing-search','MarketingController@find_customers');
Route::get('/postcard-search','MarketingController@postcard_customers');
Route::get('/template-search','MarketingController@find_templates');
Route::get('/send_review_sms/{invoice}','ServiceBookingController@send_review_sms');
Route::post('booking-status','ServiceBookingController@change_boking_status');
Route::post('update-booking-message','SettingController@booking_message_update');
Route::get('get-template-preview/{id}','PowerMailingController@get_template_preview');
Route::get('add-holidays','SettingController@add_holidays');
Route::post('update-holidays','SettingController@update_holidays');
Route::get('delete-holidays/{id}','SettingController@delete_holidays');

Route::resource('/inventories','InventoryController');
Route::get('inventories-available', [InventoryController::class, 'available'])->name('inventories.available');
Route::resource('/inventories_purchases','InventoryPurchasesController');

    // Referred customers CRUD
    Route::resource('/referred_customers', 'ReferredCustomerController');

Route::post('/check-inventory-quantity', 'InventoryController@checkQuantity');

Route::resource('suppliers', 'SupplierController');
Route::resource('supplier-payments', 'SupplierPaymentController');
Route::get('/suppliers/{id}/balance-summary', [SupplierController::class, 'balanceSummary'])->name('suppliers.balance_summary');

Route::get('company-graph-report', 'CompanyReportController@company_graph_report');
Route::get('company-booking-chart', 'CompanyReportController@company_booking_chart');

Route::get('company-expense-reports', 'CompanyReportController@expense_index');
Route::get('company/record/expense','CompanyReportController@expense_record');

Route::get('company-inventory-reports', 'CompanyReportController@inventory_index');
Route::get('company/record/inventory','CompanyReportController@inventory_record');


Route::get('/employee-graph-report', [EmployeeController::class, 'employee_graph_report']);
Route::get('/employee-booking-chart', [EmployeeController::class, 'employee_booking_chart']);
                //------------------Employee Management-------------------------//
Route::get('employee_attendance', [EmployeeAttendanceController::class, 'employee_attendance'])->name('employee_attendance');
Route::post('/attendance/store', [EmployeeAttendanceController::class, 'store'])->name('attendance.store');
Route::get('attendance', [EmployeeAttendanceController::class, 'attendance'])->name('attendance');
Route::post('/update-exit-time', [EmployeeAttendanceController::class, 'updateExitTime'])->name('update.exit.time');
Route::get('/work-hours', [EmployeeAttendanceController::class, 'work_hours'])->name('work_hours');

Route::get('user/payslip/{id}', 'UserController@payslip');
Route::get('/user/payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
Route::get('user/monthly/payslip/{id}', [PayrollController::class, 'payslip'])->name('user.monthly.payslip');

Route::get('/customers/import', [CustomerController::class, 'showImportForm'])->name('customers.import.form');
Route::post('/customers/import', [CustomerController::class, 'import'])->name('customers.import');

Route::resource('awards', AwardController::class);

Route::post('/admin/booking/change-status', [BookingController::class, 'changeStatus'])->name('admin.booking.change-status');

Route::get('/redirect-to-paypal', function (Request $request) {
    $bookingId = $request->query('booking_id');
    // Get booking details from database
    $booking = \App\ServiceBooking::findOrFail($bookingId);
    // PayPal API integration (example, replace with actual logic)
    $paypalUrl = "https://www.paypal.com/checkout?amount={$booking->total_price}&currency=USD";
    // Redirect to PayPal
    return redirect()->to($paypalUrl);
})->name('redirect.to.paypal');

Route::get("/", [StripeController::class, 'index'])->name('service_bookings.index');
Route::post('/checkout', [StripeController::class, 'checkout'])->name('checkout');

Route::get("/success", [StripeController::class, 'success'])->name('success');

});

Route::group(["middleware" => 'company'], function () {
    Route::resource('companies', 'CompanyController');
    Route::resource('templates', 'EmailTemplateController');
    Route::get('/email-template-status', 'EmailTemplateController@templateStatus');
    Route::get('company-status', 'CompanyController@company_status');
    Route::get('company/destroy/{id}', 'CompanyController@destroy');
});



