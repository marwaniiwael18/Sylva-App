<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'EUR',
            'type' => $this->faker->randomElement(['tree_planting', 'maintenance', 'awareness']),
            'user_id' => \App\Models\User::factory(),
            'event_id' => \App\Models\Event::factory(),
            'message' => $this->faker->optional(0.7)->sentence(),
            'anonymous' => $this->faker->boolean(20), // 20% chance of being anonymous
            'payment_status' => 'pending', // Use default instead of random
            'payment_method' => 'card',
        ];
    }
}
