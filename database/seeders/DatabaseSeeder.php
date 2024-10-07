<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersSeeder::class);
        $this->call(ManuscriptsSeeder::class);
        $this->call(PartsSeeder::class);
        $this->call(WorksSeeder::class);
        $this->call(AgentsSeeder::class);
        $this->call(PlacesSeeder::class);
        $this->call(BibliographySeeder::class);
        $this->call(LanguagesSeeder::class);
        $this->call(ReferencesSeeder::class);
        $this->call(FormContextsSeeder::class);
        $this->call(FeaturesSeeder::class);
		$this->call(RolesPermissionsSeeder::class);
    }
}
