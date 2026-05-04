<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmImage extends Model
{
    protected $fillable = [
        'farm_id',
        'image_path',
        'caption',
    ];

    /**
     * Get the farm that owns the image.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
