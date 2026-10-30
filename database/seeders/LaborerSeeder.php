<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class LaborerSeeder extends Seeder
{
    /**
     * Create development accounts for the Manage Labor Assignments use case.
     */
    public function run(): void
    {
        $laborerRoleId = Role::query()
            ->where('roleName', 'Laborer')
            ->value('RoleID');

        if (! $laborerRoleId) {
            throw new RuntimeException('The Laborer role does not exist. Run RoleSeeder first.');
        }

        $laborers = [
            ['Andres', 'Bautista'],
            ['Benito', 'Castillo'],
            ['Carlos', 'Domingo'],
            ['Daniel', 'Espino'],
            ['Eduardo', 'Flores'],
            ['Felipe', 'Garcia'],
            ['Gabriel', 'Herrera'],
            ['Hector', 'Ignacio'],
            ['Ismael', 'Jimenez'],
            ['Jose', 'Lorenzo'],
            ['Kevin', 'Mendoza'],
            ['Luis', 'Navarro'],
            ['Manuel', 'Ortega'],
            ['Nestor', 'Pascual'],
            ['Oscar', 'Quintos'],
            ['Paolo', 'Ramos'],
            ['Ramon', 'Santos'],
            ['Samuel', 'Torres'],
            ['Tomas', 'Valdez'],
            ['Victor', 'Zamora'],
        ];

        foreach ($laborers as $index => [$firstName, $lastName]) {
            $number = $index + 1;

            User::query()->updateOrCreate(
                ['email' => sprintf('laborer%02d@infracon.test', $number)],
                [
                    'RoleID' => $laborerRoleId,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'contactNo' => sprintf('09170000%03d', $number),
                    'password' => 'LaborerPassword123',
                    'status' => 'active',
                ]
            );
        }
    }
}
