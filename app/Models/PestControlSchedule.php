<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PestControlSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'crop_id',
        'field_id',
        'crop_cycle_id',
        'crop_type',
        'crop_variety',
        'planting_date',
        'growth_stage',
        'pest_name',
        'threat_level',
        'description',
        'scheduled_date',
        'frequency_days',
        'growth_stage_trigger',
        'rei_hours',
        'phi_days',
        'temperature',
        'rain_forecast',
        'wind_speed',
        'mixing_ratio',
        'water_volume',
        'equipment',
        'area_covered',
        'operator',
        'gear_gloves',
        'gear_mask',
        'gear_overalls',
        'safety_notes',
        'inspected_date',
        'treatment_date',
        'treatment_method',
        'treatment_notes',
        'status',
        'cost',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'inspected_date' => 'date',
        'treatment_date' => 'date',
    ];

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_INSPECTED = 'inspected';
    const STATUS_TREATED = 'treated';
    const STATUS_CANCELLED = 'cancelled';

    const THREAT_LOW = 'low';
    const THREAT_MEDIUM = 'medium';
    const THREAT_HIGH = 'high';

    public static function getThreatLevels(): array
    {
        return [
            self::THREAT_LOW => 'Low',
            self::THREAT_MEDIUM => 'Medium',
            self::THREAT_HIGH => 'High',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }
}