<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::create([
			'id' => 'sinai-oc',
			'collection' => 'Old Collection',
			'repository' => 'St. Catherine\'s Monastery of the Sinai',
			'country' => 'Egypt',
		]);

		Location::create([
			'id' => 'sinai-nf',
			'collection' => 'New Finds',
			'repository' => 'St. Catherine\'s Monastery of the Sinai',
			'country' => 'Egypt',
		]);
    }
}
