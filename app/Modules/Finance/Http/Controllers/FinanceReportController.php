<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Finance\Services\CostReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceReportController extends Controller
{
    public function summary(Request $request, CostReportService $reports): View
    {
        $filters = $request->only(['organization_id', 'farm_id', 'date_from', 'date_to']);
        return view('finance::reports.summary', $reports->dashboard($filters) + ['filters' => $filters, 'farms' => Farm::orderBy('name')->get(), 'organizations' => Organization::orderBy('name')->get()]);
    }

    public function categories(Request $request, CostReportService $reports): View
    {
        return view('finance::reports.table', ['title' => 'Costs by category', 'rows' => $reports->byCategory($request->only(['organization_id', 'farm_id', 'date_from', 'date_to']))]);
    }

    public function allocations(Request $request, CostReportService $reports): View
    {
        return view('finance::reports.table', ['title' => 'Costs by allocation type', 'rows' => $reports->byAllocation($request->only(['organization_id', 'farm_id']))]);
    }

    public function cropCycles(Request $request, CostReportService $reports): View
    {
        return view('finance::reports.table', ['title' => 'Crop cycle allocations', 'rows' => $reports->byAllocation($request->only(['organization_id', 'farm_id']))->where('label', 'crop_cycle')]);
    }

    public function livestock(Request $request, CostReportService $reports): View
    {
        return view('finance::reports.table', ['title' => 'Livestock allocations', 'rows' => $reports->byAllocation($request->only(['organization_id', 'farm_id']))->whereIn('label', ['livestock_animal', 'livestock_group', 'livestock_event'])]);
    }

    public function assets(Request $request, CostReportService $reports): View
    {
        return view('finance::reports.table', ['title' => 'Asset and maintenance allocations', 'rows' => $reports->byAllocation($request->only(['organization_id', 'farm_id']))->whereIn('label', ['asset', 'maintenance_record'])]);
    }
}
