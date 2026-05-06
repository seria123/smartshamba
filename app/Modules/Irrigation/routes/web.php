<?php

use App\Modules\Irrigation\Http\Controllers\IrrigationDashboardController;
use App\Modules\Irrigation\Http\Controllers\IrrigationEventController;
use App\Modules\Irrigation\Http\Controllers\IrrigationIssueController;
use App\Modules\Irrigation\Http\Controllers\IrrigationScheduleController;
use App\Modules\Irrigation\Http\Controllers\IrrigationZoneController;
use App\Modules\Irrigation\Http\Controllers\WaterReadingController;
use App\Modules\Irrigation\Http\Controllers\WaterSourceController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/irrigation')
    ->name('irrigation.')
    ->middleware(['web', 'auth', 'admin.access:irrigation.view'])
    ->group(function (): void {
        Route::get('/', IrrigationDashboardController::class)->name('dashboard');

        Route::get('water-sources', [WaterSourceController::class, 'index'])->middleware('admin.access:water-sources.view')->name('water-sources.index');
        Route::get('water-sources/create', [WaterSourceController::class, 'create'])->middleware('admin.access:water-sources.create')->name('water-sources.create');
        Route::post('water-sources', [WaterSourceController::class, 'store'])->middleware('admin.access:water-sources.create')->name('water-sources.store');
        Route::get('water-sources/{source}', [WaterSourceController::class, 'show'])->middleware('admin.access:water-sources.view')->name('water-sources.show');
        Route::get('water-sources/{source}/edit', [WaterSourceController::class, 'edit'])->middleware('admin.access:water-sources.update')->name('water-sources.edit');
        Route::put('water-sources/{source}', [WaterSourceController::class, 'update'])->middleware('admin.access:water-sources.update')->name('water-sources.update');
        Route::post('water-sources/{source}/deactivate', [WaterSourceController::class, 'deactivate'])->middleware('admin.access:water-sources.deactivate')->name('water-sources.deactivate');

        Route::get('zones', [IrrigationZoneController::class, 'index'])->middleware('admin.access:irrigation-zones.view')->name('zones.index');
        Route::get('zones/create', [IrrigationZoneController::class, 'create'])->middleware('admin.access:irrigation-zones.create')->name('zones.create');
        Route::post('zones', [IrrigationZoneController::class, 'store'])->middleware('admin.access:irrigation-zones.create')->name('zones.store');
        Route::get('zones/{zone}', [IrrigationZoneController::class, 'show'])->middleware('admin.access:irrigation-zones.view')->name('zones.show');
        Route::get('zones/{zone}/edit', [IrrigationZoneController::class, 'edit'])->middleware('admin.access:irrigation-zones.update')->name('zones.edit');
        Route::put('zones/{zone}', [IrrigationZoneController::class, 'update'])->middleware('admin.access:irrigation-zones.update')->name('zones.update');
        Route::post('zones/{zone}/deactivate', [IrrigationZoneController::class, 'deactivate'])->middleware('admin.access:irrigation-zones.deactivate')->name('zones.deactivate');

        Route::get('schedules', [IrrigationScheduleController::class, 'index'])->middleware('admin.access:irrigation-schedules.view')->name('schedules.index');
        Route::get('schedules/create', [IrrigationScheduleController::class, 'create'])->middleware('admin.access:irrigation-schedules.create')->name('schedules.create');
        Route::post('schedules', [IrrigationScheduleController::class, 'store'])->middleware('admin.access:irrigation-schedules.create')->name('schedules.store');
        Route::get('schedules/{schedule}', [IrrigationScheduleController::class, 'show'])->middleware('admin.access:irrigation-schedules.view')->name('schedules.show');
        Route::get('schedules/{schedule}/edit', [IrrigationScheduleController::class, 'edit'])->middleware('admin.access:irrigation-schedules.update')->name('schedules.edit');
        Route::put('schedules/{schedule}', [IrrigationScheduleController::class, 'update'])->middleware('admin.access:irrigation-schedules.update')->name('schedules.update');
        Route::post('schedules/{schedule}/cancel', [IrrigationScheduleController::class, 'cancel'])->middleware('admin.access:irrigation-schedules.cancel')->name('schedules.cancel');
        Route::post('schedules/{schedule}/complete', [IrrigationScheduleController::class, 'complete'])->middleware('admin.access:irrigation-schedules.update')->name('schedules.complete');

        Route::get('events', [IrrigationEventController::class, 'index'])->middleware('admin.access:irrigation-events.view')->name('events.index');
        Route::get('events/create', [IrrigationEventController::class, 'create'])->middleware('admin.access:irrigation-events.create')->name('events.create');
        Route::post('events', [IrrigationEventController::class, 'store'])->middleware('admin.access:irrigation-events.create')->name('events.store');
        Route::get('events/{event}', [IrrigationEventController::class, 'show'])->middleware('admin.access:irrigation-events.view')->name('events.show');
        Route::get('events/{event}/edit', [IrrigationEventController::class, 'edit'])->middleware('admin.access:irrigation-events.update')->name('events.edit');
        Route::put('events/{event}', [IrrigationEventController::class, 'update'])->middleware('admin.access:irrigation-events.update')->name('events.update');
        Route::post('events/{event}/cancel', [IrrigationEventController::class, 'cancel'])->middleware('admin.access:irrigation-events.cancel')->name('events.cancel');

        Route::get('readings', [WaterReadingController::class, 'index'])->middleware('admin.access:water-readings.view')->name('readings.index');
        Route::get('readings/create', [WaterReadingController::class, 'create'])->middleware('admin.access:water-readings.create')->name('readings.create');
        Route::post('readings', [WaterReadingController::class, 'store'])->middleware('admin.access:water-readings.create')->name('readings.store');

        Route::get('issues', [IrrigationIssueController::class, 'index'])->middleware('admin.access:irrigation-issues.view')->name('issues.index');
        Route::get('issues/create', [IrrigationIssueController::class, 'create'])->middleware('admin.access:irrigation-issues.create')->name('issues.create');
        Route::post('issues', [IrrigationIssueController::class, 'store'])->middleware('admin.access:irrigation-issues.create')->name('issues.store');
        Route::get('issues/{issue}', [IrrigationIssueController::class, 'show'])->middleware('admin.access:irrigation-issues.view')->name('issues.show');
        Route::get('issues/{issue}/edit', [IrrigationIssueController::class, 'edit'])->middleware('admin.access:irrigation-issues.update')->name('issues.edit');
        Route::put('issues/{issue}', [IrrigationIssueController::class, 'update'])->middleware('admin.access:irrigation-issues.update')->name('issues.update');
        Route::post('issues/{issue}/resolve', [IrrigationIssueController::class, 'resolve'])->middleware('admin.access:irrigation-issues.resolve')->name('issues.resolve');
    });
