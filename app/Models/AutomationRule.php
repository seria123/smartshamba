<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'name',
        'sensor_type',
        'operator',
        'threshold',
        'action',
        'is_active',
        'cooldown_minutes',
        'last_triggered',
    ];

    protected $casts = [
        'threshold' => 'decimal:2',
        'is_active' => 'boolean',
        'cooldown_minutes' => 'integer',
        'last_triggered' => 'datetime',
    ];

    /**
     * Get the field that owns the automation rule.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Check if the rule can be triggered based on cooldown
     */
    public function canTrigger(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->last_triggered) {
            return true;
        }

        return $this->last_triggered->addMinutes($this->cooldown_minutes)->isPast();
    }

    /**
     * Evaluate the rule against a sensor reading
     */
    public function evaluate(float $value): bool
    {
        return match ($this->operator) {
            '<' => $value < $this->threshold,
            '<=' => $value <= $this->threshold,
            '>' => $value > $this->threshold,
            '>=' => $value >= $this->threshold,
            '==' => $value == $this->threshold,
            '!=' => $value != $this->threshold,
            default => false,
        };
    }

    /**
     * Mark the rule as triggered
     */
    public function markTriggered(): void
    {
        $this->update(['last_triggered' => now()]);
    }

    /**
     * Get display name for sensor type
     */
    public function getSensorTypeNameAttribute(): string
    {
        return match ($this->sensor_type) {
            'soil_moisture' => 'Soil Moisture',
            'soil_ph' => 'Soil pH',
            'temperature' => 'Temperature',
            'humidity' => 'Humidity',
            'light_intensity' => 'Light Intensity',
            'rain_detection' => 'Rain Detection',
            default => ucfirst($this->sensor_type),
        };
    }

    /**
     * Get display name for action
     */
    public function getActionNameAttribute(): string
    {
        return match ($this->action) {
            'irrigation_on' => 'Turn Irrigation ON',
            'irrigation_off' => 'Turn Irrigation OFF',
            'cooling_on' => 'Activate Cooling',
            'cooling_off' => 'Deactivate Cooling',
            'shade_on' => 'Activate Shade',
            'shade_off' => 'Deactivate Shade',
            'alert' => 'Send Alert',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get icon for action
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'irrigation_on' => '💧',
            'irrigation_off' => '🚫',
            'cooling_on' => '❄️',
            'cooling_off' => '🔥',
            'shade_on' => '☂️',
            'shade_off' => '☀️',
            'alert' => '⚠️',
            default => '⚡',
        };
    }
}
