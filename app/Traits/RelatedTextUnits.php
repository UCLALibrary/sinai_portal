<?php

namespace App\Traits;

use App\Models\TextUnit;
use Illuminate\Support\Facades\DB;

trait RelatedTextUnits
{
    use RelatedWorks;
    
    public function getRelatedTextUnits(string $table, string $id, string $jsonQuery): array
    {
        $textUnitsQuery = DB::table($table)
            ->selectRaw("jsonb_path_query(jsonb, ?) AS text_unit", [$jsonQuery])
            ->where('id', $id)
            ->get();
        
        return $textUnitsQuery->map(function ($textUnit) {
            $textUnitData = json_decode($textUnit->text_unit, true);
            return $this->buildTextUnitData($textUnitData);
        })->filter()->values()->toArray();
    }
    
    public function getTextUnitsByArks(array $arks): array
    {
        if (empty($arks)) {
            return [];
        }
        
        $textUnits = DB::table('text_units')
            ->whereIn('ark', $arks)
            ->get();
        
        return $textUnits->map(function ($textUnit) {
            $textUnitData = json_decode($textUnit->jsonb, true);
            return $this->buildTextUnitData($textUnitData, $textUnit);
        })->filter()->toArray();
    }
    
    private function buildTextUnitData(array $textUnitData, $textUnitObject = null): array
    {
        $resultData = [
            'id' => $textUnitObject->id ?? null,
            'ark' => $textUnitObject->ark ?? ($textUnitData['id'] ?? null),
            'label' => $textUnitData['label'] ?? null,
            'locus' => $textUnitData['locus'] ?? '',
            'text_unit' => [
                'label' => $textUnitData['label'] ?? null,
                'locus' => $textUnitData['locus'] ?? null,
                'lang' => $textUnitData['lang'] ?? null,
            ],
        ];
        
        if (isset($textUnitData['id'])) {
            $textUnitObject = $textUnitObject ?? TextUnit::where('ark', $textUnitData['id'])->first();
            
            if ($textUnitObject) {
                $textUnitObjectJson = json_decode($textUnitObject->jsonb, true);
                
                $resultData['id'] = $textUnitObject->id;
                $resultData['ark'] = $textUnitObject->ark;
                $resultData['text_unit']['label'] = $textUnitObjectJson['label'] ?? null;
                $resultData['text_unit']['locus'] = $textUnitObjectJson['locus'] ?? null;
                $resultData['text_unit']['lang'] = $textUnitObjectJson['lang'] ?? null;
                
                if (!empty($textUnitObjectJson['work_wit'])) {
                    $resultData['text_unit']['work_wit'] = $this->getRelatedWorks('text_units', $textUnitObject->id, '$.work_wit[*]');
                }
            }
        }
        
        return $resultData;
    }
}