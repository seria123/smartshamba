<?php

namespace App\Modules\Crops\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Tasks\Models\OpsTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropTreatmentApplication extends Model
{
    protected $table = 'crop_treatment_applications';
    protected $fillable = ['organization_id', 'farm_id', 'crop_cycle_id', 'field_id', 'crop_activity_id', 'task_id', 'product_id', 'application_type', 'application_date', 'product_name_snapshot', 'product_code_snapshot', 'quantity_used', 'quantity_unit', 'application_rate', 'target_problem', 'method', 'weather_notes', 'phi_days', 'rei_hours', 'applied_by', 'notes'];
    protected function casts(): array { return ['application_date' => 'date', 'quantity_used' => 'decimal:2']; }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function cropCycle(): BelongsTo { return $this->belongsTo(CropCycle::class, 'crop_cycle_id'); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function activity(): BelongsTo { return $this->belongsTo(CropActivity::class, 'crop_activity_id'); }
    public function task(): BelongsTo { return $this->belongsTo(OpsTask::class, 'task_id'); }
    public function product(): BelongsTo { return $this->belongsTo(InventoryProduct::class, 'product_id'); }
    public function appliedBy(): BelongsTo { return $this->belongsTo(User::class, 'applied_by'); }
}
