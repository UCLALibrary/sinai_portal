<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Manuscript>
 */
class ManuscriptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ark' => 'ark:/21198/z1.' . fake()->numberBetween(10000, 99999),
            'shelfmark' => 'MS ' . fake()->randomNumber(),
            'json' => '{}',
        ];
    }
}
