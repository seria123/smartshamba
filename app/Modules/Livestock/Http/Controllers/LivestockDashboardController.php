<?php

namespace App\Modules\Livestock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Livestock\Models\LivestockSpecies;
use App\Modules\Livestock\Models\LivestockTreatmentRecord;
use App\Modules\Livestock\Models\LivestockWithdrawalPeriod;
use Illuminate\View\View;

class LivestockDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('livestock::dashboard', [
            'speciesCount' => LivestockSpecies::query()->count(),
            'animalCount' => LivestockAnimal::query()->count(),
            'groupCount' => LivestockAnimalGroup::query()->count(),
            'treatmentCount' => LivestockTreatmentRecord::query()->count(),
            'activeWithdrawals' => LivestockWithdrawalPeriod::query()->where('status', 'active')->count(),
        ]);
    }
}
