<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FumigationLog extends Model
{
    protected $fillable = [
        'fumigation_schedule_id',
        'user_id',
        'action',
        'status',
        'notes',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(FumigationSchedule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
