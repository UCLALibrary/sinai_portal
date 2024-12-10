<?php

namespace App\Traits;

use App\Models\Work;
use Illuminate\Support\Facades\DB;

trait RelatedWorks
{
    public function getRelatedWorks(string $table, string $id, string $jsonQuery): array
    {
        $worksQuery = DB::table($table)
            ->selectRaw("jsonb_path_query(jsonb, ?) AS work", [$jsonQuery])
            ->where('id', $id)
            ->get();
        
        return $worksQuery->map(function ($work) {
            $workData = json_decode($work->work, true);
            $workId = $workData['work']['id'] ?? null; // Extract work.id
            $workObject = Work::where('ark', $workId)->first();
            
            if ($workObject) {
                $workJson = json_decode($workObject->jsonb, true);
                
                return [
                    'id' => $workObject->id,
                    'ark' => $workJson['ark'] ?? '',
                    'pref_title' => $workJson['pref_title'] ?? ''
                ];
            }
            
            if (isset($workData['work']['desc_title'])) {
                return [
                    'id' => null,
                    'ark' => null,
                    'desc_title' => $workData['work']['desc_title'] ?? '',
                ];
            }
            
            return null;
        })->filter()->values()->toArray();
    }
    
}
