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
            $manuscript = Manuscript::factory()->create();

            // retrieve the primary key
            $id = $manuscript->id;

            // create the data array including the primary key
            $data = [
                'id' => $id,
                'ark' => $manuscript->ark,
                'shelfmark' => $manuscript->shelfmark,
                'assoc_date' => DB::table('dates')->inRandomOrder()->limit(3)->pluck('id')
            ];

            // update the record with the json field
            $manuscript->json = json_encode($data);
            $manuscript->save();
        }
    }
}
