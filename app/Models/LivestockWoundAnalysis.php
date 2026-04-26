<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LivestockWoundAnalysis extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'livestock_id',
        'user_id',
        'image_path',
        'wound_type',
        'severity',
        'description',
        'treatment_plan',
        'urgency',
        'estimated_healing_time',
        'detected_issues',
        'confidence_score',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'detected_issues' => 'array',
        'confidence_score' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the livestock that owns the wound analysis.
     */
    public function livestock()
    {
        return $this->belongsTo(Livestock::class);
    }

    /**
     * Get the user that performed the analysis.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get urgency color for display.
     */
    public function getUrgencyColorAttribute(): string
    {
        return match ($this->urgency) {
            'immediate' => 'danger',
            'urgent' => 'warning',
            'routine' => 'info',
            'monitoring' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get severity color for display.
     */
    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'minor' => 'success',
            'moderate' => 'warning',
            'severe' => 'danger',
            'critical' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get status color for display.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'analyzed' => 'info',
            'treated' => 'success',
            'healed' => 'primary',
            default => 'secondary',
        };
    }
}
