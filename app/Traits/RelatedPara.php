<?php

namespace App\Traits;

use App\Models\Agent;
use App\Models\Place;
use Illuminate\Support\Facades\DB;

trait RelatedPara
{
    public function getParaByQuery(string $table, string $id, string $query = null): array
    {
        $paraQuery = DB::table($table)
            ->selectRaw("jsonb_path_query(jsonb, ?) AS para", [$query])
            ->where('id', $id)
            ->get();
        
        return $paraQuery->map(function ($para) {
            $paraData = json_decode($para->para, true);
            
            if (isset($paraData['assoc_name']) && is_array($paraData['assoc_name'])) {
                foreach ($paraData['assoc_name'] as &$assocName) {
                    if (isset($assocName['id'])) {
                        $agent = Agent::where('ark', $assocName['id'])->first();
                        $assocName['pref_name'] = $agent ? $agent->pref_name : null;
                    } else {
                        $assocName['pref_name'] = $assocName['value'] ?? null;
                    }
                }
            }
            
            if (isset($paraData['assoc_place']) && is_array($paraData['assoc_place'])) {
                foreach ($paraData['assoc_place'] as &$assocPlace) {
                    if (isset($assocPlace['id'])) {
                        $place = Place::where('ark', $assocPlace['id'])->first();
                        $assocPlace['pref_name'] = $place ? $place->pref_name : null;
                    } else {
                        $assocPlace['pref_name'] = $assocPlace['value'] ?? null;
                    }
                }
            }
            
            return $paraData;
        })->toArray();
    }
}