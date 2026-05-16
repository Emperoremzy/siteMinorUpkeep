<?php

namespace Database\Factories;

use App\Models\Monitor;
use App\Models\MonitorHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonitorHistory>
 */
class MonitorHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isUp = fake()->boolean(90);

        return [
            'monitor_id' => Monitor::factory(),
            'status_code' => $isUp ? fake()->randomElement([200, 204, 301, 302]) : fake()->randomElement([0, 400, 404, 500, 503]),
            'response_time_ms' => fake()->optional(0.95)->numberBetween(20, 3000),
            'is_up' => $isUp,
            'checked_at' => fake()->dateTimeBetween('-1 day'),
        ];
    }

    /**
     * Indicate that the check failed due to a connection error or timeout.
     */
    public function timeout(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_code' => 0,
            'response_time_ms' => null,
            'is_up' => false,
        ]);
    }
}
