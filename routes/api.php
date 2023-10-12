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

Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');
Route::post('register', 'Auth\RegisterController@register');
Route::post('reset-password', 'Auth\ResetPasswordController@resetPassword');

Route::get('customer', 'Crm_CustomerController@find');
Route::get('customer/update', 'Crm_CustomerController@update');
Route::post('bed/status', 'Operation_BedOrderController@updateStatus');
Route::post('bed/show', 'Operation_BedOrderController@showBed');
Route::post('bed/select', 'Operation_BedOrderController@selectBed');
Route::post('bed/getServices', 'Operation_BedOrderController@getServices');
Route::get('bed/checkBeds', 'Operation_BedOrderController@checkBeds');
Route::post('bed/finish', 'Operation_BedOrderController@finishOrder');
Route::get('customer/services', 'Crm_CustomerController@services');
Route::get('workSchedule/generate', 'Operation_WorkScheduleController@generate');
Route::get('roomOrder/checkRooms', 'Operation_RoomOrderController@checkRooms');
Route::get('roomOrder/getService', 'Operation_RoomOrderController@getService');

Route::post('register', 'RegisterController@register');
Route::post('login', 'RegisterController@login');
Route::middleware('auth:api')->group( function () {
    Route::resource('units', 'Facility_UnitController');
    Route::resource('news', 'Mkt_NewsController');
    Route::resource('services', 'Prod_ServiceController');
    Route::resource('technicians', 'TechnicianController');
});