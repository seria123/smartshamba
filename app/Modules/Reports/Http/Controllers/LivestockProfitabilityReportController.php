<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Http\Controllers\Concerns\PreparesReportRequests;
use App\Modules\Reports\Services\LivestockProfitabilityReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LivestockProfitabilityReportController extends Controller
{
    use PreparesReportRequests;

    public function index(Request $request, LivestockProfitabilityReportService $report): View
    {
        $prepared = $this->prepareReport($request);

        return view('reports::livestock-profitability', $prepared + ['rows' => $report->rows($prepared['filters'])]);
    }
}
