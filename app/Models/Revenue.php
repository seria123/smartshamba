<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'crop_id',
        'livestock_id',
        'crop_cycle_id',
        'buyer_id',
        'amount',
        'sale_date',
        'quantity_sold',
        'unit',
        'price_per_unit',
        'payment_status',
        'payment_date',
        'payment_method',
        'invoice_number',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'quantity_sold' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';

    /**
     * Get the farm that owns the revenue.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the crop that owns the revenue.
     */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    /**
     * Get the crop cycle that owns the revenue.
     */
    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    /**
     * Get the buyer.
     */
    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PARTIAL => 'info',
            self::STATUS_PAID => 'success',
            self::STATUS_OVERDUE => 'danger',
            default => 'secondary',
        };
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::STATUS_PAID;
    }

    public function calculateProfit(): float
    {
        $expenses = Expense::where('farm_id', $this->farm_id)
            ->where('expense_date', '<=', $this->sale_date)
            ->sum('amount');

        return (float) $this->amount - $expenses;
    }
}
