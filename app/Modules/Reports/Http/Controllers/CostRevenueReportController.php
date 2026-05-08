<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Http\Controllers\Concerns\PreparesReportRequests;
use App\Modules\Reports\Services\CostRevenueReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CostRevenueReportController extends Controller
{
    use PreparesReportRequests;

    public function index(Request $request, CostRevenueReportService $report): View
    {
        $prepared = $this->prepareReport($request);

        return view('reports::cost-vs-revenue', $prepared + ['rows' => $report->rows($prepared['filters'])]);
    }
}
