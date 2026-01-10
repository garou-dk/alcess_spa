<?php

namespace Database\Factories;

use App\Models\IslandGroup;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        $regionName = $this->faker->state();
        return [
            'region_code' => $this->faker->unique()->numerify('REG###'),
            'region_name' => $regionName,
            'region_name_long' => $regionName . ' Region',
            'island_group_id' => IslandGroup::factory(),
        ];
    }
}
