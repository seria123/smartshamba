<?php

use App\Modules\UsersPermissions\Http\Controllers\AccessDashboardController;
use App\Modules\UsersPermissions\Http\Controllers\AuthenticatedSessionController;
use App\Modules\UsersPermissions\Http\Controllers\RoleController;
use App\Modules\UsersPermissions\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    });

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');

    Route::prefix('admin/access')
        ->name('access.')
        ->middleware(['auth', 'admin.access:access.manage'])
        ->group(function (): void {
            Route::get('/', AccessDashboardController::class)->name('dashboard');
            Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
            Route::resource('users', UserController::class)->except(['destroy']);
            Route::resource('roles', RoleController::class)->only(['index', 'show']);
        });
});
