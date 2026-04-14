<?php

namespace Aliziodev\LaravelKaryawanCore\Database\Factories;

use Aliziodev\LaravelKaryawanCore\Enums\ActiveStatus;
use Aliziodev\LaravelKaryawanCore\Enums\EmploymentType;
use Aliziodev\LaravelKaryawanCore\Enums\Gender;
use Aliziodev\LaravelKaryawanCore\Enums\MaritalStatus;
use Aliziodev\LaravelKaryawanCore\Enums\Religion;
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Aliziodev\LaravelKaryawanCore\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(Gender::cases());
        $joinDate = $this->faker->dateTimeBetween('-5 years', '-1 month');
        $prefix = (string) config('karyawan.employee_code.prefix', 'EMP');
        $padLength = (int) config('karyawan.employee_code.pad_length', 5);

        $maxNumber = (10 ** max($padLength, 1)) - 1;
        $randomNumber = (string) $this->faker->unique()->numberBetween(1, $maxNumber);
        $employeeCode = strtoupper($prefix).str_pad($randomNumber, $padLength, '0', STR_PAD_LEFT);

        return [
            'user_id' => null,
            'company_id' => Company::factory(),
            'branch_id' => null,
            'department_id' => null,
            'position_id' => null,
            'manager_employee_id' => null,
            'employee_code' => $employeeCode,
            'full_name' => $this->faker->name($gender === Gender::Male ? 'male' : 'female'),
            'nick_name' => $this->faker->firstName(),
            'work_email' => $this->faker->unique()->safeEmail(),
            'personal_email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'national_id_number' => $this->faker->numerify('################'),
            'family_card_number' => $this->faker->numerify('################'),
            'tax_number' => $this->faker->numerify('##.###.###.#-###.###'),
            'gender' => $gender->value,
            'religion' => $this->faker->randomElement(Religion::cases())->value,
            'marital_status' => $this->faker->randomElement(MaritalStatus::cases())->value,
            'birth_place' => $this->faker->city(),
            'birth_date' => $this->faker->dateTimeBetween('-55 years', '-22 years')->format('Y-m-d'),
            'citizenship' => 'WNI',
            'permanent_address' => $this->faker->address(),
            'current_address' => $this->faker->address(),
            'join_date' => $joinDate->format('Y-m-d'),
            'permanent_date' => null,
            'exit_date' => null,
            'employment_type' => $this->faker->randomElement(EmploymentType::cases())->value,
            'active_status' => ActiveStatus::Active->value,
            'notes' => null,
        ];
    }

    public function withUser(int $userId): static
    {
        return $this->state(['user_id' => $userId]);
    }

    public function resigned(): static
    {
        return $this->state([
            'active_status' => ActiveStatus::Resigned->value,
            'exit_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['active_status' => ActiveStatus::Inactive->value]);
    }

    public function permanent(): static
    {
        return $this->state([
            'employment_type' => EmploymentType::Permanent->value,
            'permanent_date' => $this->faker->dateTimeBetween('-3 years', '-1 month')->format('Y-m-d'),
        ]);
    }
}
