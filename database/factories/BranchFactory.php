<?php

namespace Aliziodev\LaravelKaryawanCore\Database\Factories;

use Aliziodev\LaravelKaryawanCore\Models\Branch;
use Aliziodev\LaravelKaryawanCore\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'code' => strtoupper($this->faker->unique()->lexify('BR-???')),
            'name' => $this->faker->city().' Branch',
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
