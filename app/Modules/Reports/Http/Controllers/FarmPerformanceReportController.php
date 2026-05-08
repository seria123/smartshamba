<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Http\Controllers\Concerns\PreparesReportRequests;
use App\Modules\Reports\Services\FarmPerformanceReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FarmPerformanceReportController extends Controller
{
    use PreparesReportRequests;

    public function index(Request $request, FarmPerformanceReportService $report): View
    {
        $prepared = $this->prepareReport($request);

        return view('reports::farm-performance', $prepared + ['rows' => $report->rows($prepared['filters'])]);
    }
}
