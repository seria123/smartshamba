<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $table = 'staff_attendances';

    protected $fillable = [
        'staff_id',
        'date',
        'clock_in',
        'clock_out',
        'hours_worked',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime:H:i',
        'clock_out' => 'datetime:H:i',
        'hours_worked' => 'decimal:2',
    ];

    const STATUS_PRESENT = 'present';

    const STATUS_ABSENT = 'absent';

    const STATUS_LATE = 'late';

    const STATUS_HALF_DAY = 'half_day';

    const STATUS_ON_LEAVE = 'on_leave';

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function calculateHoursWorked(): void
    {
        if ($this->clock_in && $this->clock_out) {
            $in = strtotime($this->clock_in);
            $out = strtotime($this->clock_out);
            $diff = $out - $in;
            $this->hours_worked = max(0, $diff / 3600);
        }
    }

    public function isPresent(): bool
    {
        return in_array($this->status, [self::STATUS_PRESENT, self::STATUS_LATE]);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'success',
            self::STATUS_LATE => 'warning',
            self::STATUS_ABSENT => 'danger',
            self::STATUS_HALF_DAY => 'info',
            self::STATUS_ON_LEAVE => 'secondary',
            default => 'secondary',
        };
    }
}
