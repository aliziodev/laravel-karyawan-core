<?php

namespace Aliziodev\LaravelKaryawanCore\Database\Seeders;

use Aliziodev\LaravelKaryawanCore\Models\Branch;
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Aliziodev\LaravelKaryawanCore\Models\Department;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Aliziodev\LaravelKaryawanCore\Models\Position;
use Illuminate\Database\Seeder;

class KaryawanDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::factory()->create([
            'code' => 'MAIN',
            'name' => 'PT Contoh Indonesia',
        ]);

        $hq = Branch::factory()->create([
            'company_id' => $company->id,
            'code' => 'HQ',
            'name' => 'Kantor Pusat Jakarta',
        ]);

        $hrDept = Department::factory()->create([
            'company_id' => $company->id,
            'code' => 'HR',
            'name' => 'Human Resources',
        ]);

        $itDept = Department::factory()->create([
            'company_id' => $company->id,
            'code' => 'IT',
            'name' => 'Information Technology',
        ]);

        $managerPos = Position::factory()->create([
            'company_id' => $company->id,
            'code' => 'MGR',
            'name' => 'Manager',
        ]);

        $staffPos = Position::factory()->create([
            'company_id' => $company->id,
            'code' => 'STF',
            'name' => 'Staff',
        ]);

        // HR Manager
        $hrManager = Employee::factory()->permanent()->create([
            'company_id' => $company->id,
            'branch_id' => $hq->id,
            'department_id' => $hrDept->id,
            'position_id' => $managerPos->id,
            'employee_code' => 'EMP00001',
            'full_name' => 'Agus Prasetyo',
            'work_email' => 'agus.prasetyo@company.com',
        ]);

        // HR Staff under HR Manager
        Employee::factory()->count(3)->create([
            'company_id' => $company->id,
            'branch_id' => $hq->id,
            'department_id' => $hrDept->id,
            'position_id' => $staffPos->id,
            'manager_employee_id' => $hrManager->id,
        ]);

        // IT Staff
        Employee::factory()->count(5)->create([
            'company_id' => $company->id,
            'branch_id' => $hq->id,
            'department_id' => $itDept->id,
            'position_id' => $staffPos->id,
        ]);

        $this->command?->info('Karyawan demo data berhasil dibuat.');
    }
}
