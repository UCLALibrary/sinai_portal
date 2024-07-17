<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PersonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $persons = $this->getSyriac12Persons();
        $this->seedPersons($persons);
    }

    private function seedPersons($persons)
    {
        foreach ($persons as $jsonData) {
            // extract and flatten fields from the JSON representation of the metadata to create the record
            $place = Person::create([
                'role' => $jsonData['role'],
                'as_written' => $jsonData['as_written'],
                'json' => '{}'
            ]);

            // include the primary key in the JSON representation of the metadata
            $jsonData['id'] = $place->id;

            // update the record with the json field
            $place->json = json_encode($jsonData);
            $place->save();
        }
    }

    private function getSyriac12Persons()
    {
        return [
            [
                'as_written' => 'ابجد',
                'role' => 'owner',
                'note' => [
                    [
                        'type' => 'general',
                        'value' => 'A former owner endowed this ms to the monastery'
                    ]
                ]
            ],
            [
                'as_written' => 'Michael the Scribe',
                'role' => 'scribe',
                'note' => [
                    [
                        'type' => 'assoc_name',
                        'value' => 'A note about how we know Michael the Scribe wrote this manuscript'
                    ]
                ]
            ],
        ];
    }
}
