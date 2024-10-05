<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CategoryFactory extends Factory
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
            'name' => fake()->title(),
            'active' => fake()->randomElement([false, true]),
            'created_by' => $userID,
            'updated_by' => $userID,
        ];
    }
}
