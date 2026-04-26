<?php

use App\Http\Controllers\Api\FarmerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CropController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])->post('/auth/login', [FarmerController::class, 'login']);
Route::middleware('web')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])->post('/auth/register', [FarmerController::class, 'register']);

// Web-style login endpoint (uses web middleware, CSRF exempt)
Route::middleware('web')->post('/login', [AuthController::class, 'loginApi'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [FarmerController::class, 'logout']);

    Route::get('/profile', [FarmerController::class, 'profile']);
    Route::put('/profile', [FarmerController::class, 'updateProfile']);

    Route::put('/farm-details', [FarmerController::class, 'updateFarmDetails']);

    Route::get('/crop-history', [FarmerController::class, 'getCropHistory']);
    Route::post('/crop-history', [FarmerController::class, 'addCropHistory']);
    Route::put('/crop-history/{id}', [FarmerController::class, 'updateCropHistory']);
    Route::delete('/crop-history/{id}', [FarmerController::class, 'deleteCropHistory']);

    Route::post('/documents', [FarmerController::class, 'uploadDocument']);
    Route::get('/documents', [FarmerController::class, 'getDocuments']);
    Route::delete('/documents/{id}', [FarmerController::class, 'deleteDocument']);
});

Route::get('/farmers', [FarmerController::class, 'index']);
Route::get('/farmers/{id}', [FarmerController::class, 'show']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/crops', [CropController::class, 'index']);
Route::get('/crops/{id}', [CropController::class, 'show']);
Route::post('/crops', [CropController::class, 'store']);
Route::put('/crops/{id}', [CropController::class, 'update']);
Route::delete('/crops/{id}', [CropController::class, 'destroy']);
Route::get('/crops-categories', [CropController::class, 'getCategories']);
Route::get('/crops-season-types', [CropController::class, 'getSeasonTypes']);

Route::get('/crops/calendar', [CropController::class, 'getCalendar']);
Route::post('/crops/{id}/seasons', [CropController::class, 'addSeason']);

Route::get('/crops/{id}/rotations', [CropController::class, 'getRotations']);
Route::get('/crops/{id}/rotations/suggest', [CropController::class, 'suggestRotations']);
Route::post('/crops/{id}/rotations', [CropController::class, 'addRotation']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/yield-estimations', [CropController::class, 'getYieldEstimations']);
    Route::post('/yield-estimations', [CropController::class, 'createYieldEstimation']);
    Route::put('/yield-estimations/{id}', [CropController::class, 'updateYieldEstimation']);
});
