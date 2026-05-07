<?php

use App\Modules\Assets\Http\Controllers\AssetCategoryController;
use App\Modules\Assets\Http\Controllers\AssetController;
use App\Modules\Assets\Http\Controllers\AssetDashboardController;
use App\Modules\Assets\Http\Controllers\AssetUsageController;
use App\Modules\Assets\Http\Controllers\BreakdownRecordController;
use App\Modules\Assets\Http\Controllers\MaintenanceRecordController;
use App\Modules\Assets\Http\Controllers\MaintenanceScheduleController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/assets')
    ->name('assets.')
    ->middleware(['web', 'auth', 'admin.access:assets.view'])
    ->group(function (): void {
        Route::get('/', AssetDashboardController::class)->name('dashboard');

        Route::get('categories', [AssetCategoryController::class, 'index'])->middleware('admin.access:asset-categories.view')->name('categories.index');
        Route::get('categories/create', [AssetCategoryController::class, 'create'])->middleware('admin.access:asset-categories.create')->name('categories.create');
        Route::post('categories', [AssetCategoryController::class, 'store'])->middleware('admin.access:asset-categories.create')->name('categories.store');
        Route::get('categories/{category}', [AssetCategoryController::class, 'show'])->middleware('admin.access:asset-categories.view')->name('categories.show');
        Route::get('categories/{category}/edit', [AssetCategoryController::class, 'edit'])->middleware('admin.access:asset-categories.update')->name('categories.edit');
        Route::put('categories/{category}', [AssetCategoryController::class, 'update'])->middleware('admin.access:asset-categories.update')->name('categories.update');
        Route::post('categories/{category}/deactivate', [AssetCategoryController::class, 'deactivate'])->middleware('admin.access:asset-categories.deactivate')->name('categories.deactivate');

        Route::get('items', [AssetController::class, 'index'])->middleware('admin.access:assets.view')->name('items.index');
        Route::get('items/create', [AssetController::class, 'create'])->middleware('admin.access:assets.create')->name('items.create');
        Route::post('items', [AssetController::class, 'store'])->middleware('admin.access:assets.create')->name('items.store');
        Route::get('items/{asset}', [AssetController::class, 'show'])->middleware('admin.access:assets.view')->name('items.show');
        Route::get('items/{asset}/edit', [AssetController::class, 'edit'])->middleware('admin.access:assets.update')->name('items.edit');
        Route::put('items/{asset}', [AssetController::class, 'update'])->middleware('admin.access:assets.update')->name('items.update');
        Route::post('items/{asset}/deactivate', [AssetController::class, 'deactivate'])->middleware('admin.access:assets.deactivate')->name('items.deactivate');
        Route::post('items/{asset}/status', [AssetController::class, 'status'])->middleware('admin.access:assets.update')->name('items.status');

        Route::get('maintenance-schedules', [MaintenanceScheduleController::class, 'index'])->middleware('admin.access:maintenance-schedules.view')->name('maintenance-schedules.index');
        Route::get('maintenance-schedules/create', [MaintenanceScheduleController::class, 'create'])->middleware('admin.access:maintenance-schedules.create')->name('maintenance-schedules.create');
        Route::post('maintenance-schedules', [MaintenanceScheduleController::class, 'store'])->middleware('admin.access:maintenance-schedules.create')->name('maintenance-schedules.store');
        Route::get('maintenance-schedules/{schedule}', [MaintenanceScheduleController::class, 'show'])->middleware('admin.access:maintenance-schedules.view')->name('maintenance-schedules.show');
        Route::get('maintenance-schedules/{schedule}/edit', [MaintenanceScheduleController::class, 'edit'])->middleware('admin.access:maintenance-schedules.update')->name('maintenance-schedules.edit');
        Route::put('maintenance-schedules/{schedule}', [MaintenanceScheduleController::class, 'update'])->middleware('admin.access:maintenance-schedules.update')->name('maintenance-schedules.update');
        Route::post('maintenance-schedules/{schedule}/cancel', [MaintenanceScheduleController::class, 'cancel'])->middleware('admin.access:maintenance-schedules.cancel')->name('maintenance-schedules.cancel');
        Route::post('maintenance-schedules/{schedule}/complete', [MaintenanceScheduleController::class, 'complete'])->middleware('admin.access:maintenance-schedules.update')->name('maintenance-schedules.complete');

        Route::get('maintenance-records', [MaintenanceRecordController::class, 'index'])->middleware('admin.access:maintenance-records.view')->name('maintenance-records.index');
        Route::get('maintenance-records/create', [MaintenanceRecordController::class, 'create'])->middleware('admin.access:maintenance-records.create')->name('maintenance-records.create');
        Route::post('maintenance-records', [MaintenanceRecordController::class, 'store'])->middleware('admin.access:maintenance-records.create')->name('maintenance-records.store');
        Route::get('maintenance-records/{record}', [MaintenanceRecordController::class, 'show'])->middleware('admin.access:maintenance-records.view')->name('maintenance-records.show');

        Route::get('breakdowns', [BreakdownRecordController::class, 'index'])->middleware('admin.access:breakdowns.view')->name('breakdowns.index');
        Route::get('breakdowns/create', [BreakdownRecordController::class, 'create'])->middleware('admin.access:breakdowns.create')->name('breakdowns.create');
        Route::post('breakdowns', [BreakdownRecordController::class, 'store'])->middleware('admin.access:breakdowns.create')->name('breakdowns.store');
        Route::get('breakdowns/{breakdown}', [BreakdownRecordController::class, 'show'])->middleware('admin.access:breakdowns.view')->name('breakdowns.show');
        Route::get('breakdowns/{breakdown}/edit', [BreakdownRecordController::class, 'edit'])->middleware('admin.access:breakdowns.update')->name('breakdowns.edit');
        Route::put('breakdowns/{breakdown}', [BreakdownRecordController::class, 'update'])->middleware('admin.access:breakdowns.update')->name('breakdowns.update');
        Route::post('breakdowns/{breakdown}/resolve', [BreakdownRecordController::class, 'resolve'])->middleware('admin.access:breakdowns.resolve')->name('breakdowns.resolve');

        Route::get('usage', [AssetUsageController::class, 'index'])->middleware('admin.access:asset-usage.view')->name('usage.index');
        Route::get('usage/create', [AssetUsageController::class, 'create'])->middleware('admin.access:asset-usage.create')->name('usage.create');
        Route::post('usage', [AssetUsageController::class, 'store'])->middleware('admin.access:asset-usage.create')->name('usage.store');
    });
