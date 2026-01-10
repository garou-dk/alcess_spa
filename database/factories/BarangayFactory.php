<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\Municity;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangayFactory extends Factory
{
    protected $model = Barangay::class;

    public function definition(): array
    {
        return [
            'barangay_code' => $this->faker->unique()->numerify('BRG###'),
            'barangay_name' => $this->faker->streetName(),
            'municity_id' => Municity::factory(),
        ];
    }
}
