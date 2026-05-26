<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Crop;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CropStage extends Model
{
    protected $fillable = [
        'crop_cycle_id',
        'stage_name',
        'start_date',
        'end_date',
        'health_status',
        'germination_rate',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'germination_rate' => 'decimal:2',
    ];

    /**
     * Get the crop cycle that owns the stage.
     */
    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    /**
     * Get the activities for the stage.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the growth measurements for the stage.
     */
    public function measurements(): HasMany
    {
        return $this->hasMany(GrowthMeasurement::class);
    }
    public function crop()
{
    return $this->belongsTo(Crop::class);
}
    
}
