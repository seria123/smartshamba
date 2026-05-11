<?php

namespace App\Modules\Audit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Audit\Http\Controllers\Concerns\PreparesAuditRequests;
use App\Modules\Audit\Services\AuditSummaryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditReportController extends Controller
{
    use PreparesAuditRequests;

    public function modules(Request $request, AuditSummaryService $summary): View
    {
        $prepared = $this->prepare($request);
        return view('audit::reports.modules', $prepared + ['rows' => $summary->topModules($prepared['context'], $prepared['filters'])]);
    }

    public function actors(Request $request, AuditSummaryService $summary): View
    {
        $prepared = $this->prepare($request);
        return view('audit::reports.actors', $prepared + ['rows' => $summary->topActors($prepared['context'], $prepared['filters'])]);
    }

    public function actions(Request $request, AuditSummaryService $summary): View
    {
        $prepared = $this->prepare($request);
        return view('audit::reports.actions', $prepared + ['rows' => $summary->topActions($prepared['context'], $prepared['filters'])]);
    }

    public function daily(Request $request, AuditSummaryService $summary): View
    {
        $prepared = $this->prepare($request);
        return view('audit::reports.daily', $prepared + ['rows' => $summary->daily($prepared['context'], $prepared['filters'])]);
    }
}
