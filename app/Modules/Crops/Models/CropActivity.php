<?php

namespace App\Modules\Crops\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CropActivity extends Model
{
    protected $table = 'crop_activities';

    protected $fillable = ['organization_id', 'farm_id', 'crop_cycle_id', 'field_id', 'task_id', 'performed_by_user_id', 'worker_id', 'team_id', 'activity_type', 'activity_date', 'status', 'notes'];

    protected function casts(): array { return ['activity_date' => 'date']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function cropCycle(): BelongsTo { return $this->belongsTo(CropCycle::class, 'crop_cycle_id'); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function task(): BelongsTo { return $this->belongsTo(OpsTask::class, 'task_id'); }
    public function performedBy(): BelongsTo { return $this->belongsTo(User::class, 'performed_by_user_id'); }
    public function worker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'worker_id'); }
    public function team(): BelongsTo { return $this->belongsTo(LabourTeam::class, 'team_id'); }
    public function plantingDetail(): HasOne { return $this->hasOne(CropPlantingDetail::class, 'crop_activity_id'); }
}
