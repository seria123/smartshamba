<?php

namespace App\Modules\Audit\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AuditActivityLog extends Model
{
    protected $fillable = [
        'organization_id', 'farm_id', 'actor_user_id', 'actor_name', 'actor_email',
        'module', 'event', 'action_label', 'subject_type', 'subject_id', 'subject_label',
        'description', 'before_values', 'after_values', 'changed_values', 'metadata',
        'request_method', 'request_url', 'ip_address', 'user_agent', 'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'before_values' => 'array',
            'after_values' => 'array',
            'changed_values' => 'array',
            'metadata' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_user_id'); }

    public function getActorDisplayNameAttribute(): string
    {
        return $this->actor_name ?: $this->actor?->name ?: 'System';
    }

    public function getSubjectDisplayAttribute(): string
    {
        if ($this->subject_label) {
            return $this->subject_label;
        }

        if ($this->subject_type && $this->subject_id) {
            return Str::headline(class_basename($this->subject_type)).' #'.$this->subject_id;
        }

        return 'No subject';
    }

    public function getEventLabelAttribute(): string
    {
        return $this->action_label ?: Str::headline(str_replace(['.', '_', '-'], ' ', $this->event));
    }

    public function getModuleLabelAttribute(): string
    {
        return Str::headline(str_replace(['.', '_', '-'], ' ', $this->module));
    }
}
