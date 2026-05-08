<?php

namespace App\Modules\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notifications\Http\Controllers\Concerns\PreparesNotificationRequests;
use App\Modules\Notifications\Models\FarmNotification;
use App\Modules\Notifications\Services\NotificationLifecycleService;
use App\Modules\Notifications\Services\NotificationSummaryService;
use App\Modules\Notifications\Services\NotificationsAccessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    use PreparesNotificationRequests;

    public function index(Request $request, NotificationSummaryService $summary): View
    {
        $prepared = $this->prepare($request);
        $notifications = $summary->filtered($prepared['context'], $prepared['filters'])->latest()->paginate(20)->withQueryString();

        return view('notifications::index', $prepared + ['notifications' => $notifications]);
    }

    public function show(Request $request, FarmNotification $notification): View
    {
        $this->authorizeNotification($request, $notification);

        return view('notifications::show', ['notification' => $notification->load(['farm', 'rule'])]);
    }

    public function markRead(Request $request, FarmNotification $notification, NotificationLifecycleService $service): RedirectResponse
    {
        $this->authorizeNotification($request, $notification);
        $service->markRead($notification, $request->user());

        return back()->with('status', 'Notification marked read.');
    }

    public function markUnread(Request $request, FarmNotification $notification, NotificationLifecycleService $service): RedirectResponse
    {
        $this->authorizeNotification($request, $notification);
        $service->markUnread($notification, $request->user());

        return back()->with('status', 'Notification marked unread.');
    }

    public function dismiss(Request $request, FarmNotification $notification, NotificationLifecycleService $service): RedirectResponse
    {
        $this->authorizeNotification($request, $notification);
        $service->dismiss($notification, $request->user());

        return back()->with('status', 'Notification dismissed.');
    }

    public function resolve(Request $request, FarmNotification $notification, NotificationLifecycleService $service): RedirectResponse
    {
        $this->authorizeNotification($request, $notification);
        $service->resolve($notification, $request->user());

        return back()->with('status', 'Notification resolved.');
    }

    public function markAllRead(Request $request, NotificationLifecycleService $service): RedirectResponse
    {
        $context = NotificationsAccessContext::forUser($request->user());
        $count = $service->markAllRead($request->user(), $context);

        return back()->with('status', $count.' notifications marked read.');
    }
}
