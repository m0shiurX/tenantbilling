<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\PasswordController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\TwoFactorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Account Routes (Personal Settings)
|--------------------------------------------------------------------------
|
| Routes for user's personal account settings including profile,
| password, two-factor authentication, and appearance preferences.
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('account', [AccountController::class, 'index'])->name('account.index');

    Route::get('account/profile', [ProfileController::class, 'edit'])->name('account.profile.edit');
    Route::patch('account/profile', [ProfileController::class, 'update'])->name('account.profile.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::delete('account/profile', [ProfileController::class, 'destroy'])->name('account.profile.destroy');

    Route::get('account/password', [PasswordController::class, 'edit'])->name('account.password.edit');

    Route::put('account/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('account.password.update');

    Route::get('account/two-factor', [TwoFactorController::class, 'show'])
        ->name('account.two-factor.show');
});
