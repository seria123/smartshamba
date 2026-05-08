<?php

namespace App\Modules\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notifications\Services\NotificationSignalDetector;
use App\Modules\Notifications\Services\NotificationsAccessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationRefreshController extends Controller
{
    public function __invoke(Request $request, NotificationSignalDetector $detector): RedirectResponse
    {
        $summary = $detector->refresh(NotificationsAccessContext::forUser($request->user()));

        return back()->with('status', "Alerts refreshed: {$summary['created']} created, {$summary['updated']} updated, {$summary['skipped']} skipped.");
    }
}
