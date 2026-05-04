<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'subcounty',
        'physical_address',
        'farm_type',
        'ownership_type',
        'latitude',
        'longitude',
        'size_hectares',
        'description',
        'storage_facilities',
        'estimated_budget',
        'main_purpose',
        'main_image_id',
        'farm_operation_details',
    ];

    protected $casts = [
        'size_hectares' => 'decimal:2',
        'estimated_budget' => 'decimal:2',
        'storage_facilities' => 'boolean',
        'farm_operation_details' => 'array',
    ];

    /**
     * Get the fields for the farm.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the crop cycles for the farm.
     */
    public function cropCycles(): HasMany
    {
        return $this->hasMany(CropCycle::class);
    }

    /**
     * Get the images for the farm.
     */
    public function images(): HasMany
    {
        return $this->hasMany(FarmImage::class);
    }

    /**
     * Get the documents for the farm.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(FarmDocument::class);
    }

    /**
     * Get the main/featured image for the farm.
     */
    public function mainImage(): BelongsTo
    {
        return $this->belongsTo(FarmImage::class, 'main_image_id');
    }
}
