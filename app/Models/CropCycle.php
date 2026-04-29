<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_name',
        'start_date',
        'expected_harvest',
        'farm_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_harvest' => 'date',
    ];

    /**
     * Get the farm that owns the crop cycle.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the stages for the crop cycle.
     */
    public function stages(): HasMany
    {
        return $this->hasMany(CropStage::class);
    }
}
