<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Landing page ──────────────────────────────────────────────────
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ── User Auth (Breeze handles login, register, logout) ────────────
require __DIR__.'/auth.php';

// ── Authenticated user routes ─────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Reports ───────────────────────────────────────────────────
    Route::get('/reports/lost/create',  [ReportController::class, 'createLost'])->name('reports.lost.create');
    Route::post('/reports/lost',        [ReportController::class, 'storeLost'])->name('reports.lost.store');

    Route::get('/reports/found/create', [ReportController::class, 'createFound'])->name('reports.found.create');
    Route::post('/reports/found',       [ReportController::class, 'storeFound'])->name('reports.found.store');

    Route::get('/reports/my-reports',   [ReportController::class, 'myReports'])->name('reports.my');
    Route::get('/reports/search',       [ReportController::class, 'search'])->name('reports.search');
    Route::post('/reports/{type}/{id}/resolve', [ReportController::class, 'resolve'])->name('reports.resolve');
    Route::delete('/reports/{type}/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::get('/reports/{type}/{id}',  [ReportController::class, 'show'])->name('reports.show');

    // ── Notifications ─────────────────────────────────────────────
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // ── In-platform messaging ────────────────────────────────────
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');

});

// ── Admin routes ──────────────────────────────────────────────────
Route::prefix('admin')->group(function () {

    Route::get('/login',  [AdminAuthController::class, 'create'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'store'])->name('admin.login.store');
    Route::post('/logout',[AdminAuthController::class, 'destroy'])->name('admin.logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/reports/lost/create', [AdminDashboardController::class, 'createLost'])->name('admin.reports.lost.create');
        Route::post('/reports/lost', [AdminDashboardController::class, 'storeLost'])->name('admin.reports.lost.store');
        Route::get('/reports/found/create', [AdminDashboardController::class, 'createFound'])->name('admin.reports.found.create');
        Route::post('/reports/found', [AdminDashboardController::class, 'storeFound'])->name('admin.reports.found.store');
        Route::get('/reports', [AdminDashboardController::class, 'reportsIndex'])->name('admin.reports.index');
        Route::get('/reports/{type}/{id}', [AdminDashboardController::class, 'showReport'])->name('admin.reports.show');
        Route::post('/reports/{type}/{id}/resolve', [AdminDashboardController::class, 'resolveReport'])->name('admin.reports.resolve');
        Route::delete('/reports/{type}/{id}', [AdminDashboardController::class, 'destroyReport'])->name('admin.reports.destroy');
    });

});