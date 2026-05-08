<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Http\Controllers\Concerns\PreparesReportRequests;
use App\Modules\Reports\Services\CustomerRevenueReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerRevenueReportController extends Controller
{
    use PreparesReportRequests;

    public function index(Request $request, CustomerRevenueReportService $report): View
    {
        $prepared = $this->prepareReport($request);

        return view('reports::customers', $prepared + ['rows' => $report->rows($prepared['filters'])]);
    }
}
