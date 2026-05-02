<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Input extends Model
{
    protected $fillable = [
        'activity_id',
        'input_type',
        'name',
        'quantity',
        'unit',
        'cost',
        'application_date',
        'application_method',
        'crop_cycle_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost' => 'decimal:2',
        'application_date' => 'date',
    ];

    /**
     * Get the activity that owns the input.
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the crop cycle that owns the input.
     */
    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }
}
