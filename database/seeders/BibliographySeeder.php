<?php

namespace Database\Seeders;

use App\Models\Bibliography;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BibliographySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bibliography = $this->getSyriac12Bibliography();
        $this->seedBibliography($bibliography);

    }

    private function seedBibliography($bibliography)
    {
        foreach ($bibliography as $jsonData) {
            // extract and flatten fields from the JSON representation of the metadata to create the record
            $bibliography = Bibliography::create([
                'type' => $jsonData['type'],
                'range' => $jsonData['range'],
                'alt_shelf' => $jsonData['alt_shelf'],
                'json' => '{}'
            ]);

            // include the primary key in the JSON representation of the metadata
            $jsonData['id'] = $bibliography->id;

            // update the record with the json field
            $bibliography->json = json_encode($jsonData);
            $bibliography->save();
        }
    }

    private function getSyriac12Bibliography()
    {
        return [
            [
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
            ],
            [
                'type' => 'ref',
                'range' => '[50], pg. 152',
                'alt_shelf' => 'VALUE',
                'url' => 'https://viscodex.library.utoronto.ca/project/60c18ea186a72250fa7ca176',
                'note' => [
                    [
                        'type' => 'bib',
                        'value' => 'Kamil incorrectly dates this manuscript to the 10th century'
                    ]
                ]
            ],
        ];
    }
}
