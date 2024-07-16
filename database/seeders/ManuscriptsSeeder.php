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
        $numRecords = 3;
        for ($index = 0; $index < $numRecords; $index++) {
            // create a random identifier
            $identifierType = fake()->randomElement(['shelfmark', 'part_no', 'uto_mark']);
            $identifierLabel = $identifierType === 'shelfmark'
                ? 'Shelfmark'
                : ($identifierType === 'part_no'
                    ? 'Part'
                    : 'UTO');
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
