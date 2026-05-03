<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farmer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'national_id',
        'date_of_birth',
        'gender',
        'address',
        'village',
        'ward',
        'district',
        'region',
        'latitude',
        'longitude',
        'farm_size_hectares',
        'farm_type',
        'crop_history',
        'farming_methods',
        'is_active',
    ];

    protected $casts = [
        'crop_history' => 'array',
        'farming_methods' => 'array',
        'date_of_birth' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'farm_size_hectares' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(FarmerDocument::class);
    }

    public function cropHistories(): HasMany
    {
        return $this->hasMany(CropHistory::class);
    }

    public function yieldEstimations(): HasMany
    {
        return $this->hasMany(YieldEstimation::class);
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
