<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $user_role = [
            ['roleName' => 'Admin'],
            ['roleName' => 'Foreman'],
            ['roleName' => 'Timekeeper'],
            ['roleName' => 'Inventory Personnel'],
            ['roleName' => 'Worker'],
            ['roleName' => 'Laborer'],
            ['roleName' => 'Client']
        ];

        foreach ($user_role as $role) {
            Role::updateOrCreate($role);
        }
    }
}