<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Warehouse;
use App\Modules\Inventory\Models\InventoryMovement;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Inventory\Models\InventoryProductCategory;
use App\Modules\Inventory\Models\InventoryStockLot;
use App\Modules\Inventory\Models\InventorySupplier;
use Illuminate\Database\Seeder;

class InventoryInputsSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $organization = Organization::query()->first();
        $farm = Farm::query()->where('organization_id', $organization?->id)->first();
        $warehouse = Warehouse::query()->where('farm_id', $farm?->id)->first();
        $user = User::query()->where('email', 'admin@smartshamba.test')->first();

        if (! $organization || ! $farm || ! $warehouse || ! $user) {
            return;
        }

        $category = InventoryProductCategory::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'fertilizers'],
            ['name' => 'Fertilizers', 'description' => 'Local/testing input category.', 'status' => 'active'],
        );

        $product = InventoryProduct::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'code' => 'NPK-17'],
            [
                'category_id' => $category->id,
                'name' => 'NPK 17:17:17',
                'sku' => 'NPK-17-50KG',
                'product_type' => 'fertilizer',
                'unit_of_measure' => 'kg',
                'tracks_batch' => true,
                'tracks_expiry' => false,
                'reorder_level' => 100,
                'default_unit_cost' => 80,
                'status' => 'active',
            ],
        );

        $supplier = InventorySupplier::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'name' => 'Demo Agro Supplier'],
            ['code' => 'SUP-001', 'supplier_type' => 'agro-inputs', 'status' => 'active'],
        );

        $lot = InventoryStockLot::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'lot_number' => 'LOT-OPENING-001'],
            [
                'farm_id' => $farm->id,
                'warehouse_id' => $warehouse->id,
                'product_id' => $product->id,
                'supplier_id' => $supplier->id,
                'batch_number' => 'BATCH-001',
                'quantity_on_hand' => 200,
                'reserved_quantity' => 0,
                'unit_of_measure' => 'kg',
                'unit_cost' => 80,
                'currency' => 'KES',
                'status' => 'active',
            ],
        );

        InventoryMovement::query()->updateOrCreate(
            ['movement_number' => 'INV-2026-00001'],
            [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'movement_type' => 'opening_balance',
                'date' => now()->toDateString(),
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'stock_lot_id' => $lot->id,
                'quantity' => 200,
                'unit' => 'kg',
                'unit_cost' => 80,
                'total_cost' => 16000,
                'supplier_id' => $supplier->id,
                'reason' => 'Local/testing opening balance.',
                'status' => 'posted',
                'performed_by' => $user->id,
            ],
        );
    }
}
