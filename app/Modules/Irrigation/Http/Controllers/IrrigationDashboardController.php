<?php

namespace App\Modules\Irrigation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Irrigation\Models\IrrigationEvent;
use App\Modules\Irrigation\Models\IrrigationIssue;
use App\Modules\Irrigation\Models\IrrigationSchedule;
use App\Modules\Irrigation\Models\IrrigationWaterReading;
use App\Modules\Irrigation\Models\IrrigationWaterSource;
use App\Modules\Irrigation\Models\IrrigationZone;
use Illuminate\View\View;

class IrrigationDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('irrigation::dashboard', [
            'activeSources' => IrrigationWaterSource::query()->where('status', 'active')->count(),
            'activeZones' => IrrigationZone::query()->where('status', 'active')->count(),
            'schedulesToday' => IrrigationSchedule::query()->whereDate('scheduled_date', today())->count(),
            'missedSchedules' => IrrigationSchedule::query()->where('status', 'missed')->count(),
            'recentEvents' => IrrigationEvent::query()->count(),
            'readings' => IrrigationWaterReading::query()->count(),
            'openIssues' => IrrigationIssue::query()->whereIn('status', ['open', 'in_progress'])->count(),
        ]);
    }
}
