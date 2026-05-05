<?php

namespace App\Modules\Core\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    public function farms(): HasMany
    {
        return $this->hasMany(Farm::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    public function paddocks(): HasMany
    {
        return $this->hasMany(Paddock::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role_key', 'joined_at'])
            ->withTimestamps();
    }
}
