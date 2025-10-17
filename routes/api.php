<?php

use Illuminate\Http\Request;

use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\CustomerController;

use App\Http\Controllers\Api\CategoryController;

use App\Http\Controllers\Api\ServiceController;

use App\Http\Controllers\Api\ServiceBookingController;

use App\Http\Controllers\Api\UserController;

use App\Http\Controllers\Api\RoleController;

use App\Http\Controllers\Api\ExpenseTypeController;

use App\Http\Controllers\Api\ExpenseController;

use App\Http\Controllers\Api\QuoteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'store']);

Route::post('/categories', [CategoryController::class, 'index']);
Route::post('/categories/show', [CategoryController::class, 'show']);
Route::post('/categories/create', [CategoryController::class, 'store']);
Route::post('/categories/update', [CategoryController::class, 'update']);
Route::post('/categories/delete', [CategoryController::class, 'destroy']);
Route::post('/category-status', [CategoryController::class, 'categoryStatus']);
Route::post('/active-categories', [CategoryController::class, 'activeCategories']);


Route::post('/services', [ServiceController::class, 'index']);
Route::post('/services/show', [ServiceController::class, 'show']);
Route::post('/services/create', [ServiceController::class, 'store']);
Route::post('/services/update', [ServiceController::class, 'update']);
Route::post('/services/delete', [ServiceController::class, 'destroy']);
Route::post('/service-status', [ServiceController::class, 'serviceStatus']);
Route::post('/active-services', [ServiceController::class, 'activeServices']);
Route::post('/category-services', [ServiceController::class, 'cateandservices']);

Route::post('/customers', [CustomerController::class, 'index']);
Route::post('/customers/show', [CustomerController::class, 'show']);
Route::post('/customers/create', [CustomerController::class, 'store']);
Route::post('/customers/update', [CustomerController::class, 'update']);
Route::post('/customers/delete', [CustomerController::class, 'destroy']);
Route::post('/customers/status', [CustomerController::class, 'status']);

Route::post('/service-bookings', [ServiceBookingController::class, 'index']);
Route::post('/booking-listings', [ServiceBookingController::class, 'booking_listings']);
Route::post('/service-bookings/show', [ServiceBookingController::class, 'show']);
Route::post('/service-bookings/create', [ServiceBookingController::class, 'create']);
Route::post('/service-bookings/store', [ServiceBookingController::class, 'store']);
Route::post('/service-bookings/update', [ServiceBookingController::class, 'update']);
Route::post('/service-bookings/delete', [ServiceBookingController::class, 'destroy']);
Route::post('/service-price', [ServiceBookingController::class, 'service_price']);
Route::post('/booking-status', [ServiceBookingController::class, 'book_status']);
Route::post('/get-booking-orders', [ServiceBookingController::class, 'get_booking_orders']);
Route::post('/get-user-bookings', [ServiceBookingController::class, 'get_user_bookings']);
Route::post('/service-bookings/email-setting', [ServiceBookingController::class, 'email_setting']);
Route::post('/tax-info', [ServiceBookingController::class, 'tax_info']);

Route::post('/users', [UserController::class, 'index']);
Route::post('/users/image', [UserController::class, 'userImage']);
Route::post('/users/show', [UserController::class, 'show']);
Route::post('/users/create', [UserController::class, 'store']);
Route::post('/users/update', [UserController::class, 'update']);
Route::post('/users/delete', [UserController::class, 'destroy']);
Route::post('/device-token', [UserController::class, 'device_token']);
Route::post('/user-status', [UserController::class, 'user_status']);

Route::post('/roles', [RoleController::class, 'index']);
Route::post('/roles/permissions-name', [RoleController::class, 'permissions_name']);
Route::post('/roles/create', [RoleController::class, 'create']);
Route::post('/roles/store', [RoleController::class, 'store']);
Route::post('/roles/edit', [RoleController::class, 'edit']);
Route::post('/roles/update', [RoleController::class, 'update']);
Route::post('/roles/delete', [RoleController::class, 'destroy']);
Route::post('/roles/permissions', [RoleController::class, 'role_permissions']);

Route::post('/expense-types', [ExpenseTypeController::class, 'index']);
Route::post('/expense-types/show', [ExpenseTypeController::class, 'show']);
Route::post('/expense-types/create', [ExpenseTypeController::class, 'store']);
Route::post('/expense-types/update', [ExpenseTypeController::class, 'update']);
Route::post('/expense-types/delete', [ExpenseTypeController::class, 'destroy']);

Route::post('/expenses', [ExpenseController::class, 'index']);
Route::post('/expenses/show', [ExpenseController::class, 'show']);
Route::post('/expenses/create', [ExpenseController::class, 'store']);
Route::post('/expenses/update', [ExpenseController::class, 'update']);
Route::post('/expenses/delete', [ExpenseController::class, 'destroy']);

Route::post('/quotations', [QuoteController::class, 'index']);
Route::post('/quotations/create', [QuoteController::class, 'store']);
Route::post('/quotations/show', [QuoteController::class, 'show']);

Route::post('/receiving-notification', [ServiceBookingController::class, 'receiving_notification']);

Route::post('/send-invoice', [ServiceBookingController::class, 'send_invoice']);


