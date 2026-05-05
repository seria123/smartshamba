<?php

namespace App\Modules\Inventory\Models;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryStockLot extends Model
{
    protected $fillable = [
        'organization_id', 'farm_id', 'warehouse_id', 'product_id', 'supplier_id', 'lot_number',
        'batch_number', 'expiry_date', 'quantity_on_hand', 'reserved_quantity', 'unit_of_measure',
        'unit_cost', 'currency', 'status',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'quantity_on_hand' => 'decimal:2',
            'reserved_quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function product(): BelongsTo { return $this->belongsTo(InventoryProduct::class, 'product_id'); }
    public function supplier(): BelongsTo { return $this->belongsTo(InventorySupplier::class, 'supplier_id'); }
    public function movements(): HasMany { return $this->hasMany(InventoryMovement::class, 'stock_lot_id'); }
}
