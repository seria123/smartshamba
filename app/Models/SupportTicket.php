<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'priority',
        'status',
        'category',
        'support_channel',
        'auto_tags',
        'suggested_solutions',
        'context_snapshot',
        'media_paths',
        'assigned_role',
        'assigned_to',
        'first_response_at',
        'resolved_at',
        'sla_due_at',
        'reference_number',
        'closed_at',
        'satisfaction_rating',
        'satisfaction_comment',
    ];

    protected $casts = [
        'auto_tags' => 'array',
        'suggested_solutions' => 'array',
        'context_snapshot' => 'array',
        'media_paths' => 'array',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'sla_due_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (! $ticket->reference_number) {
                $ticket->reference_number = 'SUP-'.str_pad((string) mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getFirstResponseMinutesAttribute(): ?int
    {
        return $this->first_response_at ? (int) $this->created_at->diffInMinutes($this->first_response_at) : null;
    }

    public function getResolutionMinutesAttribute(): ?int
    {
        return $this->resolved_at ? (int) $this->created_at->diffInMinutes($this->resolved_at) : null;
    }

    public function getSlaStatusAttribute(): string
    {
        if (! $this->sla_due_at || in_array($this->status, ['resolved', 'closed'], true)) {
            return 'ok';
        }

        return $this->sla_due_at->isPast() ? 'breached' : 'on_track';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'info',
            'in_progress' => 'warning',
            'waiting_reply' => 'secondary',
            'resolved' => 'success',
            'closed' => 'danger',
            default => 'secondary',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'secondary',
            'normal', 'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary',
        };
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting_reply']);
    }
}
