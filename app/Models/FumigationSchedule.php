<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FumigationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'farm_id',
        'field_id',
        'crop_cycle_id',
        'target_pest',
        'fumigation_type',
        'chemical_id',
        'chemical_form',
        'active_ingredient',
        'mode_of_action',
        'toxicity_level',
        'quantity',
        'unit',
        'scheduled_date',
        'start_time',
        'end_time',
        'exposure_duration_hours',
        'frequency_days',
        'next_schedule_date',
        'completed_date',
        'status',
        'temperature',
        'humidity_level',
        'wind_speed',
        'location',
        'area_covered',
        'volume_covered',
        'dosage',
        'application_method',
        'performed_by',
        'notes',
        'cost',
        'enclosure_type',
        'sealing_status',
        'ventilation_method',
        'rei_hours',
        'safe_entry_at',
        'actual_start_at',
        'actual_end_at',
        'pest_activity_before',
        'pest_activity_after',
        'effectiveness_rating',
        'incident_report',
        'operator_notes',
        'compliance_status',
        'restricted_warning',
        'staff_operator_id',
        'operator_certified',
        'certification_expiry',
        'emergency_contacts',
        'ppe_respirator',
        'ppe_gloves',
        'ppe_suit',
        'ppe_complete',
        'ai_recommended',
        'ai_suggestion',
        'infestation_risk',
        'next_recommended_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'next_schedule_date' => 'date',
        'next_recommended_date' => 'date',
        'start_time' => 'time',
        'end_time' => 'time',
        'temperature' => 'decimal:2',
        'humidity_level' => 'decimal:2',
        'wind_speed' => 'decimal:2',
        'area_covered' => 'decimal:2',
        'volume_covered' => 'decimal:2',
        'exposure_duration_hours' => 'integer',
        'frequency_days' => 'integer',
        'safe_entry_at' => 'datetime',
        'actual_start_at' => 'datetime',
        'actual_end_at' => 'datetime',
        'rei_hours' => 'integer',
        'ppe_respirator' => 'boolean',
        'ppe_gloves' => 'boolean',
        'ppe_suit' => 'boolean',
        'ppe_complete' => 'boolean',
        'operator_certified' => 'boolean',
        'ai_recommended' => 'boolean',
    ];

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ACTIVE = 'active';
    const STATUS_VENTILATING = 'ventilating';
    const STATUS_COMPLETED = 'completed';
    const STATUS_MISSED = 'missed';
    const STATUS_CANCELLED = 'cancelled';

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_operator_id');
    }

    public function photos()
    {
        return $this->hasMany(\App\Models\FumigationPhoto::class);
    }

    public function logs()
    {
        return $this->hasMany(\App\Models\FumigationLog::class);
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\FumigationNotification::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function chemical(): BelongsTo
    {
        return $this->belongsTo(ChemicalType::class);
    }
}