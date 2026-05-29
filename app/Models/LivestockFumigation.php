<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivestockFumigation extends Model
{
    use HasFactory;

    protected $table = 'livestock_fumigations';

    protected $fillable = [
        'livestock_id',
        'farm_id',
        'user_id',
        'procedure_type',
        'chemical_used',
        'quantity',
        'unit',
        'procedure_date',
        'next_schedule_date',
        'status',
        'administrator',
        'cost',
        'notes',
    ];

    protected $casts = [
        'procedure_date' => 'date',
        'next_schedule_date' => 'date',
        'quantity' => 'decimal:2',
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

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}