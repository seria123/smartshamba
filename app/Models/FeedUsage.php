<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'feed_type_id',
        'livestock_id',
        'quantity',
        'unit',
        'usage_date',
        'usage_type',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'usage_date' => 'date',
    ];

    public function feedType(): BelongsTo
    {
        return $this->belongsTo(FeedType::class);
    }

    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class);
    }

    public function totalCost(): float
    {
        // This would need to join with feed_types table to get unit_cost from food_stocks
        // For now, we'll return 0 and implement properly in service/repository
        return 0;
    }
}
