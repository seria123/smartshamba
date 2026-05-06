<?php

namespace App\Modules\Livestock\Models;

use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockBreed extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_breeds';

    protected $fillable = ['organization_id', 'species_id', 'name', 'code', 'description', 'status', 'created_by', 'updated_by'];

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function species(): BelongsTo { return $this->belongsTo(LivestockSpecies::class, 'species_id'); }
    public function animals(): HasMany { return $this->hasMany(LivestockAnimal::class, 'breed_id'); }
    public function animalGroups(): HasMany { return $this->hasMany(LivestockAnimalGroup::class, 'breed_id'); }
}
