<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(2, true),
            'type' => $this->faker->randomElement(['tree_planting', 'maintenance', 'pollution', 'green_space_suggestion']),
            'urgency' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['pending', 'validated', 'in_progress', 'completed', 'rejected']),
            'latitude' => $this->faker->latitude(40, 50),
            'longitude' => $this->faker->longitude(-5, 10),
            'address' => $this->faker->address(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
