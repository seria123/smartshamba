<?php

namespace App\Modules\Livestock\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockMovementRecord extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_movement_records';
    protected $fillable = ['organization_id', 'farm_id', 'animal_id', 'animal_group_id', 'from_paddock_id', 'to_paddock_id', 'movement_date', 'movement_reason', 'moved_by_user_id', 'moved_by_worker_id', 'team_id', 'notes', 'created_by'];
    protected function casts(): array { return ['movement_date' => 'date']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function animal(): BelongsTo { return $this->belongsTo(LivestockAnimal::class, 'animal_id'); }
    public function animalGroup(): BelongsTo { return $this->belongsTo(LivestockAnimalGroup::class, 'animal_group_id'); }
    public function fromPaddock(): BelongsTo { return $this->belongsTo(Paddock::class, 'from_paddock_id'); }
    public function toPaddock(): BelongsTo { return $this->belongsTo(Paddock::class, 'to_paddock_id'); }
    public function movedByUser(): BelongsTo { return $this->belongsTo(User::class, 'moved_by_user_id'); }
    public function movedByWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'moved_by_worker_id'); }
    public function team(): BelongsTo { return $this->belongsTo(LabourTeam::class); }
}
