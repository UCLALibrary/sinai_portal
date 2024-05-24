<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $numRecords = 10;
        for ($index = 0; $index < $numRecords; $index++) {
            $place = Place::factory()->create();

            // retrieve the primary key
            $id = $place->id;

            // create the data array including the primary key
            $data = [
                'id' => $id,
                'type' => $place->type,
                'as_written' => $place->as_written,
                'note' => $place->note
            ];

            // update the record with the json field
            $place->json = json_encode($data);
            $place->save();
        }
    }
}
