<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'id' => 'arab1395',
                'label' => 'Arabic',
                'iso' => 'ara',
                'glottolog' => 'arab1395',
                'writing_systems' => 'Arabic',
                'other_names' => null,
                'when_in_use' => null,
                'regions' => null,
            ],
            [
                'id' => 'clas1252',
                'label' => 'Syriac',
                'iso' => 'syc',
                'glottolog' => 'clas1252',
                'writing_systems' => 'Syriac',
                'other_names' => null,
                'when_in_use' => '1st-13th c. CE ; still in liturgical use',
                'regions' => null,
            ],
            [
                'id' => 'nucl1301',
                'label' => 'Turkish',
                'iso' => 'tur',
                'glottolog' => 'nucl1301',
                'writing_systems' => 'Ottoman Turkish, Roman',
                'other_names' => null,
                'when_in_use' => null,
                'regions' => null,
            ],
            [
                'id' => 'gree1276',
                'label' => 'Greek',
                'iso' => 'grc',
                'glottolog' => 'gree1276',
                'writing_systems' => 'Greek',
                'other_names' => null,
                'when_in_use' => null,
                'regions' => null,
            ],
            [
                'id' => 'chri1239',
                'label' => 'CPA',
                'iso' => null,
                'glottolog' => 'chri1239',
                'writing_systems' => 'Syriac',
                'other_names' => null,
                'when_in_use' => 'ca. 400–1200 CE',
                'regions' => 'Palestine, Transjordan, Sinai',
            ],
            [
                'id' => 'copt1239',
                'label' => 'Coptic',
                'iso' => 'cop',
                'glottolog' => 'copt1239',
                'writing_systems' => 'Coptic',
                'other_names' => null,
                'when_in_use' => 'beginning 3rd c. CE',
                'regions' => 'Egypt',
            ],
            [
                'id' => 'lati1261',
                'label' => 'Latin',
                'iso' => 'lat',
                'glottolog' => 'lati1261',
                'writing_systems' => 'Roman',
                'other_names' => null,
                'when_in_use' => null,
                'regions' => null,
            ],
            [
                'id' => 'nucl1302',
                'label' => 'Georgian',
                'iso' => 'geo',
                'glottolog' => 'nucl1302',
                'writing_systems' => 'Georgian',
                'other_names' => null,
                'when_in_use' => null,
                'regions' => null,
            ],
            [
                'id' => 'chur1257',
                'label' => 'Church Slavonic',
                'iso' => 'chu',
                'glottolog' => 'chur1257',
                'writing_systems' => 'Glagolitic, Cyrillic, Roman',
                'other_names' => 'Old Church Slavic',
                'when_in_use' => null,
                'regions' => null,
            ],
            [
                'id' => 'arme1241',
                'label' => 'Armenian',
                'iso' => 'arm',
                'glottolog' => 'arme1241',
                'writing_systems' => 'Armenian',
                'other_names' => null,
                'when_in_use' => null,
                'regions' => null,
            ],
            [
                'id' => 'geez1241',
                'label' => 'Ge\'ez',
                'iso' => 'gez',
                'glottolog' => 'geez1241',
                'writing_systems' => 'Ge\'ez',
                'other_names' => null,
                'when_in_use' => null,
                'regions' => null,
            ],
            [
                'id' => 'aghw1237',
                'label' => 'Caucasian Albanian',
                'iso' => 'xag',
                'glottolog' => 'aghw1237',
                'writing_systems' => 'Caucasian Albanian',
                'other_names' => 'Old Udi, Alsan, Aghwan',
                'when_in_use' => '6th-8th century CE',
                'regions' => null,
            ],
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }

    }
}
