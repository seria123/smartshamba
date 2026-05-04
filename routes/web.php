<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\CropAnalysisController;
use App\Http\Controllers\CropCycleController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FarmDocumentController;
use App\Http\Controllers\FarmImageController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\LivestockAnalysisController;
use App\Http\Controllers\LivestockController;
use App\Http\Controllers\LivestockTypeController;
use App\Http\Controllers\PlantingScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\YieldEstimationController;
use App\Models\Farmer;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->name('admin.dashboard');

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
    Route::patch('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Farm CRUD routes
Route::middleware(['auth'])->group(function () {
    Route::resource('farms', FarmController::class);
    Route::post('farms/{farm}/images', [FarmImageController::class, 'store'])->name('farms.images.store');
    Route::post('farms/{farm}/images/{image}/set-main', [FarmImageController::class, 'setMain'])->name('farms.images.set-main');
    Route::delete('farms/{farm}/images/{image}', [FarmImageController::class, 'destroy'])->name('farms.images.destroy');
    Route::post('farms/{farm}/documents', [FarmDocumentController::class, 'store'])->name('farms.documents.store');
    Route::delete('farms/{farm}/documents/{document}', [FarmDocumentController::class, 'destroy'])->name('farms.documents.destroy');
});

// Farm onboarding (step 1)
Route::middleware(['auth'])->post('/farms/onboarding', [FarmerController::class, 'store'])->name('farms.onboarding.store');

// Field CRUD routes
Route::middleware(['auth'])->group(function () {
    Route::resource('fields', FieldController::class);
    Route::resource('crops', CropController::class);
     Route::resource('sensors', SensorController::class);
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
    Route::get('expenses/summary', [\App\Http\Controllers\ExpenseController::class, 'summary'])->name('expenses.summary');
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
    Route::resource('reports', \App\Http\Controllers\ReportController::class);
    Route::post('reports/generate', [\App\Http\Controllers\ReportController::class, 'generate'])->name('reports.generate');
    Route::post('revenues/{revenue}/mark-paid', [\App\Http\Controllers\RevenueController::class, 'markAsPaid'])->name('revenues.markAsPaid');
});

// Staff / Labor Management routes
Route::middleware(['auth'])->group(function () {
    Route::resource('staff', StaffController::class);
    Route::get('staff/{staff}/attendance', [StaffController::class, 'attendance'])->name('staff.attendance');
    Route::post('staff/{staff}/attendance', [StaffController::class, 'storeAttendance'])->name('staff.attendance.store');
    Route::get('staff/{staff}/wages', [StaffController::class, 'wages'])->name('staff.wages');
    Route::post('staff/{staff}/wages', [StaffController::class, 'storeWage'])->name('staff.wages.store');
    Route::put('staff/{staff}/wages/{wage}', [StaffController::class, 'updateWage'])->name('staff.wages.update');
    Route::get('staff/{staff}/tasks', [StaffController::class, 'tasks'])->name('staff.tasks');
    Route::post('staff/{staff}/terminate', [StaffController::class, 'terminate'])->name('staff.terminate');
});

// Yield Analysis Routes
Route::middleware(['auth'])->group(function () {
    Route::get('yield_estimations/dashboard', [YieldEstimationController::class, 'dashboard'])->name('yield_estimations.dashboard');
    Route::get('yield_estimations/statistics', [YieldEstimationController::class, 'statistics'])->name('yield_estimations.statistics');
    Route::resource('yield_estimations', YieldEstimationController::class);
});

require __DIR__.'/auth.php';
