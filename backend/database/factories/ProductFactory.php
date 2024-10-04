<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userID = User::factory()->create(['type' => 'admin'])->id;
        return [
            'title' => fake()->word(),
            'price' => fake()->unique()->numberBetween(0, 2000),
            'quantity' => fake()->unique()->numberBetween(0, 100),
            'description' => fake()->paragraph('7'),
            'published' => fake()->randomElement([true, false]),
            'created_by' => $userID,
            'updated_by' => $userID,

        ];
    }
}
