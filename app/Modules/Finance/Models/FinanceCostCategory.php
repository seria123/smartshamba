<?php

namespace App\Modules\Finance\Models;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceCostCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['organization_id', 'farm_id', 'name', 'code', 'description', 'cost_nature', 'default_source_module', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function entries(): HasMany { return $this->hasMany(FinanceCostEntry::class, 'cost_category_id'); }
}
