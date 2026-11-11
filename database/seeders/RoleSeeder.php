<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
//This one must exist in prod
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $user_role = [
            ['RoleID' => 1, 'roleName' => 'Project Manager (Admin)'],
            ['RoleID' => 2, 'roleName' => 'Contractor'],
            ['RoleID' => 3, 'roleName' => 'Foreman'],
            ['RoleID' => 4, 'roleName' => 'Timekeeper'],
            ['RoleID' => 5, 'roleName' => 'Inventory Personnel'],
            ['RoleID' => 6, 'roleName' => 'Worker'],
            ['RoleID' => 7, 'roleName' => 'Laborer'],
            ['RoleID' => 8, 'roleName' => 'Client']
        ];

        foreach ($user_role as $role) {
            Role::updateOrCreate(
                ['RoleID' => $role['RoleID']],
                ['roleName' => $role['roleName']],
            );
        }
    }
}
