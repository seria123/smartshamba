<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerWage extends Model
{
    use HasFactory;

    protected $table = 'worker_wages';

    protected $fillable = [
        'worker_id',
        'farm_id',
        'period_start',
        'period_end',
        'days_worked',
        'hours_worked',
        'daily_rate',
        'gross_wage',
        'deductions',
        'net_wage',
        'status',
        'payment_date',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'payment_date' => 'date',
        'days_worked' => 'integer',
        'hours_worked' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'gross_wage' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_wage' => 'decimal:2',
    ];

    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_PAID = 'paid';

    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_METHOD_CASH = 'cash';

    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';

    const PAYMENT_METHOD_MOBILE_MONEY = 'mobile_money';

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function calculateNetWage(): void
    {
        $this->net_wage = $this->gross_wage - $this->deductions;
    }

    public function markAsPaid(string $method): void
    {
        $this->status = self::STATUS_PAID;
        $this->payment_date = now();
        $this->payment_method = $method;
        $this->save();
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_PAID => 'success',
            self::STATUS_CANCELLED => 'secondary',
            default => 'secondary',
        };
    }

    public function getPeriodAttribute(): string
    {
        return $this->period_start->format('M d').' - '.$this->period_end->format('M d, Y');
    }
}
