<?php

namespace App\Modules\Audit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Audit\Http\Controllers\Concerns\PreparesAuditRequests;
use App\Modules\Audit\Services\AuditSummaryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditDashboardController extends Controller
{
    use PreparesAuditRequests;

    public function __invoke(Request $request, AuditSummaryService $summary): View
    {
        $prepared = $this->prepare($request);

        return view('audit::dashboard', $prepared + $summary->dashboard($prepared['context'], $prepared['filters']));
    }
}
