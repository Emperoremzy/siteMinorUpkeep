<?php

namespace Database\Factories;

use App\Models\Monitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Monitor>
 */
class MonitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->unique()->url(),
            'check_interval' => fake()->numberBetween(1, 60),
            'threshold' => fake()->numberBetween(1, 5),
            'status' => fake()->randomElement(['pending', 'up', 'down']),
            'last_checked_at' => fake()->optional()->dateTimeBetween('-1 day'),
            'uptime_percentage' => fake()->optional()->randomFloat(2, 0, 100),
        ];
    }

    /**
     * Indicate that the monitor is pending its first check.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'last_checked_at' => null,
            'uptime_percentage' => null,
        ]);
    }
}
