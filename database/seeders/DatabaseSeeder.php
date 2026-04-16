<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed departments only if not already seeded
        if (\App\Models\Department::count() === 0) {
            $this->call(DepartmentSeeder::class);
        }

        // Seed sample accounts for each role
        $this->call(UserSeeder::class);
    }
}
