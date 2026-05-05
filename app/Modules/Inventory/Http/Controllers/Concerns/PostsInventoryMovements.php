<?php

namespace App\Modules\Inventory\Http\Controllers\Concerns;

use App\Modules\Inventory\Models\InventoryMovement;
use App\Modules\Inventory\Models\InventoryStockLot;
use Illuminate\Support\Facades\DB;

trait PostsInventoryMovements
{
    use ValidatesInventoryScope;

    private function postIncoming(InventoryStockLot $lot, string $type, float $quantity, float $unitCost, ?int $supplierId, ?string $reason, int $userId): InventoryMovement
    {
        return DB::transaction(function () use ($lot, $type, $quantity, $unitCost, $supplierId, $reason, $userId): InventoryMovement {
            $lot->update(['quantity_on_hand' => (float) $lot->quantity_on_hand + $quantity, 'unit_cost' => $unitCost]);

            return $this->movement($lot, $type, $quantity, $unitCost, $supplierId, $reason, $userId, [
                'warehouse_id' => $lot->warehouse_id,
            ]);
        });
    }

    private function postOutgoing(InventoryStockLot $lot, string $type, float $quantity, ?string $reason, int $userId): InventoryMovement
    {
        abort_if((float) $lot->quantity_on_hand < $quantity, 422);

        return DB::transaction(function () use ($lot, $type, $quantity, $reason, $userId): InventoryMovement {
            $lot->update(['quantity_on_hand' => (float) $lot->quantity_on_hand - $quantity]);

            return $this->movement($lot, $type, $quantity, (float) $lot->unit_cost, $lot->supplier_id, $reason, $userId, [
                'warehouse_id' => $lot->warehouse_id,
            ]);
        });
    }

    private function postTransfer(InventoryStockLot $sourceLot, int $toWarehouseId, float $quantity, ?string $reason, int $userId): void
    {
        abort_if((float) $sourceLot->quantity_on_hand < $quantity, 422);
        $this->ensureWarehouseBelongsToFarm($toWarehouseId, (int) $sourceLot->farm_id);

        DB::transaction(function () use ($sourceLot, $toWarehouseId, $quantity, $reason, $userId): void {
            $sourceLot->update(['quantity_on_hand' => (float) $sourceLot->quantity_on_hand - $quantity]);

            $destinationLot = InventoryStockLot::query()->create([
                'organization_id' => $sourceLot->organization_id,
                'farm_id' => $sourceLot->farm_id,
                'warehouse_id' => $toWarehouseId,
                'product_id' => $sourceLot->product_id,
                'supplier_id' => $sourceLot->supplier_id,
                'lot_number' => $sourceLot->lot_number,
                'batch_number' => $sourceLot->batch_number,
                'expiry_date' => $sourceLot->expiry_date,
                'quantity_on_hand' => $quantity,
                'reserved_quantity' => 0,
                'unit_of_measure' => $sourceLot->unit_of_measure,
                'unit_cost' => $sourceLot->unit_cost,
                'currency' => $sourceLot->currency,
                'status' => $sourceLot->status,
            ]);

            $this->movement($sourceLot, 'transfer_out', $quantity, (float) $sourceLot->unit_cost, $sourceLot->supplier_id, $reason, $userId, [
                'from_warehouse_id' => $sourceLot->warehouse_id,
                'to_warehouse_id' => $toWarehouseId,
            ]);

            $this->movement($destinationLot, 'transfer_in', $quantity, (float) $destinationLot->unit_cost, $destinationLot->supplier_id, $reason, $userId, [
                'from_warehouse_id' => $sourceLot->warehouse_id,
                'to_warehouse_id' => $toWarehouseId,
            ]);
        });
    }

    private function movement(InventoryStockLot $lot, string $type, float $quantity, float $unitCost, ?int $supplierId, ?string $reason, int $userId, array $warehouses): InventoryMovement
    {
        return InventoryMovement::query()->create(array_merge([
            'organization_id' => $lot->organization_id,
            'farm_id' => $lot->farm_id,
            'movement_number' => $this->nextMovementNumber(),
            'movement_type' => $type,
            'date' => now()->toDateString(),
            'product_id' => $lot->product_id,
            'stock_lot_id' => $lot->id,
            'quantity' => $quantity,
            'unit' => $lot->unit_of_measure,
            'unit_cost' => $unitCost,
            'total_cost' => $quantity * $unitCost,
            'supplier_id' => $supplierId,
            'reason' => $reason,
            'status' => 'posted',
            'performed_by' => $userId,
        ], $warehouses));
    }
}
