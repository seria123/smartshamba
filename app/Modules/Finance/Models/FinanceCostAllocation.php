<?php

namespace App\Modules\Finance\Models;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceCostAllocation extends Model
{
    protected $fillable = ['cost_entry_id', 'organization_id', 'farm_id', 'allocation_type', 'allocatable_type', 'allocatable_id', 'allocation_label', 'allocation_percent', 'amount', 'notes'];

    protected function casts(): array
    {
        return ['allocation_percent' => 'decimal:4', 'amount' => 'decimal:2'];
    }

    public function entry(): BelongsTo { return $this->belongsTo(FinanceCostEntry::class, 'cost_entry_id'); }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
}
