<?php

namespace App\Modules\Reports\Services;

use App\Modules\Finance\Models\FinanceCostAllocation;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Livestock\Models\LivestockMortalityRecord;
use App\Modules\Livestock\Models\LivestockYieldRecord;
use App\Modules\Reports\Services\Concerns\BuildsReportQueries;
use App\Modules\Sales\Models\SalesRecordLine;
use Illuminate\Support\Collection;

class LivestockProfitabilityReportService
{
    use BuildsReportQueries;

    public function rows(array $filters): Collection
    {
        return $this->animalRows($filters)->merge($this->groupRows($filters))->sortByDesc('revenue')->values();
    }

    private function animalRows(array $filters): Collection
    {
        return $this->applyScope(LivestockAnimal::query()->with(['farm', 'paddock', 'species', 'breed']), $filters, 'livestock_animals')
            ->get()
            ->map(fn (LivestockAnimal $animal) => $this->row('livestock_animal', $animal->id, $animal->name ?: $animal->tag_number ?: $animal->animal_code, $animal->species?->name, $animal->breed?->name, $animal->farm?->name, $animal->paddock?->name, $animal->status, $filters));
    }

    private function groupRows(array $filters): Collection
    {
        return $this->applyScope(LivestockAnimalGroup::query()->with(['farm', 'paddock', 'species', 'breed']), $filters, 'livestock_animal_groups')
            ->get()
            ->map(fn (LivestockAnimalGroup $group) => $this->row('livestock_group', $group->id, $group->name ?: $group->group_code, $group->species?->name, $group->breed?->name, $group->farm?->name, $group->paddock?->name, $group->status, $filters));
    }

    private function row(string $type, int $id, ?string $label, ?string $species, ?string $breed, ?string $farm, ?string $paddock, ?string $status, array $filters): array
    {
        $costs = $this->costs($type, $id, $filters);
        $revenue = $this->revenue($type, $id, $filters);

        return [
            'target' => $label ?: $type.' #'.$id,
            'type' => $type,
            'species' => trim(($species ?? '').' '.($breed ?? '')),
            'farm' => trim(($farm ?? '').' '.($paddock ? '/ '.$paddock : '')),
            'costs' => $costs,
            'revenue' => $revenue,
            'margin' => $revenue - $costs,
            'yield_quantity' => $this->yieldQuantity($type, $id, $filters),
            'status' => $status,
            'mortality' => $type === 'livestock_animal' ? LivestockMortalityRecord::query()->where('animal_id', $id)->exists() : LivestockMortalityRecord::query()->where('animal_group_id', $id)->exists(),
        ];
    }

    private function costs(string $type, int $id, array $filters): float
    {
        return (float) FinanceCostAllocation::query()
            ->join('finance_cost_entries', 'finance_cost_entries.id', '=', 'finance_cost_allocations.cost_entry_id')
            ->where('finance_cost_entries.status', 'confirmed')
            ->where('finance_cost_allocations.allocation_type', $type)
            ->where('finance_cost_allocations.allocatable_id', $id)
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('finance_cost_entries.entry_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('finance_cost_entries.entry_date', '<=', $date))
            ->sum('finance_cost_allocations.amount');
    }

    private function revenue(string $type, int $id, array $filters): float
    {
        $column = $type === 'livestock_animal' ? 'animal_id' : 'animal_group_id';

        return (float) SalesRecordLine::query()
            ->join('sales_records', 'sales_records.id', '=', 'sales_record_lines.sales_record_id')
            ->where('sales_records.status', 'confirmed')
            ->where('sales_record_lines.'.$column, $id)
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('sales_records.sale_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('sales_records.sale_date', '<=', $date))
            ->sum('sales_record_lines.line_total_amount');
    }

    private function yieldQuantity(string $type, int $id, array $filters): float
    {
        $column = $type === 'livestock_animal' ? 'animal_id' : 'animal_group_id';

        return (float) LivestockYieldRecord::query()
            ->where($column, $id)
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('yield_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('yield_date', '<=', $date))
            ->sum('quantity');
    }
}
