<?php

namespace App\Traits;

use App\Models\Work;
use Illuminate\Support\Facades\DB;

trait RelatedWorks
{
    use RelatedAgents;
    
    public function getRelatedWorks(string $table, string $id, string $jsonQuery): array
    {
        $worksQuery = DB::table($table)
            ->selectRaw("jsonb_path_query(jsonb, ?) AS work", [$jsonQuery])
            ->where('id', $id)
            ->get();
        
        return $worksQuery->map(function ($work) {
            $workJsonData = json_decode($work->work, true);
            $workArk = $workJsonData['work']['id'] ?? null;
            $workObject = Work::where('ark', $workArk)->first();
            
            return $this->buildWorkData($workObject, $workJsonData);
        })->filter()->values()->toArray();
    }
    
    public function getWorkByArk(string $ark): ?array
    {
        $workObject = Work::where('ark', $ark)->first();
        return $this->buildWorkData($workObject);
    }
    
    public function getWorksByArks(array $arks): array {
        $workObjects = Work::whereIn('ark', $arks)->get();
        
        return $workObjects->map(function ($workObject) {
            return $this->buildWorkData($workObject);
        })->filter()->values()->toArray();
    }
    
    private function buildWorkData(?Work $workObject, array $workJsonData = null): ?array
    {
        if ($workObject) {
            $workJson = json_decode($workObject->jsonb, true);
            
            return [
                'id' => $workObject->id,
                'ark' => $workJson['ark'] ?? '',
                'pref_title' => $workJson['pref_title'] ?? '',
                'alt_title' => $workJson['alt_title'] ?? '',
                'creator' => $this->getRelatedAgents('works', $workObject->id, '$.creator[*]')
            ];
        }
        
        if ($workJsonData && isset($workJsonData['work']['desc_title'])) {
            return [
                'id' => null,
                'ark' => null,
                'desc_title' => $workJsonData['work']['desc_title'],
                'creator' => $workJsonData['work']['creator'] ?? []
            ];
        }
        
        return null;
    }
}
