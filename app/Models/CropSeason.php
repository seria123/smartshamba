<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropSeason extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_id',
        'name',
        'season_period',
        'planting_start_date',
        'planting_end_date',
        'expected_harvest_start',
        'expected_harvest_end',
        'notes',
        'is_optimal',
    ];

    protected $casts = [
        'planting_start_date' => 'date',
        'planting_end_date' => 'date',
        'expected_harvest_start' => 'date',
        'expected_harvest_end' => 'date',
        'is_optimal' => 'boolean',
    ];

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function isPlantingTime(string $date): bool
    {
        return $date >= $this->planting_start_date && $date <= $this->planting_end_date;
    }

    public function isHarvestTime(string $date): bool
    {
        return $date >= $this->expected_harvest_start && $date <= $this->expected_harvest_end;
    }
}
