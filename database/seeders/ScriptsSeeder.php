<?php

namespace Database\Seeders;

use App\Models\Script;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScriptsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scripts = [
            [
                'id' => 'perlschrift',
                'label' => 'Perlschrift',
                'writing_system' => 'Greek',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'minuscule-bouclee',
                'label' => 'Minuscule bouclée',
                'writing_system' => 'Greek',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'naskh',
                'label' => 'Naskh',
                'writing_system' => 'Arabic',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'nastaliq',
                'label' => 'Nastaliq',
                'writing_system' => 'Arabic',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'kufic',
                'label' => 'Kufic',
                'writing_system' => 'Arabic',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'thuluth',
                'label' => 'Thuluth',
                'writing_system' => 'Arabic',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'arabic-undetermined',
                'label' => 'Undetermined (Arabic)',
                'writing_system' => 'Arabic',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'erkatagir',
                'label' => 'Erkatagir',
                'writing_system' => 'Armenian',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'geez-abugida',
                'label' => 'Geʽez abugida',
                'writing_system' => 'Ge\'ez',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'geez-abjad',
                'label' => 'Geʽez abjad',
                'writing_system' => 'Ge\'ez',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'asomtavruli',
                'label' => 'Asomtavruli',
                'writing_system' => 'Georgian',
                'when_in_use' => '',
                'region' => '',
                'notes' => 'often boustrophedon: written bi-directional ; In Nuskhuri manuscripts, Asomtavruli are used for titles and illuminated capitals - The combination is called Khutsuri',
            ],
            [
                'id' => 'nuskhurimt',
                'label' => 'Nuskhurimt',
                'writing_system' => 'Georgian',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'mkhedruli',
                'label' => 'Mkhedruli',
                'writing_system' => 'Georgian',
                'when_in_use' => '',
                'region' => '',
                'notes' => 'Secular only, no church',
            ],
            [
                'id' => 'khutsuri',
                'label' => 'Khutsuri',
                'writing_system' => 'Georgian',
                'when_in_use' => '',
                'region' => '',
                'notes' => 'combo of Asomtavruli and Nuskhuir (caps and lowercase)',
            ],
            [
                'id' => 'glagolitic',
                'label' => 'Glagolitic',
                'writing_system' => 'Glagolitic',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'greek-maj',
                'label' => 'Greek majuscule',
                'writing_system' => 'Greek',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'greek-min',
                'label' => 'Greek minuscule',
                'writing_system' => 'Greek',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'carolingian-maj',
                'label' => 'Carolingian majuscule',
                'writing_system' => 'Roman',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'carolingian-min',
                'label' => 'Carolingian minuscule',
                'writing_system' => 'Roman',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'east-syriac',
                'label' => 'East Syriac',
                'writing_system' => 'Syriac',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'cpa',
                'label' => 'Christian Palestinian Aramaic',
                'writing_system' => 'Syriac',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'serto',
                'label' => 'Serto',
                'writing_system' => 'Syriac',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'estrangela',
                'label' => 'Estrangela',
                'writing_system' => 'Syriac',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'melkite',
                'label' => 'Melkite',
                'writing_system' => 'Syriac',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
            [
                'id' => 'syriac-undetermined',
                'label' => 'Undetermined (Syriac)',
                'writing_system' => 'Syriac',
                'when_in_use' => '',
                'region' => '',
                'notes' => '',
            ],
        ];

        foreach ($scripts as $script) {
            Script::create($script);
        }

    }
}
