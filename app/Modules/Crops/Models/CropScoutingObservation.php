<?php

namespace App\Modules\Crops\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropScoutingObservation extends Model
{
    protected $table = 'crop_scouting_observations';
    protected $fillable = ['organization_id', 'farm_id', 'crop_cycle_id', 'field_id', 'crop_activity_id', 'observation_date', 'observation_type', 'severity', 'affected_area', 'affected_area_unit', 'pest_or_disease', 'symptoms', 'recommendation', 'observed_by', 'notes'];
    protected function casts(): array { return ['observation_date' => 'date', 'affected_area' => 'decimal:2']; }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function cropCycle(): BelongsTo { return $this->belongsTo(CropCycle::class, 'crop_cycle_id'); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function activity(): BelongsTo { return $this->belongsTo(CropActivity::class, 'crop_activity_id'); }
    public function observedBy(): BelongsTo { return $this->belongsTo(User::class, 'observed_by'); }
}
