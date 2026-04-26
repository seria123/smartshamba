<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IrrigationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'irrigation_zone_id',
        'triggered_by',
        'event_type',
        'started_at',
        'ended_at',
        'duration_minutes',
        'water_used_liters',
        'soil_moisture_before',
        'soil_moisture_after',
        'status',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_minutes' => 'integer',
        'water_used_liters' => 'decimal:2',
        'soil_moisture_before' => 'decimal:2',
        'soil_moisture_after' => 'decimal:2',
    ];

    // Event Types
    const EVENT_SCHEDULED = 'scheduled';
    const EVENT_MANUAL = 'manual';
    const EVENT_TRIGGERED = 'triggered';
    const EVENT_STOPPED = 'stopped';

    // Status
    const STATUS_RUNNING = 'running';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    public function irrigationZone(): BelongsTo
    {
        return $this->belongsTo(IrrigationZone::class);
    }

    public function triggeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function getWaterEfficiencyScore(): ?float
    {
        if (!$this->soil_moisture_before || !$this->soil_moisture_after) {
            return null;
        }
        
        $moistureIncrease = $this->soil_moisture_after - $this->soil_moisture_before;
        $waterPerLiter = $this->water_used_liters > 0 ? $moistureIncrease / $this->water_used_liters : 0;
        
        return $waterPerLiter * 100;
    }
}