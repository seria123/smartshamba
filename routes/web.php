<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\CropAnalysisController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\CropCycleController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FarmDocumentController;
use App\Http\Controllers\CropStageController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\FarmerController;
use  App\Http\Controllers\FeedTypeController;
use App\Http\Controllers\FarmImageController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LivestockAnalysisController;
use App\Http\Controllers\LivestockController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\LivestockTypeController;
use App\Http\Controllers\PlantingScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\HarvestController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\YieldEstimationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Development only: quick login as admin (only available in local environment)
if (app()->environment('local')) {
    Route::get('/dev-login', function () {
        $admin = \App\Models\User::where('email', 'admin@smartshamba.com')->first();
        if ($admin) {
            Auth::login($admin);

            return redirect()->intended(route('admin.dashboard'));
        }
        abort(404, 'Admin user not found. Run database seeders first.');
    })->name('dev.login');
}

// Admin User Management is handled by Filament at /admin/users

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.dashboard');

// Admin User Management
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
});

// Alert routes
Route::middleware(['auth'])->group(function () {
    Route::post('/staff/{staff}/terminate', [StaffController::class, 'terminate'])->name('staff.terminate');
});
// Comments
Route::middleware(['auth'])->group(function () {
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
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
Route::resource('fields', FieldController::class);

// Farm CRUD routes
Route::middleware(['auth'])->group(function () {
    Route::resource('farms', FarmController::class);
    Route::post('farms/{farm}/images', [FarmImageController::class, 'store'])->name('farms.images.store');
    Route::post('farms/{farm}/images/{image}/set-main', [FarmImageController::class, 'setMain'])->name('farms.images.set-main');
    Route::delete('farms/{farm}/images/{image}', [FarmImageController::class, 'destroy'])->name('farms.images.destroy');
    Route::post('farms/{farm}/documents', [FarmDocumentController::class, 'store'])->name('farms.documents.store');
    Route::delete('farms/{farm}/documents/{document}', [FarmDocumentController::class, 'destroy'])->name('farms.documents.destroy');
});
Route::get('/crops/create', [CropController::class, 'create'])->name('crops.create');
Route::get('/crops', [CropController::class, 'index'])->name('crops.index');
Route::post('/crops', [CropController::class, 'store'])->name('crops.store');
Route::get('/crops/{crop}', [CropController::class, 'show'])->name('crops.show');
Route::delete('/crops/{crop}', [CropController::class, 'destroy'])->name('crops.destroy');
Route::put('/crops/{crop}', [CropController::class, 'update'])->name('crops.update');

Route::get('/crops/{crop}/edit', [CropController::class, 'edit'])->name('crops.edit');
// Farm onboarding (step 1)
Route::middleware(['auth'])->post('/farms/onboarding', [FarmerController::class, 'store'])->name('farms.onboarding.store');

// Field CRUD routes
Route::middleware(['auth'])->group(function () {
    Route::resource('yield_estimations', YieldEstimationController::class);
});

// Settings routes
Route::middleware(['auth'])->group(function () {
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/farm', [App\Http\Controllers\SettingsController::class, 'updateFarm'])->name('settings.farm.update');
    Route::put('/settings/notifications', [App\Http\Controllers\SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    Route::put('/settings/system', [App\Http\Controllers\SettingsController::class, 'updateSystem'])->name('settings.system.update');
    Route::put('/settings/password', [App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.password.update');
});

// Crop Cycle CRUD routes
Route::middleware(['auth'])->group(function () {
    Route::resource('crop_cycles', CropCycleController::class);
});

// Activity CRUD routes
Route::middleware(['auth'])->group(function () {
    Route::resource('activities', App\Http\Controllers\ActivityController::class);
});

// Crop Stage CRUD routes
Route::middleware(['auth'])->group(function () {
    Route::resource('crop_stages', App\Http\Controllers\CropStageController::class);
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


Route::get('/crops/create', [CropController::class, 'create'])->name('crops.create');
Route::get('/sensors/create', [SensorController::class, 'create'])
    ->name('sensors.create');
    Route::get('/sensors/{sensor}/edit', [SensorController::class, 'edit'])
    ->name('sensors.edit');
    Route::get('/sensors', [SensorController::class, 'index'])
    ->name('sensors.index');
    Route::put('/sensors/{sensor}', [SensorController::class, 'update'])
    ->name('sensors.update');
Route::get('/sensors/{sensor}', [SensorController::class, 'show'])
    ->name('sensors.show');
Route::delete('/sensors/{sensor}', [SensorController::class, 'destroy'])
    ->name('sensors.destroy');
    Route::post('/sensors', [SensorController::class, 'store'])
    ->name('sensors.store');

    
Route::middleware(['auth'])->group(function () {
   Route::post('/livestock-analysis/{livestockAnalysis}/mark-reviewed', [LivestockAnalysisController::class, 'markReviewed'])
    ->name('livestock-analysis.markReviewed');
});

// Finance Routes (Income, Expenses, Reports)
Route::middleware(['auth'])->group(function () {
    Route::resource('revenues', \App\Http\Controllers\RevenueController::class);
    Route::get('expenses/summary', [\App\Http\Controllers\ExpenseController::class, 'summary'])->name('expenses.summary');
Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
    Route::resource('reports', \App\Http\Controllers\ReportController::class);
    Route::post('reports/generate', [\App\Http\Controllers\ReportController::class, 'generate'])->name('reports.generate');
    Route::post('revenues/{revenue}/mark-paid', [\App\Http\Controllers\RevenueController::class, 'markAsPaid'])->name('revenues.markAsPaid');
    Route::get('/reports/{report}/download', [ReportController::class, 'download'])
        ->name('reports.download');
});

        // Feed Types routes for farmers/users (full CRUD)
        Route::middleware(['auth'])->group(function () {
            Route::resource('feed-types', FeedTypeController::class)
                ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
                ->names([
                    'index' => 'feed-types.index',
                    'create' => 'feed-types.create',
                    'store' => 'feed-types.store',
                    'edit' => 'feed-types.edit',
                    'update' => 'feed-types.update',
                    'destroy' => 'feed-types.destroy',
                ]);
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
Route::middleware(['auth'])->group(function () {
    Route::resource('equipment', EquipmentController::class);
    Route::resource('harvests', HarvestController::class)->except([
        'dashboard'

    ]);

    Route::get('harvests/dashboard', [HarvestController::class, 'dashboard'])->name('harvests.dashboard');
    Route::get('harvests/statistics', [HarvestController::class, 'statistics'])
        ->name('harvests.statistics');
});

// ============================================================
//  EXPORT & IMPORT ROUTES  (Data Tools)
// ============================================================
use App\Http\Controllers\ExportController;

Route::middleware(['auth'])->prefix('exports')->name('exports.')->group(function () {
    // Index / hub page
    Route::get('/', [ExportController::class, 'index'])->name('index');

    // ---- CSV / XLSX exports ----
    Route::get('/sensor-readings', [ExportController::class, 'exportSensorReadings'])->name('sensorReadings');
    Route::get('/tasks',           [ExportController::class, 'exportTasks'])->name('tasks');
    Route::get('/irrigation-logs', [ExportController::class, 'exportIrrigationLogs'])->name('irrigationLogs');
    Route::get('/weather-data',    [ExportController::class, 'exportWeatherData'])->name('weatherData');
    Route::get('/crops',           [ExportController::class, 'exportCrops'])->name('crops');
    Route::get('/crop-analyses',   [ExportController::class, 'exportCropAnalyses'])->name('cropAnalyses');
    Route::get('/livestock',       [ExportController::class, 'exportLivestock'])->name('livestock');
    Route::get('/equipment',       [ExportController::class, 'exportEquipment'])->name('equipment');
    Route::get('/fields',          [ExportController::class, 'exportFields'])->name('fields');
    Route::get('/crop-cycles',     [ExportController::class, 'exportCropCycles'])->name('cropCycles');

    // ---- PDF exports ----
    Route::get('/pdf/livestock',   [ExportController::class, 'pdfLivestock'])->name('pdf.livestock');
    Route::get('/pdf/harvests',    [ExportController::class, 'pdfHarvests'])->name('pdf.harvests');
    Route::get('/pdf/farm/{farm}', [ExportController::class, 'pdfFarmSummary'])->name('pdf.farm');

    // ---- Sample template downloads (import helper) ----
    Route::get('/sample/livestock', [ExportController::class, 'sampleLivestock'])->name('sample.livestock');
    Route::get('/sample/crops',     [ExportController::class, 'sampleCrops'])->name('sample.crops');
    Route::get('/sample/equipment', [ExportController::class, 'sampleEquipment'])->name('sample.equipment');

    // ---- CSV imports ----
    Route::post('/import/livestock', [ExportController::class, 'importLivestock'])->name('import.livestock');
    Route::post('/import/crops',     [ExportController::class, 'importCrops'])->name('import.crops');
    Route::post('/import/equipment', [ExportController::class, 'importEquipment'])->name('import.equipment');
});

// Posts within livestock group
Route::post('/alerts/{alert}/read', [AlertController::class, 'markAsRead'])
    ->name('alerts.markAsRead');
Route::get('/alerts/{alert}/read', function (\App\Models\Alert $alert) {
    return redirect()->route('alerts.show', $alert)
        ->with('error', 'Please use the button to mark alerts as read.');
})->name('alerts.markAsRead.get');
Route::middleware('auth')->group(function () {
    Route::resource('support-tickets', SupportTicketController::class);
});

// Yield Analysis Routes
Route::middleware(['auth'])->group(function () {
    Route::get('yield_estimations/dashboard', [YieldEstimationController::class, 'dashboard'])->name('yield_estimations.dashboard');
    Route::get('yield_estimations/statistics', [YieldEstimationController::class, 'statistics'])->name('yield_estimations.statistics');
    Route::resource('yield_estimations', YieldEstimationController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/bulk-update', [SettingsController::class, 'bulkUpdate'])->name('settings.bulkUpdate');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
   Route::put('/settings/{id}', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/system', [SettingsController::class, 'editSystem'])
        ->name('settings.edit');

});

Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');

// Irrigation Routes
Route::middleware(['auth'])->group(function () {
    Route::get('irrigation', [\App\Http\Controllers\IrrigationController::class, 'index'])->name('irrigation.index');
    Route::get('irrigation/create', [\App\Http\Controllers\IrrigationController::class, 'create'])->name('irrigation.create');
    Route::post('irrigation', [\App\Http\Controllers\IrrigationController::class, 'store'])->name('irrigation.store');
    Route::get('irrigation/{irrigation}', [\App\Http\Controllers\IrrigationController::class, 'show'])->name('irrigation.show');
    Route::get('irrigation/{irrigation}/edit', [\App\Http\Controllers\IrrigationController::class, 'edit'])->name('irrigation.edit');
    Route::put('irrigation/{irrigation}', [\App\Http\Controllers\IrrigationController::class, 'update'])->name('irrigation.update');
    Route::delete('irrigation/{irrigation}', [\App\Http\Controllers\IrrigationController::class, 'destroy'])->name('irrigation.destroy');
    Route::post('irrigation/{irrigation}/start', [\App\Http\Controllers\IrrigationController::class, 'start'])->name('irrigation.start');
    Route::post('irrigation/{irrigation}/stop', [\App\Http\Controllers\IrrigationController::class, 'stop'])->name('irrigation.stop');
    Route::get('irrigation/logs', [\App\Http\Controllers\IrrigationController::class, 'logs'])->name('irrigation.logs');
    Route::get('irrigation/{irrigation}/create-record', [\App\Http\Controllers\IrrigationController::class, 'createRecord'])->name('irrigation.create-record');
    Route::post('irrigation/{irrigation}/store-record', [\App\Http\Controllers\IrrigationController::class, 'storeRecord'])->name('irrigation.store-record');
});

require __DIR__.'/auth.php';
require __DIR__.'/plantid_test.php';
require __DIR__.'/plantid_service_test.php';
