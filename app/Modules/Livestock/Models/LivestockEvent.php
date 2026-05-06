<?php

namespace App\Modules\Livestock\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockEvent extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_events';

    protected $fillable = ['organization_id', 'farm_id', 'animal_id', 'animal_group_id', 'paddock_id', 'related_task_id', 'event_number', 'event_type', 'event_date', 'status', 'performed_by_user_id', 'performed_by_worker_id', 'team_id', 'notes', 'created_by', 'updated_by'];

    protected function casts(): array { return ['event_date' => 'date']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function animal(): BelongsTo { return $this->belongsTo(LivestockAnimal::class, 'animal_id'); }
    public function animalGroup(): BelongsTo { return $this->belongsTo(LivestockAnimalGroup::class, 'animal_group_id'); }
    public function paddock(): BelongsTo { return $this->belongsTo(Paddock::class); }
    public function relatedTask(): BelongsTo { return $this->belongsTo(OpsTask::class, 'related_task_id'); }
    public function performedByUser(): BelongsTo { return $this->belongsTo(User::class, 'performed_by_user_id'); }
    public function performedByWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'performed_by_worker_id'); }
    public function team(): BelongsTo { return $this->belongsTo(LabourTeam::class); }
}
