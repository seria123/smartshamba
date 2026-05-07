<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'priority',
        'is_read',
        'read_at',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    // Types
    const TYPE_ALERT = 'alert';

    const TYPE_TASK = 'task';

    const TYPE_IRRIGATION = 'irrigation';

    const TYPE_SENSOR = 'sensor';

    const TYPE_SYSTEM = 'system';

    const TYPE_WEATHER = 'weather';

    const TYPE_CROP = 'crop';

    const TYPE_AUTOMATION = 'automation';

    // Priority
    const PRIORITY_LOW = 'low';

    const PRIORITY_NORMAL = 'normal';

    const PRIORITY_HIGH = 'high';

    const PRIORITY_CRITICAL = 'critical';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'secondary',
            self::PRIORITY_NORMAL => 'info',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_CRITICAL => 'danger',
            default => 'secondary',
        };
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ALERT => 'exclamation-triangle',
            self::TYPE_TASK => 'clipboard-list',
            self::TYPE_IRRIGATION => 'tint',
            self::TYPE_SENSOR => 'satellite-dish',
            self::TYPE_SYSTEM => 'cog',
            self::TYPE_WEATHER => 'cloud',
            self::TYPE_CROP => 'seedling',
            self::TYPE_AUTOMATION => 'robot',
            default => 'bell',
        };
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', self::PRIORITY_CRITICAL);
    }
}
