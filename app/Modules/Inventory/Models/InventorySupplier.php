<?php

namespace App\Modules\Inventory\Models;

use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventorySupplier extends Model
{
    protected $fillable = [
        'organization_id', 'name', 'code', 'contact_name', 'phone', 'email', 'address',
        'supplier_type', 'status', 'notes',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
