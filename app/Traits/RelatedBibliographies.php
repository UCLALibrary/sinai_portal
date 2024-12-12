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
        
        return $referencesQuery->map(function ($reference) {
            $referenceData = json_decode($reference->reference, true);
            
            if (isset($referenceData['id'])) {
                $ref = Reference::where('id', $referenceData['id'])->first();
                if ($ref) {
                    $referenceData['short_title'] = $ref->short_title;
                    $referenceData['formatted_citation'] = $ref->formatted_citation;
                }
            }
            
            $referenceData['alt_shelf'] = $referenceData['alt_shelf'] ?? null;
            $referenceData['range'] = $referenceData['range'] ?? null;
            $referenceData['url'] = $referenceData['url'] ?? null;
            $referenceData['note'] = $referenceData['note'] ?? [];
            
            return $referenceData;
        })->toArray();
    }
    
}