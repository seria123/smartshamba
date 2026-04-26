<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropRotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_id',
        'previous_crop_id',
        'sequence_order',
        'yield_benefit_percentage',
        'benefits',
        'risks',
        'recommendations',
    ];

    protected $casts = [
        'yield_benefit_percentage' => 'decimal:2',
    ];

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class, 'crop_id');
    }

    public function previousCrop(): BelongsTo
    {
        return $this->belongsTo(Crop::class, 'previous_crop_id');
    }
}
