<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crop extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'field_id',
        'name',
        'category',
        'description',
        'variety',
        'days_to_maturity',
        'average_yield_per_hectare',
        'yield_unit',
        'season_type',
        'planting_date',
        'expected_harvest_date',
        'notes',
        'growth_stages',
        'soil_requirements',
        'water_requirements',
        'pest_vulnerabilities',
        'min_temperature',
        'max_temperature',
        'optimal_ph_min',
        'optimal_ph_max',
    ];

    protected $casts = [
        'growth_stages' => 'array',
        'soil_requirements' => 'array',
        'water_requirements' => 'array',
        'pest_vulnerabilities' => 'array',
        'min_temperature' => 'decimal:2',
        'max_temperature' => 'decimal:2',
        'optimal_ph_min' => 'decimal:2',
        'optimal_ph_max' => 'decimal:2',
        'average_yield_per_hectare' => 'decimal:2',
        'planting_date' => 'datetime',
        'expected_harvest_date' => 'datetime',
    ];

    public function seasons(): HasMany
    {
        return $this->hasMany(CropSeason::class);
    }

    public function rotations(): HasMany
    {
        return $this->hasMany(CropRotation::class, 'crop_id');
    }

    public function previousRotations(): HasMany
    {
        return $this->hasMany(CropRotation::class, 'previous_crop_id');
    }

    public function yieldEstimations(): HasMany
    {
        return $this->hasMany(YieldEstimation::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
