<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $superRole    = Role::create(["name" => "superuser"]);
        $adminRole    = Role::create(["name" => "admin"    ]);
        $customerRole = Role::create(["name" => "customer" ]);
        $supplierRole = Role::create(["name" => "supplier" ]);
    }
}
