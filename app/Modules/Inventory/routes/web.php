<?php

use App\Modules\Inventory\Http\Controllers\InventoryDashboardController;
use App\Modules\Inventory\Http\Controllers\ProductCategoryController;
use App\Modules\Inventory\Http\Controllers\ProductController;
use App\Modules\Inventory\Http\Controllers\StockController;
use App\Modules\Inventory\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/inventory')
    ->name('inventory.')
    ->middleware(['web', 'auth', 'admin.access:inventory.view'])
    ->group(function (): void {
        Route::get('/', InventoryDashboardController::class)->name('dashboard');

        Route::get('categories', [ProductCategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [ProductCategoryController::class, 'create'])->middleware('admin.access:products.create')->name('categories.create');
        Route::post('categories', [ProductCategoryController::class, 'store'])->middleware('admin.access:products.create')->name('categories.store');
        Route::get('categories/{category}', [ProductCategoryController::class, 'show'])->name('categories.show');
        Route::get('categories/{category}/edit', [ProductCategoryController::class, 'edit'])->middleware('admin.access:products.update')->name('categories.edit');
        Route::put('categories/{category}', [ProductCategoryController::class, 'update'])->middleware('admin.access:products.update')->name('categories.update');
        Route::post('categories/{category}/deactivate', [ProductCategoryController::class, 'deactivate'])->middleware('admin.access:products.deactivate')->name('categories.deactivate');

        Route::get('products', [ProductController::class, 'index'])->middleware('admin.access:products.view')->name('products.index');
        Route::get('products/create', [ProductController::class, 'create'])->middleware('admin.access:products.create')->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->middleware('admin.access:products.create')->name('products.store');
        Route::get('products/{product}', [ProductController::class, 'show'])->middleware('admin.access:products.view')->name('products.show');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->middleware('admin.access:products.update')->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->middleware('admin.access:products.update')->name('products.update');
        Route::post('products/{product}/deactivate', [ProductController::class, 'deactivate'])->middleware('admin.access:products.deactivate')->name('products.deactivate');

        Route::get('suppliers', [SupplierController::class, 'index'])->middleware('admin.access:suppliers.view')->name('suppliers.index');
        Route::get('suppliers/create', [SupplierController::class, 'create'])->middleware('admin.access:suppliers.create')->name('suppliers.create');
        Route::post('suppliers', [SupplierController::class, 'store'])->middleware('admin.access:suppliers.create')->name('suppliers.store');
        Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->middleware('admin.access:suppliers.view')->name('suppliers.show');
        Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->middleware('admin.access:suppliers.update')->name('suppliers.edit');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->middleware('admin.access:suppliers.update')->name('suppliers.update');
        Route::post('suppliers/{supplier}/deactivate', [SupplierController::class, 'deactivate'])->middleware('admin.access:suppliers.deactivate')->name('suppliers.deactivate');

        Route::get('stock', [StockController::class, 'balances'])->middleware('admin.access:stock.view')->name('stock.balances');
        Route::get('movements', [StockController::class, 'movements'])->middleware('admin.access:stock.view')->name('movements.index');
        Route::get('stock/receive', [StockController::class, 'receiveForm'])->middleware('admin.access:stock.receive')->name('stock.receive.form');
        Route::post('stock/receive', [StockController::class, 'receive'])->middleware('admin.access:stock.receive')->name('stock.receive');
        Route::get('stock/issue', [StockController::class, 'issueForm'])->middleware('admin.access:stock.issue')->name('stock.issue.form');
        Route::post('stock/issue', [StockController::class, 'issue'])->middleware('admin.access:stock.issue')->name('stock.issue');
        Route::get('stock/transfer', [StockController::class, 'transferForm'])->middleware('admin.access:stock.transfer')->name('stock.transfer.form');
        Route::post('stock/transfer', [StockController::class, 'transfer'])->middleware('admin.access:stock.transfer')->name('stock.transfer');
        Route::get('stock/adjust', [StockController::class, 'adjustmentForm'])->middleware('admin.access:stock.adjust')->name('stock.adjust.form');
        Route::post('stock/adjust', [StockController::class, 'adjust'])->middleware('admin.access:stock.adjust')->name('stock.adjust');
    });
