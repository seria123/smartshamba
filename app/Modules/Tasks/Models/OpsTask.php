<?php

namespace App\Modules\Tasks\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Core\Models\Site;
use App\Modules\Core\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpsTask extends Model
{
    use SoftDeletes;

    protected $table = 'ops_tasks';

    protected $fillable = [
        'organization_id',
        'farm_id',
        'work_order_id',
        'task_number',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'site_id',
        'field_id',
        'paddock_id',
        'warehouse_id',
        'assigned_by',
        'supervisor_user_id',
        'start_date',
        'due_date',
        'started_at',
        'submitted_at',
        'approved_at',
        'completed_at',
        'cancelled_at',
        'rejected_at',
        'approval_notes',
        'cancellation_reason',
        'created_by',
        'updated_by',
        'approved_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(OpsWorkOrder::class, 'work_order_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function paddock(): BelongsTo
    {
        return $this->belongsTo(Paddock::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(OpsTaskAssignment::class, 'task_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(OpsTaskUpdate::class, 'task_id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(OpsTaskChecklistItem::class, 'task_id')->orderBy('sort_order')->orderBy('id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
