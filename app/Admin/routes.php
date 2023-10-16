<?php

use Encore\Admin\Facades\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::resource('admin/auth/users', \App\Admin\Controllers\System_CustomUserController::class)->middleware(config('admin.route.middleware'));
Route::get('admin/auth/setting', [\App\Admin\Controllers\System_CustomSettingController::class, 'display'])->middleware(config('admin.route.middleware'));

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    Route::get('/pdf', 'PDFController@createPDF');
    //Core
    $router->resource('roles', Core_RoleController::class);
    $router->resource('customer-types', Core_CustomerTypeController::class);
    $router->resource('sources', Core_SourceController::class);
    //Financial
    $router->resource('point-topups', Fin_PointTopupController::class);
    $router->resource('receiver-accounts', Fin_ReceiverAccountController::class);
    $router->resource('bank-bins', Fin_BankBinController::class);
    //Product
    $router->resource('services', Prod_ServiceController::class); 
    //Marketing
    $router->resource('news', Mkt_NewsController::class);
    //Sales
    $router->resource('users', Sales_UserController::class);
    $router->resource('bills', Sales_BillController::class);
    //Crm
    $router->resource('msgs', Crm_MsgController::class);
    $router->resource('prospect-customers', Crm_ProspectCustomerController::class);
    //Facility
    $router->resource('branches', Facility_BranchController::class);
    $router->resource('zones', Facility_ZoneController::class);
    $router->resource('rooms', Facility_RoomController::class);
    $router->resource('beds', Facility_BedController::class);
    //Operation
    Route::get('/select-bed', 'Operation_BedOrderController@selectBed');
    $router->resource('work-schedules', Operation_WorkScheduleController::class);
    $router->resource('room-orders', Operation_RoomOrderController::class);
    //Hrm
    $router->resource('hrm', Hrm_EmployeeController::class);
});
