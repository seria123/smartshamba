<?php

namespace App\Modules\Livestock\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockPregnancyCheck extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_pregnancy_checks';
    protected $fillable = ['organization_id', 'farm_id', 'animal_id', 'animal_group_id', 'related_breeding_record_id', 'check_date', 'result', 'expected_due_date', 'checked_by_user_id', 'checked_by_worker_id', 'notes', 'created_by'];
    protected function casts(): array { return ['check_date' => 'date', 'expected_due_date' => 'date']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function animal(): BelongsTo { return $this->belongsTo(LivestockAnimal::class, 'animal_id'); }
    public function animalGroup(): BelongsTo { return $this->belongsTo(LivestockAnimalGroup::class, 'animal_group_id'); }
    public function relatedBreedingRecord(): BelongsTo { return $this->belongsTo(LivestockBreedingRecord::class, 'related_breeding_record_id'); }
    public function checkedByUser(): BelongsTo { return $this->belongsTo(User::class, 'checked_by_user_id'); }
    public function checkedByWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'checked_by_worker_id'); }
}
