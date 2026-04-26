<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'generated_by',
        'report_type',
        'title',
        'description',
        'report_period_start',
        'report_period_end',
        'file_path',
        'data',
        'status',
    ];

    protected $casts = [
        'report_period_start' => 'datetime',
        'report_period_end' => 'datetime',
        'data' => 'array',
    ];

    // Report Types
    const TYPE_DAILY_SUMMARY = 'daily_summary';
    const TYPE_WEEKLY_SUMMARY = 'weekly_summary';
    const TYPE_MONTHLY_SUMMARY = 'monthly_summary';
    const TYPE_CROP_ANALYSIS = 'crop_analysis';
    const TYPE_IRRIGATION_REPORT = 'irrigation_report';
    const TYPE_SENSOR_ANALYSIS = 'sensor_analysis';
    const TYPE_FINANCIAL = 'financial';
    const TYPE_YIELD_PREDICTION = 'yield_prediction';
    const TYPE_WEATHER_IMPACT = 'weather_impact';
    const TYPE_AUTOMATION_PERFORMANCE = 'automation_performance';

    // Status
    const STATUS_PENDING = 'pending';
    const STATUS_GENERATING = 'generating';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getReportTypeNameAttribute(): string
    {
        return match($this->report_type) {
            self::TYPE_DAILY_SUMMARY => 'Daily Summary',
            self::TYPE_WEEKLY_SUMMARY => 'Weekly Summary',
            self::TYPE_MONTHLY_SUMMARY => 'Monthly Summary',
            self::TYPE_CROP_ANALYSIS => 'Crop Analysis Report',
            self::TYPE_IRRIGATION_REPORT => 'Irrigation Report',
            self::TYPE_SENSOR_ANALYSIS => 'Sensor Analysis Report',
            self::TYPE_FINANCIAL => 'Financial Report',
            self::TYPE_YIELD_PREDICTION => 'Yield Prediction',
            self::TYPE_WEATHER_IMPACT => 'Weather Impact Report',
            self::TYPE_AUTOMATION_PERFORMANCE => 'Automation Performance',
            default => 'Unknown Report',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_GENERATING => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FAILED => 'danger',
            default => 'secondary',
        };
    }

    public function getDaysInPeriodAttribute(): int
    {
        return $this->report_period_start->diffInDays($this->report_period_end) + 1;
    }
}