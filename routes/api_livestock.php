<?php

use App\Http\Controllers\Api\FoodStockController;
use App\Http\Controllers\Api\LivestockController;
use App\Http\Controllers\Api\LivestockTypeController;
use App\Http\Controllers\LivestockAnalysisController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    // Livestock Types
    Route::apiResource('livestock-types', LivestockTypeController::class);

    // Livestock
    Route::apiResource('livestock', LivestockController::class);
    Route::get('livestock/summary', [LivestockController::class, 'summary'])->name('livestock.summary');

    // AI Livestock Disease Analysis
    Route::apiResource('livestock-analysis', LivestockAnalysisController::class)->parameters([
        'livestock-analysis' => 'livestockAnalysis',
    ]);
    Route::post('livestock-analysis/analyze', [LivestockAnalysisController::class, 'apiAnalyze'])->name('livestock-analysis.analyze');

    // Food Stocks
    Route::apiResource('food-stocks', FoodStockController::class);
    Route::get('food-stocks/alerts', [FoodStockController::class, 'alerts'])->name('food-stocks.alerts');
    Route::get('food-stocks/summary', [FoodStockController::class, 'summary'])->name('food-stocks.summary');
});
