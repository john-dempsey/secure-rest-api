<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(Role $role): bool
    {
        return $this->roles->contains($role);
    }

    public function assignRole(Role $role): void
    {
        if (!$this->hasRole($role)) {
            $this->roles()->attach($role);
        }
    }

    public function removeRole(Role $role): void
    {
        if ($this->hasRole($role)) { 
            $this->roles()->detach($role);
        }
    }

    public function getPermissions()
    {
        $permissions = new Collection();
        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission) {
                if (!$permissions->contains($permission)) {
                    $permissions->push($permission);
                }
            }
        }
        return $permissions;
    }

    public function hasPermission(Permission $permission): bool
    {
        return $this->roles->contains(
            fn (Role $role) => $role->hasPermission($permission)
        );
    }
}
