<?php

namespace App\Modules\Livestock\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockTreatmentRecord extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_treatment_records';

    protected $fillable = ['organization_id', 'farm_id', 'animal_id', 'animal_group_id', 'related_event_id', 'related_task_id', 'treatment_type', 'treatment_date', 'condition_or_reason', 'diagnosis', 'product_id', 'product_name_snapshot', 'dosage', 'dosage_unit', 'route', 'frequency', 'duration_days', 'withdrawal_meat_days', 'withdrawal_milk_days', 'withdrawal_egg_days', 'treated_by_user_id', 'treated_by_worker_id', 'team_id', 'follow_up_date', 'notes', 'created_by'];

    protected function casts(): array { return ['treatment_date' => 'date', 'follow_up_date' => 'date', 'dosage' => 'decimal:2']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function animal(): BelongsTo { return $this->belongsTo(LivestockAnimal::class, 'animal_id'); }
    public function animalGroup(): BelongsTo { return $this->belongsTo(LivestockAnimalGroup::class, 'animal_group_id'); }
    public function relatedEvent(): BelongsTo { return $this->belongsTo(LivestockEvent::class, 'related_event_id'); }
    public function relatedTask(): BelongsTo { return $this->belongsTo(OpsTask::class, 'related_task_id'); }
    public function product(): BelongsTo { return $this->belongsTo(InventoryProduct::class, 'product_id'); }
    public function treatedByUser(): BelongsTo { return $this->belongsTo(User::class, 'treated_by_user_id'); }
    public function treatedByWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'treated_by_worker_id'); }
    public function team(): BelongsTo { return $this->belongsTo(LabourTeam::class); }
    public function withdrawalPeriods(): HasMany { return $this->hasMany(LivestockWithdrawalPeriod::class, 'treatment_record_id'); }
}
