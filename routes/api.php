<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('customer', 'Crm_ProspectCustomerController@find');
Route::get('customer/update', 'Crm_ProspectCustomerController@update');
Route::post('bed/status', 'Operation_BedOrderController@updateStatus');
Route::post('bed/show', 'Operation_BedOrderController@showBed');
Route::post('bed/select', 'Operation_BedOrderController@selectBed');
Route::post('bed/getServices', 'Operation_BedOrderController@getServices');
Route::get('bed/checkBeds', 'Operation_BedOrderController@checkBeds');
Route::post('bed/finish', 'Operation_BedOrderController@finishOrder');
Route::get('customer/services', 'Crm_ProspectCustomerController@services');
Route::get('workSchedule/generate', 'Operation_WorkScheduleController@generate');
Route::get('roomOrder/checkRooms', 'Operation_RoomOrderController@checkRooms');
Route::get('roomOrder/getService', 'Operation_RoomOrderController@getService');
Route::get('zone', 'Facility_ZoneController@find');
Route::get('room', 'Facility_RoomController@find');
Route::middleware('auth:api')->group(function () {
    Route::resource('news', 'Mkt_NewsController');
    Route::resource('services', 'Prod_ServiceController');
    Route::resource('technicians', 'TechnicianController');
});
Route::get('search', 'UserController@search')->name('search');
//RESTAPI
//Auth
Route::post('v1/register', 'Auth\RegisterController@register');
Route::post('v1/verify_register', 'Auth\VerifyRegisterController@verifyRegister');
Route::post('v1/login', 'Auth\LoginController@login');
Route::post('v1/forgot_password_by_phone_number', 'Auth\ForgotPasswordController@sendOTPByPhoneNumber');
Route::post('/v1/change_password_after_forgot', 'Auth\ForgotPasswordController@forgotPasswordByOTP');
Route::post('/v1/verify_forgot_password', 'Auth\ForgotPasswordController@verifyOTPByPhoneNumber');

//Business
Route::post('v1/email_by_phone_number', 'Auth\Business\GetEmailByPhoneNumberController@getEmail');
//Authorization by access_token
Route::middleware('custom_auth')->group(function () {
//User
    Route::get('v1/profile', 'UserController@get');
    Route::post('v1/update_profile', 'UserController@update');
    Route::delete('v1/account', 'UserController@delete');
    Route::post('v1/change_password', 'UserController@changePassword');
//Service
    Route::get('v1/services', 'Prod_ServiceController@get');
    Route::get('v1/services/{id}', 'Prod_ServiceController@detail');
    Route::get('v1/promotions', 'Prod_PromotionController@get');
    Route::get('v1/save_promotion', 'Prod_PromotionController@getPromotionSave');
    Route::post('v1/saved_promotions', 'Prod_PromotionController@save');
//Branch
    Route::get('v1/branches', 'Facility_BranchController@get');
//Employee
    //Technician
        Route::get('v1/technicians', 'Hrm_EmployeeController@getTechnicians');
//Schedule
    Route::get('v1/schedules', 'Operation_ScheduleOrderController@getSchedule');
    Route::post('v1/create_schedule', 'Operation_ScheduleOrderController@createSchedule');
    Route::post('v1/cancel_schedule/{id}', 'Operation_ScheduleOrderController@cancelSchedule');
    Route::put('v1/update_schedule/{id}', 'Operation_ScheduleOrderController@updateSchedule');
});


//RESTAPI Website None Auth
Route::get('web/v1/services', 'Prod_ServiceController@getWebsite');
Route::get('web/v1/services/{id}', 'Prod_ServiceController@detailWebsite');

//RESTAPI IN ERP 
Route::post('erp/login', 'Auth\LoginController@loginUserSystem');
Route::middleware('erp_auth')->group(function () {
    Route::get('erp/profile', 'UserController@getUserErp');
});
#Todo Middlerware
Route::post('/process-payment', 'Sales_PaymentController@processPayment')->name('process.payment');
Route::post('erp/user', 'UserController@getAll');

