<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'product_name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'product_price' => $this->faker->randomFloat(2, 10, 1000),
            'product_quantity' => $this->faker->numberBetween(0, 100),
            'low_stock_threshold' => $this->faker->numberBetween(5, 20),
            'product_image' => null,
            'is_active' => true,
            'available_online' => true,
            'category_id' => Category::factory(),
            'unit_id' => Unit::factory(),
        ];
    }
}
