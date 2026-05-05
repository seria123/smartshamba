<?php

namespace App\Modules\Crops\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Tasks\Models\OpsTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropHarvestRecord extends Model
{
    protected $table = 'crop_harvest_records';
    protected $fillable = ['organization_id', 'farm_id', 'crop_cycle_id', 'field_id', 'crop_activity_id', 'task_id', 'harvest_date', 'quantity', 'unit', 'grade', 'destination', 'harvested_by', 'notes'];
    protected function casts(): array { return ['harvest_date' => 'date', 'quantity' => 'decimal:2']; }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function cropCycle(): BelongsTo { return $this->belongsTo(CropCycle::class, 'crop_cycle_id'); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function activity(): BelongsTo { return $this->belongsTo(CropActivity::class, 'crop_activity_id'); }
    public function task(): BelongsTo { return $this->belongsTo(OpsTask::class, 'task_id'); }
    public function harvestedBy(): BelongsTo { return $this->belongsTo(User::class, 'harvested_by'); }
}
