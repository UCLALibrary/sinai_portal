<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TextUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
	    $this->seedTextUnits();
    }
	
	private function seedTextUnits()
	{
		$path = database_path('portal_data/text_units');
		
		if (!File::exists($path)) {
			$this->command->error('Text units data directory not found at ' . $path);
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
			$label = $data['label'] ?? null;
			
			if (!$ark || !$label) {
				$this->command->error('Missing "ark" or "label" in file: ' . $file->getFilename());
				continue;
			}
			
			DB::table('text_units')->insert([
				'id' => explode('/', $data['ark'])[2],
				'ark' => $ark,
				'label' => $label,
				'json' => $jsonContent,
				'created_at' => now(),
				'updated_at' => now(),
			]);
		}
		
	}
}
