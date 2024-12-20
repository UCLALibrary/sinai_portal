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
            
            $resultData = [
                'id' => null,
                'ark' => null,
                'event' => $placeData['event'] ?? null,
                'value' => $placeData['value'] ?? null,
                'note' => $placeData['note'] ?? null,
                'as_written' => $placeData['as_written'] ?? null,
            ];
            
            if (isset($placeData['id'])) {
                $placeObject = Place::where('ark', $placeData['id'])->first();
                
                if ($placeObject) {
                    $placeObjectJson = json_decode($placeObject->jsonb, true);
                    
                    $resultData['id'] = $placeObject->id;
                    $resultData['ark'] = $placeObject->id;
                    $resultData['pref_name'] = $placeObjectJson['pref_name'] ?? null;
                }
            }
            
            return $resultData;
        })->filter()->values()->toArray();
    }
}
