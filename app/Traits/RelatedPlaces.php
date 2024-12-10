<?php

namespace App\Traits;

use App\Models\Place;
use Illuminate\Support\Facades\DB;

trait RelatedPlaces
{
    public function getRelatedPlaces(string $table, string $id, string $jsonQuery): array
    {
        $placesQuery = DB::table($table)
            ->selectRaw("jsonb_path_query(jsonb, ?) AS place", [$jsonQuery])
            ->where('id', $id)
            ->get();
        
        return $placesQuery->map(function ($place) {
            $placeData = json_decode($place->place, true);
            $placeObject = Place::where('ark', $placeData['id'])->first();
            
            if ($placeObject) {
                $placeJson = json_decode($placeObject->jsonb, true);
                
                return [
                    'id' => $placeObject->id,
                    'ark' => $placeJson['ark'] ?? '',
                    'event' => [
                        'id' => $placeData['event']['id'] ?? ''
                    ],
                    'pref_name' => $placeJson['pref_name'] ?? ''
                ];
            }
            
            return null;
        })->filter()->values()->toArray();
    }
}
