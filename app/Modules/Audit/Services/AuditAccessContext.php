<?php

namespace App\Modules\Audit\Services;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AuditAccessContext
{
    public function __construct(private readonly User $user) {}

    public static function forUser(User $user): self
    {
        return new self($user);
    }

    public function canView(): bool
    {
        return $this->user->canAccessAdmin('audit.view');
    }

    public function canViewReports(): bool
    {
        return $this->user->canAccessAdmin('audit.reports');
    }

    public function organizations(): Collection
    {
        return Organization::query()->whereIn('id', $this->memberships()->pluck('organization_id')->unique())->orderBy('name')->get();
    }

    public function farms(?int $organizationId = null): Collection
    {
        $memberships = $this->memberships();
        $orgWideIds = $memberships->whereNull('farm_id')->pluck('organization_id')->unique();
        $farmIds = $memberships->pluck('farm_id')->filter()->unique();

        return Farm::query()
            ->whereIn('organization_id', $memberships->pluck('organization_id')->unique())
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->where(fn ($query) => $query->whereIn('organization_id', $orgWideIds)->orWhereIn('id', $farmIds))
            ->orderBy('name')
            ->get();
    }

    public function filters(array $input): array
    {
        $filters = collect($input)->only(['date_from', 'date_to', 'organization_id', 'organisation_id', 'farm_id', 'actor_user_id', 'module', 'event', 'subject_type', 'keyword', 'search'])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->all();

        if (isset($filters['organisation_id']) && ! isset($filters['organization_id'])) {
            $filters['organization_id'] = $filters['organisation_id'];
            unset($filters['organisation_id']);
        }

        $organizations = $this->organizations();
        if (isset($filters['organization_id'])) {
            abort_unless($organizations->contains('id', (int) $filters['organization_id']), 403);
        }

        $farms = $this->farms(isset($filters['organization_id']) ? (int) $filters['organization_id'] : null);
        if (isset($filters['farm_id'])) {
            abort_unless($farms->contains('id', (int) $filters['farm_id']), 403);
        }

        return $filters;
    }

    public function applyScope(Builder $query): Builder
    {
        $memberships = $this->memberships();
        $orgWideIds = $memberships->whereNull('farm_id')->pluck('organization_id')->unique();
        $farmIds = $memberships->pluck('farm_id')->filter()->unique();

        return $query->where(function ($query) use ($orgWideIds, $farmIds): void {
            $query->whereNull('organization_id')
                ->orWhereIn('organization_id', $orgWideIds)
                ->orWhereIn('farm_id', $farmIds);
        });
    }

    private function memberships(): Collection
    {
        return $this->user->memberships()->where('status', 'active')->get(['organization_id', 'farm_id']);
    }
}
