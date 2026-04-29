<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LivestockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'livestock_id',
        'user_id',
        'movement_type',
        'from_farm_id',
        'to_farm_id',
        'from_field_id',
        'to_field_id',
        'movement_date',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     *Get the livestock that was moved.
     */
    public function livestock()
    {
        return $this->belongsTo(Livestock::class)->withDefault();
    }

    /**
     * Get the user who recorded the movement.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * Get the source farm.
     */
    public function fromFarm()
    {
        return $this->belongsTo(Farm::class, 'from_farm_id')->withDefault();
    }

    /**
     * Get the destination farm.
     */
    public function toFarm()
    {
        return $this->belongsTo(Farm::class, 'to_farm_id')->withDefault();
    }

    /**
     * Get the source field.
     */
    public function fromField()
    {
        return $this->belongsTo(Field::class, 'from_field_id')->withDefault();
    }

    /**
     * Get the destination field.
     */
    public function toField()
    {
        return $this->belongsTo(Field::class, 'to_field_id')->withDefault();
    }

    /**
     * Scope for movements within a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }

    /**
     * Scope for movements by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('movement_type', $type);
    }
}
