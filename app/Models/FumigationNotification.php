<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FumigationNotification extends Model
{
    protected $fillable = [
        'fumigation_schedule_id',
        'user_id',
        'type',
        'channel',
        'status',
        'sent_at',
        'message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
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
