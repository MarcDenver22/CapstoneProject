<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a Super Admin user for testing
        User::create([
            'name' => 'Marc Denver Riturban',
            'email' => '22ur0749@psu.edu.ph',
            'password' => Hash::make('fernandez2003'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        // Create an Admin user for testing
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Test users created successfully!');
    }
}
