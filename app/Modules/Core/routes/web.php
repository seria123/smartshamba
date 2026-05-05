<?php

use App\Modules\Core\Http\Controllers\CoreDashboardController;
use App\Modules\Core\Http\Controllers\FarmController;
use App\Modules\Core\Http\Controllers\FieldController;
use App\Modules\Core\Http\Controllers\ModuleRegistryController;
use App\Modules\Core\Http\Controllers\OrganizationController;
use App\Modules\Core\Http\Controllers\PaddockController;
use App\Modules\Core\Http\Controllers\SiteController;
use App\Modules\Core\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/core')->name('admin.core.')->group(function (): void {
    Route::get('/', CoreDashboardController::class)->name('dashboard');

    Route::resource('organizations', OrganizationController::class)->only(['index', 'show']);
    Route::resource('farms', FarmController::class)->only(['index', 'show']);
    Route::resource('sites', SiteController::class)->except(['destroy']);
    Route::resource('fields', FieldController::class)->except(['destroy']);
    Route::resource('paddocks', PaddockController::class)->except(['destroy']);
    Route::resource('warehouses', WarehouseController::class)->except(['destroy']);
    Route::resource('modules', ModuleRegistryController::class)->only(['index']);
});
