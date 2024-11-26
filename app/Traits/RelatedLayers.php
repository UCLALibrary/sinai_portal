<?php

namespace App\Traits;

use App\Models\Layer;
use Illuminate\Support\Facades\DB;

trait RelatedLayers
{
    use RelatedTextUnits;
    
    public function getRelatedLayersWithTextUnits(string $table, string $id, string $jsonQuery): array
    {
        $layersQuery = DB::table($table)
            ->selectRaw("jsonb_path_query(jsonb, '$jsonQuery') AS layer")
            ->where('id', $id)
            ->get();
        
        return $layersQuery->map(function ($layer) {
            $manuscriptLayerJson = json_decode($layer->layer, true);
            $layer = Layer::where('ark', $manuscriptLayerJson['id'])->first();
            
            if (!$layer) {
                return null;
            }
            
            $layerJson = json_decode($layer->jsonb, true);
            
            return [
                'id' => $layer->id,
                'ark' => $layer->ark,
                'label' => $layerJson['label'] ?? null,
                'text_units' => $this->getRelatedTextUnits('layers', $layer->id, '$.text_unit[*]'),
            ];
        })->filter()->toArray();
    }
}
