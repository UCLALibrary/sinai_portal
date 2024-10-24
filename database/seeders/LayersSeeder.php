<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LayersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedLayers();
    }

    private function seedLayers()
    {
        $path = database_path('portal_data/layers');

        if (!File::exists($path)) {
            $this->command->error('Layers data directory not found at ' . $path);
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
            $identifier = $data['label'] ?? null;

            if (!$ark || !$identifier) {
                $this->command->error('Missing "ark" or "identifier" in file: ' . $file->getFilename());
                continue;
            }
            
            DB::table('layers')->insert([
                'id' => basename($ark),  // use the trailing ark segment as the id
                'ark' => $ark,
                'identifier' => $identifier,
                'json' => $jsonContent,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

}
