<?php

use App\Modules\Audit\Http\Controllers\AuditDashboardController;
use App\Modules\Audit\Http\Controllers\AuditLogController;
use App\Modules\Audit\Http\Controllers\AuditReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/audit')
    ->name('audit.')
    ->middleware(['web', 'auth', 'admin.access:audit.view'])
    ->group(function (): void {
        Route::get('/', AuditDashboardController::class)->name('dashboard');
        Route::get('logs', [AuditLogController::class, 'index'])->name('logs.index');
        Route::get('logs/{auditActivityLog}', [AuditLogController::class, 'show'])->name('logs.show');

        Route::prefix('reports')->name('reports.')->middleware('admin.access:audit.reports')->group(function (): void {
            Route::get('modules', [AuditReportController::class, 'modules'])->name('modules');
            Route::get('actors', [AuditReportController::class, 'actors'])->name('actors');
            Route::get('actions', [AuditReportController::class, 'actions'])->name('actions');
            Route::get('daily', [AuditReportController::class, 'daily'])->name('daily');
        });
    });
