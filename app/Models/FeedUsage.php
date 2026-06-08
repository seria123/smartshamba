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
        'group_name',
        'quantity',
        'unit',
        'feeding_frequency',
        'feeding_time',
        'reminder_at',
        'unit_conversion_factor',
        'unit_cost',
        'total_cost',
        'output_type',
        'output_quantity',
        'output_unit',
        'weight_gain',
        'feed_conversion_ratio',
        'deduct_inventory',
        'inventory_deducted',
        'stock_before',
        'stock_after',
        'trend_change_percent',
        'anomaly_status',
        'ai_recommendation',
        'season',
        'production_stage',
        'target_protein',
        'target_energy',
        'minerals',
        'staff_id',
        'quality_image_path',
        'quality_notes',
        'spoilage_status',
        'batch_number',
        'supplier_id',
        'usage_date',
        'usage_type',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'usage_date' => 'date',
        'reminder_at' => 'datetime',
        'unit_conversion_factor' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'output_quantity' => 'decimal:2',
        'weight_gain' => 'decimal:2',
        'feed_conversion_ratio' => 'decimal:4',
        'deduct_inventory' => 'boolean',
        'inventory_deducted' => 'boolean',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
        'trend_change_percent' => 'decimal:2',
        'target_protein' => 'decimal:2',
        'target_energy' => 'decimal:2',
        'minerals' => 'array',
    ];

    public function feedType(): BelongsTo
    {
        return $this->belongsTo(FeedType::class);
    }

    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function totalCost(): float
    {
        return (float) ($this->total_cost ?: ((float) $this->quantity * (float) $this->unit_cost));
    }

    public function normalizedQuantity(): float
    {
        return (float) $this->quantity * (float) ($this->unit_conversion_factor ?: 1);
    }

    public function outputEfficiency(): float
    {
        $feed = $this->normalizedQuantity();

        return $feed > 0 ? (float) $this->output_quantity / $feed : 0;
    }

    public function calculateFcr(): ?float
    {
        $output = (float) ($this->weight_gain ?: $this->output_quantity);

        return $output > 0 ? $this->normalizedQuantity() / $output : null;
    }

    public function nutritionalBalanceSummary(): string
    {
        $feed = $this->feedType;

        if (! $feed) {
            return 'No feed nutrition profile available.';
        }

        $gaps = [];

        if ($this->target_protein && $feed->protein && $feed->protein < $this->target_protein) {
            $gaps[] = 'protein';
        }

        if ($this->target_energy && $feed->energy_calories && $feed->energy_calories < $this->target_energy) {
            $gaps[] = 'energy';
        }

        return $gaps ? 'Possible deficiency: '.implode(', ', $gaps).'.' : 'Nutrition target looks balanced for recorded targets.';
    }

    public function assignmentLabel(): string
    {
        if ($this->livestock) {
            return $this->livestock->tag_number.' '.($this->livestock->name ? '- '.$this->livestock->name : '');
        }

        return $this->group_name ?: 'Group feeding';
    }
}
