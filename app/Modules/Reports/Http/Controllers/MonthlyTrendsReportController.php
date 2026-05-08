<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Http\Controllers\Concerns\PreparesReportRequests;
use App\Modules\Reports\Services\MonthlyTrendsReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthlyTrendsReportController extends Controller
{
    use PreparesReportRequests;

    public function index(Request $request, MonthlyTrendsReportService $report): View
    {
        $prepared = $this->prepareReport($request);

        return view('reports::monthly-trends', $prepared + ['rows' => $report->rows($prepared['filters'])]);
    }
}
