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
use Illuminate\Database\Eloquent\SoftDeletes;

class IrrigationEvent extends Model
{
    use SoftDeletes;

    protected $table = 'irrigation_events';
    protected $fillable = ['organization_id', 'farm_id', 'irrigation_zone_id', 'water_source_id', 'field_id', 'crop_cycle_id', 'schedule_id', 'related_task_id', 'event_number', 'irrigation_date', 'start_time', 'end_time', 'duration_minutes', 'water_volume', 'water_volume_unit', 'method', 'status', 'performed_by_user_id', 'performed_by_worker_id', 'team_id', 'notes', 'created_by', 'updated_by', 'cancelled_by', 'cancelled_at', 'cancellation_reason'];
    protected function casts(): array { return ['irrigation_date' => 'date', 'water_volume' => 'decimal:2', 'cancelled_at' => 'datetime']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function zone(): BelongsTo { return $this->belongsTo(IrrigationZone::class, 'irrigation_zone_id'); }
    public function waterSource(): BelongsTo { return $this->belongsTo(IrrigationWaterSource::class, 'water_source_id'); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function cropCycle(): BelongsTo { return $this->belongsTo(CropCycle::class); }
    public function schedule(): BelongsTo { return $this->belongsTo(IrrigationSchedule::class); }
    public function relatedTask(): BelongsTo { return $this->belongsTo(OpsTask::class, 'related_task_id'); }
    public function performedByUser(): BelongsTo { return $this->belongsTo(User::class, 'performed_by_user_id'); }
    public function performedByWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'performed_by_worker_id'); }
    public function team(): BelongsTo { return $this->belongsTo(LabourTeam::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
