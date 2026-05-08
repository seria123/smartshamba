<?php

namespace App\Modules\Notifications\Http\Controllers\Concerns;

use App\Modules\Notifications\Models\FarmNotification;
use App\Modules\Notifications\Models\NotificationRule;
use App\Modules\Notifications\Services\NotificationsAccessContext;
use Illuminate\Http\Request;

trait PreparesNotificationRequests
{
    private function prepare(Request $request): array
    {
        $validated = $request->validate([
            'organization_id' => ['nullable', 'integer'],
            'farm_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:80'],
            'severity' => ['nullable', 'string', 'max:80'],
            'source_module' => ['nullable', 'string', 'max:80'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'due_soon' => ['nullable', 'boolean'],
        ]);

        $context = NotificationsAccessContext::forUser($request->user());
        $filters = $context->filters($validated);

        return [
            'context' => $context,
            'filters' => $filters,
            'organizations' => $context->organizations(),
            'farms' => $context->farms($filters['organization_id'] ?? null),
        ];
    }

    private function authorizeNotification(Request $request, FarmNotification $notification): NotificationsAccessContext
    {
        $context = NotificationsAccessContext::forUser($request->user());
        abort_unless($context->applyScope(FarmNotification::query(), 'farm_notifications')->whereKey($notification->id)->exists(), 403);

        return $context;
    }

    private function authorizeRule(Request $request, NotificationRule $rule): NotificationsAccessContext
    {
        $context = NotificationsAccessContext::forUser($request->user());
        abort_unless($context->applyScope(NotificationRule::query(), 'notification_rules')->whereKey($rule->id)->exists(), 403);

        return $context;
    }
}
