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
				'id' => 'tejerus',
				'ark' => 'ark:/21198/tejerus',
                'type' => $jsonData['type'],
                'pref_name' => $jsonData['pref_name'],
                'json' => '{}'
            ]);
			
            // update the record with the json field
            $place->json = json_encode($jsonData);
            $place->save();
        }
    }

    private function getSyriac12Places()
    {
        return [
            [
	            'ark' => 'ark:/21198/tejerus',
                'type' => 'mountain',
                'pref_name' => 'Mount Sinai',
                'alt_name' => [
                    [
                        'lang' => 'English',
                        'value' => 'Sinai'
                    ],
                    [
                        'lang' => 'Syriac',
                        'value' => 'ܛܘܪܐ ܕܣܝܢܝ'
                    ]
                ],
                'desc' => 'A mountain in the Sinai Peninsula.',
                'rel_con' => [
                    [
                        'label' => 'Mount Sinai',
                        'uri' => 'http://syriaca.org/place/563',
                        'source' => 'Syriaca'
                    ],
                    [
                        'label' => 'Syna M.',
                        'uri' => 'https://pleiades.stoa.org/places/746815',
                        'source' => 'Pleiades'
                    ],
                    [
                        'label' => 'Sinai, Mount',
                        'uri' => 'https://w3id.org/haf/place/445637422551',
                        'source' => 'HAF'
                    ],
                    [
                        'label' => 'Mūsá, Jabal (mountain) ',
                        'uri' => 'http://vocab.getty.edu/page/tgn/7001247',
                        'source' => 'TGN'
                    ],
                    [
                        'label' => 'Sinai, Mount (Egypt)',
                        'uri' => 'http://viaf.org/viaf/315149342',
                        'source' => 'VIAF'
                    ]
                ],
                'bib' => [
                    5,
                ],
                'note' => [
                    [
                        'type' => 'admin',
                        'value' => 'Add contained-within relationship to the Sinai Peninsula'
                    ]
                ],
                'assoc_date' => [
                    [
                        'type' => 'inhabitation',
                        'iso' => [
                            'notBefore' => '0575',
                            'notAfter' => '2024'
                        ],
                        'value' => 'from the 6th century onward',
                        'note' => [
                            [
                                'type' => 'assoc_date',
                                'value' => 'The region around the mountain has been continuously inhabited by the monks of St. Catherine, as well as people in surrounding villages, since the 6th c. CE'
                            ]
                        ]
                    ]
                ],
                'assoc_name' => [
                ],
                'assoc_place' => [
                ],
                'cataloguer' => [
                    [
                        'id' => '$id-for-Will',
                        'timestamp' => '2024-05-21T15:27:32.728Z',
                        'comment' => 'Created record and added metadata'
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
                'pref_name' => $place->pref_name,
            ];

            // update the record with the json field
            $place->json = json_encode($data);
            $place->save();
        }
    }
}
