<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FertilizerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'fertilizer_id',
        'field_id',
        'quantity_used',
        'unit',
        'application_date',
        'growth_stage',
        'application_method',
        'notes',
    ];

    protected $casts = [
        'quantity_used' => 'decimal:2',
        'application_date' => 'date',
    ];

    public function fertilizer(): BelongsTo
    {
        return $this->belongsTo(Fertilizer::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function totalCost(): float
    {
        // This would need to join with fertilizers table to get unit_cost
        // For now, we'll return 0 and implement properly in service/repository
        return 0;
    }
}
