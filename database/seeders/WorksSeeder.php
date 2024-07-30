<?php

namespace Database\Seeders;

use App\Models\Work;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $works = $this->getWorks();
        $this->seedWorks($works);
    }

    private function seedWorks($works)
    {
        foreach ($works as $jsonData) {
            // extract and flatten fields from the JSON representation of the metadata to create the record
            $work = Work::create([
                'pref_title' => $jsonData['pref_title'],
                'json' => '{}'
            ]);

            // include the primary key in the JSON representation of the metadata
            $jsonData['id'] = $work->id;

            // update the record with the json field
            $work->json = json_encode($jsonData);
            $work->save();
        }
    }

    private function getWorks()
    {
        return [
            [
                'pref_title' => 'The Ladder of Divine Ascent',
                'orig_lang' => [
                    'Greek'
                ],
                'orig_lang_title' => [
                    'Κλῖμαξ'
                ],
                'alt_title' => [
                    [
                        'lang' => 'Latin',
                        'value' => 'Scala Paradisi'
                    ]
                ],
                'desc' => 'The Ladder of Divine Ascent is an ascetic treatise written at St. Catherine\'s Monastery in the early seventh century.',
                'genre' => [
                    'Theological works'
                ],
                'excerpt' => [
                    [
                        'type' => 'incipit',
                        'transcription' => 'Τοῦ ἀγαθοῦ καὶ ὑπεραγάθου καὶ παναγάθου',
                        'translation' => [
                            [
                                'lang' => 'English',
                                'value' => 'This is the incipit for Scala paradisi'
                            ]
                        ],
                        'note' => [
                            [
                                'type' => 'excerpt',
                                'value' => 'Transcription of incipit as found for CPG 7852'
                            ]
                        ]
                    ]
                ],
                'rel_con' => [
                    [
                        'label' => 'John, Climacus, Saint, active 6th century. | Scala Paradisi',
                        'uri' => 'https://viaf.org/viaf/313377307',
                        'source' => 'VIAF'
                    ],
                    [
                        'label' => 'Iohannes Climacus, Scala paradisi',
                        'uri' => 'https://pinakes.irht.cnrs.fr/notices/oeuvre/12161/',
                        'source' => 'Pinakes'
                    ],
                    [
                        'label' => 'John, Climacus, Saint, active 6th century. Scala paradisi',
                        'uri' => 'https://w3id.org/haf/work/772036848335',
                        'source' => 'HAF'
                    ]
                ],
                'bib' => [
                    6
                ],
                'note' => [
                    [
                        'type' => 'admin',
                        'value' => 'Add John Climacus record as author of this work'
                    ]
                ],
                'assoc_date' => [
                    [
                        'type' => 'creation',
                        'iso' => [
                            'notBefore' => '0575',
                            'notAfter' => '0625'
                        ],
                        'value' => 'ca. 600 CE',
                        'note' => [
                            [
                                'type' => 'assoc_date',
                                'value' => 'Most likely written at the turn of the 7th century.'
                            ]
                        ]
                    ]
                ],
                'assoc_name' => [
                ],
                'assoc_place' => [
                ],
                'rel_work' => [
                    [
                        'type' => 'hasVersion',
                        'id' => 'id-for-Syriac-version-of-the-Ladder',
                        'note' => [
                            [
                                'type' => 'rel_work',
                                'value' => 'The Ladder has a Syriac recension, several manuscripts of which are found in the Sinai collections'
                            ]
                        ]
                    ]
                ],
                'cataloguer' => [
                    [
                        'id' => '$id-for-Maroun',
                        'timestamp' => '2024-05-21T15:27:32.728Z',
                        'comment' => 'Created record and added metadata'
                    ]
                ]
            ]
        ];
    }
}
