<?php

namespace App\Modules\Finance\Services;

use App\Modules\Finance\Models\FinanceCostAllocation;
use App\Modules\Finance\Models\FinanceCostEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CostReportService
{
    public function dashboard(array $filters = []): array
    {
        $base = $this->filteredEntries($filters);

        return [
            'confirmedTotal' => (clone $base)->where('status', 'confirmed')->sum('amount'),
            'draftCount' => (clone $base)->where('status', 'draft')->count(),
            'draftTotal' => (clone $base)->where('status', 'draft')->sum('amount'),
            'voidCount' => (clone $base)->where('status', 'void')->count(),
            'voidTotal' => (clone $base)->where('status', 'void')->sum('amount'),
            'byCategory' => $this->byCategory($filters),
            'byFarm' => $this->byFarm($filters),
            'byMonth' => $this->byMonth($filters),
            'bySource' => $this->bySource($filters),
            'recentEntries' => (clone $base)->with(['category', 'centre', 'farm'])->latest('entry_date')->limit(10)->get(),
        ];
    }

    public function byCategory(array $filters = []): Collection
    {
        return $this->filteredEntries($filters)
            ->where('finance_cost_entries.status', 'confirmed')
            ->join('finance_cost_categories', 'finance_cost_categories.id', '=', 'finance_cost_entries.cost_category_id')
            ->selectRaw('finance_cost_categories.name as label, sum(finance_cost_entries.amount) as total')
            ->groupBy('finance_cost_categories.name')
            ->orderByDesc('total')
            ->get();
    }

    public function byFarm(array $filters = []): Collection
    {
        return $this->filteredEntries($filters)
            ->where('finance_cost_entries.status', 'confirmed')
            ->leftJoin('farms', 'farms.id', '=', 'finance_cost_entries.farm_id')
            ->selectRaw("coalesce(farms.name, 'No farm') as label, sum(finance_cost_entries.amount) as total")
            ->groupBy('farms.name')
            ->orderByDesc('total')
            ->get();
    }

    public function bySource(array $filters = []): Collection
    {
        return $this->filteredEntries($filters)
            ->where('status', 'confirmed')
            ->selectRaw('source_module as label, sum(amount) as total')
            ->groupBy('source_module')
            ->orderByDesc('total')
            ->get();
    }

    public function byMonth(array $filters = []): Collection
    {
        return $this->filteredEntries($filters)
            ->where('status', 'confirmed')
            ->selectRaw('substr(entry_date, 1, 7) as label, sum(amount) as total')
            ->groupByRaw('substr(entry_date, 1, 7)')
            ->orderBy('label')
            ->get();
    }

    public function byAllocation(array $filters = []): Collection
    {
        return FinanceCostAllocation::query()
            ->join('finance_cost_entries', 'finance_cost_entries.id', '=', 'finance_cost_allocations.cost_entry_id')
            ->where('finance_cost_entries.status', 'confirmed')
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('finance_cost_entries.organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('finance_cost_entries.farm_id', $id))
            ->selectRaw('finance_cost_allocations.allocation_type as label, sum(finance_cost_allocations.amount) as total')
            ->groupBy('finance_cost_allocations.allocation_type')
            ->orderByDesc('total')
            ->get();
    }

    public function filteredEntries(array $filters = []): Builder
    {
        return FinanceCostEntry::query()
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('farm_id', $id))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('entry_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('entry_date', '<=', $date));
    }
}
