<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LivestockType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'requires_individual_tracking',
    ];

    protected $casts = [
        'requires_individual_tracking' => 'boolean',
    ];

    public function livestock(): HasMany
    {
        return $this->hasMany(Livestock::class);
    }

    public function getTotalCountAttribute(): int
    {
        return $this->livestock()->where('status', '!=', 'sold')->where('status', '!=', 'dead')->count();
    }

    public function getHealthyCountAttribute(): int
    {
        return $this->livestock()->where('status', 'healthy')->count();
    }

    public function getSickCountAttribute(): int
    {
        return $this->livestock()->where('status', 'sick')->count();
    }
}
