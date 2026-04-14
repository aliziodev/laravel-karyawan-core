<?php

namespace Aliziodev\LaravelKaryawanCore\Database\Factories;

use Aliziodev\LaravelKaryawanCore\Models\Company;
use Aliziodev\LaravelKaryawanCore\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    protected $model = Position::class;

    private array $positionNames = [
        'Staff', 'Senior Staff', 'Supervisor', 'Manager', 'Senior Manager',
        'Assistant Manager', 'Director', 'VP', 'GM', 'Head of Division',
    ];

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'code' => strtoupper($this->faker->unique()->lexify('POS-???')),
            'name' => $this->faker->randomElement($this->positionNames),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
