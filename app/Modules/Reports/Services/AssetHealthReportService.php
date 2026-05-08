<?php

namespace App\Modules\Reports\Services;

use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetBreakdownRecord;
use App\Modules\Assets\Models\AssetUsageRecord;
use App\Modules\Finance\Models\FinanceCostAllocation;
use App\Modules\Reports\Services\Concerns\BuildsReportQueries;
use Illuminate\Support\Collection;

class AssetHealthReportService
{
    use BuildsReportQueries;

    public function rows(array $filters): Collection
    {
        return $this->applyScope(Asset::query()->with(['category', 'farm', 'site']), $filters, 'assets')
            ->orderBy('name')
            ->get()
            ->map(fn (Asset $asset) => [
                'asset' => $asset->name ?: $asset->asset_code,
                'category' => $asset->category?->name ?? $asset->asset_type,
                'location' => trim(($asset->farm?->name ?? '').($asset->site ? ' / '.$asset->site->name : '')) ?: 'Unspecified',
                'status' => trim($asset->status.' / '.$asset->condition_status, ' /'),
                'open_breakdowns' => $this->openBreakdowns($asset->id, $filters),
                'last_service_date' => $asset->last_service_date?->toDateString(),
                'next_service_date' => $asset->next_service_date?->toDateString(),
                'usage_count' => $this->usageCount($asset->id, $filters),
                'maintenance_cost' => $this->maintenanceCost($asset->id, $filters),
            ]);
    }

    private function openBreakdowns(int $assetId, array $filters): int
    {
        return AssetBreakdownRecord::query()
            ->where('asset_id', $assetId)
            ->whereNotIn('status', ['resolved', 'cancelled'])
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('breakdown_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('breakdown_date', '<=', $date))
            ->count();
    }

    private function usageCount(int $assetId, array $filters): int
    {
        return AssetUsageRecord::query()
            ->where('asset_id', $assetId)
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('usage_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('usage_date', '<=', $date))
            ->count();
    }

    private function maintenanceCost(int $assetId, array $filters): float
    {
        return (float) FinanceCostAllocation::query()
            ->join('finance_cost_entries', 'finance_cost_entries.id', '=', 'finance_cost_allocations.cost_entry_id')
            ->where('finance_cost_entries.status', 'confirmed')
            ->where('finance_cost_allocations.allocation_type', 'asset')
            ->where('finance_cost_allocations.allocatable_id', $assetId)
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('finance_cost_entries.entry_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('finance_cost_entries.entry_date', '<=', $date))
            ->sum('finance_cost_allocations.amount');
    }
}
