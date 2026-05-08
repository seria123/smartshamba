<?php

namespace App\Modules\Documents\Services;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DocumentsAccessContext
{
    public function __construct(private readonly User $user) {}

    public static function forUser(User $user): self
    {
        return new self($user);
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
        $filters = collect($input)->only(['organization_id', 'farm_id', 'attachment_category_id', 'attachable_module', 'attachable_type', 'mime_type', 'date_from', 'date_to', 'uploaded_by', 'search'])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->all();

        $organizations = $this->organizations();
        if (isset($filters['organization_id'])) {
            abort_unless($organizations->contains('id', (int) $filters['organization_id']), 403);
        } elseif ($organizations->count() === 1) {
            $filters['organization_id'] = $organizations->first()->id;
        }

        $farms = $this->farms(isset($filters['organization_id']) ? (int) $filters['organization_id'] : null);
        if (isset($filters['farm_id'])) {
            abort_unless($farms->contains('id', (int) $filters['farm_id']), 403);
        } elseif ($farms->count() === 1) {
            $filters['farm_id'] = $farms->first()->id;
        }

        return $filters;
    }

    public function applyScope(Builder $query, string $table): Builder
    {
        $memberships = $this->memberships();
        $orgWideIds = $memberships->whereNull('farm_id')->pluck('organization_id')->unique();
        $farmIds = $memberships->pluck('farm_id')->filter()->unique();

        return $query->where(function ($query) use ($table, $orgWideIds, $farmIds): void {
            $query->whereIn($table.'.organization_id', $orgWideIds)
                ->orWhereIn($table.'.farm_id', $farmIds);
        });
    }

    private function memberships(): Collection
    {
        return $this->user->memberships()->where('status', 'active')->get(['organization_id', 'farm_id']);
    }
}
