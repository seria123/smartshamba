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
        'farm_type',
        'ownership_type',
        'latitude',
        'longitude',
        'size_hectares',
        'description',
        'farm_operation_details',
    ];

    protected $casts = [
        'size_hectares' => 'decimal:2',
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
}
