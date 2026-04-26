<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YieldEstimation extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_id',
        'farmer_id',
        'field_id',
        'hectares',
        'season',
        'year',
        'estimated_yield',
        'actual_yield',
        'yield_per_hectare',
        'yield_unit',
        'estimated_income',
        'actual_income',
        'notes',
        'status',
    ];

    protected $casts = [
        'hectares' => 'decimal:2',
        'estimated_yield' => 'decimal:2',
        'actual_yield' => 'decimal:2',
        'yield_per_hectare' => 'decimal:2',
        'estimated_income' => 'decimal:2',
        'actual_income' => 'decimal:2',
    ];

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function calculateYieldPerHectare(): void
    {
        if ($this->hectares && $this->estimated_yield) {
            $this->yield_per_hectare = $this->estimated_yield / $this->hectares;
        }
    }

    public function isCompleted(): bool
    {
        return $this->status === 'harvested';
    }
}
