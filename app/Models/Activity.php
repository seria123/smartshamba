<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    protected $fillable = [
        'crop_stage_id',
        'crop_cycle_id',
        'activity_name',
        'description',
        'cost',
        'activity_date',
        'staff_id',
        'labor_type',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'activity_date' => 'date',
    ];

    /**
     * Get the crop stage that owns the activity.
     */
    public function cropStage(): BelongsTo
    {
        return $this->belongsTo(CropStage::class);
    }

    /**
     * Get the crop cycle that owns the activity.
     */
    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    /**
     * Get the staff member that performed the activity.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get the inputs for the activity.
     */
    public function inputs(): HasMany
    {
        return $this->hasMany(Input::class);
    }
}
