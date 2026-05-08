<?php

namespace App\Modules\Reports\Services;

use App\Modules\Finance\Models\FinanceCostAllocation;
use App\Modules\Reports\Services\Concerns\BuildsReportQueries;
use App\Modules\Sales\Models\SalesRecordLine;
use Illuminate\Support\Collection;

class CostRevenueReportService
{
    use BuildsReportQueries;

    public function __construct(private readonly ReportLabelResolver $labels) {}

    public function rows(array $filters): Collection
    {
        $rows = collect();

        foreach ($this->costs($filters) as $cost) {
            $key = $cost->target_type.'|'.$cost->target_id;
            $rows[$key] = [
                'target_type' => $cost->target_type,
                'target_id' => $cost->target_id,
                'target_label' => $this->labels->target($cost->target_type, $cost->target_id),
                'cost' => (float) $cost->total,
                'revenue' => 0.0,
            ];
        }

        foreach ($this->revenue($filters) as $revenue) {
            $key = $revenue['target_type'].'|'.$revenue['target_id'];
            $row = $rows[$key] ?? [
                'target_type' => $revenue['target_type'],
                'target_id' => $revenue['target_id'],
                'target_label' => $this->labels->target($revenue['target_type'], $revenue['target_id']),
                'cost' => 0.0,
                'revenue' => 0.0,
            ];
            $row['revenue'] += (float) $revenue['total'];
            $rows[$key] = $row;
        }

        return $rows->values()->map(function (array $row): array {
            $row['margin'] = $row['revenue'] - $row['cost'];
            $row['margin_percent'] = $this->marginPercent($row['revenue'], $row['margin']);

            return $row;
        })->sortByDesc('revenue')->values();
    }

    private function costs(array $filters): Collection
    {
        return FinanceCostAllocation::query()
            ->join('finance_cost_entries', 'finance_cost_entries.id', '=', 'finance_cost_allocations.cost_entry_id')
            ->where('finance_cost_entries.status', 'confirmed')
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('finance_cost_entries.organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('finance_cost_entries.farm_id', $id))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('finance_cost_entries.entry_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('finance_cost_entries.entry_date', '<=', $date))
            ->when($filters['target_type'] ?? null, fn ($query, $type) => $query->where('finance_cost_allocations.allocation_type', $type))
            ->selectRaw('finance_cost_allocations.allocation_type as target_type, finance_cost_allocations.allocatable_id as target_id, sum(finance_cost_allocations.amount) as total')
            ->groupBy('finance_cost_allocations.allocation_type', 'finance_cost_allocations.allocatable_id')
            ->get();
    }

    private function revenue(array $filters): Collection
    {
        $base = SalesRecordLine::query()
            ->join('sales_records', 'sales_records.id', '=', 'sales_record_lines.sales_record_id')
            ->where('sales_records.status', 'confirmed')
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('sales_records.organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('sales_records.farm_id', $id))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('sales_records.sale_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('sales_records.sale_date', '<=', $date));

        $rows = collect();
        foreach ([['crop_cycle_id', 'crop_cycle'], ['animal_id', 'livestock_animal'], ['animal_group_id', 'livestock_group']] as [$column, $type]) {
            if (($filters['target_type'] ?? null) && $filters['target_type'] !== $type) {
                continue;
            }

            $rows = $rows->merge((clone $base)
                ->whereNotNull('sales_record_lines.'.$column)
                ->selectRaw('? as target_type, sales_record_lines.'.$column.' as target_id, sum(sales_record_lines.line_total_amount) as total', [$type])
                ->groupBy('sales_record_lines.'.$column)
                ->get()
                ->map(fn ($row) => ['target_type' => $type, 'target_id' => $row->target_id, 'total' => $row->total]));
        }

        if (! ($filters['target_type'] ?? null) || $filters['target_type'] === 'general') {
            $general = (clone $base)
                ->whereNull('sales_record_lines.crop_cycle_id')
                ->whereNull('sales_record_lines.animal_id')
                ->whereNull('sales_record_lines.animal_group_id')
                ->sum('sales_record_lines.line_total_amount');

            if ($general > 0) {
                $rows->push(['target_type' => 'general', 'target_id' => null, 'total' => $general]);
            }
        }

        return $rows;
    }
}
