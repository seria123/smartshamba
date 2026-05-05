<?php

use App\Modules\Crops\Http\Controllers\CropController;
use App\Modules\Crops\Http\Controllers\CropCycleController;
use App\Modules\Crops\Http\Controllers\CropDashboardController;
use App\Modules\Crops\Http\Controllers\CropRecordController;
use App\Modules\Crops\Http\Controllers\CropSeasonController;
use App\Modules\Crops\Http\Controllers\CropVarietyController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/crops')
    ->name('crops.')
    ->middleware(['web', 'auth', 'admin.access:crops.view'])
    ->group(function (): void {
        Route::get('/', CropDashboardController::class)->name('dashboard');

        Route::get('master', [CropController::class, 'index'])->middleware('admin.access:crop-master.view')->name('crops.index');
        Route::get('master/create', [CropController::class, 'create'])->middleware('admin.access:crop-master.create')->name('crops.create');
        Route::post('master', [CropController::class, 'store'])->middleware('admin.access:crop-master.create')->name('crops.store');
        Route::get('master/{crop}', [CropController::class, 'show'])->middleware('admin.access:crop-master.view')->name('crops.show');
        Route::get('master/{crop}/edit', [CropController::class, 'edit'])->middleware('admin.access:crop-master.update')->name('crops.edit');
        Route::put('master/{crop}', [CropController::class, 'update'])->middleware('admin.access:crop-master.update')->name('crops.update');
        Route::post('master/{crop}/deactivate', [CropController::class, 'deactivate'])->middleware('admin.access:crop-master.deactivate')->name('crops.deactivate');

        Route::get('varieties', [CropVarietyController::class, 'index'])->middleware('admin.access:crop-master.view')->name('varieties.index');
        Route::get('varieties/create', [CropVarietyController::class, 'create'])->middleware('admin.access:crop-master.create')->name('varieties.create');
        Route::post('varieties', [CropVarietyController::class, 'store'])->middleware('admin.access:crop-master.create')->name('varieties.store');
        Route::get('varieties/{variety}', [CropVarietyController::class, 'show'])->middleware('admin.access:crop-master.view')->name('varieties.show');
        Route::get('varieties/{variety}/edit', [CropVarietyController::class, 'edit'])->middleware('admin.access:crop-master.update')->name('varieties.edit');
        Route::put('varieties/{variety}', [CropVarietyController::class, 'update'])->middleware('admin.access:crop-master.update')->name('varieties.update');
        Route::post('varieties/{variety}/deactivate', [CropVarietyController::class, 'deactivate'])->middleware('admin.access:crop-master.deactivate')->name('varieties.deactivate');

        Route::get('seasons', [CropSeasonController::class, 'index'])->middleware('admin.access:crop-seasons.view')->name('seasons.index');
        Route::get('seasons/create', [CropSeasonController::class, 'create'])->middleware('admin.access:crop-seasons.create')->name('seasons.create');
        Route::post('seasons', [CropSeasonController::class, 'store'])->middleware('admin.access:crop-seasons.create')->name('seasons.store');
        Route::get('seasons/{season}', [CropSeasonController::class, 'show'])->middleware('admin.access:crop-seasons.view')->name('seasons.show');
        Route::get('seasons/{season}/edit', [CropSeasonController::class, 'edit'])->middleware('admin.access:crop-seasons.update')->name('seasons.edit');
        Route::put('seasons/{season}', [CropSeasonController::class, 'update'])->middleware('admin.access:crop-seasons.update')->name('seasons.update');
        Route::post('seasons/{season}/close', [CropSeasonController::class, 'close'])->middleware('admin.access:crop-seasons.close')->name('seasons.close');

        Route::get('cycles', [CropCycleController::class, 'index'])->middleware('admin.access:crop-cycles.view')->name('cycles.index');
        Route::get('cycles/create', [CropCycleController::class, 'create'])->middleware('admin.access:crop-cycles.create')->name('cycles.create');
        Route::post('cycles', [CropCycleController::class, 'store'])->middleware('admin.access:crop-cycles.create')->name('cycles.store');
        Route::get('cycles/{cycle}', [CropCycleController::class, 'show'])->middleware('admin.access:crop-cycles.view')->name('cycles.show');
        Route::get('cycles/{cycle}/edit', [CropCycleController::class, 'edit'])->middleware('admin.access:crop-cycles.update')->name('cycles.edit');
        Route::put('cycles/{cycle}', [CropCycleController::class, 'update'])->middleware('admin.access:crop-cycles.update')->name('cycles.update');
        Route::post('cycles/{cycle}/close', [CropCycleController::class, 'close'])->middleware('admin.access:crop-cycles.close')->name('cycles.close');
        Route::post('cycles/{cycle}/cancel', [CropCycleController::class, 'cancel'])->middleware('admin.access:crop-cycles.close')->name('cycles.cancel');

        Route::post('cycles/{cycle}/activities', [CropRecordController::class, 'storeActivity'])->middleware('admin.access:crop-activities.create')->name('cycles.activities.store');
        Route::post('cycles/{cycle}/scouting', [CropRecordController::class, 'storeScouting'])->middleware('admin.access:crop-scouting.create')->name('cycles.scouting.store');
        Route::post('cycles/{cycle}/treatments', [CropRecordController::class, 'storeTreatment'])->middleware('admin.access:crop-treatments.create')->name('cycles.treatments.store');
        Route::post('cycles/{cycle}/harvests', [CropRecordController::class, 'storeHarvest'])->middleware('admin.access:crop-harvests.create')->name('cycles.harvests.store');
        Route::post('cycles/{cycle}/losses', [CropRecordController::class, 'storeLoss'])->middleware('admin.access:crop-losses.create')->name('cycles.losses.store');
    });
