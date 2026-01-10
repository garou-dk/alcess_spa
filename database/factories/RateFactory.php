<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RateFactory extends Factory
{
    protected $model = Rate::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'product_order_id' => ProductOrder::factory(),
            'rate' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->sentence(),
            'reply' => null,
        ];
    }
}
