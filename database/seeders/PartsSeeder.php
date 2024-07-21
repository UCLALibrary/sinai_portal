<?php

namespace Database\Seeders;

use App\Models\Part;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parts = $this->getSyriac12Parts();
        $this->seedParts($parts);

        $this->seedRandomManuscripts(100);
    }

    private function seedParts($parts)
    {
        foreach ($parts as $jsonData) {
            // extract the identifier
            $identifierType = $jsonData['idno'][0]['type'];
            $identifierLabel = $identifierType === 'shelfmark'
                ? 'Shelfmark'
                : ($identifierType === 'part_no'
                    ? 'Part'
                    : ($identifierType === 'uto_mark'
                        ? 'UTO Mark'
                        : ''));
            $identifierValue = $jsonData['idno'][0]['value'];

            // extract and flatten fields from the JSON representation of the metadata to create the record
            $part = Part::create([
                'ark' => $jsonData['ark'],
                'identifier' => $identifierLabel . ': ' . $identifierValue,
                'json' => '{}'
            ]);

            // include the primary key in the JSON representation of the metadata
            $jsonData['id'] = $part->id;

            // update the record with the json field
            $part->json = json_encode($jsonData);
            $part->save();
        }
    }

    private function getSyriac12Parts()
    {
        return [
            [
                'ark' => 'ark://21198/z1999999',
                'ms_objs' => [
                    1
                ],
                'type' => 'part',
                'idno' => [
                    [
                        'type' => 'part_no',
                        'value' => 'Syriac 12, Part 1, ff. 1-54'
                    ]
                ],
                'summary' => 'The first part of Syriac 12 is a 13th c. paper manuscript containing a Synaxarion according to the Byzantine rite',
                'locus' => 'Ff. 1r-54v',
                'support' => [
                    'Paper'
                ],
                'extent' => '54 ff.',
                'dim' => [
                    [
                        'type' => 'av_folio',
                        'value' => '130 x 45 mm'
                    ]
                ],
                'fol' => 'Ff. 1, 2a, 2b, 3-45a, 45b-54',
                'coll' => [
                    '<I>: ff. 3–10; <II>: ff. 11–16 (f. 11 may be a replacement); <III>:	ff. 17–23 (one f. missing after f. 17); IV: ff. 24–31; V: ff. 32–39; VI: ff. 40–46 (ff. 45a, 45b); VII: ff. 47–54'
                ],
                'writing' => [
                    [
                        'script' => [
                            'Melkite'
                        ],
                        'locus' => 'ff. 3r-54v',
                        'note' => [
                            [
                                'type' => 'writing',
                                'value' => 'The writing shows elements of a transitional Melkite script'
                            ]
                        ]
                    ]
                ],
                'content_units' => [],
                'para' => [],
                'cod_units' => [],
                'layout' => [
                    [
                        'locus' => 'ff. 3r-54v',
                        'columns' => '1',
                        'lines' => '15',
                        'dim' => [
                            [
                                'type' => 'writing-area',
                                'value' => '105 x 35 mm'
                            ]
                        ],
                        'notes' => [
                            [
                                'type' => 'layout',
                                'value' => 'Layout is regular, some evidence for pricking, ff. 4r'
                            ]
                        ]
                    ]
                ],
                'assoc_name' => [
                    2
                ],
                'assoc_place' => [
                    2
                ],
                'assoc_date' => [
                    3
                ],
                'features' => [
                    'Headpiece(s)',
                    'Decoration, Geometric',
                    'Signatures'
                ],
                'note' => [
                    [
                        'type' => 'foliaton',
                        'value' => 'Ff. 1-2b are paper flyleaves'
                    ],
                    [
                        'type' => 'coll',
                        'value' => 'Quire signatures are marked in the bottom margin on the recto side of the first folio of a quire; quires of 8 ff. with the exception of quire II (6 ff.)'
                    ],
                    [
                        'type' => 'ornamentation',
                        'value' => 'Geometric headpieces throughout'
                    ],
                    [
                        'type' => 'condition',
                        'value' => 'F. 11 may be a replacement; a folio is missing after f. 17'
                    ],
                    [
                        'type' => 'provenance',
                        'value' => 'This book appears to have been purchased by the Monastery before being bound with the second part of Syriac 12'
                    ],
                    [
                        'type' => 'admin',
                        'value' => 'Fly-leaves may need re-imaging with white backing'
                    ]
                ],
                'related_mss' => [
                    [
                        'type' => 'disjecta',
                        'label' => 'Disjecta in Syriac NF X 43',
                        'note' => [
                            [
                                'type' => 'related_mss',
                                'value' => 'The first part of Syriac 12 has a few missing folios that may be found in NF X 43'
                            ]
                        ],
                        'mss' => [
                            [
                                'label' => 'Syriac NF X 43',
                                'id' => '$local-id-for-nfx-43',
                                'url' => 'https://sinaimanuscripts.library.ucla.edu/catalog/ark:%2F21198%2Fz15f0f9b'
                            ]
                        ]
                    ]
                ],
                'viscodex' => [
                    [
                        'type' => 'core',
                        'label' => 'Viscodex for Syriac 12, part 1',
                        'url' => 'https://viscodex.library.utoronto.ca/project/60c18ea186a72250fa7ca176'
                    ]
                ],
                'bib' => [
                    3
                ],
                'cataloguer' => [
                    [
                        'id' => '$id-for-Grigory',
                        'timestamp' => '2024-05-16T15:27:32.728Z',
                        'comment' => 'Created record and added metadata'
                    ]
                ],
                'iiif' => [
                    [
                        'type' => 'reordered',
                        'manifest' => 'https://ingest.iiif.library.ucla.edu/ark%3A%2F21198%2Fz15f0f9b/manifest',
                        'text_direction' => 'right-to-left',
                        'behavior' => 'paged',
                        'thumbnail' => 'https://iiif.sinaimanuscripts.library.ucla.edu/iiif/2/ark%3A%2F21198%2Fz15f0f9b%2Fp161m45m/full/!200,200/0/default.jpg'
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

            $part = Part::factory()->create([
                'identifier' => $identifierLabel . ': ' . $identifierValue
            ]);

            // create the data array including the primary key
            $data = [
                'id' => $part->id,
                'ark' => $part->ark,
                'idno' => [
                    [
                        'type' => $identifierType,
                        'value' => $identifierValue,
                    ],
                ],
                'summary' => fake()->sentence(),
                'locus' => 'f. ' . fake()->randomNumber(),
                'assoc_date' => DB::table('dates')->inRandomOrder()->limit(3)->pluck('id'),
            ];

            // update the record with the json field
            $part->json = json_encode($data);
            $part->save();
        }
    }
}
