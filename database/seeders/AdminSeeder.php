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
        DB::table('construction_users')->insert([
            'RoleID' => 1,
            'firstName' => 'Contractor',
            'lastName' => '/Project Manager',
            'email'     => 'admin@linfracon.com',
            'password'  => Hash::make('StrongAdminPassword123'),
            'contactNo'   => '09397008867',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}