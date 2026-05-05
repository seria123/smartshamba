<?php

namespace App\Modules\Tasks\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpsTaskUpdate extends Model
{
    protected $table = 'ops_task_updates';

    protected $fillable = [
        'organization_id',
        'farm_id',
        'task_id',
        'user_id',
        'worker_id',
        'update_type',
        'status_from',
        'status_to',
        'progress_percent',
        'quantity_done',
        'quantity_unit',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_done' => 'decimal:2',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(OpsTask::class, 'task_id');
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

    public function worker(): BelongsTo
    {
        return $this->belongsTo(LabourWorker::class, 'worker_id');
    }
}
