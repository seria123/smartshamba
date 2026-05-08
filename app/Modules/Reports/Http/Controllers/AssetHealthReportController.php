<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Http\Controllers\Concerns\PreparesReportRequests;
use App\Modules\Reports\Services\AssetHealthReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetHealthReportController extends Controller
{
    use PreparesReportRequests;

    public function index(Request $request, AssetHealthReportService $report): View
    {
        $prepared = $this->prepareReport($request);

        return view('reports::asset-health', $prepared + ['rows' => $report->rows($prepared['filters'])]);
    }
}
