<?php

namespace App\Modules\Livestock\Models;

use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockSpecies extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_species';

    protected $fillable = ['organization_id', 'name', 'code', 'species_type', 'description', 'status', 'created_by', 'updated_by'];

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function breeds(): HasMany { return $this->hasMany(LivestockBreed::class, 'species_id'); }
    public function animals(): HasMany { return $this->hasMany(LivestockAnimal::class, 'species_id'); }
    public function animalGroups(): HasMany { return $this->hasMany(LivestockAnimalGroup::class, 'species_id'); }
}
