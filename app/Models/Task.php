<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'crop_id',
        'title',
        'description',
        'task_type',
        'status',
        'priority',
        'scheduled_date',
        'completed_date',
        'assigned_to',
        'estimated_hours',
        'actual_hours',
        'estimated_cost',
        'actual_cost',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'completed_date' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    // Task Types
    const TYPE_PLANTING = 'planting';
    const TYPE_HARVESTING = 'harvesting';
    const TYPE_IRRIGATION = 'irrigation';
    const TYPE_FERTILIZING = 'fertilizing';
    const TYPE_PEST_CONTROL = 'pest_control';
    const TYPE_WEEDING = 'weeding';
    const TYPE_SOIL_PREPARATION = 'soil_preparation';
    const TYPE_MAINTENANCE = 'maintenance';
    const TYPE_INSPECTION = 'inspection';
    const TYPE_OTHER = 'other';

    // Status
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ON_HOLD = 'on_hold';

    // Priority
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isOverdue(): bool
    {
        return $this->scheduled_date->isPast() && 
               $this->status !== self::STATUS_COMPLETED && 
               $this->status !== self::STATUS_CANCELLED;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_IN_PROGRESS => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'secondary',
            self::STATUS_ON_HOLD => 'secondary',
            default => 'secondary',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'secondary',
            self::PRIORITY_NORMAL => 'primary',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_URGENT => 'danger',
            default => 'secondary',
        };
    }
}