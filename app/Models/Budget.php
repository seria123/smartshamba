<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'crop_id',
        'crop_cycle_id',
        'livestock_id',
        'loan_id',
        'owner_user_id',
        'owner_group_name',
        'responsible_role',
        'name',
        'season_name',
        'budget_type',
        'category',
        'line_items',
        'planned_amount',
        'actual_amount_override',
        'start_date',
        'end_date',
        'alert_threshold_percent',
        'allow_overspend',
        'alert_thresholds',
        'forecast_amount',
        'expected_income',
        'generated_profit',
        'market_price_assumption',
        'what_if_scenarios',
        'ai_suggestions',
        'cost_efficiency_notes',
        'approval_status',
        'approved_by',
        'approved_at',
        'adjustment_log',
        'document_paths',
        'inventory_link_notes',
        'notes',
    ];

    protected $casts = [
        'planned_amount' => 'decimal:2',
        'actual_amount_override' => 'decimal:2',
        'alert_threshold_percent' => 'decimal:2',
        'allow_overspend' => 'boolean',
        'alert_thresholds' => 'array',
        'line_items' => 'array',
        'forecast_amount' => 'decimal:2',
        'expected_income' => 'decimal:2',
        'generated_profit' => 'decimal:2',
        'market_price_assumption' => 'decimal:2',
        'what_if_scenarios' => 'array',
        'approved_at' => 'datetime',
        'adjustment_log' => 'array',
        'document_paths' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function actualSpend(): float
    {
        $query = Expense::where('farm_id', $this->farm_id)
            ->whereBetween('expense_date', [$this->start_date, $this->end_date]);

        if ($this->crop_id) {
            $query->where('crop_id', $this->crop_id);
        }

        if ($this->crop_cycle_id && Schema::hasColumn('expenses', 'crop_cycle_id')) {
            $query->where('crop_cycle_id', $this->crop_cycle_id);
        }

        if ($this->livestock_id) {
            $query->where('livestock_id', $this->livestock_id);
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        return (float) $query->sum('amount');
    }

    public function actualAmount(): float
    {
        return $this->actual_amount_override !== null ? (float) $this->actual_amount_override : $this->actualSpend();
    }

    public function variance(): float
    {
        return $this->actualAmount() - (float) $this->planned_amount;
    }

    public function isExceeded(): bool
    {
        return $this->actualAmount() > ((float) $this->planned_amount * ((float) $this->alert_threshold_percent / 100));
    }

    public function spentPercent(): float
    {
        return (float) $this->planned_amount > 0 ? ($this->actualAmount() / (float) $this->planned_amount) * 100 : 0;
    }

    public function weeklyBurnRate(): float
    {
        $daysElapsed = max(1, $this->start_date->diffInDays(now(), false) + 1);

        return ($this->actualAmount() / $daysElapsed) * 7;
    }

    public function projectedTotal(): float
    {
        if ($this->forecast_amount !== null) {
            return (float) $this->forecast_amount;
        }

        $totalDays = max(1, $this->start_date->diffInDays($this->end_date) + 1);
        $daysElapsed = max(1, min($totalDays, $this->start_date->diffInDays(now(), false) + 1));

        return ($this->actualAmount() / $daysElapsed) * $totalDays;
    }

    public function healthStatus(): string
    {
        if ($this->spentPercent() >= 100 || (! $this->allow_overspend && $this->isExceeded())) {
            return 'overrun';
        }

        if ($this->spentPercent() >= 80 || $this->projectedTotal() > (float) $this->planned_amount) {
            return 'at_risk';
        }

        return 'healthy';
    }

    public function efficiencyScore(): float
    {
        $planned = (float) $this->planned_amount;

        if ($planned <= 0) {
            return 0;
        }

        return max(0, min(100, 100 - max(0, $this->spentPercent() - 100)));
    }

    public function roi(): float
    {
        $spent = max(1, $this->actualAmount());

        return ((float) $this->generated_profit / $spent) * 100;
    }

    public function alertMessages(): array
    {
        $percent = $this->spentPercent();
        $thresholds = $this->alert_thresholds ?: [50, 80, 100];

        return collect($thresholds)
            ->filter(fn ($threshold) => $percent >= (float) $threshold)
            ->map(fn ($threshold) => 'Budget has reached '.number_format((float) $threshold, 0).'% usage.')
            ->values()
            ->all();
    }

    public function categoryBreakdown(): array
    {
        return collect($this->line_items ?: [])
            ->map(function ($item) {
                $planned = (float) ($item['planned'] ?? 0);
                $category = $item['category'] ?? null;

                return [
                    'category' => $category,
                    'planned' => $planned,
                    'actual' => $category ? $this->actualForCategory($category) : 0,
                    'owner' => $item['owner'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ];
            })
            ->values()
            ->all();
    }

    private function actualForCategory(string $category): float
    {
        return (float) Expense::where('farm_id', $this->farm_id)
            ->whereBetween('expense_date', [$this->start_date, $this->end_date])
            ->where('category', $category)
            ->sum('amount');
    }
}
