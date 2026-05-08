<?php

namespace App\Modules\Reports\Services;

use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetBreakdownRecord;
use App\Modules\Core\Models\Farm;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Finance\Models\FinanceCostEntry;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Inventory\Models\InventoryStockLot;
use App\Modules\Irrigation\Models\IrrigationIssue;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Reports\Services\Concerns\BuildsReportQueries;
use App\Modules\Sales\Models\SalesRecord;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Tasks\Models\OpsWorkOrder;
use Illuminate\Support\Collection;

class FarmPerformanceReportService
{
    use BuildsReportQueries;

    public function dashboard(array $filters): array
    {
        $costs = $this->costs($filters);
        $revenue = $this->revenue($filters);
        $margin = $revenue - $costs;

        return [
            'cards' => [
                ['label' => 'Total confirmed costs', 'value' => $costs, 'money' => true],
                ['label' => 'Total revenue', 'value' => $revenue, 'money' => true],
                ['label' => 'Gross margin', 'value' => $margin, 'money' => true],
                ['label' => 'Gross margin %', 'value' => $this->marginPercent($revenue, $margin), 'percent' => true],
                ['label' => 'Outstanding customer payments', 'value' => $this->outstanding($filters), 'money' => true],
                ['label' => 'Active crop cycles', 'value' => $this->activeCropCycles($filters)],
                ['label' => 'Livestock animals / groups', 'value' => $this->livestockSummary($filters)],
                ['label' => 'Low stock warnings', 'value' => $this->lowStockCount($filters)],
                ['label' => 'Open tasks / work orders', 'value' => $this->openTasks($filters).' / '.$this->openWorkOrders($filters)],
                ['label' => 'Open irrigation issues', 'value' => $this->openIrrigationIssues($filters)],
                ['label' => 'Asset breakdowns', 'value' => $this->assetBreakdowns($filters)],
            ],
            'recentSales' => $this->salesBase($filters)->with(['customer', 'farm'])->latest('sale_date')->limit(8)->get(),
            'recentCosts' => $this->costBase($filters)->with(['category', 'farm'])->latest('entry_date')->limit(8)->get(),
        ];
    }

    public function rows(array $filters): Collection
    {
        return $this->farmBase($filters)->get()->map(function (Farm $farm) use ($filters): array {
            $farmFilters = $filters + ['farm_id' => $farm->id];
            $costs = $this->costs($farmFilters);
            $revenue = $this->revenue($farmFilters);
            $margin = $revenue - $costs;

            return [
                'farm' => $farm->name,
                'costs' => $costs,
                'revenue' => $revenue,
                'margin' => $margin,
                'margin_percent' => $this->marginPercent($revenue, $margin),
                'outstanding' => $this->outstanding($farmFilters),
                'active_crop_cycles' => $this->activeCropCycles($farmFilters),
                'livestock' => $this->livestockSummary($farmFilters),
                'open_tasks' => $this->openTasks($farmFilters),
                'asset_breakdowns' => $this->assetBreakdowns($farmFilters),
            ];
        });
    }

    private function farmBase(array $filters)
    {
        return Farm::query()
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('id', $id))
            ->orderBy('name');
    }

    private function costBase(array $filters)
    {
        return $this->applyDateRange($this->applyScope(FinanceCostEntry::query(), $filters, 'finance_cost_entries'), $filters, 'finance_cost_entries', 'entry_date')
            ->where('status', 'confirmed');
    }

    private function salesBase(array $filters)
    {
        return $this->applyDateRange($this->applyScope(SalesRecord::query(), $filters, 'sales_records'), $filters, 'sales_records', 'sale_date')
            ->where('status', 'confirmed');
    }

    private function costs(array $filters): float { return (float) $this->costBase($filters)->sum('amount'); }
    private function revenue(array $filters): float { return (float) $this->salesBase($filters)->sum('total_amount'); }
    private function outstanding(array $filters): float { return (float) $this->salesBase($filters)->sum('balance_amount'); }
    private function activeCropCycles(array $filters): int { return $this->applyScope(CropCycle::query(), $filters, 'crop_cycles')->whereIn('status', ['planned', 'planted', 'active'])->count(); }
    private function openTasks(array $filters): int { return $this->applyScope(OpsTask::query(), $filters, 'ops_tasks')->whereNotIn('status', ['completed', 'cancelled', 'approved'])->count(); }
    private function openWorkOrders(array $filters): int { return $this->applyScope(OpsWorkOrder::query(), $filters, 'ops_work_orders')->whereNotIn('status', ['completed', 'cancelled'])->count(); }
    private function openIrrigationIssues(array $filters): int { return $this->applyScope(IrrigationIssue::query(), $filters, 'irrigation_issues')->whereNotIn('status', ['resolved', 'cancelled'])->count(); }
    private function assetBreakdowns(array $filters): int { return $this->applyScope(AssetBreakdownRecord::query(), $filters, 'asset_breakdown_records')->whereNotIn('status', ['resolved', 'cancelled'])->count(); }
    private function livestockSummary(array $filters): string { return $this->applyScope(LivestockAnimal::query(), $filters, 'livestock_animals')->count().' / '.$this->applyScope(LivestockAnimalGroup::query(), $filters, 'livestock_animal_groups')->count(); }

    private function lowStockCount(array $filters): int
    {
        return InventoryStockLot::query()
            ->join('inventory_products', 'inventory_products.id', '=', 'inventory_stock_lots.product_id')
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('inventory_stock_lots.organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('inventory_stock_lots.farm_id', $id))
            ->whereColumn('inventory_stock_lots.quantity_on_hand', '<=', 'inventory_products.reorder_level')
            ->count();
    }
}
