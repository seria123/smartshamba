<?php

namespace App\Modules\Inventory\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'organization_id', 'farm_id', 'movement_number', 'movement_type', 'date', 'product_id',
        'warehouse_id', 'from_warehouse_id', 'to_warehouse_id', 'stock_lot_id', 'quantity',
        'unit', 'unit_cost', 'total_cost', 'supplier_id', 'reference_type', 'reference_id',
        'reason', 'status', 'performed_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function product(): BelongsTo { return $this->belongsTo(InventoryProduct::class, 'product_id'); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class, 'warehouse_id'); }
    public function fromWarehouse(): BelongsTo { return $this->belongsTo(Warehouse::class, 'from_warehouse_id'); }
    public function toWarehouse(): BelongsTo { return $this->belongsTo(Warehouse::class, 'to_warehouse_id'); }
    public function stockLot(): BelongsTo { return $this->belongsTo(InventoryStockLot::class, 'stock_lot_id'); }
    public function supplier(): BelongsTo { return $this->belongsTo(InventorySupplier::class, 'supplier_id'); }
    public function performedBy(): BelongsTo { return $this->belongsTo(User::class, 'performed_by'); }
}
