<?php

namespace App\Modules\Assets\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Core\Models\Warehouse;
use App\Modules\Irrigation\Models\IrrigationEvent;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetUsageRecord extends Model
{
    use SoftDeletes;

    protected $fillable = ['organization_id', 'farm_id', 'asset_id', 'usage_date', 'usage_type', 'related_task_id', 'related_irrigation_event_id', 'field_id', 'paddock_id', 'warehouse_id', 'used_by_user_id', 'used_by_worker_id', 'team_id', 'start_time', 'end_time', 'duration_minutes', 'meter_start', 'meter_end', 'usage_quantity', 'usage_unit', 'notes', 'created_by'];

    protected function casts(): array { return ['usage_date' => 'date', 'meter_start' => 'decimal:2', 'meter_end' => 'decimal:2', 'usage_quantity' => 'decimal:2']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function asset(): BelongsTo { return $this->belongsTo(Asset::class); }
    public function relatedTask(): BelongsTo { return $this->belongsTo(OpsTask::class, 'related_task_id'); }
    public function relatedIrrigationEvent(): BelongsTo { return $this->belongsTo(IrrigationEvent::class, 'related_irrigation_event_id'); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function paddock(): BelongsTo { return $this->belongsTo(Paddock::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function usedByUser(): BelongsTo { return $this->belongsTo(User::class, 'used_by_user_id'); }
    public function usedByWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'used_by_worker_id'); }
    public function team(): BelongsTo { return $this->belongsTo(LabourTeam::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
