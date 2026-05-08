<?php

namespace App\Modules\Notifications\Services;

use App\Modules\Notifications\Models\FarmNotification;

class NotificationSummaryService
{
    public function dashboard(NotificationsAccessContext $context, array $filters = []): array
    {
        $base = $this->filtered($context, $filters);

        return [
            'unreadCount' => (clone $base)->where('status', 'unread')->count(),
            'highCriticalOpenCount' => (clone $base)->whereIn('severity', ['high', 'critical'])->whereNotIn('status', ['dismissed', 'resolved'])->count(),
            'dueSoonCount' => (clone $base)->whereNotNull('due_at')->where('due_at', '<=', now()->addDays(7))->whereNotIn('status', ['dismissed', 'resolved'])->count(),
            'recentAlerts' => (clone $base)->latest('generated_at')->latest()->limit(10)->get(),
            'bySource' => (clone $base)->selectRaw("coalesce(source_module, 'manual') as label, count(*) as total")->groupBy('source_module')->orderByDesc('total')->get(),
            'bySeverity' => (clone $base)->selectRaw('severity as label, count(*) as total')->groupBy('severity')->orderByDesc('total')->get(),
        ];
    }

    public function filtered(NotificationsAccessContext $context, array $filters = [])
    {
        return $context->applyScope(FarmNotification::query()->with(['farm', 'rule']), 'farm_notifications')
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('farm_id', $id))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['severity'] ?? null, fn ($query, $severity) => $query->where('severity', $severity))
            ->when($filters['source_module'] ?? null, fn ($query, $module) => $query->where('source_module', $module))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['due_soon'] ?? null, fn ($query) => $query->whereNotNull('due_at')->where('due_at', '<=', now()->addDays(7)));
    }
}
