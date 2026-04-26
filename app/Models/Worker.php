<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'first_name',
        'last_name',
        'phone',
        'national_id',
        'date_of_birth',
        'gender',
        'role',
        'daily_wage',
        'payment_type',
        'address',
        'emergency_contact',
        'emergency_phone',
        'hire_date',
        'termination_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'daily_wage' => 'decimal:2',
    ];

    const ROLE_GENERAL_WORKER = 'general_worker';

    const ROLE_SUPERVISOR = 'supervisor';

    const ROLE_TECHNICIAN = 'technician';

    const ROLE_DRIVER = 'driver';

    const ROLE_HARVESTER = 'harvester';

    const ROLE_PLANTING = 'planting';

    const ROLE_IRRIGATION = 'irrigation';

    const PAYMENT_DAILY = 'daily';

    const PAYMENT_WEEKLY = 'weekly';

    const PAYMENT_MONTHLY = 'monthly';

    const PAYMENT_PIECE_RATE = 'piece_rate';

    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    const STATUS_TERMINATED = 'terminated';

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(WorkerAttendance::class);
    }

    public function wages(): HasMany
    {
        return $this->hasMany(WorkerWage::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getTodayAttendance(): ?WorkerAttendance
    {
        return $this->attendances()
            ->whereDate('date', now()->toDateString())
            ->first();
    }

    public function getMonthlyAttendance(int $year, int $month): HasMany
    {
        return $this->attendances()
            ->whereYear('date', $year)
            ->whereMonth('date', $month);
    }

    public function getTotalWorkedHours(int $year, int $month): float
    {
        return (float) $this->attendances()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('status', WorkerAttendance::STATUS_PRESENT)
            ->sum('hours_worked');
    }

    public function getTotalWagesDue(int $year, int $month): float
    {
        $hours = $this->getTotalWorkedHours($year, $month);

        if ($this->payment_type === self::PAYMENT_DAILY) {
            return $hours / 8 * $this->daily_wage;
        }

        return $hours * ($this->daily_wage / 8);
    }
}
