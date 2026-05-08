<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Http\Controllers\Concerns\PreparesReportRequests;
use App\Modules\Reports\Services\CostDriversReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CostDriversReportController extends Controller
{
    use PreparesReportRequests;

    public function index(Request $request, CostDriversReportService $report): View
    {
        $prepared = $this->prepareReport($request);

        return view('reports::cost-drivers', $prepared + ['rows' => $report->rows($prepared['filters'])]);
    }
}
