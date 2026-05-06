<?php

namespace App\Modules\Livestock\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockYieldRecord extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_yield_records';
    protected $fillable = ['organization_id', 'farm_id', 'animal_id', 'animal_group_id', 'yield_date', 'yield_type', 'quantity', 'unit_of_measure', 'grade', 'destination', 'recorded_by', 'notes'];
    protected function casts(): array { return ['yield_date' => 'date', 'quantity' => 'decimal:2']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function animal(): BelongsTo { return $this->belongsTo(LivestockAnimal::class, 'animal_id'); }
    public function animalGroup(): BelongsTo { return $this->belongsTo(LivestockAnimalGroup::class, 'animal_group_id'); }
    public function recordedBy(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}
