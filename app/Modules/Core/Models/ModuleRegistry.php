<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleRegistry extends Model
{
    protected $table = 'module_registry';

    protected $fillable = [
        'key',
        'name',
        'description',
        'status',
        'sort_order',
        'activated_at',
    ];

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
        ];
    }
}
