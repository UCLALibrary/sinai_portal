<?php

namespace App\Traits;

use App\Models\Reference;
use Illuminate\Support\Facades\DB;

trait RelatedBibliographies
{
    public function getReferencesByType(string $table, string $id, string $type): array
    {
        $query = '$.bib[*] ? (@.type.id == "' . $type . '")';
        
        $referencesQuery = DB::table($table)
            ->selectRaw("jsonb_path_query(jsonb, ?) AS reference", [$query])
            ->where('id', $id)
            ->get();
        
        $jsonObjects = $referencesQuery->map(function ($reference) {
            return json_decode($reference->reference, true);
        })->toArray();
        
        return $this->getReferencesByJsonObjects($jsonObjects);
    }
    
    public function getReferencesByJsonObjects(array $jsonObjects): array
    {
        if (empty($jsonObjects)) {
            return [];
        }
        
        $ids = array_column($jsonObjects, 'id');
        
        $references = DB::table('references')
            ->whereIn('id', $ids)
            ->get();
        
        return $references->map(function ($reference) use ($jsonObjects) {
            $referenceData = [
                'id' => $reference->id,
                'short_title' => $reference->short_title,
                'formatted_citation' => $reference->formatted_citation,
            ];
            
            $additionalData = collect($jsonObjects)->firstWhere('id', $reference->id);
            if ($additionalData) {
                $referenceData = array_merge($referenceData, $additionalData);
            }
            
            return $referenceData;
        })->toArray();
    }
}
