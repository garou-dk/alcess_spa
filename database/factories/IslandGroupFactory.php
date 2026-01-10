<?php

namespace Database\Factories;

use App\Models\IslandGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class IslandGroupFactory extends Factory
{
    protected $model = IslandGroup::class;

    public function definition(): array
    {
        return [
            'island_group_code' => $this->faker->unique()->numerify('IG###'),
            'island_group_name' => $this->faker->randomElement(['Luzon', 'Visayas', 'Mindanao']),
        ];
    }
}
