<?php

namespace App\Modules\Assets\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Core\Models\Site;
use App\Modules\Core\Models\Warehouse;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = ['organization_id', 'farm_id', 'category_id', 'asset_code', 'name', 'description', 'asset_type', 'serial_number', 'model', 'manufacturer', 'purchase_date', 'purchase_cost', 'currency', 'current_value', 'site_id', 'field_id', 'paddock_id', 'warehouse_id', 'assigned_worker_id', 'assigned_user_id', 'status', 'condition_status', 'acquisition_source', 'warranty_expiry_date', 'last_service_date', 'next_service_date', 'notes', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return ['purchase_date' => 'date', 'purchase_cost' => 'decimal:2', 'current_value' => 'decimal:2', 'warranty_expiry_date' => 'date', 'last_service_date' => 'date', 'next_service_date' => 'date'];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function category(): BelongsTo { return $this->belongsTo(AssetCategory::class, 'category_id'); }
    public function site(): BelongsTo { return $this->belongsTo(Site::class); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function paddock(): BelongsTo { return $this->belongsTo(Paddock::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function assignedWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'assigned_worker_id'); }
    public function assignedUser(): BelongsTo { return $this->belongsTo(User::class, 'assigned_user_id'); }
    public function maintenanceSchedules(): HasMany { return $this->hasMany(AssetMaintenanceSchedule::class); }
    public function maintenanceRecords(): HasMany { return $this->hasMany(AssetMaintenanceRecord::class); }
    public function breakdownRecords(): HasMany { return $this->hasMany(AssetBreakdownRecord::class); }
    public function usageRecords(): HasMany { return $this->hasMany(AssetUsageRecord::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updatedBy(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
}
