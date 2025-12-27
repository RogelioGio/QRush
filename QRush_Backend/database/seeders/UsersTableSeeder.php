<?php

namespace Database\Seeders;

use DB;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'first_name' => 'Management',
                'last_name' => 'User',
                'access_pin' => '1234',
                'password' => Hash::make('password'),
                'role_id' => 1, // Assuming 1 is the ID for admin role
            ],
            [
                'first_name' => 'Cashier',
                'last_name' => 'User',
                'access_pin' => '5678',
                'password' => Hash::make('password'),
                'role_id' => 2, // Assuming 2 is the ID for staff role
            ],
            [
                'first_name' => 'Kitchen',
                'last_name' => 'User',
                'access_pin' => '9012',
                'password' => Hash::make('password'),
                'role_id' => 3, // Assuming 3 is the ID for kitchen staff role
            ],]);
    }
}
