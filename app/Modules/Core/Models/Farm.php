<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'status',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    public function paddocks(): HasMany
    {
        return $this->hasMany(Paddock::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }
}
