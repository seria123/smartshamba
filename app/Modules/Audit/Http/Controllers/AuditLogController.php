<?php

namespace App\Modules\Audit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Audit\Http\Controllers\Concerns\PreparesAuditRequests;
use App\Modules\Audit\Models\AuditActivityLog;
use App\Modules\Audit\Services\AuditSubjectResolver;
use App\Modules\Audit\Services\AuditSummaryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    use PreparesAuditRequests;

    public function index(Request $request, AuditSummaryService $summary): View
    {
        $prepared = $this->prepare($request);
        $logs = $summary->query($prepared['context'], $prepared['filters'])
            ->with(['organization', 'farm', 'actor'])
            ->latest('occurred_at')
            ->paginate(25)
            ->withQueryString();

        return view('audit::logs.index', $prepared + ['logs' => $logs]);
    }

    public function show(Request $request, AuditActivityLog $auditActivityLog, AuditSubjectResolver $resolver): View
    {
        $context = $this->prepare($request)['context'];
        $scoped = $context->applyScope(AuditActivityLog::query())->whereKey($auditActivityLog->id)->exists();
        abort_unless($scoped, 403);

        $auditActivityLog->load(['organization', 'farm', 'actor']);

        return view('audit::logs.show', [
            'log' => $auditActivityLog,
            'subjectLabel' => $resolver->label($auditActivityLog),
        ]);
    }
}
