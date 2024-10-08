<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Auth\Access\Response;

class ProductPolicy
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
        $permission = Permission::where("name", "viewAny-product")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function view(User $user, Product $product): bool
    {
        $permission = Permission::where("name", "view-product")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function create(User $user): bool
    {
        $permission = Permission::where("name", "create-product")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function update(User $user, Product $product): bool
    {
        $permission = Permission::where("name", "update-product")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function delete(User $user, Product $product): bool
    {
        $permission = Permission::where("name", "delete-product")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function restore(User $user, Product $product): bool
    {
        $permission = Permission::where("name", "restore-product")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function forceDelete(User $user, Product $product): bool
    {
        $permission = Permission::where("name", "forceDelete-product")->firstOrFail();
        return $user->hasPermission($permission);
    }
}
