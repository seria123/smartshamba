<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CropAnalysisController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\CropCycleController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FarmDocumentController;
use App\Http\Controllers\CropStageController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\FeedUsageController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\BreedController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\CropAnalysisImageController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FertilizerApplicationController;
use App\Http\Controllers\FertilizerController;
use App\Http\Controllers\FertilizerTypeController;
use App\Http\Controllers\AutomationRuleController;
use App\Http\Controllers\HarvestController;
use App\Http\Controllers\IrrigationController;
use App\Http\Controllers\IrrigationZoneController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\YieldEstimationController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\SensorReadingController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\WeatherDataController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TaskController;

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
use App\Http\Controllers\FarmerDocumentController;

use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffFieldAssignmentController;
use App\Http\Controllers\StaffSkillController;
use App\Http\Controllers\StaffScheduleController;
use App\Http\Controllers\StaffPerformanceReviewController;
use App\Http\Controllers\StaffNotificationController;
use App\Http\Controllers\StaffProofOfWorkController;
use App\Http\Controllers\StaffLocationController;
use App\Http\Controllers\StaffActivityLogController;
use App\Http\Controllers\StaffAnalyticsController;

use App\Http\Controllers\SprayingScheduleController;
use App\Http\Controllers\PestControlScheduleController;
use App\Http\Controllers\LivestockVaccinationScheduleController;
use App\Http\Controllers\LivestockDewormingScheduleController;
use App\Http\Controllers\LivestockFumigationScheduleController;
use App\Http\Controllers\FumigationScheduleController;
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
Route::get('/profile/edit', [ProfileController::class, 'edit'])
    ->name('profile.edit');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('farms', FarmController::class);
    Route::resource('fields', FieldController::class);
    Route::resource('crops', CropController::class);
    Route::resource('crop_cycles', CropCycleController::class);
    Route::resource('crop_stages', CropStageController::class);
    Route::resource('livestock', LivestockController::class);
    Route::resource('livestock_types', LivestockTypeController::class);
    Route::resource('breeds', BreedController::class);
    Route::resource('livestock_analyses', LivestockAnalysisController::class);
    Route::resource('farmer_documents', FarmerDocumentController::class);
    Route::resource('farm_documents', FarmDocumentController::class);
    Route::resource('farm_images', FarmImageController::class);
    Route::resource('equipment', EquipmentController::class);
    Route::resource('activities', ActivityController::class);
    Route::resource('alerts', AlertController::class);
    Route::resource('notifications', NotificationController::class);
    Route::resource('support_tickets', SupportTicketController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('sensors', SensorController::class);
    Route::resource('irrigation_zones', IrrigationZoneController::class);
    Route::resource('weather_data', WeatherDataController::class);
    Route::resource('sensor_readings', SensorReadingController::class);
    Route::resource('harvests', HarvestController::class);
    Route::resource('revenues', RevenueController::class);
    Route::post('revenues/{revenue}/mark-as-paid', [RevenueController::class, 'markAsPaid'])->name('revenues.markAsPaid');
    Route::resource('buyers', BuyerController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('feed_types', FeedTypeController::class);
    Route::resource('feed_usages', FeedUsageController::class);
    Route::resource('fertilizers', FertilizerController::class);
    Route::resource('fertilizer_types', FertilizerTypeController::class);
    Route::resource('fertilizer_applications', FertilizerApplicationController::class);
    Route::resource('crop_analyses', CropAnalysisController::class);
    Route::resource('crop_analysis_images', CropAnalysisImageController::class);
    Route::resource('automation_rules', AutomationRuleController::class);
    Route::resource('planting_schedules', PlantingScheduleController::class);
    Route::resource('spraying_schedules', SprayingScheduleController::class);
    Route::resource('pest_control_schedules', PestControlScheduleController::class);
    Route::resource('livestock_vaccination_schedules', LivestockVaccinationScheduleController::class);
    Route::resource('livestock_deworming_schedules', LivestockDewormingScheduleController::class);
    Route::resource('livestock_fumigation_schedules', LivestockFumigationScheduleController::class);
    Route::resource('fumigation_schedules', FumigationScheduleController::class);
    Route::get('fumigation-schedules/dashboard', [FumigationScheduleController::class, 'dashboard'])->name('fumigation_schedules.dashboard');
    Route::post('fumigation-schedules/{fumigationSchedule}/start', [FumigationScheduleController::class, 'startFumigation'])->name('fumigation_schedules.start');
    Route::post('fumigation-schedules/{fumigationSchedule}/complete', [FumigationScheduleController::class, 'completeFumigation'])->name('fumigation_schedules.complete');
    Route::post('fumigation-schedules/{fumigationSchedule}/ventilate', [FumigationScheduleController::class, 'startVentilation'])->name('fumigation_schedules.ventilate');
    Route::post('fumigation-schedules/{fumigationSchedule}/photos', [FumigationScheduleController::class, 'uploadPhoto'])->name('fumigation_schedules.photos.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('irrigation', [\App\Http\Controllers\IrrigationController::class, 'index'])->name('irrigation.index');
    Route::get('irrigation/create', [\App\Http\Controllers\IrrigationController::class, 'create'])->name('irrigation.create');
    Route::post('irrigation', [\App\Http\Controllers\IrrigationController::class, 'store'])->name('irrigation.store');
    Route::get('irrigation/{irrigation}', [\App\Http\Controllers\IrrigationController::class, 'show'])->name('irrigation.show');
    Route::get('irrigation/{irrigation}/edit', [\App\Http\Controllers\IrrigationController::class, 'edit'])->name('irrigation.edit');
    Route::put('irrigation/{irrigation}', [\App\Http\Controllers\IrrigationController::class, 'update'])->name('irrigation.update');
    Route::delete('irrigation/{irrigation}', [\App\Http\Controllers\IrrigationController::class, 'destroy'])->name('irrigation.destroy');
});
Route::get('/livestock-types', [LivestockTypeController::class, 'index'])
    ->name('livestock-types.index');

    Route::resource('livestock-analysis', LivestockAnalysisController::class);
    Route::resource('feed-types', FeedTypeController::class);
    Route::get('staff/presence/fields', [StaffLocationController::class, 'fieldPresence'])->name('staff.presence.fields');
Route::get('staff/analytics', [StaffAnalyticsController::class, 'index'])->name('staff.analytics.index');
Route::get('staff/{staff}/analytics', [StaffAnalyticsController::class, 'show'])->name('staff.analytics.show');
    Route::resource('staff.field_assignments', StaffFieldAssignmentController::class)->shallow();
    Route::resource('staff.skills', StaffSkillController::class)->shallow();
    Route::resource('staff.schedules', StaffScheduleController::class)->shallow();
    Route::resource('staff.performance', StaffPerformanceReviewController::class)->shallow();
    Route::resource('staff.notifications', StaffNotificationController::class)->shallow();
    Route::resource('staff.proofs', StaffProofOfWorkController::class)->shallow();
    Route::resource('staff.locations', StaffLocationController::class)->shallow();
    Route::resource('staff.activity_logs', StaffActivityLogController::class)->shallow();
Route::resource('staff', StaffController::class);
Route::get('/expenses/summary', [ExpenseController::class, 'summary'])
    ->name('expenses.summary');
Route::resources([
    'expenses' => ExpenseController::class,
]);
Route::get('finance', [FinanceController::class, 'dashboard'])->name('finance.dashboard');
Route::resource('budgets', BudgetController::class)->except(['show']);
Route::resource('loans', LoanController::class)->except(['show']);
Route::resource('reports', ReportController::class);
Route::resource('yield-estimations', YieldEstimationController::class);

Route::middleware(['auth'])->group(function () {
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'store'])->name('settings.store');
});
Route::get('/exports', [ExportController::class, 'index'])->name('exports.index');
Route::middleware(['auth'])->group(function () {
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/react', [CommentController::class, 'react'])->name('comments.react');
    Route::post('/comments/{comment}/report', [CommentController::class, 'report'])->name('comments.report');
    Route::post('/comments/{comment}/moderate', [CommentController::class, 'moderate'])->name('comments.moderate');
    Route::post('/comments/{comment}/convert', [CommentController::class, 'convert'])->name('comments.convert');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/support-tickets/chat', [SupportTicketController::class, 'chat'])->name('support-tickets.chat');
    Route::get('/support-tickets/knowledge-base', [SupportTicketController::class, 'knowledgeBase'])->name('support-tickets.knowledge-base');
    Route::post('/support-tickets/{supportTicket}/rate', [SupportTicketController::class, 'rate'])->name('support-tickets.rate');
    Route::resource('support-tickets', SupportTicketController::class)->only(['index', 'create', 'store', 'show']);
});



require __DIR__.'/auth.php';
