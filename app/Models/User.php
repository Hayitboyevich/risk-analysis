<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $guard_name = 'web';

    protected $guarded = false;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getRoleFromToken()
    {
        return (string)JWTAuth::parseToken()->getClaim('role_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(UserStatus::class, 'user_status_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function getFullNameAttribute()
    {
        return isset($this->name) ? ucwords("{$this->surname} {$this->name} {$this->middle_name}") : null;
    }

    public function hasPermissionForRole(string $permission_name, ?int $role_id = null): bool
    {
        if (!$role_id) {
            return false;
        }
        $this->loadMissing('roles.permissions');
        $role = $this->roles->firstWhere('id', $role_id);

        if (!$role) {
            return false;
        }
        return $role->permissions->contains('name', $permission_name);
    }
}
