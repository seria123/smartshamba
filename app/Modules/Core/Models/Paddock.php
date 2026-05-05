<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paddock extends Model
{
    protected $fillable = [
        'organization_id',
        'farm_id',
        'site_id',
        'name',
        'code',
        'area',
        'area_unit',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'area' => 'decimal:2',
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

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
