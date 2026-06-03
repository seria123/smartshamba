<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SprayingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'crop_id',
        'field_id',
        'crop_cycle_id',
        'spray_type',
        'chemical_name',
        'quantity',
        'unit',
        'scheduled_date',
        'applied_date',
        'application_method',
        'notes',
        'status',
        'cost',
        'crop_type',
        'crop_variety',
        'planting_date',
        'growth_stage',
        'active_ingredient',
        'target_pest_disease',
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
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'applied_date' => 'date',
    ];

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const TYPE_PESTICIDE = 'pesticide';
    const TYPE_HERBICIDE = 'herbicide';
    const TYPE_FUNGICIDE = 'fungicide';
    const TYPE_INSECTICIDE = 'insecticide';

    public static function getSprayTypes(): array
    {
        return [
            self::TYPE_PESTICIDE => 'Pesticide',
            self::TYPE_HERBICIDE => 'Herbicide',
            self::TYPE_FUNGICIDE => 'Fungicide',
            self::TYPE_INSECTICIDE => 'Insecticide',
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