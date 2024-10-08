<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
 
class Role extends Model
{
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission(Permission $permission): bool
    {
        return $this->permissions->contains($permission);
    }

    public function assignPermission(Permission $permission): void
    {
        if (!$this->hasPermission($permission)) {
            $this->permissions()->attach($permission);
        }
    }
    public function removePermission(Permission $permission): void
    {
        if ($this->hasPermission($permission)) {
            $this->permissions()->detach($permission);
        }
    }
}
