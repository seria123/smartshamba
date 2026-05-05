<?php

namespace App\Modules\Crops\Models;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CropSeason extends Model
{
    protected $table = 'crop_seasons';

    protected $fillable = ['organization_id', 'farm_id', 'name', 'code', 'start_date', 'end_date', 'season_type', 'status'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function cycles(): HasMany { return $this->hasMany(CropCycle::class, 'season_id'); }
}
