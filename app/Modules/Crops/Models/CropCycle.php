<?php

namespace App\Modules\Crops\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CropCycle extends Model
{
    protected $table = 'crop_cycles';

    protected $fillable = [
        'organization_id', 'farm_id', 'field_id', 'crop_id', 'variety_id', 'season_id', 'cycle_number', 'name',
        'planned_start_date', 'actual_planting_date', 'expected_harvest_date', 'area_planted', 'area_unit',
        'plant_population', 'spacing', 'seed_source', 'manager_user_id', 'status', 'closed_at', 'closed_by',
        'closure_notes', 'cancelled_at', 'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'planned_start_date' => 'date',
            'actual_planting_date' => 'date',
            'expected_harvest_date' => 'date',
            'area_planted' => 'decimal:2',
            'closed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function crop(): BelongsTo { return $this->belongsTo(Crop::class, 'crop_id'); }
    public function variety(): BelongsTo { return $this->belongsTo(CropVariety::class, 'variety_id'); }
    public function season(): BelongsTo { return $this->belongsTo(CropSeason::class, 'season_id'); }
    public function manager(): BelongsTo { return $this->belongsTo(User::class, 'manager_user_id'); }
    public function activities(): HasMany { return $this->hasMany(CropActivity::class, 'crop_cycle_id'); }
    public function scoutingObservations(): HasMany { return $this->hasMany(CropScoutingObservation::class, 'crop_cycle_id'); }
    public function treatments(): HasMany { return $this->hasMany(CropTreatmentApplication::class, 'crop_cycle_id'); }
    public function harvests(): HasMany { return $this->hasMany(CropHarvestRecord::class, 'crop_cycle_id'); }
    public function losses(): HasMany { return $this->hasMany(CropLossRecord::class, 'crop_cycle_id'); }
}
