<?php

namespace Database\Seeders;

use App\Models\Date;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dates = [
            [
                'type' => 'creation',
                'as_written' => '1st c.',
                'not_before' => '0001',
                'not_after' => '0100',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '2nd c.',
                'not_before' => '0101',
                'not_after' => '0200',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '3rd c.',
                'not_before' => '0201',
                'not_after' => '0300',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '4th c.',
                'not_before' => '0301',
                'not_after' => '0400',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '5th c.',
                'not_before' => '0401',
                'not_after' => '0500',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '6th c.',
                'not_before' => '0501',
                'not_after' => '0600',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '7th c.',
                'not_before' => '0601',
                'not_after' => '0700',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '8th c.',
                'not_before' => '0701',
                'not_after' => '0800',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '9th c.',
                'not_before' => '0801',
                'not_after' => '0900',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '10th c.',
                'not_before' => '0901',
                'not_after' => '1000',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '11th c.',
                'not_before' => '1001',
                'not_after' => '1100',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '12th c.',
                'not_before' => '1101',
                'not_after' => '1200',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '13th c.',
                'not_before' => '1201',
                'not_after' => '1300',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '14th c.',
                'not_before' => '1301',
                'not_after' => '1400',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '15th c.',
                'not_before' => '1401',
                'not_after' => '1500',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '16th c.',
                'not_before' => '1501',
                'not_after' => '1600',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '17th c.',
                'not_before' => '1601',
                'not_after' => '1700',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '18th c.',
                'not_before' => '1701',
                'not_after' => '1800',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '19th c.',
                'not_before' => '1801',
                'not_after' => '1900',
                'note' => fake()->sentence(),
            ],
            [
                'type' => 'creation',
                'as_written' => '20th c.',
                'not_before' => '1901',
                'not_after' => '2000',
                'note' => fake()->sentence(),
            ],
        ];

        for ($index = 0; $index < count($dates); $index++) {
            // create the record
            $date = Date::create(['json' => '{}', ...$dates[$index]]);

            // create the data array including the primary key
            $data = [
                'id' => $date->id,
                ...$dates[$index]
            ];

            // update the record with the json field
            $date->json = json_encode($data);
            $date->save();
        }
    }
}
