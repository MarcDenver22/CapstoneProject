<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a department for assignment
        $department = Department::on('supabase')->first();
        $departmentId = $department?->id ?? null;

        // Create a Super Admin sample account
        User::on('supabase')->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'department_id' => $departmentId,
            'employee_id' => 'SA001',
            'position' => 'System Administrator',
            'email_verified_at' => now(),
        ]);

        // Create an Admin sample account
        User::on('supabase')->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'department_id' => $departmentId,
            'employee_id' => 'AD001',
            'position' => 'System Administrator',
            'email_verified_at' => now(),
        ]);

        // Create an HR sample account
        User::on('supabase')->create([
            'name' => 'HR Manager',
            'email' => 'hr@example.com',
            'password' => Hash::make('password123'),
            'role' => 'hr',
            'department_id' => $departmentId,
            'employee_id' => 'HR001',
            'position' => 'HR Manager',
            'email_verified_at' => now(),
        ]);

        // Create an Employee sample account
        User::on('supabase')->create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => Hash::make('password123'),
            'role' => 'employee',
            'department_id' => $departmentId,
            'employee_id' => 'EMP001',
            'position' => 'Staff Member',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Sample accounts created for all roles:');
        $this->command->info('- Super Admin: superadmin@example.com');
        $this->command->info('- Admin: admin@example.com');
        $this->command->info('- HR: hr@example.com');
        $this->command->info('- Employee: employee@example.com');
        $this->command->info('Password for all accounts: password123');
        if ($departmentId) {
            $this->command->info("- All users assigned to: {$department->name}");
        }
    }
}
