<?php

use App\Modules\Reports\Http\Controllers\AssetHealthReportController;
use App\Modules\Reports\Http\Controllers\CostDriversReportController;
use App\Modules\Reports\Http\Controllers\CostRevenueReportController;
use App\Modules\Reports\Http\Controllers\CropProfitabilityReportController;
use App\Modules\Reports\Http\Controllers\CustomerRevenueReportController;
use App\Modules\Reports\Http\Controllers\FarmPerformanceReportController;
use App\Modules\Reports\Http\Controllers\InventorySnapshotReportController;
use App\Modules\Reports\Http\Controllers\LivestockProfitabilityReportController;
use App\Modules\Reports\Http\Controllers\MonthlyTrendsReportController;
use App\Modules\Reports\Http\Controllers\OperationalActivityReportController;
use App\Modules\Reports\Http\Controllers\ReportsDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/reports')
    ->name('reports.')
    ->middleware(['web', 'auth', 'admin.access:reports.view'])
    ->group(function (): void {
        Route::get('/', [ReportsDashboardController::class, 'index'])->name('dashboard');
        Route::get('farm-performance', [FarmPerformanceReportController::class, 'index'])->name('farm-performance');
        Route::get('crop-profitability', [CropProfitabilityReportController::class, 'index'])->name('crop-profitability');
        Route::get('livestock-profitability', [LivestockProfitabilityReportController::class, 'index'])->name('livestock-profitability');
        Route::get('cost-vs-revenue', [CostRevenueReportController::class, 'index'])->name('cost-vs-revenue');
        Route::get('customers', [CustomerRevenueReportController::class, 'index'])->name('customers');
        Route::get('cost-drivers', [CostDriversReportController::class, 'index'])->name('cost-drivers');
        Route::get('monthly-trends', [MonthlyTrendsReportController::class, 'index'])->name('monthly-trends');
        Route::get('operations', [OperationalActivityReportController::class, 'index'])->name('operations');
        Route::get('inventory-snapshot', [InventorySnapshotReportController::class, 'index'])->name('inventory-snapshot');
        Route::get('asset-health', [AssetHealthReportController::class, 'index'])->name('asset-health');
    });
