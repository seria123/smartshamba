<?php

namespace App\Modules\Assets\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetMaintenanceSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = ['organization_id', 'farm_id', 'asset_id', 'schedule_number', 'maintenance_type', 'scheduled_date', 'frequency_type', 'frequency_interval', 'priority', 'status', 'assigned_user_id', 'assigned_worker_id', 'assigned_team_id', 'related_task_id', 'instructions', 'created_by', 'updated_by', 'cancelled_by', 'cancelled_at', 'cancellation_reason', 'completed_at'];

    protected function casts(): array { return ['scheduled_date' => 'date', 'cancelled_at' => 'datetime', 'completed_at' => 'datetime']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function asset(): BelongsTo { return $this->belongsTo(Asset::class); }
    public function relatedTask(): BelongsTo { return $this->belongsTo(OpsTask::class, 'related_task_id'); }
    public function assignedUser(): BelongsTo { return $this->belongsTo(User::class, 'assigned_user_id'); }
    public function assignedWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'assigned_worker_id'); }
    public function assignedTeam(): BelongsTo { return $this->belongsTo(LabourTeam::class, 'assigned_team_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
