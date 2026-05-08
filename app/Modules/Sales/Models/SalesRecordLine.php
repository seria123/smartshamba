<?php

namespace App\Modules\Sales\Models;

use App\Modules\Crops\Models\CropCycle;
use App\Modules\Crops\Models\CropHarvestRecord;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Livestock\Models\LivestockYieldRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesRecordLine extends Model
{
    protected $fillable = ['sales_record_id', 'sales_catalog_item_id', 'description', 'category', 'unit', 'quantity', 'unit_price', 'discount_amount', 'line_total_amount', 'crop_cycle_id', 'crop_harvest_id', 'animal_id', 'animal_group_id', 'livestock_yield_id', 'notes'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:3', 'unit_price' => 'decimal:2', 'discount_amount' => 'decimal:2', 'line_total_amount' => 'decimal:2'];
    }

    public function record(): BelongsTo { return $this->belongsTo(SalesRecord::class, 'sales_record_id'); }
    public function catalogItem(): BelongsTo { return $this->belongsTo(SalesCatalogItem::class, 'sales_catalog_item_id'); }
    public function cropCycle(): BelongsTo { return $this->belongsTo(CropCycle::class, 'crop_cycle_id'); }
    public function cropHarvest(): BelongsTo { return $this->belongsTo(CropHarvestRecord::class, 'crop_harvest_id'); }
    public function animal(): BelongsTo { return $this->belongsTo(LivestockAnimal::class, 'animal_id'); }
    public function animalGroup(): BelongsTo { return $this->belongsTo(LivestockAnimalGroup::class, 'animal_group_id'); }
    public function livestockYield(): BelongsTo { return $this->belongsTo(LivestockYieldRecord::class, 'livestock_yield_id'); }
}
