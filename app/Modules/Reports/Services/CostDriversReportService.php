<?php

namespace App\Modules\Reports\Services;

use App\Modules\Finance\Models\FinanceCostEntry;
use Illuminate\Support\Collection;

class CostDriversReportService
{
    public function rows(array $filters): Collection
    {
        $total = (float) $this->base($filters)->sum('finance_cost_entries.amount');

        return $this->base($filters)
            ->join('finance_cost_categories', 'finance_cost_categories.id', '=', 'finance_cost_entries.cost_category_id')
            ->leftJoin('farms', 'farms.id', '=', 'finance_cost_entries.farm_id')
            ->selectRaw('finance_cost_categories.name as category, count(*) as entry_count, sum(finance_cost_entries.amount) as total_amount, avg(finance_cost_entries.amount) as average_amount, farms.name as top_farm')
            ->groupBy('finance_cost_categories.name', 'farms.name')
            ->orderByDesc('total_amount')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category,
                'total_amount' => (float) $row->total_amount,
                'entry_count' => $row->entry_count,
                'average_amount' => (float) $row->average_amount,
                'percent' => $total > 0 ? ((float) $row->total_amount / $total) * 100 : null,
                'top_farm' => $row->top_farm ?? 'No farm',
            ]);
    }

    private function base(array $filters)
    {
        return FinanceCostEntry::query()
            ->where('finance_cost_entries.status', 'confirmed')
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('finance_cost_entries.organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('finance_cost_entries.farm_id', $id))
            ->when($filters['cost_category_id'] ?? null, fn ($query, $id) => $query->where('finance_cost_entries.cost_category_id', $id))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('finance_cost_entries.entry_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('finance_cost_entries.entry_date', '<=', $date));
    }
}
