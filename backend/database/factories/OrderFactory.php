<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userID = User::factory()->create(['type' => 'client'])->id;
        return [
            'total_price' => fake()->unique()->numberBetween(0, 2000),
            'status' => fake()->unique()->randomElement(OrderStatus::toArray()),
            'created_by' => $userID,
            'updated_by' => $userID,

        ];
    }
}
