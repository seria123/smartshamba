<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('login', [AuthController::class, 'login'])
        ->name('login.post');

    Route::get('forgot-password', [AuthController::class, 'showLoginForm']) // Temporary - redirect to login
        ->name('password.request');

    Route::post('forgot-password', [AuthController::class, 'login']) // Temporary - redirect to login
        ->name('password.email');

    Route::get('reset-password/{token}', [AuthController::class, 'showLoginForm']) // Temporary - redirect to login
        ->name('password.reset');

    Route::post('reset-password', [AuthController::class, 'login']) // Temporary - redirect to login
        ->name('password.update');

    Route::get('confirm-password', [AuthController::class, 'showLoginForm']) // Temporary - redirect to login
        ->name('password.confirm');

    Route::post('confirm-password', [AuthController::class, 'login']) // Temporary - redirect to login
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [VerificationController::class, 'show'])
        ->middleware('signed')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [AuthController::class, 'showLoginForm']) // Temporary - redirect to login
        ->name('password.confirm');

    Route::post('confirm-password', [AuthController::class, 'login']) // Temporary - redirect to login
        ->name('password.store');

    Route::post('logout', [AuthController::class, 'logout'])
        ->name('logout');
});
