<?php

use App\Http\Controllers\Settings\PermissionController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Settings\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // User Management
    Route::resource('settings/users', UserController::class)
        ->names([
            'index' => 'settings.users.index',
            'create' => 'settings.users.create',
            'store' => 'settings.users.store',
            'edit' => 'settings.users.edit',
            'update' => 'settings.users.update',
            'destroy' => 'settings.users.destroy',
        ])
        ->except(['show']);

    // Role Management
    Route::resource('settings/roles', RoleController::class)
        ->names([
            'index' => 'settings.roles.index',
            'create' => 'settings.roles.create',
            'store' => 'settings.roles.store',
            'edit' => 'settings.roles.edit',
            'update' => 'settings.roles.update',
            'destroy' => 'settings.roles.destroy',
        ])
        ->except(['show']);

    // Permissions (read-only)
    Route::get('settings/permissions', [PermissionController::class, 'index'])
        ->name('settings.permissions.index');
});
