<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'farm_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'profile_photo',
        'national_id',
        'employee_id',
        'date_of_birth',
        'gender',
        'role',
        'employment_type',
        'daily_wage',
        'payment_type',
        'address',
        'emergency_contact',
        'emergency_phone',
        'hire_date',
        'termination_date',
        'status',
        'notes',
        'available_equipment',
        'current_field_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'daily_wage' => 'decimal:2',
        'available_equipment' => 'array',
    ];

    const ROLE_GENERAL_WORKER = 'general_worker';

    const ROLE_SUPERVISOR = 'supervisor';

    const ROLE_TECHNICIAN = 'technician';

    const ROLE_DRIVER = 'driver';

    const ROLE_HARVESTER = 'harvester';

    const ROLE_PLANTING = 'planting';

    const ROLE_IRRIGATION = 'irrigation';

    const ROLE_MANAGER = 'manager';

    const ROLE_WORKER = 'worker';

    const ROLE_AGRONOMIST = 'agronomist';

    const EMPLOYMENT_PERMANENT = 'permanent';

    const EMPLOYMENT_CASUAL = 'casual';

    const EMPLOYMENT_SEASONAL = 'seasonal';

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
        return $this->hasMany(StaffAttendance::class);
    }

    public function wages(): HasMany
    {
        return $this->hasMany(StaffWage::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_staff')
            ->withTimestamps();
    }

    public function currentField(): BelongsTo
    {
        return $this->belongsTo(Field::class, 'current_field_id');
    }

    public function fieldAssignments(): HasMany
    {
        return $this->hasMany(StaffFieldAssignment::class);
    }

    public function activeFieldAssignments(): HasMany
    {
        return $this->hasMany(StaffFieldAssignment::class)->active();
    }

    public function skills(): HasMany
    {
        return $this->hasMany(StaffSkill::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(StaffSchedule::class);
    }

    public function performanceReviews(): HasMany
    {
        return $this->hasMany(StaffPerformanceReview::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(StaffNotification::class);
    }

    public function proofsOfWork(): HasMany
    {
        return $this->hasMany(StaffProofOfWork::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(StaffLocation::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(StaffActivityLog::class);
    }

    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the staff's full name as an accessor.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getTodayAttendance(): ?StaffAttendance
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
            ->where('status', StaffAttendance::STATUS_PRESENT)
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

    public function getPerformanceScoreAttribute(): ?float
    {
        $latestReview = $this->performanceReviews()->latest()->first();
        return $latestReview?->overall_score;
    }

    public function getAttendanceRateAttribute(): ?float
    {
        $latestReview = $this->performanceReviews()->latest()->first();
        return $latestReview?->attendance_rate;
    }

    public function getTasksCompletedCount(int $year, int $month): int
    {
        return $this->tasks()
            ->whereYear('completed_date', $year)
            ->whereMonth('completed_date', $month)
            ->where('status', Task::STATUS_COMPLETED)
            ->count();
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->unread()->count();
    }

    public function getTasksCountAttribute(): int
    {
        return $this->tasks()->where('status', Task::STATUS_PENDING)->count();
    }

    public function getActiveLocationAttribute(): ?StaffLocation
    {
        return $this->locations()->whereNotNull('checked_in_at')->whereNull('checked_out_at')->latest('checked_in_at')->first();
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_INACTIVE => 'warning',
            self::STATUS_TERMINATED => 'secondary',
            default => 'secondary',
        };
    }

    public function getRoleBadgeColorAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_MANAGER => 'danger',
            self::ROLE_SUPERVISOR => 'primary',
            self::ROLE_TECHNICIAN => 'info',
            self::ROLE_HARVESTER => 'success',
            self::ROLE_PLANTING => 'success',
            self::ROLE_IRRIGATION => 'info',
            default => 'secondary',
        };
    }
}
