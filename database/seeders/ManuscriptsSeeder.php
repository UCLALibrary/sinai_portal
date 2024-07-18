<?php

namespace Database\Seeders;

use App\Models\Manuscript;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManuscriptsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manuscripts = $this->getSyriac12Manuscripts();
        $this->seedManuscripts($manuscripts);

        $this->seedRandomManuscripts(100);
    }

    private function seedManuscripts($manuscripts)
    {
        foreach ($manuscripts as $jsonData) {
            // extract the identifier
            $identifierType = $jsonData['idno'][0]['type'];
            $identifierLabel = $identifierType === 'shelfmark'
                ? 'Shelfmark'
                : ($identifierType === 'part_no'
                    ? 'Part'
                    : 'UTO');
            $identifierValue = $jsonData['idno'][0]['value'];

            // extract and flatten fields from the JSON representation of the metadata to create the record
            $manuscript = Manuscript::create([
                'ark' => $jsonData['ark'],
                'identifier' => $identifierLabel . ': ' . $identifierValue,
                'json' => '{}'
            ]);

            // include the primary key in the JSON representation of the metadata
            $jsonData['id'] = $manuscript->id;

            // update the record with the json field
            $manuscript->json = json_encode($jsonData);
            $manuscript->save();
        }
    }

    private function getSyriac12Manuscripts()
    {
        return [
            [
                'ark' => 'ark:/21198/z15f0f9b',
                'type' => 'shelf',
                'idno' => [
                    [
                        'type' => 'shelfmark',
                        'value' => 'Sinai Syriac 1'
                    ]
                ],
                'summary' => '(1st part) Synaxarion (Gospel Lectionary for the movable feast days according to the Byzantine rite); (2nd part) Gospel of Luke (Peshitta version)',
                'extent' => '146 ff.',
                'weight' => '1287.7 g',
                'dim' => [
                    [
                        'type' => 'ms_block',
                        'value' => '240 x 159 x 81.0 m'
                    ],
                ],
                'state' => 'Codex',
                'form' => 'Codex',
                'fol' => 'Ff. 1, 2a, 2b, 3-44, 45a, 45b, 46-144',
                'features' => [
                    'Inscription(s)'
                ],
                'cod_units' => [
                    1
                ],
                'para' => [
                    [
                        'type' => 'endowment',
                        'locus' => 'F. 1v',
                        'lang' => 'Arabic',
                        'label' => 'Waqf Note',
                        'transcription' => 'وقف الكتاب',
                        'translation' => [
                            [
                                'lang' => 'English',
                                'value' => 'Endowment of the book'
                            ]
                        ],
                        'assoc_name' => [
                            1
                        ],
                        'assoc_place' => [
                            1
                        ],
                        'assoc_date' => [
                            1
                        ],
                        'note' => [
                            [
                                'type' => 'general',
                                'value' => 'A note about the endowment paratex'
                            ]
                        ]
                    ]
                ],
                'has_bind' => true,
                'iiif' => [
                    [
                        'type' => 'main',
                        'manifest' => 'https://ingest.iiif.library.ucla.edu/ark%3A%2F21198%2Fz15f0f9b/manifest',
                        'text_direction' => 'right-to-left',
                        'behavior' => 'paged',
                        'thumbnail' => 'https://iiif.sinaimanuscripts.library.ucla.edu/iiif/2/ark%3A%2F21198%2Fz15f0f9b%2Fp161m45m/full/!200,200/0/default.jp'
                    ]
                ],
                'location' => [
                    [
                        'collection' => 'Old Collection',
                        'repository' => '$id-for-St-Catherine'
                    ]
                ],
                'assoc_date' => [
                    2
                ],
                'note' => [
                    [
                        'type' => 'foliation',
                        'value' => 'Recent foliation in Syriac numerals covering the 1st part (ff. 55–144). The foliation was added after a replacement folio (f. 68) was attached but before two parts were bound togethe'
                    ],
                    [
                        'type' => 'general',
                        'value' => 'Ff. 1-54 are paper, ff. 55-144 are parchmen'
                    ],
                    [
                        'type' => 'paratext',
                        'value' => 'F. 55r is written in Greek minuscule, oriented upside-down relative to the other pages in the manuscrip'
                    ],
                    [
                        'type' => 'binding',
                        'value' => 'Reinforcement strips derive from a Melkite paper manuscript (ff. 58–64, 142–144), a Syriac parchment codex (ff. 64–66, 70–72, 105–106, 110–111, 113–114), and a parchment Arabic (?) codex (ff. 55–56, 57–58, 58–59'
                    ],
                    [
                        'type' => 'collation',
                        'value' => 'Flyleaves: paper flyleaf (f. 1) with writing in Arabic; paper flyleaf (f. 2) with writing in Melkite script'
                    ]
                ],
                'related_mss' => [
                    [
                        'type' => 'filiation',
                        'label' => 'Exemplar for Syriac NF M 39',
                        'note' => [
                            [
                                'type' => 'related_mss',
                                'value' => 'Syriac 12 was used as an exemplar for Syriac NF M 3'
                            ],
                        ],
                        'mss' => [
                            [
                                'id' => '$id-for-nfm39',
                                'label' => 'Sinai Syriac NF M 39',
                                'url' => 'https://sinaimanuscripts.library.ucla.edu/catalog/ark:%2F21198%2Fz1nw19c'
                            ]
                        ]
                    ]
                ],
                'viscodex' => [
                    [
                        'type' => 'ms_obj',
                        'label' => 'Codicological Structure of the Two Parts',
                        'url' => 'https://viscodex.library.utoronto.ca/project/60c18ea186a72250fa7ca176/viewOnl'
                    ]
                ],
                'bib' => [
                    [
                        'id' => '$id-for-Kamil',
                        'type' => 'ref',
                        'range' => '[50], pg. 152',
                        'url' => 'https://search.worldcat.org/title/101411',
                        'alt_shelf' => 'Lorem ipsum',
                        'note' => [
                            [
                                'type' => 'bib',
                                'value' => 'Kamil inadvertently dated this manuscript to the 10th century, not realizing there are two parts'
                            ],
                        ]
                    ],
                    [
                        'id' => '$id-for-LOC',
                        'type' => 'otherdigversion',
                        'range' => 'Entry: Syriac Manuscripts 12. Lectionary and Gospel of Luke',
                        'url' => 'https://www.loc.gov/item/00279386334-ms/',
                        'alt_shelf' => 'Syriac Manuscripts 12',
                        'note' => [
                            [
                                'type' => 'bib',
                                'value' => 'The LOC provides the incorrect date of 7th c. for this manuscript, which is only true of the first part'
                            ]
                        ]
                    ]
                ],
                'cataloguer' => [
                    [
                        'id' => '$id-for-GrigoryKessel',
                        'timestamp' => '2024-03-06T16:42:09.616Z',
                        'comment' => 'Manuscript description by Grigory Kesse'
                    ],
                    [
                        'id' => '$id-for-WillPotter',
                        'timestamp' => '2024-03-06T16:43:13.017Z',
                        'comment' => 'Descriptive metadata entered by Will Potter on behalf of Grigory Kesse'
                    ]
                ]
            ]
        ];
    }

    private function seedRandomManuscripts($numRecords)
    {
        for ($index = 0; $index < $numRecords; $index++) {
            // create a random identifier
            $identifierType = fake()->randomElement(['shelfmark', 'part_no', 'uto_mark']);
            $identifierLabel = $identifierType === 'shelfmark'
                ? 'Shelfmark'
                : ($identifierType === 'part_no'
                    ? 'Part'
                    : ($identifierType === 'uto_mark'
                        ? 'UTO Mark'
                        : ''));
            $identifierValue = 'MS. ' . fake()->numberBetween(10, 99);

            $manuscript = Manuscript::factory()->create([
                'identifier' => $identifierLabel . ': ' . $identifierValue,
            ]);

            // create the data array including the primary key
            $data = [
                'id' => $manuscript->id,
                'ark' => $manuscript->ark,
                'type' => fake()->randomElement(['shelf', 'rebind']),
                'idno' => [
                    [
                        'type' => $identifierType,
                        'value' => $identifierValue,
                    ],
                ],
                'cod_units' => DB::table('parts')->inRandomOrder()->limit(2)->pluck('id'),
                'assoc_date' => DB::table('dates')->inRandomOrder()->limit(3)->pluck('id')
            ];

            // update the record with the json field
            $manuscript->json = json_encode($data);
            $manuscript->save();
        }
    }
}
