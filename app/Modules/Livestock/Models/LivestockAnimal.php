<?php

namespace App\Modules\Livestock\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockAnimal extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_animals';

    protected $fillable = [
        'organization_id', 'farm_id', 'paddock_id', 'species_id', 'breed_id', 'animal_code', 'tag_number',
        'rfid_number', 'name', 'sex', 'date_of_birth', 'source', 'status', 'health_status', 'production_status',
        'dam_id', 'sire_id', 'current_weight', 'weight_unit', 'acquisition_date', 'acquisition_cost',
        'currency', 'notes', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'acquisition_date' => 'date',
            'current_weight' => 'decimal:2',
            'acquisition_cost' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function paddock(): BelongsTo { return $this->belongsTo(Paddock::class); }
    public function species(): BelongsTo { return $this->belongsTo(LivestockSpecies::class, 'species_id'); }
    public function breed(): BelongsTo { return $this->belongsTo(LivestockBreed::class, 'breed_id'); }
    public function dam(): BelongsTo { return $this->belongsTo(self::class, 'dam_id'); }
    public function sire(): BelongsTo { return $this->belongsTo(self::class, 'sire_id'); }
    public function events(): HasMany { return $this->hasMany(LivestockEvent::class, 'animal_id'); }
    public function treatments(): HasMany { return $this->hasMany(LivestockTreatmentRecord::class, 'animal_id'); }
    public function withdrawalPeriods(): HasMany { return $this->hasMany(LivestockWithdrawalPeriod::class, 'animal_id'); }
    public function breedingRecords(): HasMany { return $this->hasMany(LivestockBreedingRecord::class, 'animal_id'); }
    public function pregnancyChecks(): HasMany { return $this->hasMany(LivestockPregnancyCheck::class, 'animal_id'); }
    public function birthRecordsAsMother(): HasMany { return $this->hasMany(LivestockBirthRecord::class, 'mother_animal_id'); }
    public function weightRecords(): HasMany { return $this->hasMany(LivestockWeightRecord::class, 'animal_id'); }
    public function feedRecords(): HasMany { return $this->hasMany(LivestockFeedRecord::class, 'animal_id'); }
    public function movementRecords(): HasMany { return $this->hasMany(LivestockMovementRecord::class, 'animal_id'); }
    public function mortalityRecords(): HasMany { return $this->hasMany(LivestockMortalityRecord::class, 'animal_id'); }
    public function yieldRecords(): HasMany { return $this->hasMany(LivestockYieldRecord::class, 'animal_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
