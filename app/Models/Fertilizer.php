<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fertilizer extends Model
{
    use HasFactory;

    protected $fillable = [
        'fertilizer_type_id',
        'name',
        'quantity',
        'unit',
        'unit_cost',
        'expiry_date',
        'supplier_id',
        'is_active',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function fertilizerType(): BelongsTo
    {
        return $this->belongsTo(FertilizerType::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(FertilizerApplication::class);
    }

    public function totalCost(): float
    {
        return $this->quantity * ($this->unit_cost ?: 0);
    }
}
