<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'farm_id',
        'name',
        'size_hectares',
        'location',
        'rainfall_zone',
        'topography',
        'water_source',
        'soil_type',
        'gps_latitude',
        'gps_longitude',
        'description',
    ];

    protected $casts = [
        'size_hectares' => 'decimal:2',
        'gps_latitude' => 'decimal:8',
        'gps_longitude' => 'decimal:8',
    ];

    /**
     * Get the farm that owns the field.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the sensors for the field.
     */
    public function sensors(): HasMany
    {
        return $this->hasMany(Sensor::class);
    }

    /**
     * Get the automation rules for the field.
     */
    public function automationRules(): HasMany
    {
        return $this->hasMany(AutomationRule::class);
    }

    /**
     * Get the current crop for the field.
     */
    public function crop(): HasOne
    {
        return $this->hasOne(Crop::class);
    }

    /**
     * Get the crop cycles for the field.
     */
    public function cropCycles(): HasMany
    {
        return $this->hasMany(CropCycle::class);
    }

    /**
     * Get the active crop cycle for the field (most recent).
     */
    public function activeCropCycle(): HasOne
    {
        return $this->hasOne(CropCycle::class)->latestOfMany();
    }
}
