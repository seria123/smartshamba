<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'sensor_reading_id',
        'type',
        'severity',
        'message',
        'parameter',
        'value',
        'threshold',
        'is_read',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'threshold' => 'decimal:2',
        'is_read' => 'boolean',
    ];

    /**
     * Get the sensor reading that owns the alert.
     */
    public function sensorReading(): BelongsTo
    {
        return $this->belongsTo(SensorReading::class);
    }
}
