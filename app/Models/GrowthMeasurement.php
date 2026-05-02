<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrowthMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_stage_id',
        'measurement_date',
        'height_cm',
        'leaf_count',
        'fruit_count',
        'health_notes',
    ];

    protected $casts = [
        'measurement_date' => 'date',
        'height_cm' => 'decimal:2',
    ];

    /**
     * Get the crop stage that owns the measurement.
     */
    public function cropStage(): BelongsTo
    {
        return $this->belongsTo(CropStage::class);
    }
}
