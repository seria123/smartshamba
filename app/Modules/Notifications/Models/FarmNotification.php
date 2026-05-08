<?php

namespace App\Modules\Notifications\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\UsersPermissions\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FarmNotification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id', 'farm_id', 'notification_rule_id', 'recipient_user_id', 'recipient_role_id',
        'title', 'message', 'severity', 'status', 'source_module', 'source_type', 'source_id', 'signal_type',
        'source_label', 'action_url', 'due_at', 'read_at', 'dismissed_at', 'resolved_at', 'generated_at',
        'created_by_user_id', 'updated_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'read_at' => 'datetime',
            'dismissed_at' => 'datetime',
            'resolved_at' => 'datetime',
            'generated_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function rule(): BelongsTo { return $this->belongsTo(NotificationRule::class, 'notification_rule_id'); }
    public function recipientUser(): BelongsTo { return $this->belongsTo(User::class, 'recipient_user_id'); }
    public function recipientRole(): BelongsTo { return $this->belongsTo(Role::class, 'recipient_role_id'); }
}
