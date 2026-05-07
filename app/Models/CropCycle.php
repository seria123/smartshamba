<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CropCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_name',
        'start_date',
        'expected_harvest_date',
        'field_id',
        'crop_id',
        'farm_id',
        'category',
        'variety',
        'season',
        'irrigation_type',
        'irrigation_schedule',
        'drainage',
        'ph_level',
        'previous_crop_cycle_id',
        'soil_type_override',
        'water_source_override',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_harvest_date' => 'date',
        'ph_level' => 'decimal:2',
    ];

    /**
     * Get the farm that owns the crop cycle.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the field that owns the crop cycle.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the crop associated with the cycle.
     */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    /**
     * Get the stages for the crop cycle.
     */
    public function stages(): HasMany
    {
        return $this->hasMany(CropStage::class);
    }

    /**
     * Get the pest/disease treatments for the cycle.
     */
    public function pestDiseaseTreatments(): HasMany
    {
        return $this->hasMany(PestDiseaseTreatment::class);
    }

    /**
     * Get the disease analyses (images) for the cycle.
     */
    public function analyses(): HasMany
    {
        return $this->hasMany(CropAnalysis::class);
    }

    /**
     * Get the weather records for the cycle.
     */
    public function weather(): HasMany
    {
        return $this->hasMany(WeatherData::class);
    }

    /**
     * Get the revenues for the cycle.
     */
    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class);
    }

    /**
     * Get the inputs for the cycle.
     */
    public function inputs(): HasMany
    {
        return $this->hasMany(Input::class);
    }

    /**
     * Get the activities for the cycle.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the harvests for the cycle.
     */
    public function harvests(): HasMany
    {
        return $this->hasMany(Harvest::class);
    }

    /**
     * Get the previous crop cycle.
     */
    public function previousCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class, 'previous_crop_cycle_id');
    }

    /**
     * Get the next crop cycle.
     */
    public function nextCycle(): HasOne
    {
        return $this->hasOne(CropCycle::class, 'previous_crop_cycle_id');
    }
}
