<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivestockVaccination extends Model
{
    use HasFactory;

    protected $fillable = [
        'livestock_id',
        'user_id',
        'vaccine_name',
        'vaccine_type',
        'vaccination_date',
        'next_due_date',
        'scheduled_date',
        'status',
        'administrator',
        'cost',
        'notes',
    ];

    protected $casts = [
        'vaccination_date' => 'date',
        'next_due_date' => 'date',
        'scheduled_date' => 'date',
        'cost' => 'decimal:2',
    ];

    const STATUS_COMPLETED = 'completed';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_MISSED = 'missed';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_MISSED => 'Missed',
        ];
    }

    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}