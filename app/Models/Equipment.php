<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    protected $fillable = [
        'farm_id',
        'name',
        'type',
        'status',
        'model_number',
        'serial_number',
        'description',
        'purchase_date',
        'purchase_cost',
        'condition',
        'assigned_to',
        'last_service_date',
        'next_service_date',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'last_service_date' => 'date',
        'next_service_date' => 'date',
        'purchase_cost' => 'decimal:2',
    ];

    /**
     * Get the farm that owns the equipment.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the staff member assigned to this equipment.
     */
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_to');
    }

    /**
     * Get the activities that use this equipment.
     */
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_equipment')
            ->withPivot('notes')
            ->withTimestamps();
    }
}
