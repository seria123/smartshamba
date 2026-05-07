<?php

namespace App\Modules\Assets\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetBreakdownRecord extends Model
{
    use SoftDeletes;

    protected $fillable = ['organization_id', 'farm_id', 'asset_id', 'related_task_id', 'breakdown_number', 'breakdown_date', 'issue_type', 'severity', 'status', 'description', 'reported_by_user_id', 'reported_by_worker_id', 'resolved_by_user_id', 'resolved_at', 'resolution_notes', 'created_by', 'updated_by'];

    protected function casts(): array { return ['breakdown_date' => 'date', 'resolved_at' => 'datetime']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function asset(): BelongsTo { return $this->belongsTo(Asset::class); }
    public function relatedTask(): BelongsTo { return $this->belongsTo(OpsTask::class, 'related_task_id'); }
    public function reportedByUser(): BelongsTo { return $this->belongsTo(User::class, 'reported_by_user_id'); }
    public function reportedByWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'reported_by_worker_id'); }
    public function resolvedByUser(): BelongsTo { return $this->belongsTo(User::class, 'resolved_by_user_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
