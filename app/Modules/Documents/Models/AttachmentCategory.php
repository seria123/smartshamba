<?php

namespace App\Modules\Documents\Models;

use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttachmentCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['organization_id', 'name', 'slug', 'description', 'is_system', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_system' => 'boolean', 'is_active' => 'boolean'];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
}
