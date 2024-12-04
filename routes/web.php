<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ReauthenticateController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Auth\LoginController;

// Public routes
Route::get('/', function () {
    return view('home');
});

// Default authentication routes
Auth::routes(); // Includes login, register, logout, and password reset routes

// Home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Client-related routes (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::resource('client', ClientController::class);
});

// Admin routes (auth + admin role check middleware)
Route::middleware(['auth', 'check.role:admin'])->group(function () {
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings', [AdminController::class, 'saveSettings'])->name('admin.saveSettings');
    Route::post('/admin/change-user-password', [AdminController::class, 'changeUserPassword'])
        ->name('admin.change.user.password'); // Ensure this method exists in AdminController
    Route::post('/admin/update-failed-attempts', [AdminController::class, 'updateFailedAttempts'])
        ->name('admin.update.failed.attempts'); // Ensure this method exists in AdminController
});

// Routes for changing passwords
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [ChangePasswordController::class, 'showChangePasswordForm'])
        ->name('password.change.form'); // Display the password change form
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])
        ->name('password.change'); // Process the password change request
});

// Residential client routes (specific role check)
Route::middleware(['auth', 'check.role:Préposé aux clients résidentiels'])->group(function () {
    Route::get('/clients/residential', [ClientController::class, 'showResidentialClients'])->name('clients.residential');
});

// Session management routes
Route::middleware('auth')->group(function () {
    Route::get('/active-sessions', [SessionController::class, 'index'])->name('sessions.index');
    Route::delete('/active-sessions/{id}', [SessionController::class, 'destroy'])->name('sessions.destroy');
});

// Reauthentication routes
Route::get('/reauthenticate', [ReauthenticateController::class, 'showForm'])->name('reauthenticate.form');
Route::post('/reauthenticate', [ReauthenticateController::class, 'reauthenticate'])->name('reauthenticate');

// Protected settings routes (reauthentication middleware)
Route::middleware(['auth', 'reauthenticate'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

// Custom login route (if overriding Auth::routes default login route)
Route::post('/login', [LoginController::class, 'login'])->name('login')->middleware('check.user.lock');
