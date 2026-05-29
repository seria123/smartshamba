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
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'administered_date' => 'date',
        'next_due_date' => 'date',
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