<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Http\Controllers\Concerns\PreparesReportRequests;
use App\Modules\Reports\Services\CropProfitabilityReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CropProfitabilityReportController extends Controller
{
    use PreparesReportRequests;

    public function index(Request $request, CropProfitabilityReportService $report): View
    {
        $prepared = $this->prepareReport($request);

        return view('reports::crop-profitability', $prepared + ['rows' => $report->rows($prepared['filters'])]);
    }
}
