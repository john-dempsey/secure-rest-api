<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superRole    = Role::where("name", "superuser")->firstOrFail();
        $adminRole    = Role::where("name", "admin"    )->firstOrFail();
        $customerRole = Role::where("name", "customer" )->firstOrFail();
        $supplierRole = Role::where("name", "supplier" )->firstOrFail();

        $superUser = User::factory()->create();
        $superUser->assignRole($superRole);

        $adminUser = User::factory()->create();
        $adminUser->assignRole($adminRole);

        $customerAdminUser = User::factory()->create();
        $customerAdminUser->assignRole($customerRole);
        
        $supplierAdminUser = User::factory()->create();
        $supplierAdminUser->assignRole($supplierRole);
    }
}
