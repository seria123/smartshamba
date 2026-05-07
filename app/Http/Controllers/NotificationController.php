<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the current user.
     */
    public function index(Request $request): View
    {
        $query = Notification::where('user_id', auth()->id());

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('read')) {
            $query->where('is_read', $request->read === 'true');
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        $unreadCount = Notification::where('user_id', auth()->id())->where('is_read', false)->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get unread notifications for dropdown.
     */
    public function getUnread()
    {
        return Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get notification count.
     */
    public function getCount()
    {
        return Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return redirect()->back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return redirect()->back();
    }

    /**
     * Clear all notifications.
     */
    public function clearAll(): RedirectResponse
    {
        Notification::where('user_id', auth()->id())->delete();

        return redirect()->back()->with('success', 'All notifications cleared.');
    }

    /**
     * Create a notification (for internal use).
     */
    public static function createNotification(
        int $userId,
        string $type,
        string $title,
        string $message,
        string $priority = Notification::PRIORITY_NORMAL,
        ?array $data = null
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'priority' => $priority,
            'data' => $data,
        ]);
    }

    /**
     * Create alert notification.
     */
    public static function createAlertNotification(int $userId, string $title, string $message, string $priority = Notification::PRIORITY_NORMAL): Notification
    {
        return self::createNotification($userId, Notification::TYPE_ALERT, $title, $message, $priority);
    }

    /**
     * Create task notification.
     */
    public static function createTaskNotification(int $userId, string $title, string $message): Notification
    {
        return self::createNotification($userId, Notification::TYPE_TASK, $title, $message, Notification::PRIORITY_NORMAL);
    }

    /**
     * Create system notification.
     */
    public static function createSystemNotification(int $userId, string $title, string $message, string $priority = Notification::PRIORITY_NORMAL): Notification
    {
        return self::createNotification($userId, Notification::TYPE_SYSTEM, $title, $message, $priority);
    }

    /**
     * Create sensor alert notification.
     */
    public static function createSensorAlert(int $userId, string $sensorName, string $message): Notification
    {
        return self::createNotification(
            $userId,
            Notification::TYPE_SENSOR,
            "Sensor Alert: {$sensorName}",
            $message,
            Notification::PRIORITY_HIGH
        );
    }

    /**
     * Broadcast to multiple users.
     */
    public static function broadcastToUsers(array $userIds, string $type, string $title, string $message, string $priority = Notification::PRIORITY_NORMAL): void
    {
        foreach ($userIds as $userId) {
            self::createNotification($userId, $type, $title, $message, $priority);
        }
    }

    /**
     * Broadcast to all users.
     */
    public static function broadcastToAll(string $type, string $title, string $message, string $priority = Notification::PRIORITY_NORMAL): void
    {
        $userIds = \App\Models\User::pluck('id')->toArray();
        self::broadcastToUsers($userIds, $type, $title, $message, $priority);
    }
}
