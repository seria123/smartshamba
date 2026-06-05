<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Staff;
use App\Models\StaffNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffNotificationController extends Controller
{
    public function index(Staff $staff): View
    {
        $notifications = $staff->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = $notifications->where('is_read', false)->count();

        return view('staff.notifications.index', compact('staff', 'notifications', 'unreadCount'));
    }

    public function markAsRead(Staff $staff, StaffNotification $notification): RedirectResponse
    {
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Staff $staff): RedirectResponse
    {
        $staff->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Staff $staff, StaffNotification $notification): RedirectResponse
    {
        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted successfully.');
    }
}
