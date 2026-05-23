<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;

use App\Http\Controllers\Student\DashboardController;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\ItemHistoryController;
use App\Http\Controllers\Admin\AnalyticsController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/',
    [RoleController::class, 'index']
)->name('role.choose');

/*
|--------------------------------------------------------------------------
| ADMIN LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', function () {

    return view('admin.login');

})->name('admin.login');

Route::post('/admin/login',
    [AdminAuthController::class, 'login']
)->name('admin.login.post');

/*
|--------------------------------------------------------------------------
| STUDENT ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/student/dashboard',
        [DashboardController::class, 'index']
    )->name('student.dashboard');

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch('/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete('/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('admin')
    ->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function () {

        return view('admin.dashboard');

    })->name('admin.dashboard');

    /*
    |--------------------------------------------------------------------------
    | ROOMS
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'rooms',
        RoomController::class
    );

    /*
    |--------------------------------------------------------------------------
    | ITEMS
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'items',
        ItemController::class
    );

    /*
    |--------------------------------------------------------------------------
    | ITEM HISTORIES
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/item-histories',
        [ItemHistoryController::class, 'index']
    )->name('item-histories.index');

    /*
    |--------------------------------------------------------------------------
    | TOGGLE SCHEDULE
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/room-schedules/{schedule}/toggle',
        [RoomController::class, 'toggleSchedule']
    )->name('room-schedules.toggle');

    /*
    |--------------------------------------------------------------------------
    | ANALYTICS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/analytics/items', 
        [AnalyticsController::class, 'alatBahan']
    )->name('admin.analytics.items');
    Route::get(
        '/analytics/rooms', 
        [AnalyticsController::class, 'ruangan']
    )->name('admin.analytics.rooms');

});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';