<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Place>
 */
class PlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement([
                'building',
                'church',
                'composite',
                'designated-space',
                'diocese',
                'fortification',
                'island',
                'madrasa',
                'monastery',
                'mosque',
                'mountain',
                'natural feature',
                'open-water',
                'parish',
                'province',
                'quarter',
                'region',
                'river',
                'settlement',
                'state',
                'synagogue',
                'temple',
                'unknown'
            ]),
            'pref_name' => fake()->name(),
            'json' => '{}',
        ];
    }
}
