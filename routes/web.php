<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('home');
});
// Route with custom throttle middleware (limit to 3 attempts per 1 minute)
Route::post('login', [LoginController::class, 'login'])
    ->middleware('customThrottle:3,1');  // Ensure this middleware name is correct

// Resource routes for ClientController (This includes the 'edit' route)
Route::resource('client', ClientController::class);

// Auth routes
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin routes
Route::middleware(['auth', 'check.role:admin'])->group(function () {
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings', [AdminController::class, 'saveSettings'])->name('admin.saveSettings');
    Route::get('/clients/business', [ClientController::class, 'showBusinessClients'])->name('clients.business');
});

// Residential client routes
Route::middleware(['auth', 'check.role:Préposé aux clients résidentiels'])->group(function () {
   Route::get('/clients/residential', [ClientController::class, 'showResidentialClients'])->name('clients.residential');
});
