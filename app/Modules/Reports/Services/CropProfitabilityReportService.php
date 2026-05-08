<?php

namespace App\Modules\Reports\Services;

use App\Modules\Crops\Models\CropCycle;
use App\Modules\Crops\Models\CropHarvestRecord;
use App\Modules\Finance\Models\FinanceCostAllocation;
use App\Modules\Reports\Services\Concerns\BuildsReportQueries;
use App\Modules\Sales\Models\SalesRecordLine;
use Illuminate\Support\Collection;

class CropProfitabilityReportService
{
    use BuildsReportQueries;

    public function rows(array $filters): Collection
    {
        return $this->applyScope(CropCycle::query()->with(['farm', 'field', 'crop', 'variety']), $filters, 'crop_cycles')
            ->when($filters['crop_cycle_id'] ?? null, fn ($query, $id) => $query->where('id', $id))
            ->orderByDesc('id')
            ->get()
            ->map(function (CropCycle $cycle) use ($filters): array {
                $costs = $this->costs($cycle->id, $filters);
                $revenue = $this->revenue($cycle->id, $filters);
                $harvest = $this->harvest($cycle->id, $filters);
                $margin = $revenue - $costs;

                return [
                    'label' => $cycle->name,
                    'farm' => $cycle->farm?->name,
                    'field' => $cycle->field?->name,
                    'crop' => trim(($cycle->crop?->name ?? '').' '.($cycle->variety?->name ?? '')),
                    'status' => $cycle->status,
                    'area' => trim($cycle->area_planted.' '.$cycle->area_unit),
                    'costs' => $costs,
                    'revenue' => $revenue,
                    'margin' => $margin,
                    'margin_percent' => $this->marginPercent($revenue, $margin),
                    'harvest_quantity' => $harvest,
                    'revenue_per_unit' => $harvest > 0 ? $revenue / $harvest : null,
                    'cost_per_unit' => $harvest > 0 ? $costs / $harvest : null,
                ];
            });
    }

    private function costs(int $cycleId, array $filters): float
    {
        return (float) FinanceCostAllocation::query()
            ->join('finance_cost_entries', 'finance_cost_entries.id', '=', 'finance_cost_allocations.cost_entry_id')
            ->where('finance_cost_entries.status', 'confirmed')
            ->where('finance_cost_allocations.allocation_type', 'crop_cycle')
            ->where('finance_cost_allocations.allocatable_id', $cycleId)
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('finance_cost_entries.entry_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('finance_cost_entries.entry_date', '<=', $date))
            ->sum('finance_cost_allocations.amount');
    }

    private function revenue(int $cycleId, array $filters): float
    {
        return (float) SalesRecordLine::query()
            ->join('sales_records', 'sales_records.id', '=', 'sales_record_lines.sales_record_id')
            ->where('sales_records.status', 'confirmed')
            ->where('sales_record_lines.crop_cycle_id', $cycleId)
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('sales_records.sale_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('sales_records.sale_date', '<=', $date))
            ->sum('sales_record_lines.line_total_amount');
    }

    private function harvest(int $cycleId, array $filters): float
    {
        return (float) CropHarvestRecord::query()
            ->where('crop_cycle_id', $cycleId)
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('harvest_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('harvest_date', '<=', $date))
            ->sum('quantity');
    }
}
