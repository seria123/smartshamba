<?php

namespace App\Modules\Assets\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetMaintenanceRecord extends Model
{
    use SoftDeletes;

    protected $fillable = ['organization_id', 'farm_id', 'asset_id', 'maintenance_schedule_id', 'related_task_id', 'record_number', 'maintenance_type', 'maintenance_date', 'status', 'performed_by_user_id', 'performed_by_worker_id', 'team_id', 'service_provider', 'problem_found', 'work_done', 'parts_used_notes', 'product_id', 'product_name_snapshot', 'quantity_used', 'quantity_unit', 'external_cost', 'currency', 'next_service_date', 'notes', 'created_by', 'updated_by'];

    protected function casts(): array { return ['maintenance_date' => 'date', 'quantity_used' => 'decimal:2', 'external_cost' => 'decimal:2', 'next_service_date' => 'date']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function asset(): BelongsTo { return $this->belongsTo(Asset::class); }
    public function maintenanceSchedule(): BelongsTo { return $this->belongsTo(AssetMaintenanceSchedule::class, 'maintenance_schedule_id'); }
    public function relatedTask(): BelongsTo { return $this->belongsTo(OpsTask::class, 'related_task_id'); }
    public function performedByUser(): BelongsTo { return $this->belongsTo(User::class, 'performed_by_user_id'); }
    public function performedByWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'performed_by_worker_id'); }
    public function team(): BelongsTo { return $this->belongsTo(LabourTeam::class); }
    public function product(): BelongsTo { return $this->belongsTo(InventoryProduct::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
