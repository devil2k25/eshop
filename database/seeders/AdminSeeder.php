<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the admin user
        User::create([
            'name'              => 'Admin User',
            'email'             => 'admin@shopvue.com',
            'password'          => Hash::make('password'),
            'is_admin'          => true,
            'email_verified_at' => now(),
        ]);

        // Create 5 regular test users with Faker
        User::factory(5)->create([
            'email_verified_at' => now(),
        ]);
    }
}
