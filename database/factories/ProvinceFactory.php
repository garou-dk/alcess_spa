<?php

namespace Database\Factories;

use App\Models\Province;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProvinceFactory extends Factory
{
    protected $model = Province::class;

    public function definition(): array
    {
        return [
            'province_code' => $this->faker->unique()->numerify('PROV###'),
            'province_name' => $this->faker->state(),
            'region_id' => Region::factory(),
        ];
    }
}
