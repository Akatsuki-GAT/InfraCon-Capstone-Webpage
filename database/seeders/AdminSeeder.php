<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('construction_users')->updateOrInsert([
            'email' => 'admin@linfracon.com',
        ], [
            'RoleID' => 1,
            'firstName' => 'Contractor',
            'lastName' => '/Project Manager',
            'password'  => Hash::make('StrongAdminPassword123'),
            'contactNo'   => '09397008867',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
