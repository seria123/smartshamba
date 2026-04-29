<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

use App\Http\Controllers\AutomationController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\CropAnalysisController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FeedTypeController;
use App\Http\Controllers\LivestockTrackingController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\CropCycleController;
use App\Http\Controllers\FertilizerController;
use App\Http\Controllers\FertilizerTypeController;
use App\Http\Controllers\FertilizerApplicationController;
use App\Http\Controllers\FeedUsageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\FoodStockController;
use App\Http\Controllers\HarvestController;
use App\Http\Controllers\IrrigationController;
 use App\Http\Controllers\LivestockAnalysisController;
 use App\Http\Controllers\LivestockController;
 use App\Http\Controllers\LivestockTypeController;
 use App\Http\Controllers\LivestockWoundAnalysisController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Include authentication routes
require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Profile (Authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

/*
|--------------------------------------------------------------------------
| Main App (Authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('farms', FarmController::class);
    Route::resource('fields', FieldController::class);
    Route::resource('sensors', SensorController::class);
    Route::resource('automation', AutomationController::class);

    Route::get('automation/rules/reorder', [AutomationController::class, 'reorder'])->name('automation.rules.reorder');
    Route::post('automation/rules/update-order', [AutomationController::class, 'updateOrder'])->name('automation.rules.update-order');
    Route::get('automation/process', [AutomationController::class, 'process'])->name('automation.process');

    Route::resource('automation_rules', \App\Http\Controllers\AutomationRuleController::class);
    Route::post('automation_rules/{automation_rule}/toggle', [AutomationRuleController::class, 'toggle'])->name('automation_rules.toggle');

      Route::resource('crops', CropController::class);
      Route::resource('crop_analysis', CropAnalysisController::class);
      Route::post('crop_analysis/{crop_analysis}/mark-reviewed', [CropAnalysisController::class, 'markReviewed'])->name('crop_analysis.markReviewed');
      
       Route::resource('crop_cycles', CropCycleController::class);
       Route::get('crop_cycles/{crop_cycle}/analyze', [CropCycleController::class, 'analyze'])->name('crop_cycles.analyze');

       // Fertilizer Management Routes
       Route::resource('fertilizer_types', FertilizerTypeController::class);
       Route::resource('fertilizers', FertilizerController::class);
       Route::resource('fertilizer_applications', FertilizerApplicationController::class);

       // Feed Management Routes
       Route::resource('feed_usages', FeedUsageController::class);

      /*
      |--------------------------------------------------------------------------
      | Livestock
      |--------------------------------------------------------------------------
      */

     // Livestock Tracking Routes (MUST come before resource route to avoid conflicts)
     Route::get('livestock/tracking', [LivestockTrackingController::class, 'index'])->name('livestock_tracking.index');
     Route::get('livestock/tracking/recent-movements', [LivestockTrackingController::class, 'recentMovements'])->name('livestock_tracking.recent-movements');
     Route::get('livestock/tracking/by-location', [LivestockTrackingController::class, 'byLocation'])->name('livestock_tracking.by-location');

     // Individual livestock location tracking
     Route::get('livestock/{livestock}/locations', [LivestockController::class, 'locationHistory'])->name('livestock.locations.index');
     Route::get('livestock/{livestock}/locations/current', [LivestockController::class, 'currentLocation'])->name('livestock.locations.current');
     Route::get('livestock/{livestock}/locations/create', [LivestockController::class, 'createLocation'])->name('livestock.locations.create');
     Route::post('livestock/{livestock}/locations', [LivestockController::class, 'storeLocation'])->name('livestock.locations.store');
     Route::get('livestock/{livestock}/movements', [LivestockController::class, 'movementHistory'])->name('livestock.movements.index');
     Route::get('livestock/{livestock}/grazing-patterns', [LivestockController::class, 'grazingPatterns'])->name('livestock.grazing.patterns');

     Route::resource('livestock', LivestockController::class);

     Route::resource('livestock_types', LivestockTypeController::class)->names([
        'index' => 'livestock-types.index',
        'create' => 'livestock-types.create',
        'store' => 'livestock-types.store',
        'show' => 'livestock-types.show',
        'edit' => 'livestock-types.edit',
        'update' => 'livestock-types.update',
        'destroy' => 'livestock-types.destroy',
     ]);

     Route::get('livestock/{livestock}/analysis-history', [LivestockAnalysisController::class, 'livestockHistory'])->name('livestock_analysis.livestockHistory');

     Route::resource('livestock_analysis', LivestockAnalysisController::class);
     Route::post('livestock_analysis/{livestockAnalysis}/mark-reviewed', [LivestockAnalysisController::class, 'markReviewed'])->name('livestock_analysis.markReviewed');

     /*
     |--------------------------------------------------------------------------
     | Wounds
     |--------------------------------------------------------------------------
     */

    Route::resource('wounds', LivestockWoundAnalysisController::class);
    Route::patch('wounds/{wound}/treated', [LivestockWoundAnalysisController::class, 'markTreated'])->name('wounds.treated');
    Route::patch('wounds/{wound}/healed', [LivestockWoundAnalysisController::class, 'markHealed'])->name('wounds.healed');
    Route::get('wounds/high-urgency', [LivestockWoundAnalysisController::class, 'highUrgency'])->name('wounds.highUrgency');

    /*
    |--------------------------------------------------------------------------
    | Other Modules
    |--------------------------------------------------------------------------
    */

    Route::resource('feed_types', FeedTypeController::class);

    Route::resource('alerts', \App\Http\Controllers\AlertController::class);
    Route::post('alerts/{alert}/mark-read', [\App\Http\Controllers\AlertController::class, 'markAsRead'])->name('alerts.markAsRead');

    Route::resource('tasks', \App\Http\Controllers\TaskController::class);

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

    Route::resource('irrigation', IrrigationController::class);
    Route::post('irrigation/{irrigation}/start', [IrrigationController::class, 'start'])->name('irrigation.start');
    Route::post('irrigation/{irrigation}/stop', [IrrigationController::class, 'stop'])->name('irrigation.stop');
    Route::get('irrigation/logs', [IrrigationController::class, 'logs'])->name('irrigation.logs');

    Route::resource('reports', ReportController::class)->except(['store']);
    Route::post('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    Route::resource('exports', ExportController::class)->only(['index']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::resource('workers', WorkerController::class);
        Route::post('workers/{worker}/terminate', [WorkerController::class, 'terminate'])->name('workers.terminate');

        Route::resource('harvests', HarvestController::class);
        Route::get('harvests/dashboard', [HarvestController::class, 'dashboard'])->name('harvests.dashboard');
        Route::get('harvests/statistics', [HarvestController::class, 'statistics'])->name('harvests.statistics');

        Route::resource('food-stocks', FoodStockController::class);

        Route::resource('expenses', ExpenseController::class);
        Route::get('expenses/summary', [ExpenseController::class, 'summary'])->name('expenses.summary');

        Route::resource('revenues', RevenueController::class);
        Route::post('revenues/{revenue}/mark-paid', [RevenueController::class, 'markAsPaid'])->name('revenues.mark-paid');

        Route::resource('buyers', BuyerController::class);
        Route::resource('orders', OrderController::class);
    });
