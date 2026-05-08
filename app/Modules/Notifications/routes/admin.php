<?php

use App\Modules\Notifications\Http\Controllers\NotificationController;
use App\Modules\Notifications\Http\Controllers\NotificationRefreshController;
use App\Modules\Notifications\Http\Controllers\NotificationRuleController;
use App\Modules\Notifications\Http\Controllers\NotificationsDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/notifications')
    ->name('notifications.')
    ->middleware(['web', 'auth', 'admin.access:notifications.view'])
    ->group(function (): void {
        Route::get('/', NotificationsDashboardController::class)->name('dashboard');
        Route::get('list', [NotificationController::class, 'index'])->name('index');
        Route::post('mark-all-read', [NotificationController::class, 'markAllRead'])->middleware('admin.access:notifications.manage')->name('mark-all-read');
        Route::post('refresh', NotificationRefreshController::class)->middleware('admin.access:notifications.manage')->name('refresh');

        Route::prefix('rules')->name('rules.')->middleware('admin.access:notifications.rules.manage')->group(function (): void {
            Route::get('/', [NotificationRuleController::class, 'index'])->name('index');
            Route::get('create', [NotificationRuleController::class, 'create'])->name('create');
            Route::post('/', [NotificationRuleController::class, 'store'])->name('store');
            Route::get('{rule}/edit', [NotificationRuleController::class, 'edit'])->name('edit');
            Route::put('{rule}', [NotificationRuleController::class, 'update'])->name('update');
            Route::post('{rule}/toggle', [NotificationRuleController::class, 'toggle'])->name('toggle');
        });

        Route::get('{notification}', [NotificationController::class, 'show'])->name('show');
        Route::post('{notification}/mark-read', [NotificationController::class, 'markRead'])->middleware('admin.access:notifications.manage')->name('mark-read');
        Route::post('{notification}/mark-unread', [NotificationController::class, 'markUnread'])->middleware('admin.access:notifications.manage')->name('mark-unread');
        Route::post('{notification}/dismiss', [NotificationController::class, 'dismiss'])->middleware('admin.access:notifications.manage')->name('dismiss');
        Route::post('{notification}/resolve', [NotificationController::class, 'resolve'])->middleware('admin.access:notifications.manage')->name('resolve');
    });
