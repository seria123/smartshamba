<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Harvest extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_id',
        'field_id',
        'farm_id',
        'harvest_date',
        'harvest_batch',
        'quantity_harvested',
        'unit',
        'quality_grade',
        'quality_percentage',
        'loss_quantity',
        'loss_reason',
        'loss_percentage',
        'storage_location',
        'storage_details',
        'moisture_content',
        'notes',
    ];

    protected $casts = [
        'harvest_date' => 'date',
        'quantity_harvested' => 'decimal:2',
        'quality_percentage' => 'decimal:2',
        'loss_quantity' => 'decimal:2',
        'loss_percentage' => 'decimal:2',
        'moisture_content' => 'decimal:2',
    ];

    const GRADE_A = 'grade_a';

    const GRADE_B = 'grade_b';

    const GRADE_C = 'grade_c';

    const GRADE_REJECT = 'reject';

    const STORAGE_FIELD = 'field';

    const STORAGE_BARN = 'barn';

    const STORAGE_WAREHOUSE = 'warehouse';

    const STORAGE_COLD_STORAGE = 'cold_storage';

    const STORAGE_SOLD_IMMEDIATELY = 'sold_immediately';

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function calculateLossPercentage(): void
    {
        if ($this->quantity_harvested > 0) {
            $this->loss_percentage = ($this->loss_quantity / $this->quantity_harvested) * 100;
        }
    }

    public function getNetQuantity(): float
    {
        return (float) $this->quantity_harvested - (float) $this->loss_quantity;
    }

    public function getQualityColorAttribute(): string
    {
        return match ($this->quality_grade) {
            self::GRADE_A => 'success',
            self::GRADE_B => 'info',
            self::GRADE_C => 'warning',
            self::GRADE_REJECT => 'danger',
            default => 'secondary',
        };
    }

    public function getQualityLabelAttribute(): string
    {
        return match ($this->quality_grade) {
            self::GRADE_A => 'Grade A (Premium)',
            self::GRADE_B => 'Grade B (Standard)',
            self::GRADE_C => 'Grade C (Economy)',
            self::GRADE_REJECT => 'Reject',
            default => 'Unknown',
        };
    }

    public function getStorageLocationLabelAttribute(): string
    {
        return match ($this->storage_location) {
            self::STORAGE_FIELD => 'In Field',
            self::STORAGE_BARN => 'Barn',
            self::STORAGE_WAREHOUSE => 'Warehouse',
            self::STORAGE_COLD_STORAGE => 'Cold Storage',
            self::STORAGE_SOLD_IMMEDIATELY => 'Sold Immediately',
            default => 'Unknown',
        };
    }

    public function scopeByGrade($query, string $grade)
    {
        return $query->where('quality_grade', $grade);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('harvest_date', [$startDate, $endDate]);
    }

    public function scopeByCrop($query, $cropId)
    {
        return $query->where('crop_id', $cropId);
    }

    public function scopeByField($query, $fieldId)
    {
        return $query->where('field_id', $fieldId);
    }
}
