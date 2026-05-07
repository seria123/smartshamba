<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Finance\Services\CostReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceDashboardController extends Controller
{
    public function __invoke(Request $request, CostReportService $reports): View
    {
        $filters = $request->only(['organization_id', 'farm_id', 'date_from', 'date_to']);

        return view('finance::dashboard', $reports->dashboard($filters) + [
            'filters' => $filters,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'farms' => Farm::query()->orderBy('name')->get(),
        ]);
    }
}
