<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IrrigationZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'name',
        'description',
        'controller_id',
        'valve_id',
        'status',
        'flow_rate_lph',
        'duration_minutes',
        'start_time',
        'end_time',
        'schedule_type',
        'schedule_days',
        'soil_moisture_threshold',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'schedule_days' => 'array',
        'soil_moisture_threshold' => 'decimal:2',
        'flow_rate_lph' => 'decimal:2',
        'duration_minutes' => 'integer',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    // Status
    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    const STATUS_MAINTENANCE = 'maintenance';

    const STATUS_ERROR = 'error';

    // Schedule Type
    const SCHEDULE_DAILY = 'daily';

    const SCHEDULE_WEEKDAYS = 'weekdays';

    const SCHEDULE_CUSTOM = 'custom';

    const SCHEDULE_MANUAL = 'manual';

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(IrrigationLog::class);
    }

    public function getActiveLog()
    {
        return $this->logs()->where('status', 'running')->first();
    }

    public function isRunning(): bool
    {
        return $this->getActiveLog() !== null;
    }

    public function getTotalWaterUsedToday(): float
    {
        return $this->logs()
            ->whereDate('started_at', now()->toDateString())
            ->where('status', 'completed')
            ->sum('water_used_liters') ?? 0;
    }

    public function getTotalDurationToday(): int
    {
        return $this->logs()
            ->whereDate('started_at', now()->toDateString())
            ->where('status', 'completed')
            ->sum('duration_minutes') ?? 0;
    }
}
