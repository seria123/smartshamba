<?php

namespace App\Modules\Irrigation\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IrrigationSchedule extends Model
{
    use SoftDeletes;

    protected $table = 'irrigation_schedules';
    protected $fillable = ['organization_id', 'farm_id', 'irrigation_zone_id', 'field_id', 'crop_cycle_id', 'related_task_id', 'schedule_number', 'scheduled_date', 'scheduled_start_time', 'scheduled_end_time', 'planned_duration_minutes', 'planned_water_volume', 'water_volume_unit', 'priority', 'status', 'assigned_user_id', 'assigned_worker_id', 'assigned_team_id', 'instructions', 'created_by', 'updated_by', 'cancelled_by', 'cancelled_at', 'cancellation_reason', 'completed_at'];
    protected function casts(): array { return ['scheduled_date' => 'date', 'planned_water_volume' => 'decimal:2', 'cancelled_at' => 'datetime', 'completed_at' => 'datetime']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function zone(): BelongsTo { return $this->belongsTo(IrrigationZone::class, 'irrigation_zone_id'); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function cropCycle(): BelongsTo { return $this->belongsTo(CropCycle::class); }
    public function relatedTask(): BelongsTo { return $this->belongsTo(OpsTask::class, 'related_task_id'); }
    public function assignedUser(): BelongsTo { return $this->belongsTo(User::class, 'assigned_user_id'); }
    public function assignedWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'assigned_worker_id'); }
    public function assignedTeam(): BelongsTo { return $this->belongsTo(LabourTeam::class, 'assigned_team_id'); }
    public function events(): HasMany { return $this->hasMany(IrrigationEvent::class, 'schedule_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
