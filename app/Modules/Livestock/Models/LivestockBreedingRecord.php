<?php

namespace App\Modules\Livestock\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockBreedingRecord extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_breeding_records';
    protected $fillable = ['organization_id', 'farm_id', 'animal_id', 'animal_group_id', 'related_event_id', 'breeding_date', 'breeding_method', 'male_animal_id', 'sire_name_snapshot', 'expected_due_date', 'status', 'performed_by_user_id', 'performed_by_worker_id', 'notes', 'created_by'];
    protected function casts(): array { return ['breeding_date' => 'date', 'expected_due_date' => 'date']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function animal(): BelongsTo { return $this->belongsTo(LivestockAnimal::class, 'animal_id'); }
    public function animalGroup(): BelongsTo { return $this->belongsTo(LivestockAnimalGroup::class, 'animal_group_id'); }
    public function maleAnimal(): BelongsTo { return $this->belongsTo(LivestockAnimal::class, 'male_animal_id'); }
    public function performedByUser(): BelongsTo { return $this->belongsTo(User::class, 'performed_by_user_id'); }
    public function performedByWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'performed_by_worker_id'); }
}
