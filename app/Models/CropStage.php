<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CropStage extends Model
{
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
}
