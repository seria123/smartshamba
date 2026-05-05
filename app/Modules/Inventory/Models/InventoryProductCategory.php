<?php

namespace App\Modules\Inventory\Models;

use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryProductCategory extends Model
{
    protected $fillable = ['organization_id', 'parent_id', 'name', 'slug', 'description', 'status'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(InventoryProduct::class, 'category_id');
    }
}
