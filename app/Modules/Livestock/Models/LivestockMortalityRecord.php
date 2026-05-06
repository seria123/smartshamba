<?php

namespace App\Modules\Livestock\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockMortalityRecord extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_mortality_records';
    protected $fillable = ['organization_id', 'farm_id', 'animal_id', 'animal_group_id', 'mortality_date', 'number_dead', 'cause', 'suspected_reason', 'disposal_method', 'reported_by', 'notes'];
    protected function casts(): array { return ['mortality_date' => 'date']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function animal(): BelongsTo { return $this->belongsTo(LivestockAnimal::class, 'animal_id'); }
    public function animalGroup(): BelongsTo { return $this->belongsTo(LivestockAnimalGroup::class, 'animal_group_id'); }
    public function reportedBy(): BelongsTo { return $this->belongsTo(User::class, 'reported_by'); }
}
