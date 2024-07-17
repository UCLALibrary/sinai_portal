<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $places = $this->getSyriac12Places();
        $this->seedPlaces($places);
    }

    private function seedPlaces($places)
    {
        foreach ($places as $jsonData) {
            // extract and flatten fields from the JSON representation of the metadata to create the record
            $place = Place::create([
                'event' => $jsonData['event'],
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

    private function getSyriac12Places()
    {
        return [
            [
                'as_written' => 'يبشيبشل',
                'event' => 'previous_repository',
                'note' => [
                    [
                        'type' => 'general',
                        'value' => 'The bishop took the ms from a previous place when endowing it'
                    ]
                ]
            ],
            [
                'as_written' => 'Antakya',
                'event' => 'origin',
                'note' => [
                    [
                        'type' => 'assoc_place',
                        'value' => 'Given the Melkite origin and the regional variations in the liturgy, it is fairly certain that this manuscript was produced in Antioch'
                    ]
                ]
            ]
        ];
    }

    private function seedRandomPlaces()
    {
        $numRecords = 10;
        for ($index = 0; $index < $numRecords; $index++) {
            $place = Place::factory()->create();

            // retrieve the primary key
            $id = $place->id;

            // create the data array including the primary key
            $data = [
                'id' => $id,
                'type' => $place->type,
                'as_written' => $place->as_written,
                'note' => $place->note
            ];

            // update the record with the json field
            $place->json = json_encode($data);
            $place->save();
        }
    }
}
