<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'buyer_type',
        'credit_limit',
        'rating',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'rating' => 'decimal:1',
        'is_active' => 'boolean',
    ];

    const TYPE_INDIVIDUAL = 'individual';

    const TYPE_RETAILER = 'retailer';

    const TYPE_WHOLESALER = 'wholesaler';

    const TYPE_PROCESSOR = 'processor';

    const TYPE_EXPORTER = 'exporter';

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class);
    }

    public function fullName(): string
    {
        return $this->company_name
            ? "{$this->name} ({$this->company_name})"
            : $this->name;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getTotalOrders(): float
    {
        return (float) $this->orders()->sum('total_amount');
    }

    public function getPendingPayments(): float
    {
        return (float) $this->orders()
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_CONFIRMED])
            ->sum('total_amount');
    }
}
