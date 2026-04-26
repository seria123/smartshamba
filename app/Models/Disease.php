<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disease extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'severity',
        'symptoms',
        'treatment',
        'prevention',
        'is_contagious',
        'is_active',
    ];

    protected $casts = [
        'is_contagious' => 'boolean',
        'is_active' => 'boolean',
    ];

    const SEVERITY_LOW = 'low';

    const SEVERITY_MEDIUM = 'medium';

    const SEVERITY_HIGH = 'high';

    const STATUS_ACTIVE = 'active';

    const STATUS_TREATED = 'treated';

    const STATUS_CHRONIC = 'chronic';

    public function livestockCases(): HasMany
    {
        return $this->hasMany(LivestockDisease::class);
    }

    public function activeCases(): HasMany
    {
        return $this->hasMany(LivestockDisease::class)->where('status', 'active');
    }

    public function scopeHighSeverity($query)
    {
        return $query->where('severity', self::SEVERITY_HIGH);
    }

    public function scopeContagious($query)
    {
        return $query->where('is_contagious', true);
    }

    public function isHighSeverity(): bool
    {
        return $this->severity === self::SEVERITY_HIGH;
    }

    public function getActiveCaseCountAttribute(): int
    {
        return $this->activeCases()->count();
    }
}
