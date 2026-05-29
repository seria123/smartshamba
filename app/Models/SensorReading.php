<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SensorReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'sensor_id',
        'soil_moisture',
        'temperature',
        'humidity',
        'soil_ph',
        'light_intensity',
        'rain_detected',
        'nitrogen_level',
        'phosphorus_level',
        'potassium_level',
        'timestamp',
        'recorded_at',
    ];

    protected $casts = [
        'soil_moisture' => 'decimal:2',
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'soil_ph' => 'decimal:2',
        'light_intensity' => 'decimal:2',
        'rain_detected' => 'boolean',
        'nitrogen_level' => 'decimal:2',
        'phosphorus_level' => 'decimal:2',
        'potassium_level' => 'decimal:2',
        'timestamp' => 'datetime',
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the sensor that owns the reading.
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }

    /**
     * Get the alerts for the sensor reading.
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }
}
