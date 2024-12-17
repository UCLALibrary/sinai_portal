<?php

namespace App\Traits;

use App\Models\Layer;
use Illuminate\Support\Facades\DB;

trait RelatedLayers
{
    use RelatedTextUnits, RelatedPlaces;
    
    public function getRelatedLayersWithTextUnits(string $table, string $id, string $jsonQuery): array
    {
        $layersQuery = DB::table($table)
            ->selectRaw("jsonb_path_query(jsonb, ?) AS layer", [$jsonQuery])
            ->where('id', $id)
            ->get();
        
        return $layersQuery->map(function ($layer) {
            $layerJson = json_decode($layer->layer, true);
            if (!$layerJson || !isset($layerJson['id'])) {
                return null;
            }
            
            $parentLabel = $layerJson['label'] ?? null;
            $parentLocus = $layerJson['locus'] ?? null;
            
            $layer = Layer::where('ark', $layerJson['id'])->first();
            $layerData = array();
            if ($layer) {
                $layerData = $this->buildLayerData($layer);
            }
            
            return array_merge(
                $layerData,
                [
                    'parentLabel' => $parentLabel,
                    'parentLocus' => $parentLocus,
                ]
            );
        })->filter()->toArray();
    }
    
    public function getLayersByArks(array $arks): array {
        if (empty($arks)) {
            return [];
        }
        
        $layers = DB::table('layers')
            ->whereIn('ark', $arks)
            ->get();
        
        return $layers->map(function ($layer) {
            return $this->buildLayerData($layer);
        })->toArray();
    }
    
    private function buildLayerData($layer): array
    {
        $layerJson = json_decode($layer->jsonb, true);
        
        $data = array_merge(
            $layerJson,
            [
                'id' => $layer->id,
                'ark' => $layer->ark,
                'text_units' => $this->getRelatedTextUnits('layers', $layer->id, '$.text_unit[*]'),
            ]
        );
        
        if (!empty($layerJson['assoc_place'])) {
            $data['assoc_place'] = $this->getRelatedPlaces('layers', $layer->id, '$.assoc_place[*]');
        }
        
        return $data;
    }
}