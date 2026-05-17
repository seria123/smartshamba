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
        'irrigation_method',
        'water_source',
        'estimated_volume_liters',
        'cost',
        'cost_type',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_minutes' => 'integer',
        'water_used_liters' => 'decimal:2',
        'estimated_volume_liters' => 'decimal:2',
        'cost' => 'decimal:2',
        'soil_moisture_before' => 'decimal:2',
        'soil_moisture_after' => 'decimal:2',
    ];

    const METHOD_DRIP = 'drip';
    const METHOD_SPRINKLER = 'sprinkler';
    const METHOD_FLOOD = 'flood';
    const METHOD_CENTER_PIVOT = 'center_pivot';
    const METHOD_SUBSURFACE = 'subsurface';
    const METHOD_MANUAL = 'manual';

    const SOURCE_BOREHOLE = 'borehole';
    const SOURCE_RIVER = 'river';
    const SOURCE_DAM = 'dam';
    const SOURCE_RAINWATER = 'rainwater';
    const SOURCE_MUNICIPAL = 'municipal';
    const SOURCE_WELL = 'well';
    const SOURCE_CANAL = 'canal';

    const COST_TYPE_PUMP = 'pump';
    const COST_TYPE_FUEL = 'fuel';
    const COST_TYPE_ELECTRICITY = 'electricity';
    const COST_TYPE_LABOR = 'labor';

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
        if (! $this->soil_moisture_before || ! $this->soil_moisture_after) {
            return null;
        }

        $moistureIncrease = $this->soil_moisture_after - $this->soil_moisture_before;
        $waterPerLiter = $this->water_used_liters > 0 ? $moistureIncrease / $this->water_used_liters : 0;

        return $waterPerLiter * 100;
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->irrigation_method) {
            self::METHOD_DRIP => 'Drip',
            self::METHOD_SPRINKLER => 'Sprinkler',
            self::METHOD_FLOOD => 'Flood',
            self::METHOD_CENTER_PIVOT => 'Center Pivot',
            self::METHOD_SUBSURFACE => 'Subsurface',
            self::METHOD_MANUAL => 'Manual',
            default => 'N/A',
        };
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->water_source) {
            self::SOURCE_BOREHOLE => 'Borehole',
            self::SOURCE_RIVER => 'River',
            self::SOURCE_DAM => 'Dam',
            self::SOURCE_RAINWATER => 'Rainwater',
            self::SOURCE_MUNICIPAL => 'Municipal',
            self::SOURCE_WELL => 'Well',
            self::SOURCE_CANAL => 'Canal',
            default => 'N/A',
        };
    }

    public function getCostTypeLabelAttribute(): string
    {
        return match ($this->cost_type) {
            self::COST_TYPE_PUMP => 'Pump',
            self::COST_TYPE_FUEL => 'Fuel',
            self::COST_TYPE_ELECTRICITY => 'Electricity',
            self::COST_TYPE_LABOR => 'Labor',
            default => 'N/A',
        };
    }
}
