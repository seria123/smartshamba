<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetBreakdownRecord;
use App\Modules\Assets\Models\AssetMaintenanceSchedule;
use App\Modules\Assets\Models\AssetUsageRecord;
use Illuminate\View\View;

class AssetDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('assets::dashboard', [
            'activeAssets' => Asset::query()->where('status', 'active')->count(),
            'underMaintenance' => Asset::query()->where('status', 'under_maintenance')->count(),
            'brokenDown' => Asset::query()->where('status', 'broken_down')->count(),
            'dueSoon' => Asset::query()->whereNotNull('next_service_date')->whereDate('next_service_date', '<=', now()->addDays(14))->count(),
            'openBreakdowns' => AssetBreakdownRecord::query()->whereIn('status', ['open', 'in_progress'])->count(),
            'recentUsage' => AssetUsageRecord::query()->whereDate('usage_date', '>=', now()->subDays(30))->count(),
            'plannedSchedules' => AssetMaintenanceSchedule::query()->whereIn('status', ['planned', 'assigned', 'in_progress'])->count(),
        ]);
    }
}
