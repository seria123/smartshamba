<?php

namespace App\Modules\Irrigation\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IrrigationZone extends Model
{
    use SoftDeletes;

    protected $table = 'irrigation_zones';
    protected $fillable = ['organization_id', 'farm_id', 'site_id', 'field_id', 'water_source_id', 'name', 'code', 'zone_type', 'irrigation_method', 'area', 'area_unit', 'status', 'notes', 'created_by', 'updated_by'];
    protected function casts(): array { return ['area' => 'decimal:2']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function site(): BelongsTo { return $this->belongsTo(Site::class); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function waterSource(): BelongsTo { return $this->belongsTo(IrrigationWaterSource::class, 'water_source_id'); }
    public function schedules(): HasMany { return $this->hasMany(IrrigationSchedule::class, 'irrigation_zone_id'); }
    public function events(): HasMany { return $this->hasMany(IrrigationEvent::class, 'irrigation_zone_id'); }
    public function readings(): HasMany { return $this->hasMany(IrrigationWaterReading::class, 'irrigation_zone_id'); }
    public function issues(): HasMany { return $this->hasMany(IrrigationIssue::class, 'irrigation_zone_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
