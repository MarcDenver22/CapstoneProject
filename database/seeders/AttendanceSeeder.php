<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // No sample attendance records will be created
        // Attendance records are created only through actual check-in/check-out
        $this->command->info('Attendance seeder skipped - no sample data created.');
    }
}
