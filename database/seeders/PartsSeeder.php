<?php

namespace Database\Seeders;

use App\Models\Part;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $numRecords = 3;
        for ($index = 0; $index < $numRecords; $index++) {
            $part = Part::factory()->create();

            // create the data array including the primary key
            $data = [
                'id' => $part->id,
                'ark' => $part->ark,
                'identifier' => $part->identifier,
                'summary' => fake()->sentence(),
                'locus' => 'f. ' . fake()->randomNumber(),
                'assoc_date' => DB::table('dates')->inRandomOrder()->limit(3)->pluck('id'),
            ];

            // update the record with the json field
            $part->json = json_encode($data);
            $part->save();
        }
    }
}
