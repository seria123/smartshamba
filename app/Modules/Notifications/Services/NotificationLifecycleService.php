<?php

namespace App\Modules\Notifications\Services;

use App\Models\User;
use App\Modules\Notifications\Models\FarmNotification;

class NotificationLifecycleService
{
    public function markRead(FarmNotification $notification, ?User $user = null): FarmNotification
    {
        $notification->update(['status' => 'read', 'read_at' => now(), 'updated_by_user_id' => $user?->id]);

        return $notification;
    }

    public function markUnread(FarmNotification $notification, ?User $user = null): FarmNotification
    {
        $notification->update(['status' => 'unread', 'read_at' => null, 'updated_by_user_id' => $user?->id]);

        return $notification;
    }

    public function dismiss(FarmNotification $notification, ?User $user = null): FarmNotification
    {
        $notification->update(['status' => 'dismissed', 'dismissed_at' => now(), 'updated_by_user_id' => $user?->id]);

        return $notification;
    }

    public function resolve(FarmNotification $notification, ?User $user = null): FarmNotification
    {
        $notification->update(['status' => 'resolved', 'resolved_at' => now(), 'updated_by_user_id' => $user?->id]);

        return $notification;
    }

    public function markAllRead(User $user, NotificationsAccessContext $context): int
    {
        return $context->applyScope(FarmNotification::query(), 'farm_notifications')
            ->where('status', 'unread')
            ->update(['status' => 'read', 'read_at' => now(), 'updated_by_user_id' => $user->id]);
    }
}
