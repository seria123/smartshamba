<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SprayingApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_cycle_id',
        'field_id',
        'user_id',
        'chemical_type',
        'chemical_name',
        'quantity',
        'unit',
        'application_date',
        'growth_stage',
        'application_method',
        'weather_conditions',
        'target_pest',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'application_date' => 'date',
    ];

    const CHEMICAL_PESTICIDE = 'pesticide';
    const CHEMICAL_HERBICIDE = 'herbicide';
    const CHEMICAL_FUNGICIDE = 'fungicide';

    public static function getChemicalTypes(): array
    {
        return [
            self::CHEMICAL_PESTICIDE => 'Pesticide',
            self::CHEMICAL_HERBICIDE => 'Herbicide',
            self::CHEMICAL_FUNGICIDE => 'Fungicide',
        ];
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class, 'crop_cycle_id', 'crop_id');
    }
}