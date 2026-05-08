<?php

namespace App\Modules\Reports\Services;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Support\Collection;

class ReportsAccessContext
{
    public function __construct(private readonly User $user) {}

    public static function forUser(User $user): self
    {
        return new self($user);
    }

    public function organizations(): Collection
    {
        $ids = $this->user->memberships()->where('status', 'active')->pluck('organization_id')->unique();

        return Organization::query()->whereIn('id', $ids)->orderBy('name')->get();
    }

    public function farms(?int $organizationId = null): Collection
    {
        $memberships = $this->user->memberships()->where('status', 'active')->get(['organization_id', 'farm_id']);
        $organizationIds = $memberships->pluck('organization_id')->unique()->values();
        $farmIds = $memberships->pluck('farm_id')->filter()->unique()->values();
        $orgWideIds = $memberships
            ->filter(fn ($membership) => $membership->farm_id === null)
            ->pluck('organization_id')
            ->unique()
            ->values();

        return Farm::query()
            ->whereIn('organization_id', $organizationIds)
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->where(function ($query) use ($farmIds, $orgWideIds): void {
                $query->whereIn('organization_id', $orgWideIds)
                    ->orWhereIn('id', $farmIds);
            })
            ->orderBy('name')
            ->get();
    }

    public function filters(array $validated): array
    {
        $filters = collect($validated)->only([
            'organization_id', 'farm_id', 'date_from', 'date_to', 'crop_cycle_id', 'customer_id',
            'cost_category_id', 'target_type', 'status',
        ])->filter(fn ($value) => $value !== null && $value !== '')->all();

        $organizations = $this->organizations();
        $farms = $this->farms(isset($filters['organization_id']) ? (int) $filters['organization_id'] : null);

        if (isset($filters['organization_id'])) {
            abort_unless($organizations->contains('id', (int) $filters['organization_id']), 403);
        } elseif ($organizations->count() === 1) {
            $filters['organization_id'] = $organizations->first()->id;
        }

        if (isset($filters['farm_id'])) {
            abort_unless($farms->contains('id', (int) $filters['farm_id']), 403);
        } elseif ($farms->count() === 1) {
            $filters['farm_id'] = $farms->first()->id;
        }

        return $filters;
    }
}
