<?php

namespace App\Modules\Documents\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Http\Controllers\Concerns\PreparesDocumentRequests;
use App\Modules\Documents\Services\AttachmentSummaryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttachmentReportController extends Controller
{
    use PreparesDocumentRequests;

    public function summary(Request $request, AttachmentSummaryService $summary): View
    {
        $prepared = $this->prepare($request);
        return view('documents::reports.summary', $prepared + $summary->dashboard($prepared['context'], $prepared['filters']));
    }

    public function byCategory(Request $request, AttachmentSummaryService $summary): View
    {
        $prepared = $this->prepare($request);
        return view('documents::reports.by-category', $prepared + ['rows' => $summary->byCategory($prepared['context'], $prepared['filters'])]);
    }

    public function bySource(Request $request, AttachmentSummaryService $summary): View
    {
        $prepared = $this->prepare($request);
        return view('documents::reports.by-source', $prepared + ['rows' => $summary->bySource($prepared['context'], $prepared['filters'])]);
    }
}
