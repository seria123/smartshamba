<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Http\Controllers\Concerns\PreparesReportRequests;
use App\Modules\Reports\Services\InventorySnapshotReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventorySnapshotReportController extends Controller
{
    use PreparesReportRequests;

    public function index(Request $request, InventorySnapshotReportService $report): View
    {
        $prepared = $this->prepareReport($request);

        return view('reports::inventory-snapshot', $prepared + ['rows' => $report->rows($prepared['filters'])]);
    }
}
