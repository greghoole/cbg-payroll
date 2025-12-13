<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('coaches', \App\Http\Controllers\CoachController::class);
    Route::resource('appointment-setters', \App\Http\Controllers\AppointmentSetterController::class);
    Route::resource('closers', \App\Http\Controllers\CloserController::class);
    Route::resource('clients', \App\Http\Controllers\ClientController::class);
    Route::resource('one-off-cash-ins', \App\Http\Controllers\OneOffCashInController::class)->except(['show']);
    Route::get('/charges', [\App\Http\Controllers\ChargeController::class, 'index'])->name('charges.index');
    Route::get('/refunds', [\App\Http\Controllers\RefundController::class, 'index'])->name('refunds.index');
    
    // Profile routes
    Route::get('/profile/password', [\App\Http\Controllers\ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    // Help routes
    Route::get('/help/api', [\App\Http\Controllers\HelpController::class, 'api'])->name('help.api');
    
    // Settings routes
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/regenerate-token', [\App\Http\Controllers\SettingsController::class, 'regenerateToken'])->name('settings.regenerate-token');
    
    Route::middleware('admin')->group(function () {
        Route::resource('admins', \App\Http\Controllers\AdminController::class)->except(['show']);
        Route::post('/settings/reset-data', [\App\Http\Controllers\SettingsController::class, 'resetData'])->name('settings.reset-data');
    });
});
