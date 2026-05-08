<?php

namespace App\Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Sales\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesDashboardController extends Controller
{
    public function __invoke(Request $request, SalesReportService $reports): View
    {
        return view('sales::dashboard', $reports->dashboard($request->only(['organization_id', 'farm_id'])));
    }
}
