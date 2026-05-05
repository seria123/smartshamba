<?php

namespace App\Modules\Inventory\Models;

use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryProduct extends Model
{
    protected $fillable = [
        'organization_id', 'category_id', 'name', 'code', 'sku', 'product_type', 'unit_of_measure',
        'brand', 'manufacturer', 'active_ingredient', 'tracks_batch', 'tracks_expiry',
        'reorder_level', 'default_unit_cost', 'status',
    ];

    protected function casts(): array
    {
        return [
            'tracks_batch' => 'boolean',
            'tracks_expiry' => 'boolean',
            'reorder_level' => 'decimal:2',
            'default_unit_cost' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryProductCategory::class, 'category_id');
    }

    public function stockLots(): HasMany
    {
        return $this->hasMany(InventoryStockLot::class, 'product_id');
    }
}
