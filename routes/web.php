<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\CropAnalysisController;
use App\Http\Controllers\CropCycleController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\LivestockAnalysisController;
use App\Http\Controllers\LivestockController;
use App\Http\Controllers\LivestockTypeController;
use App\Http\Controllers\PlantingScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkerController;
use App\Models\Farmer;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Alert routes
Route::middleware(['auth'])->group(function () {
    Route::resource('alerts', AlertController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('livestock', LivestockController::class);
    Route::post('livestock/{livestock}/mark-read', [LivestockController::class, 'markAsRead'])->name('livestock.markRead');
    Route::get('livestock/{livestock}/locations', [LivestockController::class, 'locationHistory'])->name('livestock.locations.index');
    Route::get('livestock/{livestock}/locations/create', [LivestockController::class, 'createLocation'])->name('livestock.locations.create');
    Route::post('livestock/{livestock}/locations', [LivestockController::class, 'storeLocation'])->name('livestock.locations.store');
    Route::get('livestock/{livestock}/movements', [LivestockController::class, 'movementHistory'])->name('livestock.movements.index');
    Route::get('livestock/{livestock}/grazing-patterns', [LivestockController::class, 'grazingPatterns'])->name('livestock.grazing.patterns');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('livestock-types', LivestockTypeController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('livestock-analysis', LivestockAnalysisController::class);
    Route::get('livestock/{livestock}/analysis-history', [LivestockAnalysisController::class, 'livestockHistory'])
        ->name('livestock.analysis-history');
});

Route::get('/dashboard', function () {
    $recentAnalyses = null;
    if (auth()->check()) {
        $query = \App\Models\CropAnalysis::with(['cropCycle.crop', 'cropCycle.field'])
            ->orderBy('created_at', 'desc')
            ->limit(6);
        
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }
        
        $recentAnalyses = $query->get();
    }
    
    return view('dashboard', compact('recentAnalyses'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Farm CRUD routes
Route::middleware(['auth'])->group(function () {
    Route::resource('farms', FarmController::class);
});

// Field CRUD routes
Route::middleware(['auth'])->group(function () {
    Route::resource('fields', FieldController::class);
});

// Crop Cycle CRUD routes
Route::middleware(['auth'])->group(function () {
    Route::resource('crop_cycles', CropCycleController::class);
});

// Crop Analysis routes (Image-based disease detection)
Route::middleware(['auth'])->group(function () {
    Route::get('crop_analyses', [CropAnalysisController::class, 'index'])->name('crop_analyses.index');
    Route::get('crop_analyses/create', [CropAnalysisController::class, 'create'])->name('crop_analyses.create');
    Route::post('crop_analyses', [CropAnalysisController::class, 'store'])->name('crop_analyses.store');
    Route::get('crop_analyses/{crop_analysis}', [CropAnalysisController::class, 'show'])->name('crop_analyses.show');
    Route::delete('crop_analyses/{crop_analysis}', [CropAnalysisController::class, 'destroy'])->name('crop_analyses.destroy');
    Route::post('crop_analyses/{crop_analysis}/mark-reviewed', [CropAnalysisController::class, 'markReviewed'])->name('crop_analyses.markReviewed');
});

// Planting Schedule routes
Route::middleware(['auth'])->group(function () {
    Route::get('planting-schedules', [PlantingScheduleController::class, 'index'])->name('planting-schedules.index');
    Route::get('planting-schedules/calendar', [PlantingScheduleController::class, 'calendar'])->name('planting-schedules.calendar');
    Route::get('planting-schedules/create', [PlantingScheduleController::class, 'create'])->name('planting-schedules.create');
    Route::post('planting-schedules', [PlantingScheduleController::class, 'store'])->name('planting-schedules.store');
    Route::get('planting-schedules/{plantingSchedule}', [PlantingScheduleController::class, 'show'])->name('planting-schedules.show');
    Route::get('planting-schedules/{plantingSchedule}/edit', [PlantingScheduleController::class, 'edit'])->name('planting-schedules.edit');
    Route::put('planting-schedules/{plantingSchedule}', [PlantingScheduleController::class, 'update'])->name('planting-schedules.update');
    Route::delete('planting-schedules/{plantingSchedule}', [PlantingScheduleController::class, 'destroy'])->name('planting-schedules.destroy');
});

// Finance Routes (Income, Expenses, Reports)
Route::middleware(['auth'])->group(function () {
    Route::resource('revenues', \App\Http\Controllers\RevenueController::class);
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
    Route::get('expenses/summary', [\App\Http\Controllers\ExpenseController::class, 'summary'])->name('expenses.summary');
    Route::resource('reports', \App\Http\Controllers\ReportController::class);
    Route::post('reports/generate', [\App\Http\Controllers\ReportController::class, 'generate'])->name('reports.generate');
    Route::post('revenues/{revenue}/mark-paid', [\App\Http\Controllers\RevenueController::class, 'markAsPaid'])->name('revenues.markAsPaid');
});

// Worker / Labor Management routes
Route::middleware(['auth'])->group(function () {
    Route::resource('workers', WorkerController::class);
    Route::get('workers/{worker}/attendance', [WorkerController::class, 'attendance'])->name('workers.attendance');
    Route::post('workers/{worker}/attendance', [WorkerController::class, 'storeAttendance'])->name('workers.attendance.store');
    Route::get('workers/{worker}/wages', [WorkerController::class, 'wages'])->name('workers.wages');
    Route::post('workers/{worker}/wages', [WorkerController::class, 'storeWage'])->name('workers.wages.store');
    Route::put('workers/{worker}/wages/{wage}', [WorkerController::class, 'updateWage'])->name('workers.wages.update');
    Route::get('workers/{worker}/tasks', [WorkerController::class, 'tasks'])->name('workers.tasks');
    Route::post('workers/{worker}/terminate', [WorkerController::class, 'terminate'])->name('workers.terminate');
});

require __DIR__.'/auth.php';
