<?php

namespace App\Modules\Workers\Models;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabourTeam extends Model
{
    protected $fillable = [
        'organization_id',
        'farm_id',
        'supervisor_worker_id',
        'name',
        'code',
        'team_type',
        'status',
        'notes',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(LabourWorker::class, 'supervisor_worker_id');
    }

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(LabourWorker::class, 'labour_team_worker')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(LabourAttendanceRecord::class);
    }
}
