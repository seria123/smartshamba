<?php

namespace App\Modules\Documents\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Http\Controllers\Concerns\PreparesDocumentRequests;
use App\Modules\Documents\Services\AttachmentSummaryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentDashboardController extends Controller
{
    use PreparesDocumentRequests;

    public function __invoke(Request $request, AttachmentSummaryService $summary): View
    {
        $prepared = $this->prepare($request);

        return view('documents::dashboard', $prepared + $summary->dashboard($prepared['context'], $prepared['filters']));
    }
}
