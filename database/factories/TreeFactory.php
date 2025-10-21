<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tree>
 */
class TreeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'species' => $this->faker->randomElement(['Oak', 'Pine', 'Maple', 'Birch', 'Cherry', 'Apple', 'Pear']),
            'latitude' => $this->faker->latitude(40, 50), // France latitude range
            'longitude' => $this->faker->longitude(-5, 10), // France longitude range
            'planting_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'status' => 'Not Yet', // Use default instead of random
            'type' => $this->faker->randomElement(['Fruit', 'Ornamental', 'Forest', 'Medicinal']),
            'planted_by_user' => \App\Models\User::factory(),
            'description' => $this->faker->optional(0.8)->sentence(),
            'address' => $this->faker->address(),
        ];
    }
}
