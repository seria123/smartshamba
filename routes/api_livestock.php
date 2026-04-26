<?php

use App\Http\Controllers\Api\DiseaseController;
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

    // Diseases
    Route::apiResource('diseases', DiseaseController::class);
    Route::post('diseases/diagnose', [DiseaseController::class, 'diagnoseLivestock'])->name('diseases.diagnose');
    Route::post('diseases/{livestockDisease}/treat', [DiseaseController::class, 'treatLivestock'])->name('diseases.treat');
    Route::get('diseases/high-severity/list', [DiseaseController::class, 'highSeverity'])->name('diseases.high-severity');
    Route::get('diseases/contagious/list', [DiseaseController::class, 'contagious'])->name('diseases.contagious');
});
