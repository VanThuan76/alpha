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
Route::post('v1/forgot_password_by_phone_number', 'Auth\ForgotPasswordController@forgotPasswordByPhoneNumber');
//Business
Route::post('v1/email_by_phone_number', 'Auth\Business\GetEmailByPhoneNumberController@getEmail');
Route::middleware('auth:api')->group(function () {
//User
    Route::get('v1/profile', 'UserController@get');
    Route::post('v1/update_profile', 'UserController@update');
    Route::put('v1/change_password', 'UserController@changePassword');
//Service
    Route::get('v1/services', 'Prod_ServiceController@get');
    Route::get('v1/promotions', 'Prod_PromotionController@get');
});
