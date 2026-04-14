<?php

namespace Aliziodev\LaravelKaryawanCore\Database\Factories;

use Aliziodev\LaravelKaryawanCore\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('CO-???')),
            'name' => $this->faker->company(),
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
