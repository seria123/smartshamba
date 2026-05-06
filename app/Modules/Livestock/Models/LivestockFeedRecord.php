<?php

namespace App\Modules\Livestock\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockFeedRecord extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_feed_records';
    protected $fillable = ['organization_id', 'farm_id', 'animal_id', 'animal_group_id', 'related_task_id', 'feed_date', 'product_id', 'feed_name_snapshot', 'quantity', 'quantity_unit', 'feeding_method', 'fed_by_user_id', 'fed_by_worker_id', 'team_id', 'notes', 'created_by'];
    protected function casts(): array { return ['feed_date' => 'date', 'quantity' => 'decimal:2']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function animal(): BelongsTo { return $this->belongsTo(LivestockAnimal::class, 'animal_id'); }
    public function animalGroup(): BelongsTo { return $this->belongsTo(LivestockAnimalGroup::class, 'animal_group_id'); }
    public function relatedTask(): BelongsTo { return $this->belongsTo(OpsTask::class, 'related_task_id'); }
    public function product(): BelongsTo { return $this->belongsTo(InventoryProduct::class, 'product_id'); }
    public function fedByUser(): BelongsTo { return $this->belongsTo(User::class, 'fed_by_user_id'); }
    public function fedByWorker(): BelongsTo { return $this->belongsTo(LabourWorker::class, 'fed_by_worker_id'); }
    public function team(): BelongsTo { return $this->belongsTo(LabourTeam::class); }
}
