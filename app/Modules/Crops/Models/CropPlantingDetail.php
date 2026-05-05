<?php

namespace App\Modules\Crops\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropPlantingDetail extends Model
{
    protected $table = 'crop_planting_details';
    protected $fillable = ['crop_activity_id', 'planting_method', 'seed_quantity', 'seed_unit', 'plant_population', 'spacing', 'nursery_source', 'notes'];
    protected function casts(): array { return ['seed_quantity' => 'decimal:2']; }
    public function activity(): BelongsTo { return $this->belongsTo(CropActivity::class, 'crop_activity_id'); }
}
