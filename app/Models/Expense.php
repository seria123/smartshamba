<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'crop_id',
        'expense_type',
        'description',
        'amount',
        'expense_date',
        'category',
        'payment_method',
        'receipt_number',
        'staff_id',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    const TYPE_INPUTS = 'inputs';

    const TYPE_LABOR = 'labor';

    const TYPE_EQUIPMENT = 'equipment';

    const TYPE_FERTILIZER = 'fertilizer';

    const TYPE_SEEDS = 'seeds';

    const TYPE_PESTICIDES = 'pesticides';

    const TYPE_FUEL = 'fuel';

    const TYPE_MAINTENANCE = 'maintenance';

    const TYPE_TRANSPORT = 'transport';

    const TYPE_OTHER = 'other';

    const CATEGORY_CROP_PRODUCTION = 'crop_production';

    const CATEGORY_LIVESTOCK = 'livestock';

    const CATEGORY_OPERATIONS = 'operations';

    const CATEGORY_ADMINISTRATIVE = 'administrative';

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->expense_type) {
            self::TYPE_INPUTS => 'Inputs',
            self::TYPE_LABOR => 'Labor',
            self::TYPE_EQUIPMENT => 'Equipment',
            self::TYPE_FERTILIZER => 'Fertilizer',
            self::TYPE_SEEDS => 'Seeds',
            self::TYPE_PESTICIDES => 'Pesticides',
            self::TYPE_FUEL => 'Fuel',
            self::TYPE_MAINTENANCE => 'Maintenance',
            self::TYPE_TRANSPORT => 'Transport',
            self::TYPE_OTHER => 'Other',
            default => 'Unknown',
        };
    }
}
