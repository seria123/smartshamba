<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'sub_category',
        'description',
        'default_unit',
        'unit_conversion_label',
        'unit_conversion_factor',
        'min_threshold',
        'selling_price',
        'minimum_price',
        'market_price',
        'price_history',
        'linked_crop_id',
        'linked_livestock_id',
        'supplier_id',
        'protein',
        'carbohydrates',
        'fats',
        'vitamins',
        'energy_calories',
        'expiry_period_days',
        'storage_conditions',
        'raw_input_name',
        'processed_output_name',
        'processing_cost',
        'yield_ratio',
        'input_cost',
        'labor_cost',
        'batch_number',
        'production_date',
        'source_batch',
        'demand_level',
        'best_selling_periods',
        'buyer_preferences',
        'market_regions',
        'region_pricing',
        'image_path',
        'quality_grade',
        'certifications',
        'user_notes',
        'is_active',
    ];

    protected $casts = [
        'min_threshold' => 'decimal:2',
        'unit_conversion_factor' => 'decimal:4',
        'selling_price' => 'decimal:2',
        'minimum_price' => 'decimal:2',
        'market_price' => 'decimal:2',
        'price_history' => 'array',
        'protein' => 'decimal:2',
        'carbohydrates' => 'decimal:2',
        'fats' => 'decimal:2',
        'vitamins' => 'array',
        'energy_calories' => 'decimal:2',
        'storage_conditions' => 'array',
        'processing_cost' => 'decimal:2',
        'yield_ratio' => 'decimal:4',
        'input_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'production_date' => 'date',
        'best_selling_periods' => 'array',
        'market_regions' => 'array',
        'region_pricing' => 'array',
        'certifications' => 'array',
        'is_active' => 'boolean',
    ];

    public function foodStocks(): HasMany
    {
        return $this->hasMany(FoodStock::class);
    }

    public function linkedCrop(): BelongsTo
    {
        return $this->belongsTo(Crop::class, 'linked_crop_id');
    }

    public function linkedLivestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class, 'linked_livestock_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function totalQuantity(): float
    {
        return $this->foodStocks()
            ->where('is_active', true)
            ->sum('quantity');
    }

    public function isBelowThreshold(): bool
    {
        return $this->totalQuantity() < $this->min_threshold;
    }

    public function stockValue(): float
    {
        return (float) $this->foodStocks()
            ->where('is_active', true)
            ->selectRaw('COALESCE(SUM(quantity * unit_cost), 0) as total')
            ->value('total');
    }

    public function averageUnitCost(): float
    {
        $quantity = $this->totalQuantity();

        return $quantity > 0 ? $this->stockValue() / $quantity : 0;
    }

    public function costPerUnit(): float
    {
        $yieldRatio = (float) ($this->yield_ratio ?: 1);
        $baseCost = (float) $this->input_cost + (float) $this->labor_cost + (float) $this->processing_cost;

        return $yieldRatio > 0 ? $baseCost / $yieldRatio : $baseCost;
    }

    public function profitMargin(): float
    {
        $sellingPrice = (float) ($this->selling_price ?: $this->market_price ?: 0);
        $cost = $this->costPerUnit() ?: $this->averageUnitCost();

        return $sellingPrice > 0 ? (($sellingPrice - $cost) / $sellingPrice) * 100 : 0;
    }

    public function priceBelowCost(): bool
    {
        $price = (float) ($this->market_price ?: $this->selling_price ?: 0);
        $cost = $this->costPerUnit() ?: $this->averageUnitCost();

        return $price > 0 && $cost > 0 && $price < $cost;
    }

    public function expiresAt(): ?\Carbon\Carbon
    {
        if (! $this->production_date || ! $this->expiry_period_days) {
            return null;
        }

        return $this->production_date->copy()->addDays((int) $this->expiry_period_days);
    }

    public function expiryAlert(): ?string
    {
        $expiryDate = $this->expiresAt();

        if (! $expiryDate) {
            return null;
        }

        if ($expiryDate->isPast()) {
            return 'Expired';
        }

        return $expiryDate->diffInDays(now()) <= 7 ? 'Expiring soon' : null;
    }

    public function recommendation(): string
    {
        if ($this->priceBelowCost()) {
            return 'Market price is below production cost. Review processing, inputs, or pricing before selling.';
        }

        if ($this->profitMargin() >= 25 && $this->demand_level === 'high') {
            return 'High demand and strong margin. Prioritize this food type for production and sales.';
        }

        if ($this->raw_input_name && $this->processed_output_name && (float) $this->processing_cost > 0) {
            return "Track {$this->raw_input_name} to {$this->processed_output_name} batches to confirm value-add profit.";
        }

        return 'Add price, cost, demand, and source data to unlock sharper recommendations.';
    }
}
