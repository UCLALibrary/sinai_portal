<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class WorksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('portal_data/works');

        if (!File::exists($path)) {
            $this->command->error('Works data directory not found at ' . $path);
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
            $pref_title = $data['pref_title'] ?? null;

            if (!$ark || !$pref_title) {
                $this->command->error('Missing "ark", "type", or "pref_title" in file: ' . $file->getFilename());
                continue;
            }

            DB::table('works')->insert([
                'id' => basename($ark),  // use the trailing ark segment as the id
                'ark' => $ark,
                'pref_title' => $pref_title,
                'json' => $jsonContent,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
