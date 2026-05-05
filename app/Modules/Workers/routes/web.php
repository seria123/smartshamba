<?php

use App\Modules\Workers\Http\Controllers\LabourAttendanceController;
use App\Modules\Workers\Http\Controllers\LabourDashboardController;
use App\Modules\Workers\Http\Controllers\LabourTeamController;
use App\Modules\Workers\Http\Controllers\LabourWorkerController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/labour')
    ->name('labour.')
    ->middleware(['web', 'auth', 'admin.access:workers.view'])
    ->group(function (): void {
        Route::get('/', LabourDashboardController::class)->name('dashboard');

        Route::get('workers', [LabourWorkerController::class, 'index'])->name('workers.index');
        Route::get('workers/create', [LabourWorkerController::class, 'create'])->middleware('admin.access:workers.create')->name('workers.create');
        Route::post('workers', [LabourWorkerController::class, 'store'])->middleware('admin.access:workers.create')->name('workers.store');
        Route::get('workers/{worker}', [LabourWorkerController::class, 'show'])->name('workers.show');
        Route::get('workers/{worker}/edit', [LabourWorkerController::class, 'edit'])->middleware('admin.access:workers.update')->name('workers.edit');
        Route::put('workers/{worker}', [LabourWorkerController::class, 'update'])->middleware('admin.access:workers.update')->name('workers.update');
        Route::post('workers/{worker}/deactivate', [LabourWorkerController::class, 'deactivate'])->middleware('admin.access:workers.deactivate')->name('workers.deactivate');

        Route::get('teams', [LabourTeamController::class, 'index'])->middleware('admin.access:teams.view')->name('teams.index');
        Route::get('teams/create', [LabourTeamController::class, 'create'])->middleware('admin.access:teams.create')->name('teams.create');
        Route::post('teams', [LabourTeamController::class, 'store'])->middleware('admin.access:teams.create')->name('teams.store');
        Route::get('teams/{team}', [LabourTeamController::class, 'show'])->middleware('admin.access:teams.view')->name('teams.show');
        Route::get('teams/{team}/edit', [LabourTeamController::class, 'edit'])->middleware('admin.access:teams.update')->name('teams.edit');
        Route::put('teams/{team}', [LabourTeamController::class, 'update'])->middleware('admin.access:teams.update')->name('teams.update');
        Route::post('teams/{team}/workers', [LabourTeamController::class, 'attachWorker'])->middleware('admin.access:teams.update')->name('teams.workers.store');
        Route::delete('teams/{team}/workers/{worker}', [LabourTeamController::class, 'detachWorker'])->middleware('admin.access:teams.update')->name('teams.workers.destroy');
        Route::post('teams/{team}/deactivate', [LabourTeamController::class, 'deactivate'])->middleware('admin.access:teams.deactivate')->name('teams.deactivate');

        Route::get('attendance', [LabourAttendanceController::class, 'index'])->middleware('admin.access:attendance.view')->name('attendance.index');
        Route::get('attendance/create', [LabourAttendanceController::class, 'create'])->middleware('admin.access:attendance.record')->name('attendance.create');
        Route::post('attendance', [LabourAttendanceController::class, 'store'])->middleware('admin.access:attendance.record')->name('attendance.store');
        Route::get('attendance/{attendance}/edit', [LabourAttendanceController::class, 'edit'])->middleware('admin.access:attendance.update')->name('attendance.edit');
        Route::put('attendance/{attendance}', [LabourAttendanceController::class, 'update'])->middleware('admin.access:attendance.update')->name('attendance.update');
    });
