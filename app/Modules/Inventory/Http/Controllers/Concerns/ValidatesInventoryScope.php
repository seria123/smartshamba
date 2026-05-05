<?php

namespace App\Modules\Inventory\Http\Controllers\Concerns;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Warehouse;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Inventory\Models\InventoryProductCategory;
use App\Modules\Inventory\Models\InventoryStockLot;
use App\Modules\Inventory\Models\InventorySupplier;

trait ValidatesInventoryScope
{
    private function ensureFarmBelongsToOrganization(int $farmId, int $organizationId): void
    {
        $farm = Farm::query()->findOrFail($farmId);
        abort_unless((int) $farm->organization_id === $organizationId, 422);
    }

    private function ensureWarehouseBelongsToFarm(int $warehouseId, int $farmId): Warehouse
    {
        $warehouse = Warehouse::query()->findOrFail($warehouseId);
        abort_unless((int) $warehouse->farm_id === $farmId, 422);

        return $warehouse;
    }

    private function ensureProductBelongsToOrganization(int $productId, int $organizationId): InventoryProduct
    {
        $product = InventoryProduct::query()->findOrFail($productId);
        abort_unless((int) $product->organization_id === $organizationId, 422);

        return $product;
    }

    private function ensureCategoryAvailableToOrganization(int $categoryId, int $organizationId): InventoryProductCategory
    {
        $category = InventoryProductCategory::query()->findOrFail($categoryId);
        abort_unless($category->organization_id === null || (int) $category->organization_id === $organizationId, 422);

        return $category;
    }

    private function ensureSupplierBelongsToOrganization(?int $supplierId, int $organizationId): ?InventorySupplier
    {
        if (! $supplierId) {
            return null;
        }

        $supplier = InventorySupplier::query()->findOrFail($supplierId);
        abort_unless((int) $supplier->organization_id === $organizationId, 422);

        return $supplier;
    }

    private function ensureLotBelongsToFarm(int $lotId, int $farmId): InventoryStockLot
    {
        $lot = InventoryStockLot::query()->findOrFail($lotId);
        abort_unless((int) $lot->farm_id === $farmId, 422);

        return $lot;
    }

    private function nextMovementNumber(): string
    {
        $year = now()->format('Y');
        $count = \DB::table('inventory_movements')->where('movement_number', 'like', 'INV-'.$year.'-%')->count() + 1;

        return 'INV-'.$year.'-'.str_pad((string) $count, 5, '0', STR_PAD_LEFT);
    }
}
