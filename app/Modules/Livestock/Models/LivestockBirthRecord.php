<?php

namespace App\Modules\Livestock\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockBirthRecord extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_birth_records';
    protected $fillable = ['organization_id', 'farm_id', 'mother_animal_id', 'animal_group_id', 'related_event_id', 'birth_date', 'number_born', 'number_alive', 'number_dead', 'birth_type', 'notes', 'recorded_by'];
    protected function casts(): array { return ['birth_date' => 'date']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function motherAnimal(): BelongsTo { return $this->belongsTo(LivestockAnimal::class, 'mother_animal_id'); }
    public function animalGroup(): BelongsTo { return $this->belongsTo(LivestockAnimalGroup::class, 'animal_group_id'); }
    public function recordedBy(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}
