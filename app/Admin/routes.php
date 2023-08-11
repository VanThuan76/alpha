<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::resource('admin/auth/users', \App\Admin\Controllers\CustomUserController::class)->middleware(config('admin.route.middleware'));

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    Route::get('/pdf', 'PDFController@createPDF');
    $router->resource('units', UnitController::class);
    $router->resource('branches', BranchController::class);
    $router->resource('zones', ZoneController::class);
    $router->resource('rooms', RoomController::class);
    $router->resource('sources', SourceController::class);
    $router->resource('users', UserController::class);
    $router->resource('customer-types', CustomerTypeController::class);
    $router->resource('point-topups', PointTopupController::class);
    $router->resource('services', ServiceController::class); 
    $router->resource('work-schedules', WorkScheduleController::class);
    $router->resource('bills', BillController::class);
    $router->resource('room-orders', RoomOrderController::class);
});
