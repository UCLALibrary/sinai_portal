<?php

namespace App\Traits;

use App\Models\TextUnit;
use Illuminate\Support\Facades\DB;

trait RelatedTextUnits
{
    public function getRelatedTextUnits(string $table, string $id, string $jsonQuery): array
    {
        $textUnitsQuery = DB::table($table)
            ->selectRaw("jsonb_path_query(jsonb, '$jsonQuery') AS text_unit")
            ->where('id', $id)
            ->get();
        
        return $textUnitsQuery->map(function ($textUnit) {
            $textUnitData = json_decode($textUnit->text_unit, true);
            $textUnitObject = TextUnit::where('ark', $textUnitData['id'])->first();
            
            if ($textUnitObject) {
                $textUnitJson = json_decode($textUnitObject->jsonb, true);
                
                return [
                    'id' => $textUnitObject->id,
                    'ark' => $textUnitObject->ark,
                    'label' => $textUnitJson['label'] ?? '',
                    'locus' => $textUnitJson['locus'] ?? '',
                    'lang' => $textUnitJson['lang'] ?? [],
                    'work_wit' => $textUnitJson['work_wit'] ?? [],
                ];
            }
            
            return null;
        })->filter()->values()->toArray();
    }
}
