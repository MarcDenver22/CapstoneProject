<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Department of Education',
                'code' => 'DOE',
                'description' => 'Department of Education',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Computing',
                'code' => 'DOC',
                'description' => 'Department of Computing',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Industrial Technology',
                'code' => 'DOIT',
                'description' => 'Department of Industrial Technology',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Business Administration',
                'code' => 'DBA',
                'description' => 'Department of Business Administration',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $dept) {
            Department::on('supabase')->create($dept);
        }

        $this->command->info('✓ Departments seeded successfully!');
    }
}
