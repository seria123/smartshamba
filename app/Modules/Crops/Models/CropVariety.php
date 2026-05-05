<?php

namespace App\Modules\Crops\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropVariety extends Model
{
    protected $table = 'crop_varieties';

    protected $fillable = ['crop_id', 'name', 'code', 'expected_growing_days', 'seed_rate', 'seed_rate_unit', 'status'];

    protected function casts(): array
    {
        return ['seed_rate' => 'decimal:2'];
    }

    public function crop(): BelongsTo { return $this->belongsTo(Crop::class, 'crop_id'); }
}
