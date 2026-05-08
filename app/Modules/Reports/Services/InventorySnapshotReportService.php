<?php

namespace App\Modules\Reports\Services;

use App\Modules\Inventory\Models\InventoryStockLot;
use Illuminate\Support\Collection;

class InventorySnapshotReportService
{
    public function rows(array $filters): Collection
    {
        return InventoryStockLot::query()
            ->with(['product.category', 'farm', 'warehouse'])
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('farm_id', $id))
            ->orderBy('product_id')
            ->get()
            ->map(fn (InventoryStockLot $lot) => [
                'product' => $lot->product?->name ?? 'Product #'.$lot->product_id,
                'category' => $lot->product?->category?->name ?? 'Uncategorized',
                'location' => trim(($lot->farm?->name ?? '').($lot->warehouse ? ' / '.$lot->warehouse->name : '')) ?: 'Unspecified',
                'quantity' => (float) $lot->quantity_on_hand,
                'unit' => $lot->unit_of_measure,
                'unit_cost' => (float) $lot->unit_cost,
                'value' => (float) $lot->quantity_on_hand * (float) $lot->unit_cost,
                'currency' => $lot->currency,
                'expiry_date' => $lot->expiry_date?->toDateString(),
                'low_stock' => $lot->product && $lot->product->reorder_level !== null && (float) $lot->quantity_on_hand <= (float) $lot->product->reorder_level,
            ]);
    }
}
