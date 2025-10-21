<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
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
            'date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'location' => $this->faker->address(),
            'type' => $this->faker->randomElement(array_keys(\App\Models\Event::TYPES)),
            'organized_by_user_id' => \App\Models\User::factory(),
            'status' => 'active',
        ];
    }
}
