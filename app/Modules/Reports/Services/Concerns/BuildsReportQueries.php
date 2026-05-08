<?php

namespace App\Modules\Reports\Services\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BuildsReportQueries
{
    private function applyScope(Builder $query, array $filters, string $table): Builder
    {
        return $query
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where($table.'.organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where($table.'.farm_id', $id));
    }

    private function applyDateRange(Builder $query, array $filters, string $table, string $column): Builder
    {
        return $query
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate($table.'.'.$column, '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate($table.'.'.$column, '<=', $date));
    }

    private function marginPercent(float $revenue, float $margin): ?float
    {
        return $revenue > 0 ? ($margin / $revenue) * 100 : null;
    }
}
