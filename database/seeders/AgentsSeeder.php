<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agents = $this->getSyriac12Agents();
        $this->seedAgents($agents);
    }

    private function seedAgents($agents)
    {
        foreach ($agents as $jsonData) {
            // extract and flatten fields from the JSON representation of the metadata to create the record
            $agent = Agent::create([
                'type' => $jsonData['type'],
                'pref_name' => $jsonData['pref_name'],
                'json' => '{}'
            ]);

            // include the primary key in the JSON representation of the metadata
            $jsonData['id'] = $agent->id;

            // update the record with the json field
            $agent->json = json_encode($jsonData);
            $agent->save();
        }
    }

    private function getSyriac12Agents()
    {
        return [
            [
                'type' => 'individual',
                'pref_name' => 'John, Climacus, Saint, active 6th century',
                'alt_name' => [
                    [
                        'lang' => 'English',
                        'value' => 'John Climacus'
                    ]
                ],
                'desc' => 'John Climacus was a monastic author active in the 6th century CE at Mt. Sinai, known for his ascetic treatise The Ladder of Divine Ascent.',
                'gender' => 'male',
                'rel_con' => [
                    [
                        'label' => 'John, Climacus, Saint, active 6th century',
                        'uri' => 'http://viaf.org/viaf/100159871',
                        'source' => 'VIAF'
                    ],
                    [
                        'label' => 'Iohannes Climacus',
                        'uri' => 'https://pinakes.irht.cnrs.fr/notices/auteur/1452/',
                        'source' => 'Pinakes'
                    ],
                    [
                        'label' => 'John, Climacus, active 6th century',
                        'uri' => 'https://w3id.org/haf/person/224438251608',
                        'source' => 'HAF'
                    ],
                    [
                        'label' => 'John, Climacus, Saint, active 6th century',
                        'uri' => 'https://id.loc.gov/authorities/names/n81018313',
                        'source' => 'LOC'
                    ],
                    [
                        'label' => 'John Climacus',
                        'uri' => 'http://syriaca.org/person/574',
                        'source' => 'Syriaca'
                    ]
                ],
                'bib' => [
                    4,
                ],
                'note' => [
                    [
                        'type' => 'admin',
                        'value' => 'Perhaps add associated place of St. Catherine\'s, where he was a monk'
                    ]
                ],
                'assoc_date' => [
                    [
                        'type' => 'floruit',
                        'iso' => [
                            'notBefore' => '0575',
                            'notAfter' => '0725'
                        ],
                        'value' => 'ca. 6th century',
                        'note' => [
                            [
                                'type' => 'assoc_date',
                                'value' => 'Active in the sixth century, though little is known of his life'
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
}

