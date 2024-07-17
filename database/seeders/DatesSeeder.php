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
        $dates = $this->getSyriac12Dates();
        $this->seedDates($dates);

        // $dates = $this->getCenturiesData();
        // $this->seedDates($dates);
    }

    private function seedDates($dates)
    {
        foreach ($dates as $jsonData) {
            // extract and flatten fields from the JSON representation of the metadata to create the record
            $date = Date::create([
                'type' => $jsonData['type'],
                'not_before' => $jsonData['iso']['not_before'],
                'not_after' => $jsonData['iso']['not_after'],
                'value' => $jsonData['value'],
                'as_written' => $jsonData['as_written'],
                'json' => '{}'
            ]);

            // include the primary key in the JSON representation of the metadata
            $jsonData['id'] = $date->id;

            // update the record with the json field
            $date->json = json_encode($jsonData);
            $date->save();
        }
    }

    private function getSyriac12Dates()
    {
        return [
            [
                'type' => 'purchase',
                'iso' => [
                    'not_before' => '1564',
                    'not_after' => '1564',
                ],
                'value' => '1564 CE',
                'as_written' => '1564',
                'note' => [
                    [
                        'type' => 'assoc_date',
                        'value' => 'Surprisingly, the date provided is Gregorian'
                    ]
                ],
            ],
            [
                'type' => 'binding',
                'iso' => [
                    'not_before' => '1201',
                    'not_after' => '1700',
                ],
                'value' => 'Between 13th - 17th c. CE',
                'as_written' => 'Lorem ipsum',
                'note' => [
                    [
                        'type' => 'assoc_date',
                        'value' => 'Binding date estimated from production dates of the two codicological units'
                    ]
                ],
            ],
            [
                'type' => 'origin',
                'iso' => [
                    'not_before' => '1201',
                    'not_after' => '1300',
                ],
                'value' => '13th c. CE',
                'as_written' => 'VALUE',
                'note' => [
                    [
                        'type' => 'assoc_date',
                        'value' => 'Based on the script, this manuscript is from the 13th cenutry'
                    ]
                ],
            ],
        ];
    }

    private function getCenturiesData()
    {
        return [
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '0001',
                    'not_after' => '0100',
                ],
                'value' => '1st c.',
                'as_written' => '1st c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '0101',
                    'not_after' => '0200',
                ],
                'value' => '2nd c.',
                'as_written' => '2nd c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '0201',
                    'not_after' => '0300',
                ],
                'value' => '3rd c.',
                'as_written' => '3rd c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '0301',
                    'not_after' => '0400',
                ],
                'value' => '4th c.',
                'as_written' => '4th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '0401',
                    'not_after' => '0500',
                ],
                'value' => '5th c.',
                'as_written' => '5th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '0501',
                    'not_after' => '0600',
                ],
                'value' => '6th c.',
                'as_written' => '6th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '0601',
                    'not_after' => '0700',
                ],
                'value' => '7th c.',
                'as_written' => '7th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '0701',
                    'not_after' => '0800',
                ],
                'value' => '8th c.',
                'as_written' => '8th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '0801',
                    'not_after' => '0900',
                ],
                'value' => '9th c.',
                'as_written' => '9th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '0901',
                    'not_after' => '1000',
                ],
                'value' => '10th c.',
                'as_written' => '10th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '1001',
                    'not_after' => '1100',
                ],
                'value' => '11th c.',
                'as_written' => '11th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '1101',
                    'not_after' => '1200',
                ],
                'value' => '12th c.',
                'as_written' => '12th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '1201',
                    'not_after' => '1300',
                ],
                'value' => '13th c.',
                'as_written' => '13th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '1301',
                    'not_after' => '1400',
                ],
                'value' => '14th c.',
                'as_written' => '14th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '1401',
                    'not_after' => '1500',
                ],
                'value' => '15th c.',
                'as_written' => '15th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '1501',
                    'not_after' => '1600',
                ],
                'value' => '16th c.',
                'as_written' => '16th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '1601',
                    'not_after' => '1700',
                ],
                'value' => '17th c.',
                'as_written' => '17th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '1701',
                    'not_after' => '1800',
                ],
                'value' => '18th c.',
                'as_written' => '18th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '1801',
                    'not_after' => '1900',
                ],
                'value' => '19th c.',
                'as_written' => '19th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
            [
                'type' => 'creation',
                'iso' => [
                    'not_before' => '1901',
                    'not_after' => '2000',
                ],
                'value' => '20th c.',
                'as_written' => '20th c.',
                'note' => [
                    'type' => 'assoc_date',
                    'value' => fake()->sentence(),
                ],
            ],
        ];
    }
}
