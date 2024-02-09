<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
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

Route::get('/regional_manager', [AdminController::class, 'regional_manager'])->name('regional_manager');

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

});


Route::get('/dental_offices', function () {
    return view('dental_offices');
})->middleware(['auth'])->name('dental_offices');

Route::get('/dashboard_home', function () {
    return view('dashboard_home');
})->middleware(['auth'])->name('dashboard_home');

Route::get('/regional_manager', function () {
    return view('regional_manager');
})->middleware(['auth'])->name('regional_manager');

Route::get('/area-manager', function () {
    return view('area-manager');
})->middleware(['auth'])->name('area-manager');

Route::get('/clients', function () {
    return view('client');
})->middleware(['auth'])->name('clients');

Route::get('/sales-rep', function () {
    return view('sales-rep');
})->middleware(['auth'])->name('sales-rep');
