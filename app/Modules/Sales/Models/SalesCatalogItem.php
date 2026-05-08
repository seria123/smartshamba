<?php

namespace App\Modules\Sales\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesCatalogItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['organization_id', 'farm_id', 'name', 'category', 'unit', 'sku', 'default_unit_price', 'currency', 'notes', 'is_active', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return ['default_unit_price' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function lines(): HasMany { return $this->hasMany(SalesRecordLine::class, 'sales_catalog_item_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updatedBy(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
}
