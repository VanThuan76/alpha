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

Route::get('customer', 'CustomerController@find');
Route::get('customer/update', 'CustomerController@update');
Route::post('bed/status', 'BedOrderController@updateStatus');
Route::post('bed/select', 'BedOrderController@selectBed');
Route::get('customer/services', 'CustomerController@services');
Route::get('workSchedule/generate', 'WorkScheduleController@generate');
Route::get('roomOrder/checkRooms', 'RoomOrderController@checkRooms');
Route::get('roomOrder/getService', 'RoomOrderController@getService');

Route::post('register', 'RegisterController@register');
Route::post('login', 'RegisterController@login');
Route::middleware('auth:api')->group( function () {
    Route::resource('units', 'UnitController');
    Route::resource('news', 'NewsController');
    Route::resource('services', 'ServiceController');
    Route::resource('technicians', 'TechnicianController');
});