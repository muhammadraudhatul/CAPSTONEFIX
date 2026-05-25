<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;

use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\BorrowingController;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\ItemHistoryController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\BorrowingController as AdminBorrowingController;

// ============================================
// TAMBAHKAN IMPORT UNTUK API CONTROLLER (opsional)
// ============================================
use App\Http\Controllers\Api\AnalyticsApiController;

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

    Route::get('/student/borrowings/create',
        [BorrowingController::class, 'create']
    )->name('student.borrowings.create');

    Route::post('/student/borrowings',
        [BorrowingController::class, 'store']
    )->name('student.borrowings.store');

    Route::get('/student/available-schedules',
        [BorrowingController::class, 'availableSchedules']
    );

    Route::get('/student/borrowings/{borrowing}/return',
        [BorrowingController::class, 'returnForm']
    )->name('student.borrowings.return.form');

    Route::patch('/student/borrowings/{borrowing}/return',
        [BorrowingController::class, 'submitReturn']
    )->name('student.borrowings.return.submit');

    Route::get('/student/borrowings/{borrowing}/edit',
        [BorrowingController::class, 'edit']
    )->name('student.borrowings.edit');

    Route::patch('/student/borrowings/{borrowing}',
        [BorrowingController::class, 'update']
    )->name('student.borrowings.update');

    Route::patch('/student/borrowings/{borrowing}/cancel',
        [BorrowingController::class, 'cancel']
    )->name('student.borrowings.cancel');

    Route::get('/student/borrowings/{borrowing}/edit',
        [BorrowingController::class, 'edit']
    )->name('student.borrowings.edit');

    Route::patch('/student/borrowings/{borrowing}',
        [BorrowingController::class, 'update']
    )->name('student.borrowings.update');

    Route::delete('/student/borrowings/{borrowing}',
        [BorrowingController::class, 'destroy']
    )->name('student.borrowings.destroy');
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

    Route::get(
        '/item-histories/export/excel',
        [ItemHistoryController::class, 'exportExcel']
    )->name('item-histories.export.excel');

    Route::get(
        '/item-histories/export/csv',
        [ItemHistoryController::class, 'exportCsv']
    )->name('item-histories.export.csv');

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
    | ANALYTICS (WEB VIEW)
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

    /*
    |--------------------------------------------------------------------------
    | BORROWINGS (ADMIN)
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/borrowings',
        [AdminBorrowingController::class, 'index']
    )->name('admin.borrowings.index');

    Route::patch(
        '/borrowings/{borrowing}/approve',
        [AdminBorrowingController::class, 'approve']
    )->name('admin.borrowings.approve');

    Route::patch(
        '/borrowings/{borrowing}/reject',
        [AdminBorrowingController::class, 'reject']
    )->name('admin.borrowings.reject');

    Route::patch(
        '/borrowings/{borrowing}/complete',
        [AdminBorrowingController::class, 'complete']
    )->name('admin.borrowings.complete');

});

/*
|--------------------------------------------------------------------------
| ============================================
| TAMBAHAN ROUTE UNTUK ANALYTICS API (AI SERVICE)
| ============================================
| Route ini bisa diakses oleh:
| 1. Flutter Mobile App
| 2. JavaScript/AJAX dari web
| 3. Postman untuk testing
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {

    // ============================================
    // ALAT & BAHAN ANALYTICS
    // ============================================
    
    // Ringkasan semua alat/bahan (untuk dashboard)
    Route::get('/analytics/alat-bahan/summary', 
        [AnalyticsController::class, 'apiAlatBahanSummary']
    )->name('api.analytics.alat-bahan.summary');
    
    // Detail prediksi stok untuk satu barang
    Route::post('/predict/stok', 
        [AnalyticsController::class, 'apiPredictStok']
    )->name('api.predict.stok');
    
    // Prediksi kebutuhan stok untuk periode tertentu
    Route::post('/predict/kebutuhan-stok', 
        [AnalyticsController::class, 'apiPredictKebutuhanStok']
    )->name('api.predict.kebutuhan-stok');
    
    // Daftar semua barang (master)
    Route::get('/master/barang', 
        [AnalyticsController::class, 'apiMasterBarang']
    )->name('api.master.barang');

    // ============================================
    // RUANGAN ANALYTICS
    // ============================================
    
    // Ringkasan semua ruangan
    Route::get('/analytics/ruangan/summary', 
        [AnalyticsController::class, 'apiRuanganSummary']
    )->name('api.analytics.ruangan.summary');
    
    // Trend penggunaan ruangan (per bulan)
    Route::get('/analytics/ruangan/trend', 
        [AnalyticsController::class, 'apiRuanganTrend']
    )->name('api.analytics.ruangan.trend');
    
    // Heatmap penggunaan ruangan (per jam)
    Route::get('/analytics/ruangan/heatmap', 
        [AnalyticsController::class, 'apiRuanganHeatmap']
    )->name('api.analytics.ruangan.heatmap');
    
    // Rekomendasi ruangan kosong
    Route::post('/predict/ruangan', 
        [AnalyticsController::class, 'apiPredictRuangan']
    )->name('api.predict.ruangan');
    
    // Daftar semua ruangan (master)
    Route::get('/master/ruangan', 
        [AnalyticsController::class, 'apiMasterRuangan']
    )->name('api.master.ruangan');

    // ============================================
    // HEALTH CHECK (untuk monitoring AI Service)
    // ============================================
    
    Route::get('/ai-service/health', 
        [AnalyticsController::class, 'apiAiServiceHealth']
    )->name('api.ai-service.health');

});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';