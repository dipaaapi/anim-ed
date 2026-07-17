<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/browse', [HomeController::class, 'browse'])->name('browse');
Route::get('/watch/{id}', [HomeController::class, 'watch'])->name('watch');
Route::get('/read/{id}', [HomeController::class, 'read'])->name('read');
Route::get('/ad/{ad}/click', [HomeController::class, 'clickAd'])->name('ad.click');

// User Authentication
Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider'])->name('auth.redirect');
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('auth.callback');
Route::post('/auth/{provider}/mock', [AuthController::class, 'handleMockLogin'])->name('auth.mock');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Login redirect helper
Route::get('/login', function () {
    return redirect()->route('home')->with('show-login', true);
})->name('login');

// Hidden Admin login routes (defined by environment variable)
$adminPath = env('ADMIN_LOGIN_PATH', 'secret-portal-admin');

Route::get('/' . $adminPath, [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/' . $adminPath, [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin Panel (Protected by Admin Middleware)
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/ads', [AdminDashboardController::class, 'ads'])->name('admin.ads');
    Route::post('/ads', [AdminDashboardController::class, 'storeAd'])->name('admin.ads.store');
    Route::post('/ads/{ad}/toggle', [AdminDashboardController::class, 'toggleAd'])->name('admin.ads.toggle');
    Route::delete('/ads/{ad}', [AdminDashboardController::class, 'deleteAd'])->name('admin.ads.delete');
});
