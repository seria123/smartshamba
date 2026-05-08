<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Http\Controllers\Concerns\PreparesReportRequests;
use App\Modules\Reports\Services\OperationalActivityReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperationalActivityReportController extends Controller
{
    use PreparesReportRequests;

    public function index(Request $request, OperationalActivityReportService $report): View
    {
        $prepared = $this->prepareReport($request);

        return view('reports::operations', $prepared + $report->summary($prepared['filters']));
    }
}
