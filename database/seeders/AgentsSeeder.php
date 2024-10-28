<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AgentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('portal_data/agents');

        if (!File::exists($path)) {
            $this->command->error('Agents data directory not found at ' . $path);
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
            $pref_name = $data['pref_name'] ?? null;

            if (!$ark || !$type || !$pref_name) {
                $this->command->error('Missing "ark", "type", or "pref_name" in file: ' . $file->getFilename());
                continue;
            }

            DB::table('agents')->insert([
                'id' => basename($ark),  // use the trailing ark segment as the id
                'ark' => $ark,
                'type' => $type,
                'pref_name' => $pref_name,
                'json' => $jsonContent,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

