<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    /**
     * Get the crop stage that owns the activity.
     */
    public function cropStage(): BelongsTo
    {
        return $this->belongsTo(CropStage::class);
    }

    /**
     * Get the inputs for the activity.
     */
    public function inputs(): HasMany
    {
        return $this->hasMany(Input::class);
    }
}
