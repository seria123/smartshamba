<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FertilizerType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'default_unit',
        'min_threshold',
        'is_active',
    ];

    protected $casts = [
        'min_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function fertilizers(): HasMany
    {
        return $this->hasMany(Fertilizer::class);
    }

    public function totalQuantity(): float
    {
        return $this->fertilizers()
            ->where('is_active', true)
            ->sum('quantity');
    }

    public function isBelowThreshold(): bool
    {
        return $this->totalQuantity() < $this->min_threshold;
    }
}
