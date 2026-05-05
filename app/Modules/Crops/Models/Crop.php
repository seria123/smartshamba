<?php

namespace App\Modules\Crops\Models;

use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crop extends Model
{
    protected $table = 'crop_crops';

    protected $fillable = ['organization_id', 'name', 'code', 'crop_type', 'scientific_name', 'default_growing_days', 'status'];

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function varieties(): HasMany { return $this->hasMany(CropVariety::class, 'crop_id'); }
    public function cycles(): HasMany { return $this->hasMany(CropCycle::class, 'crop_id'); }
}
