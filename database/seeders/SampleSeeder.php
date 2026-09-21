<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('construction_users')->insert([
            'RoleID' => 2,
            'firstName' => 'Kiki',
            'lastName' => 'Gigi',
            'email'     => 'Gigi@gmail.com',
            'password'  => Hash::make('SamplePassword123'),
            'contactNo'   => '09277358905',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
