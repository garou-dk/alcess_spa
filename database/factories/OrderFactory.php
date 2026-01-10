<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'barangay_id' => Barangay::factory(),
            'other_details' => $this->faker->address(),
            'contact_number' => $this->faker->phoneNumber(),
            'status' => 'Pending',
            'order_type' => $this->faker->randomElement(['Delivery', 'Pickup']),
            'payment_method' => $this->faker->randomElement(['Cash on Delivery', 'Online Payment', 'Pickup Payment']),
            'total_amount' => $this->faker->randomFloat(2, 100, 5000),
            'shipping_fee' => $this->faker->randomFloat(2, 0, 200),
            'order_public_id' => 'ORD-' . $this->faker->unique()->numerify('######'),
            'postal_code' => $this->faker->postcode(),
            'delivery_courier' => $this->faker->randomElement(['LBC', 'JRS', 'J&T']),
            'tracking_number' => $this->faker->bothify('TRACK-####-????'),
            'estimated_delivery_date_start' => $this->faker->dateTimeBetween('now', '+5 days'),
            'estimated_delivery_date_end' => $this->faker->dateTimeBetween('+6 days', '+10 days'),
        ];
    }
}
