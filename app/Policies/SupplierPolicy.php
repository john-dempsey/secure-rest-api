<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Auth\Access\Response;

class SupplierPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        $role = Role::where("name", "superuser")->firstOrFail();
        if ($user->hasRole($role)) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        $permission = Permission::where("name", "viewAny-supplier")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function view(User $user, Supplier $supplier): bool
    {
        $permission = Permission::where("name", "view-supplier")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function create(User $user): bool
    {
        $permission = Permission::where("name", "create-supplier")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function update(User $user, Supplier $supplier): bool
    {
        $permission = Permission::where("name", "update-supplier")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        $permission = Permission::where("name", "delete-supplier")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function restore(User $user, Supplier $supplier): bool
    {
        $permission = Permission::where("name", "restore-supplier")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function forceDelete(User $user, Supplier $supplier): bool
    {
        $permission = Permission::where("name", "forceDelete-supplier")->firstOrFail();
        return $user->hasPermission($permission);
    }
}
