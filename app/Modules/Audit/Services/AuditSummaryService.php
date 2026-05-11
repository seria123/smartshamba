<?php

namespace App\Modules\Audit\Services;

use App\Modules\Audit\Models\AuditActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AuditSummaryService
{
    public function dashboard(AuditAccessContext $context, array $filters): array
    {
        $base = $this->query($context, $filters);

        return [
            'totalEvents' => (clone $base)->count(),
            'eventsToday' => (clone $base)->whereDate('occurred_at', today())->count(),
            'eventsThisWeek' => (clone $base)->where('occurred_at', '>=', now()->startOfWeek())->count(),
            'topModules' => $this->topModules($context, $filters),
            'topActors' => $this->topActors($context, $filters),
            'recentActivity' => (clone $base)->with(['organization', 'farm', 'actor'])->latest('occurred_at')->limit(10)->get(),
            'highRiskActions' => (clone $base)->whereIn('event', ['deleted', 'voided', 'cancelled', 'login_failed', 'permission_changed'])->latest('occurred_at')->limit(10)->get(),
        ];
    }

    public function topModules(AuditAccessContext $context, array $filters): Collection
    {
        return $this->query($context, $filters)->select('module', DB::raw('count(*) as event_count'), DB::raw('max(occurred_at) as latest_activity'))
            ->groupBy('module')->orderByDesc('event_count')->orderByDesc('latest_activity')->limit(10)->get();
    }

    public function topActors(AuditAccessContext $context, array $filters): Collection
    {
        return $this->query($context, $filters)->select('actor_user_id', 'actor_name', 'actor_email', DB::raw('count(*) as event_count'), DB::raw('max(occurred_at) as latest_activity'))
            ->groupBy('actor_user_id', 'actor_name', 'actor_email')->orderByDesc('event_count')->orderByDesc('latest_activity')->limit(20)->get();
    }

    public function topActions(AuditAccessContext $context, array $filters): Collection
    {
        return $this->query($context, $filters)->select('event', 'action_label', DB::raw('count(*) as event_count'), DB::raw('max(occurred_at) as latest_activity'))
            ->groupBy('event', 'action_label')->orderByDesc('event_count')->orderByDesc('latest_activity')->limit(20)->get();
    }

    public function daily(AuditAccessContext $context, array $filters): Collection
    {
        $dateExpression = DB::connection()->getDriverName() === 'sqlite' ? "date(occurred_at)" : "DATE(occurred_at)";

        return $this->query($context, $filters)->selectRaw($dateExpression.' as activity_date, count(*) as event_count, count(distinct actor_user_id) as unique_actors, max(module) as top_module')
            ->groupBy('activity_date')->orderByDesc('activity_date')->limit(30)->get();
    }

    public function query(AuditAccessContext $context, array $filters): Builder
    {
        $query = $context->applyScope(AuditActivityLog::query());

        return $query
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('occurred_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('occurred_at', '<=', $date))
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('farm_id', $id))
            ->when($filters['actor_user_id'] ?? null, fn ($query, $id) => $query->where('actor_user_id', $id))
            ->when($filters['module'] ?? null, fn ($query, $module) => $query->where('module', $module))
            ->when($filters['event'] ?? null, fn ($query, $event) => $query->where('event', $event))
            ->when($filters['subject_type'] ?? null, fn ($query, $type) => $query->where('subject_type', $type))
            ->when($filters['keyword'] ?? $filters['search'] ?? null, function ($query, $keyword): void {
                $query->where(function ($query) use ($keyword): void {
                    $query->where('description', 'like', '%'.$keyword.'%')
                        ->orWhere('actor_name', 'like', '%'.$keyword.'%')
                        ->orWhere('actor_email', 'like', '%'.$keyword.'%')
                        ->orWhere('subject_label', 'like', '%'.$keyword.'%')
                        ->orWhere('module', 'like', '%'.$keyword.'%')
                        ->orWhere('event', 'like', '%'.$keyword.'%');
                });
            });
    }
}
