<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivestockDewormingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'livestock_id',
        'livestock_type_id',
        'dewormer_name',
        'dewormer_type',
        'quantity',
        'unit',
        'scheduled_date',
        'administered_date',
        'administered_by',
        'batch_number',
        'notes',
        'status',
        'cost',
        'next_due_date',
        // Animal Identification Details
        'breed',
        'group_herd_pen',
        'date_of_birth',
        'weight',
        // Deworming Treatment Details
        'manufacturer_brand',
        'expiry_date',
        // Schedule & Timing
        'deworming_frequency',
        'reminder_toggle',
        'reminder_method',
        // Health & Condition Tracking
        'body_condition_score',
        'signs_of_infection',
        'resistance_history',
        'current_weight',
        'previous_deworming_date',
        // Administration Details
        'administration_method',
        'supervised_by',
        'farm_location',
        // Notes & Observations
        'animal_reaction',
        'effectiveness',
        'side_effects_observed',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'administered_date' => 'date',
        'next_due_date' => 'date',
        'expiry_date' => 'date',
        'body_condition_score' => 'decimal:2',
        'current_weight' => 'decimal:2',
        'weight' => 'decimal:2',
        'date_of_birth' => 'date',
        'previous_deworming_date' => 'date',
        'reminder_toggle' => 'boolean',
        'signs_of_infection' => 'array',
        'resistance_history' => 'array',
    ];

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ADMINISTERED = 'administered';
    const STATUS_MISSED = 'missed';
    const STATUS_CANCELLED = 'cancelled';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class);
    }

    public function livestockType(): BelongsTo
    {
        return $this->belongsTo(LivestockType::class);
    }
}