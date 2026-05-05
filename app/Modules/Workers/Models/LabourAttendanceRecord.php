<?php

namespace App\Modules\Workers\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabourAttendanceRecord extends Model
{
    protected $fillable = [
        'organization_id',
        'farm_id',
        'labour_worker_id',
        'labour_team_id',
        'date',
        'status',
        'check_in_at',
        'check_out_at',
        'hours_worked',
        'recorded_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'hours_worked' => 'decimal:2',
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

    public function worker(): BelongsTo
    {
        return $this->belongsTo(LabourWorker::class, 'labour_worker_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(LabourTeam::class, 'labour_team_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
