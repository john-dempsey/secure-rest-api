<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
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
        $permission = Permission::where("name", "viewAny-order")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function view(User $user, Order $order): bool
    {
        $permission = Permission::where("name", "view-order")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function create(User $user): bool
    {
        $permission = Permission::where("name", "create-order")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function update(User $user, Order $order): bool
    {
        $permission = Permission::where("name", "update-order")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function delete(User $user, Order $order): bool
    {
        $permission = Permission::where("name", "delete-order")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function restore(User $user, Order $order): bool
    {
        $permission = Permission::where("name", "restore-order")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function forceDelete(User $user, Order $order): bool
    {
        $permission = Permission::where("name", "forceDelete-order")->firstOrFail();
        return $user->hasPermission($permission);
    }
}
