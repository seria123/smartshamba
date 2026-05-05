<?php

namespace App\Modules\Crops\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Crops\Models\Crop;
use App\Modules\Crops\Models\CropActivity;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Crops\Models\CropSeason;
use Illuminate\View\View;

class CropDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('crops::dashboard', [
            'cropCount' => Crop::query()->count(),
            'seasonCount' => CropSeason::query()->count(),
            'activeCycleCount' => CropCycle::query()->whereNotIn('status', ['closed', 'cancelled'])->count(),
            'activityCount' => CropActivity::query()->count(),
        ]);
    }
}
