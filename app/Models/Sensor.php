<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'name',
        'type',
        'serial_number',
        'status',
        'description',
    ];

    /**
     * Get the field that owns the sensor.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the sensor readings for the sensor.
     */
    public function sensorReadings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }

    /**
     * Get the latest sensor reading.
     */
    public function latestReading()
    {
        return $this->hasOne(SensorReading::class)->latestOfMany();
    }
}
