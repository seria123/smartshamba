<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LivestockVaccinationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'livestock_id',
        'livestock_type_id',
        'vaccine_name',
        'vaccine_type',
        'scheduled_date',
        'administered_date',
        'administered_by',
        'batch_number',
        'notes',
        'status',
        'cost',
        'next_due_date',
        // Enhanced fields
        'animal_weight',
        'route',
        'dose_amount',
        'dose_unit',
        'evidence_photo_path',
        'qr_code_data',
        'vet_notes',
        'parent_schedule_id',
        'location',
        'season',
        'is_bulk_entry',
        'alert_status',
        'last_synced_at',
        'user_role',
        'vet_approved_by',
        'vet_approved_at',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'animal_weight' => 'decimal:2',
        'dose_amount' => 'decimal:2',
        'scheduled_date' => 'date',
        'administered_date' => 'date',
        'next_due_date' => 'date',
        'last_synced_at' => 'datetime',
        'vet_approved_at' => 'datetime',
        'is_bulk_entry' => 'boolean',
    ];

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ADMINISTERED = 'administered';
    const STATUS_MISSED = 'missed';
    const STATUS_CANCELLED = 'cancelled';

    const ALERT_STATUS_NONE = 'none';
    const ALERT_STATUS_UPCOMING = 'upcoming';
    const ALERT_STATUS_DUE_SOON = 'due_soon';
    const ALERT_STATUS_OVERDUE = 'overdue';
    const ALERT_STATUS_CONTRAINDICATION = 'contraindication';
    const ALERT_STATUS_EXPIRED_VACCINE = 'expired_vaccine';

    const USER_ROLE_ADMIN = 'admin';
    const USER_ROLE_VET = 'vet';
    const USER_ROLE_WORKER = 'worker';

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

    public function parentSchedule(): BelongsTo
    {
        return $this->belongsTo(LivestockVaccinationSchedule::class, 'parent_schedule_id');
    }

    public function boosterSchedules(): HasMany
    {
        return $this->hasMany(LivestockVaccinationSchedule::class, 'parent_schedule_id');
    }

    public function vetApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_approved_by');
    }
}