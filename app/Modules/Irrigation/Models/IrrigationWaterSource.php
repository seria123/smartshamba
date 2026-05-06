<?php

namespace App\Modules\Irrigation\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IrrigationWaterSource extends Model
{
    use SoftDeletes;

    protected $table = 'irrigation_water_sources';
    protected $fillable = ['organization_id', 'farm_id', 'name', 'code', 'source_type', 'capacity', 'capacity_unit', 'location_description', 'latitude', 'longitude', 'status', 'notes', 'created_by', 'updated_by'];
    protected function casts(): array { return ['capacity' => 'decimal:2', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function zones(): HasMany { return $this->hasMany(IrrigationZone::class, 'water_source_id'); }
    public function events(): HasMany { return $this->hasMany(IrrigationEvent::class, 'water_source_id'); }
    public function readings(): HasMany { return $this->hasMany(IrrigationWaterReading::class, 'water_source_id'); }
    public function issues(): HasMany { return $this->hasMany(IrrigationIssue::class, 'water_source_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
