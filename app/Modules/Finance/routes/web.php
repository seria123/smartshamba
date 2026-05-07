<?php

use App\Modules\Finance\Http\Controllers\FinanceCostCategoryController;
use App\Modules\Finance\Http\Controllers\FinanceCostCentreController;
use App\Modules\Finance\Http\Controllers\FinanceCostEntryController;
use App\Modules\Finance\Http\Controllers\FinanceDashboardController;
use App\Modules\Finance\Http\Controllers\FinanceReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/finance')
    ->name('finance.')
    ->middleware(['web', 'auth', 'admin.access:finance.view'])
    ->group(function (): void {
        Route::get('/', FinanceDashboardController::class)->name('dashboard');

        Route::resource('categories', FinanceCostCategoryController::class)->except(['destroy'])->middleware('admin.access:finance.manage');
        Route::post('categories/{category}/deactivate', [FinanceCostCategoryController::class, 'deactivate'])->middleware('admin.access:finance.manage')->name('categories.deactivate');

        Route::resource('cost-centres', FinanceCostCentreController::class)->parameters(['cost-centres' => 'costCentre'])->except(['destroy'])->middleware('admin.access:finance.manage');
        Route::post('cost-centres/{costCentre}/deactivate', [FinanceCostCentreController::class, 'deactivate'])->middleware('admin.access:finance.manage')->name('cost-centres.deactivate');

        Route::resource('cost-entries', FinanceCostEntryController::class)->parameters(['cost-entries' => 'costEntry'])->middleware('admin.access:finance.manage');
        Route::post('cost-entries/{costEntry}/confirm', [FinanceCostEntryController::class, 'confirm'])->middleware('admin.access:finance.manage')->name('cost-entries.confirm');
        Route::post('cost-entries/{costEntry}/void', [FinanceCostEntryController::class, 'void'])->middleware('admin.access:finance.manage')->name('cost-entries.void');

        Route::prefix('reports')->name('reports.')->middleware('admin.access:finance.reports')->group(function (): void {
            Route::get('summary', [FinanceReportController::class, 'summary'])->name('summary');
            Route::get('categories', [FinanceReportController::class, 'categories'])->name('categories');
            Route::get('allocations', [FinanceReportController::class, 'allocations'])->name('allocations');
            Route::get('crop-cycles', [FinanceReportController::class, 'cropCycles'])->name('crop-cycles');
            Route::get('livestock', [FinanceReportController::class, 'livestock'])->name('livestock');
            Route::get('assets', [FinanceReportController::class, 'assets'])->name('assets');
        });
    });
