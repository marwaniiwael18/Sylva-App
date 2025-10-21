<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TreeCare>
 */
class TreeCareFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tree_id' => \App\Models\Tree::factory(),
            'user_id' => \App\Models\User::factory(),
            'event_id' => \App\Models\Event::factory(),
            'activity_type' => $this->faker->randomElement(array_keys(\App\Models\TreeCare::ACTIVITY_TYPES)),
            'notes' => $this->faker->optional(0.7)->sentence(),
            'performed_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'condition_after' => $this->faker->randomElement(array_keys(\App\Models\TreeCare::CONDITIONS)),
        ];
    }
}
