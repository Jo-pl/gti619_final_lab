<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ReauthenticateController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Auth\LoginController; // Add the LoginController import

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

// Public routes
Route::get('/', function () {
    return view('home');
});

Auth::routes(); // Default Laravel authentication routes

// Home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Client-related routes
Route::middleware(['auth'])->group(function () {
    Route::resource('client', ClientController::class);
});

// Admin-related routes
Route::middleware(['auth', 'check.role:admin'])->group(function () {
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings', [AdminController::class, 'saveSettings'])->name('admin.saveSettings');
    Route::get('/clients/business', [ClientController::class, 'showBusinessClients'])->name('clients.business');
});

// Residential client routes
Route::middleware(['auth', 'check.role:Préposé aux clients résidentiels'])->group(function () {
    Route::get('/clients/residential', [ClientController::class, 'showResidentialClients'])->name('clients.residential');
});

// Change password routes
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [ChangePasswordController::class, 'showChangePasswordForm'])->name('password.change.form');
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->name('password.change');
});

// Session management routes
Route::middleware(['auth'])->group(function () {
    Route::get('/active-sessions', [SessionController::class, 'index'])->name('sessions.index');
    Route::delete('/active-sessions/{id}', [SessionController::class, 'destroy'])->name('sessions.destroy');
});

// Reauthentication routes
Route::get('/reauthenticate', [ReauthenticateController::class, 'showForm'])->name('reauthenticate.form');
Route::post('/reauthenticate', [ReauthenticateController::class, 'reauthenticate'])->name('reauthenticate');

// Protected routes requiring reauthentication
Route::middleware(['auth', 'reauthenticate'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

// Login route (Make sure it's pointing to the correct controller)
Route::post('/login', [LoginController::class, 'login'])->middleware('check.user.lock');

