<?php

namespace App\Modules\Livestock\Models;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockWithdrawalPeriod extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_withdrawal_periods';
    protected $fillable = ['organization_id', 'farm_id', 'animal_id', 'animal_group_id', 'treatment_record_id', 'withdrawal_type', 'starts_on', 'ends_on', 'status', 'notes', 'created_by'];
    protected function casts(): array { return ['starts_on' => 'date', 'ends_on' => 'date']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function animal(): BelongsTo { return $this->belongsTo(LivestockAnimal::class, 'animal_id'); }
    public function animalGroup(): BelongsTo { return $this->belongsTo(LivestockAnimalGroup::class, 'animal_group_id'); }
    public function treatmentRecord(): BelongsTo { return $this->belongsTo(LivestockTreatmentRecord::class, 'treatment_record_id'); }
}
