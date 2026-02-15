<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RegionalController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\SalesRepController;
use App\Http\Controllers\DentalOfficeController;
use App\Http\Controllers\ClientController;

require __DIR__ . '/auth.php';

Route::group(['middleware' => 'auth'], function () {
    // --- DASHBOARD ---
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // --- DENTAL OFFICES MODULE ---
    // ✅ NEW (Correct)
    Route::get('/dental_offices', [DentalOfficeController::class, 'index'])->name('dental_offices');
    Route::post('/dental_offices/store', [DentalOfficeController::class, 'store'])->name('dental_offices.store');
    Route::get('/dental_offices/{id}/edit', [DentalOfficeController::class, 'edit'])->name('dental_offices.edit');
    Route::post('/dental_offices/update/{id}', [DentalOfficeController::class, 'update'])->name(
        'dental_offices.update',
    );
    Route::get('/dental_offices/delete/{id}', [DentalOfficeController::class, 'destroy'])->name(
        'dental_offices.delete',
    );
    // Route to fetch states based on selected region
    Route::get('/get-states-by-region/{id}', [App\Http\Controllers\DentalOfficeController::class, 'getStatesByRegion']);

    // AJAX Helper Routes for Dropdowns (Required for the modals to work)
    Route::get('/get-areas/{region_id}', [DentalOfficeController::class, 'getAreas']);
    Route::get('/get-sales-reps/{state_id}', [DentalOfficeController::class, 'getSalesReps']);

    // --- CLIENTS MODULE ---
    // Route::get('/clients', [AdminController::class, 'clients'])->name('clients');
    // Route::post('/clients/store', [AdminController::class, 'storeClient'])->name('clients.store');

    // // NEW: Edit, Update, Delete for Clients
    // Route::get('/clients/{id}/edit', [AdminController::class, 'editClient']);
    // Route::post('/clients/update/{id}', [AdminController::class, 'updateClient'])->name('clients.update');
    // Route::get('/clients/{id}/delete', [AdminController::class, 'deleteClient'])->name('clients.delete');

    // // AJAX Helper for Client Dropdowns (Fetching offices by area)
    // Route::get('/get-offices-by-area/{area_id}', [AdminController::class, 'getOfficesByArea']);

    // --- CLIENT ROUTES ---
    Route::get('/clients', [ClientController::class, 'index'])->name('clients');
    Route::post('/clients/store', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{id}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::post('/clients/update/{id}', [ClientController::class, 'update'])->name('clients.update');
    Route::get('/clients/delete/{id}', [ClientController::class, 'destroy'])->name('clients.delete');

    // AJAX Helper for Dropdowns
    Route::get('/get-offices/{state_id}', [ClientController::class, 'getOfficesByArea']);

    // --- CHARTS & ANALYTICS ---
    Route::get('/get_response', [AdminController::class, 'get_response'])->name('get_response');
    Route::get('/get_top_sales', [AdminController::class, 'get_top_sales'])->name('get_top_sales');
    Route::get('/get_monthly_sales', [AdminController::class, 'get_monthly_sales'])->name('get_monthly_sales');
    Route::get('/get_weekly_sales', [AdminController::class, 'get_weekly_sales'])->name('get_weekly_sales');
    Route::get('/get_reschedule_sales', [AdminController::class, 'get_reschedule_sales'])->name('get_reschedule_sales');
    Route::get('/get_schedule_sales', [AdminController::class, 'get_schedule_sales'])->name('get_schedule_sales');
    Route::get('/get_won_sales', [AdminController::class, 'get_won_sales'])->name('get_won_sales');
    // Route::get('/get_total_sale', [AdminController::class, 'get_total_sale'])->name('get_total_sale');
    Route::get('/get_subscriptions_sale', [AdminController::class, 'get_subscriptions_sale'])->name(
        'get_subscriptions_sale',
    );

    Route::get('/get_total_sale', [AdminController::class, 'get_total_sale'])->name('get_total_sale');
    Route::get('/get_dashboard_stats', [AdminController::class, 'get_dashboard_stats'])->name('get_dashboard_stats');
    // --- REGIONAL MANAGER MODULE ---
    Route::resource('regional_manager', 'App\Http\Controllers\RegionalController');
    Route::post('/regionalmanagerstore', 'App\Http\Controllers\RegionalController@store')->name(
        'regional_manager.store',
    );
    Route::post('/regionalmanagerupdate/{id}', 'App\Http\Controllers\RegionalController@update')->name(
        'regional_manager.update',
    );
    Route::get('/edit-manager/{id}', 'App\Http\Controllers\RegionalController@edit');

    // --- AREA MANAGER MODULE ---
    Route::resource('area_manager', 'App\Http\Controllers\AreaController');
    Route::post('/areamanagerstore', 'App\Http\Controllers\AreaController@store')->name('area_manager.store');
    Route::post('/areamanagerupdate/{id}', 'App\Http\Controllers\AreaController@update')->name('area_manager.update');
    Route::get('/edit-area-manager/{id}', 'App\Http\Controllers\AreaController@edit');
    Route::get('/get-manager-regions/{id}', [App\Http\Controllers\AreaController::class, 'getRegionsByManager']);
    // Add this to routes/web.php
    Route::get('/get-states-by-manager', [App\Http\Controllers\AreaController::class, 'getStatesByManager']);

    // Area Manager AJAX Helpers
    // Route::get('/get-areas/{region_id}', 'App\Http\Controllers\AreaController@getAreas');
    // Added optional {user_id?} parameter to handle Edit Mode correctly
    Route::get('/get-areas/{region_id}/{user_id?}', [App\Http\Controllers\AreaController::class, 'getAreas']);
    Route::get('/get-territories/{area_id}', 'App\Http\Controllers\AreaController@getterritories');

    // --- SALES REPRESENTATIVE MODULE ---
    Route::resource('sales_rep', 'App\Http\Controllers\SalesRepController');
    Route::get('sales_rep/{id}/delete', 'App\Http\Controllers\SalesRepController@destroy')->name('sales_rep.delete');
    // Route::get('/edit-salesrep/{id}', 'App\Http\Controllers\SalesRepController@edit');
    Route::get('/edit-salesrep/{id}', [App\Http\Controllers\SalesRepController::class, 'edit']);
    Route::get('/my-tasks', [App\Http\Controllers\TaskController::class, 'index'])->name('tasks.index');

    // --- Sales Rep Dashboard AJAX Routes ---
    Route::get('/get-rep-performance', [App\Http\Controllers\AdminController::class, 'getRepPerformance']);
    Route::get('/get-rep-revenue', [App\Http\Controllers\AdminController::class, 'getRepRevenue']);
    Route::get('/get-rep-converted-list', [App\Http\Controllers\AdminController::class, 'getRepConvertedList']);

    // Sales Rep AJAX Helpers for Hierarchy
    Route::get('/get-regional-managers/{region_id}', 'App\Http\Controllers\SalesRepController@getRegionalManagers');
    Route::get('/get-area-managers/{regional_manager_id}', 'App\Http\Controllers\SalesRepController@getAreaManagers');
    // Route::get('/get-area-details/{area_manager_id}', 'App\Http\Controllers\SalesRepController@getAreaDetails');
    // UPDATED: Fetch multiple states for an Area Manager
    // Route::get('/get-manager-states/{id}', [App\Http\Controllers\SalesRepController::class, 'getManagerStates']);
    // Added optional {sales_rep_id?} for conflict logic
    Route::get('/get-manager-states/{id}/{sales_rep_id?}', [
        App\Http\Controllers\SalesRepController::class,
        'getManagerStates',
    ]);

    Route::get('/get_capturing_stats', [AdminController::class, 'get_capturing_stats'])->name('get_capturing_stats');

    // Route to start a new batch of tasks
    Route::post('/iterations/start', [App\Http\Controllers\IterationController::class, 'store'])->name(
        'iterations.store',
    );

    Route::post('/tasks/{id}/done', [App\Http\Controllers\TaskController::class, 'markAsDone'])->name('tasks.done');

    // --- TEAM / MANAGER TASK OVERSIGHT ---
    Route::get('/team-tasks', [App\Http\Controllers\TeamTaskController::class, 'index'])->name('team.tasks.index');
    Route::get('/team-tasks/fetch/{rep_id}', [App\Http\Controllers\TeamTaskController::class, 'fetchRepTasks'])->name(
        'team.tasks.fetch',
    );

    // --- NOTIFICATIONS ---
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name(
        'notifications.index',
    );
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name(
        'notifications.read',
    );
    Route::post('/notifications/mark-all-read', [
        App\Http\Controllers\NotificationController::class,
        'markAllAsRead',
    ])->name('notifications.markAllRead');
    Route::get('/notifications/unread-count', [
        App\Http\Controllers\NotificationController::class,
        'unreadCount',
    ])->name('notifications.unreadCount');
});
