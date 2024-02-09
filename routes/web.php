<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RegionalController;
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
require __DIR__.'/auth.php';

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    Route::get('/dental_offices', [AdminController::class, 'dental_offices'])->name('dental_offices');


    Route::get('/area_manager', [AdminController::class, 'area_manager'])->name('area_manager');

    Route::get('/clients', [AdminController::class, 'clients'])->name('clients');

    Route::get('/sales_rep', [AdminController::class, 'sales_rep'])->name('sales_rep');

    Route::get('/get_response', [AdminController::class, 'get_response'])->name('get_response');

    Route::get('/get_top_sales', [AdminController::class, 'get_top_sales'])->name('get_top_sales');

    Route::get('/get_monthly_sales', [AdminController::class, 'get_monthly_sales'])->name('get_monthly_sales');

    Route::get('/get_weekly_sales', [AdminController::class, 'get_weekly_sales'])->name('get_weekly_sales');

    Route::get('/get_reschedule_sales', [AdminController::class, 'get_reschedule_sales'])->name('get_reschedule_sales');

    Route::get('/get_schedule_sales', [AdminController::class, 'get_schedule_sales'])->name('get_schedule_sales');

    Route::get('/get_won_sales', [AdminController::class, 'get_won_sales'])->name('get_won_sales');

    Route::get('/get_total_sale', [AdminController::class, 'get_total_sale'])->name('get_total_sale');

    Route::get('/get_subscriptions_sale', [AdminController::class, 'get_subscriptions_sale'])->name('get_subscriptions_sale');


    // Route::resource('regional_manager', [RegionalController::class]);
    Route::resource('regional_manager', 'App\Http\Controllers\RegionalController');
    Route::post('/regionalmanagerstore', 'App\Http\Controllers\RegionalController@store')->name('regional_manager.store');
    Route::post('/regionalmanagerupdate/{id}', 'App\Http\Controllers\RegionalController@update')->name('regional_manager.update');

    Route::get('/edit-manager/{id}',  'App\Http\Controllers\RegionalController@edit');

    Route::resource('area_manager', 'App\Http\Controllers\AreaController');

    Route::get('/edit-area-manager/{id}',  'App\Http\Controllers\AreaController@edit');

    Route::get('/get-areas/{region_id}', 'App\Http\Controllers\AreaController@getAreas');
    Route::get('/get-territories/{area_id}', 'App\Http\Controllers\AreaController@getterritories');
    Route::post('/areamanagerstore', 'App\Http\Controllers\AreaController@store')->name('area_manager.store');
    Route::post('/areamanagerupdate/{id}', 'App\Http\Controllers\AreaController@update')->name('area_manager.update');



    Route::get('regional_manager/{id}/confirm-delete', [RegionalManagerController::class, 'confirmDelete'])->name('regional_manager.confirm-delete');

});


