<?php

namespace App\Modules\Workers\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabourWorker extends Model
{
    protected $fillable = [
        'organization_id',
        'farm_id',
        'user_id',
        'worker_code',
        'name',
        'phone',
        'email',
        'employment_type',
        'primary_role',
        'status',
        'start_date',
        'rate_type',
        'default_rate',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'default_rate' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(LabourTeam::class, 'labour_team_worker')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(LabourAttendanceRecord::class);
    }
}
