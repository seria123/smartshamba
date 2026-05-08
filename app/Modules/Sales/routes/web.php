<?php

use App\Modules\Sales\Http\Controllers\SalesCatalogItemController;
use App\Modules\Sales\Http\Controllers\SalesCustomerController;
use App\Modules\Sales\Http\Controllers\SalesDashboardController;
use App\Modules\Sales\Http\Controllers\SalesPaymentController;
use App\Modules\Sales\Http\Controllers\SalesRecordController;
use App\Modules\Sales\Http\Controllers\SalesReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/sales')
    ->name('sales.')
    ->middleware(['web', 'auth', 'admin.access:sales.view'])
    ->group(function (): void {
        Route::get('/', SalesDashboardController::class)->name('dashboard');

        Route::resource('customers', SalesCustomerController::class)->except(['destroy'])->middleware('admin.access:sales.manage');
        Route::resource('catalog-items', SalesCatalogItemController::class)->parameters(['catalog-items' => 'catalogItem'])->except(['destroy'])->middleware('admin.access:sales.manage');

        Route::resource('records', SalesRecordController::class)->parameters(['records' => 'record'])->middleware('admin.access:sales.manage');
        Route::post('records/{record}/confirm', [SalesRecordController::class, 'confirm'])->middleware('admin.access:sales.manage')->name('records.confirm');
        Route::post('records/{record}/void', [SalesRecordController::class, 'void'])->middleware('admin.access:sales.manage')->name('records.void');
        Route::get('records/{record}/payments/create', [SalesPaymentController::class, 'create'])->middleware('admin.access:sales.manage')->name('payments.create');
        Route::post('records/{record}/payments', [SalesPaymentController::class, 'store'])->middleware('admin.access:sales.manage')->name('payments.store');

        Route::prefix('reports')->name('reports.')->middleware('admin.access:sales.reports')->group(function (): void {
            Route::get('summary', [SalesReportController::class, 'summary'])->name('summary');
            Route::get('customers', [SalesReportController::class, 'customers'])->name('customers');
            Route::get('items', [SalesReportController::class, 'items'])->name('items');
            Route::get('crop-cycles', [SalesReportController::class, 'cropCycles'])->name('crop-cycles');
            Route::get('livestock', [SalesReportController::class, 'livestock'])->name('livestock');
            Route::get('gross-margin', [SalesReportController::class, 'grossMargin'])->name('gross-margin');
        });
    });
