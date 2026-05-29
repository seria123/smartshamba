<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivestockFumigationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'farm_id',
        'fumigant_name',
        'fumigation_type',
        'quantity',
        'unit',
        'area_covered',
        'scheduled_date',
        'performed_date',
        'performed_by',
        'notes',
        'status',
        'cost',
        'next_due_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'performed_date' => 'date',
        'next_due_date' => 'date',
    ];

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_MISSED = 'missed';
    const STATUS_CANCELLED = 'cancelled';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}