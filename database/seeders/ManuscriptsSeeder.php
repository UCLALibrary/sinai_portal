<?php

namespace Database\Seeders;

use App\Models\Manuscript;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ManuscriptsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedManuscripts();
        // $this->seedRandomManuscripts(100);
    }

    private function seedManuscripts()
    {
        $path = database_path('portal_data/manuscripts');

        if (!File::exists($path)) {
            $this->command->error('Manuscripts data directory not found at ' . $path);
            return;
        }

        $files = File::files($path);

        if (empty($files)) {
            $this->command->error('No JSON files found in ' . $path);
            return;
        }

        foreach ($files as $file) {
            $jsonContent = File::get($file->getRealPath());
            $data = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->command->error('Error decoding JSON from file: ' . $file->getFilename());
                continue;
            }

            $ark = $data['ark'] ?? null;
            $type = $data['type']['label'] ?? null;
            $identifier = $data['shelfmark'] ?? null;

            if (!$ark || !$type || !$identifier) {
                $this->command->error('Missing "ark", "type", or "identifier" in file: ' . $file->getFilename());
                continue;
            }
            
            $arkSegments = explode('/', $ark);

            DB::table('manuscripts')->insert([
                'id' => end($arkSegments), // use the last part of the ARK as the id
                'ark' => $ark,
                'type' => $type,
                'identifier' => $identifier,
                'json' => $jsonContent,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
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
            ];

            // update the record with the json field
            $manuscript->json = json_encode($data);
            $manuscript->save();
        }
    }
}
