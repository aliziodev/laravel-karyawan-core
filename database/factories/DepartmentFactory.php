<?php

namespace Aliziodev\LaravelKaryawanCore\Database\Factories;

use Aliziodev\LaravelKaryawanCore\Models\Company;
use Aliziodev\LaravelKaryawanCore\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    private array $deptNames = [
        'Human Resources', 'Finance', 'Accounting', 'Marketing',
        'Operations', 'IT', 'Legal', 'Procurement', 'Sales', 'General Affairs',
    ];

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'code' => strtoupper($this->faker->unique()->lexify('DEPT-???')),
            'name' => $this->faker->randomElement($this->deptNames),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
