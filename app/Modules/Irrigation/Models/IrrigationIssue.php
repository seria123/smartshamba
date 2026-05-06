<?php

namespace App\Modules\Irrigation\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IrrigationIssue extends Model
{
    use SoftDeletes;

    protected $table = 'irrigation_issues';
    protected $fillable = ['organization_id', 'farm_id', 'irrigation_zone_id', 'water_source_id', 'field_id', 'related_task_id', 'issue_number', 'issue_date', 'issue_type', 'severity', 'status', 'description', 'reported_by_user_id', 'reported_by_worker_id', 'resolved_by', 'resolved_at', 'resolution_notes', 'created_by', 'updated_by'];
    protected function casts(): array { return ['issue_date' => 'date', 'resolved_at' => 'datetime']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function zone(): BelongsTo { return $this->belongsTo(IrrigationZone::class, 'irrigation_zone_id'); }
    public function waterSource(): BelongsTo { return $this->belongsTo(IrrigationWaterSource::class, 'water_source_id'); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function relatedTask(): BelongsTo { return $this->belongsTo(OpsTask::class, 'related_task_id'); }
    public function reportedByUser(): BelongsTo { return $this->belongsTo(User::class, 'reported_by_user_id'); }
    public function reportedByWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'reported_by_worker_id'); }
    public function resolvedBy(): BelongsTo { return $this->belongsTo(User::class, 'resolved_by'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
