<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/chinh-sach-bao-mat', function () {
    return view('website.privacy_policy');
});
Route::get('/dieu-khoan-su-dung', function () {
    return view('website.terms_of_use');
});
Route::match(['get', 'post'], '/botman', 'BotmanController@handle');