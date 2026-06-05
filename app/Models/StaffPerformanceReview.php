<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffPerformanceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'reviewed_by',
        'farm_id',
        'review_period_start',
        'review_period_end',
        'tasks_completed',
        'tasks_assigned',
        'attendance_rate',
        'work_quality_score',
        'efficiency_rating',
        'overall_score',
        'rating',
        'strengths',
        'areas_for_improvement',
        'notes',
    ];

    protected $casts = [
        'review_period_start' => 'date',
        'review_period_end' => 'date',
        'attendance_rate' => 'decimal:2',
        'work_quality_score' => 'decimal:2',
        'efficiency_rating' => 'decimal:2',
        'overall_score' => 'decimal:2',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reviewed_by');
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
