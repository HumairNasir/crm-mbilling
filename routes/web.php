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

Route::get('/get_response', [AdminController::class, 'get_response'])->name('get_response');

Route::get('/get_currentYear_sales', [AdminController::class, 'get_currentYear_sales'])->name('get_currentYear_sales');

Route::get('/get_monthly_sales', [AdminController::class, 'get_monthly_sales'])->name('get_monthly_sales');

});
