<?php

namespace Database\Factories;

use App\Models\Municity;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

class MunicityFactory extends Factory
{
    protected $model = Municity::class;

    public function definition(): array
    {
        return [
            'municity_code' => $this->faker->unique()->numerify('MUN###'),
            'municity_name' => $this->faker->city(),
            'province_id' => Province::factory(),
        ];
    }
}
