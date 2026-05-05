<?php

namespace App\Models;

use App\Modules\Core\Models\Organization;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot(['role_key', 'role_id', 'farm_id', 'status', 'joined_at'])
            ->withTimestamps();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'organization_user')
            ->withPivot(['organization_id', 'farm_id', 'status', 'joined_at'])
            ->withTimestamps();
    }

    public function hasRole(string $roleKey): bool
    {
        return $this->memberships()
            ->where('status', 'active')
            ->whereHas('role', fn ($query) => $query->where('key', $roleKey))
            ->exists();
    }

    public function hasAnyRole(array $roleKeys): bool
    {
        return $this->memberships()
            ->where('status', 'active')
            ->whereHas('role', fn ($query) => $query->whereIn('key', $roleKeys))
            ->exists();
    }

    public function hasPermission(string $permissionKey): bool
    {
        if ($this->hasAnyRole(['owner', 'system-admin'])) {
            return true;
        }

        return $this->memberships()
            ->where('status', 'active')
            ->whereHas('role.permissions', fn ($query) => $query->where('key', $permissionKey))
            ->exists();
    }

    public function canAccessAdmin(string $permissionKey): bool
    {
        return $this->status === 'active' && $this->hasPermission($permissionKey);
    }
}
