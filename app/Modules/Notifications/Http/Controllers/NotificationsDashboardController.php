<?php

namespace App\Modules\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notifications\Http\Controllers\Concerns\PreparesNotificationRequests;
use App\Modules\Notifications\Services\NotificationSummaryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationsDashboardController extends Controller
{
    use PreparesNotificationRequests;

    public function __invoke(Request $request, NotificationSummaryService $summary): View
    {
        $prepared = $this->prepare($request);

        return view('notifications::dashboard', $prepared + $summary->dashboard($prepared['context'], $prepared['filters']));
    }
}
