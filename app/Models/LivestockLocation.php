<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LivestockLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'livestock_id',
        'field_id',
        'farm_id',
        'gps_latitude',
        'gps_longitude',
        'location_type',
        'movement_type',
        'entered_at',
        'left_at',
        'duration_minutes',
        'notes',
    ];

    protected $casts = [
        'gps_latitude' => 'decimal:8',
        'gps_longitude' => 'decimal:8',
        'entered_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    /**
     * Get the livestock that owns this location record.
     */
    public function livestock()
    {
        return $this->belongsTo(Livestock::class)->withDefault();
    }

    /**
     * Get the field where the livestock was located.
     */
    public function field()
    {
        return $this->belongsTo(Field::class)->withDefault();
    }

    /**
     * Get the farm where the livestock was located.
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class)->withDefault();
    }

    /**
     * Scope for active/current locations (where left_at is null).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('left_at');
    }

    /**
     * Scope for locations within a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('entered_at', [$startDate, $endDate]);
    }

    /**
     * Calculate duration in minutes if left_at is set.
     */
    public function getActualDurationAttribute(): ?int
    {
        if ($this->left_at) {
            return $this->entered_at->diffInMinutes($this->left_at);
        }

        return null;
    }
}
