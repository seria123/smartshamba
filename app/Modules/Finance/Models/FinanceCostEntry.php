<?php

namespace App\Modules\Finance\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceCostEntry extends Model
{
    use SoftDeletes;

    protected $fillable = ['organization_id', 'farm_id', 'cost_category_id', 'cost_centre_id', 'entry_no', 'entry_date', 'title', 'description', 'source_module', 'reference_type', 'reference_id', 'reference_label', 'quantity', 'unit', 'unit_cost', 'amount', 'currency', 'status', 'payment_state', 'notes', 'created_by', 'confirmed_by', 'confirmed_at', 'voided_by', 'voided_at', 'void_reason'];

    protected function casts(): array
    {
        return ['entry_date' => 'date', 'quantity' => 'decimal:4', 'unit_cost' => 'decimal:4', 'amount' => 'decimal:2', 'confirmed_at' => 'datetime', 'voided_at' => 'datetime'];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function category(): BelongsTo { return $this->belongsTo(FinanceCostCategory::class, 'cost_category_id'); }
    public function centre(): BelongsTo { return $this->belongsTo(FinanceCostCentre::class, 'cost_centre_id'); }
    public function allocations(): HasMany { return $this->hasMany(FinanceCostAllocation::class, 'cost_entry_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function confirmedBy(): BelongsTo { return $this->belongsTo(User::class, 'confirmed_by'); }
    public function voidedBy(): BelongsTo { return $this->belongsTo(User::class, 'voided_by'); }
}
