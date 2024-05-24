<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Date>
 */
class DateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'origin',
            'as_written' => fake()->dateTimeThisCentury(),
            'not_before' => fake()->year(),
            'not_after' => fake()->year(),
            'note' => fake()->sentence(),
            'json' => '{}',
        ];
    }
}
