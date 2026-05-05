<?php

namespace App\Modules\Crops\Models;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropLossRecord extends Model
{
    protected $table = 'crop_loss_records';
    protected $fillable = ['organization_id', 'farm_id', 'crop_cycle_id', 'field_id', 'crop_activity_id', 'loss_date', 'loss_type', 'estimated_quantity', 'quantity_unit', 'affected_area', 'affected_area_unit', 'cause', 'severity', 'notes'];
    protected function casts(): array { return ['loss_date' => 'date', 'estimated_quantity' => 'decimal:2', 'affected_area' => 'decimal:2']; }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function cropCycle(): BelongsTo { return $this->belongsTo(CropCycle::class, 'crop_cycle_id'); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function activity(): BelongsTo { return $this->belongsTo(CropActivity::class, 'crop_activity_id'); }
}
