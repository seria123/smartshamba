<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'crop_id',
        'crop_cycle_id',
        'field_id',
        'farm_id',
        'planting_date',
        'planting_window_start',
        'planting_window_end',
        'expected_harvest_date',
        'actual_harvest_date',
        'estimated_quantity',
        'quantity_unit',
        'actual_quantity',
        'actual_quantity_unit',
        'status',
        'season',
        'variety',
        'notes',
        'completion_percentage',
        'current_stage',
    ];

    protected $casts = [
        'planting_date' => 'date',
        'planting_window_start' => 'date',
        'planting_window_end' => 'date',
        'expected_harvest_date' => 'date',
        'actual_harvest_date' => 'date',
        'estimated_quantity' => 'decimal:2',
        'actual_quantity' => 'decimal:2',
        'completion_percentage' => 'integer',
    ];

    /**
     * Get the user (farmer) who owns the planting schedule.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the crop for this planting schedule.
     */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    /**
     * Get the crop cycle linked to this schedule (if any).
     */
    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    /**
     * Get the field for this planting schedule.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the farm for this planting schedule.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Scope for upcoming plantings.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'planned')
            ->where('planting_date', '>=', now())
            ->orderBy('planting_date', 'asc');
    }

    /**
     * Scope for active plantings (planted or growing).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planted', 'growing', 'ready_for_harvest']);
    }

    /**
     * Scope for this season.
     */
    public function scopeBySeason($query, $season)
    {
        return $query->where('season', $season);
    }

    /**
     * Scope for planted within date range.
     */
    public function scopePlantedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('planting_date', [$startDate, $endDate]);
    }

    /**
     * Get the days until planting.
     */
    public function getDaysUntilPlantingAttribute(): int
    {
        if ($this->status === 'harvested' || $this->status === 'cancelled') {
            return -1;
        }

        return now()->startOfDay()->diffInDays($this->planting_date, false);
    }

    /**
     * Get the days until harvest.
     */
    public function getDaysUntilHarvestAttribute(): ?int
    {
        if (! $this->expected_harvest_date) {
            return null;
        }
        if ($this->status === 'harvested') {
            return -1;
        }

        return now()->startOfDay()->diffInDays($this->expected_harvest_date, false);
    }

    /**
     * Check if planting is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'planned' && $this->planting_date < now()->startOfDay();
    }

    /**
     * Check if planting window is current.
     */
    public function getIsInPlantingWindowAttribute(): bool
    {
        if (! $this->planting_window_start || ! $this->planting_window_end) {
            return false;
        }
        $today = now()->startOfDay();

        return $today->between($this->planting_window_start, $this->planting_window_end);
    }

    /**
     * Get status color for UI indicators.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'planned' => 'gray',
            'planted' => 'blue',
            'growing' => 'green',
            'ready_for_harvest' => 'yellow',
            'harvested' => 'emerald',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the growth progress as percentage.
     */
    public function getProgressAttribute(): int
    {
        if ($this->status === 'harvested') {
            return 100;
        }
        if ($this->status === 'cancelled') {
            return 0;
        }

        return $this->completion_percentage;
    }

    /**
     * Get formatted planting window for display.
     */
    public function getFormattedPlantingWindowAttribute(): string
    {
        if ($this->planting_window_start && $this->planting_window_end) {
            return $this->planting_window_start->format('M j').' - '.$this->planting_window_end->format('M j, Y');
        }

        return $this->planting_date->format('F j, Y');
    }
}
