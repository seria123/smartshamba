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
        'size_hectares',
        'description',
    ];

    protected $casts = [
        'size_hectares' => 'decimal:2',
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
