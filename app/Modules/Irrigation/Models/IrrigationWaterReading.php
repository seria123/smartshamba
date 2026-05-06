<?php

namespace App\Modules\Irrigation\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IrrigationWaterReading extends Model
{
    use SoftDeletes;

    protected $table = 'irrigation_water_readings';
    protected $fillable = ['organization_id', 'farm_id', 'water_source_id', 'irrigation_zone_id', 'reading_date', 'reading_type', 'value', 'unit_of_measure', 'recorded_by_user_id', 'recorded_by_worker_id', 'notes', 'created_by'];
    protected function casts(): array { return ['reading_date' => 'date', 'value' => 'decimal:2']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function waterSource(): BelongsTo { return $this->belongsTo(IrrigationWaterSource::class, 'water_source_id'); }
    public function zone(): BelongsTo { return $this->belongsTo(IrrigationZone::class, 'irrigation_zone_id'); }
    public function recordedByUser(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by_user_id'); }
    public function recordedByWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'recorded_by_worker_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
