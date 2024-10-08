<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
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
        $permission = Permission::where("name", "viewAny-customer")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function view(User $user, Customer $customer): bool
    {
        $permission = Permission::where("name", "view-customer")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function create(User $user): bool
    {
        $permission = Permission::where("name", "create-customer")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function update(User $user, Customer $customer): bool
    {
        $permission = Permission::where("name", "update-customer")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function delete(User $user, Customer $customer): bool
    {
        $permission = Permission::where("name", "delete-customer")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function restore(User $user, Customer $customer): bool
    {
        $permission = Permission::where("name", "restore-customer")->firstOrFail();
        return $user->hasPermission($permission);
    }

    public function forceDelete(User $user, Customer $customer): bool
    {
        $permission = Permission::where("name", "forceDelete-customer")->firstOrFail();
        return $user->hasPermission($permission);
    }
}
