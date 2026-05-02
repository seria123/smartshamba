<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PestDiseaseTreatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_cycle_id',
        'issue_type',
        'title',
        'description',
        'severity',
        'affected_area',
        'treatment_date',
        'treatment_method',
        'chemicals_used',
        'outcome',
        'cost',
        'notes',
    ];

    protected $casts = [
        'treatment_date' => 'date',
        'cost' => 'decimal:2',
    ];

    /**
     * Get the crop cycle that owns the treatment.
     */
    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }
}
