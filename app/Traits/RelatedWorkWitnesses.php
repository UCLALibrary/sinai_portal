<?php

namespace App\Traits;

use App\Models\Work;
use Illuminate\Support\Facades\DB;

trait RelatedWorkWitnesses
{
    use RelatedWorks;
    
    public function getRelatedWorkWitnesses(string $table, string $id, string $jsonQuery): array
    {
        $workWitnessesQuery = DB::table($table)
            ->selectRaw("jsonb_path_query(jsonb, ?) AS work_witness", [$jsonQuery])
            ->where('id', $id)
            ->get();
        
        return $workWitnessesQuery->map(function ($witness) use ($id) {
            $witnessJsonData = json_decode($witness->work_witness, true);
            $witnessData = [];
            
            if (isset($witnessJsonData['work']['id'])) {
                
                $work = Work::where('ark', $witnessJsonData['work']['id'])->first();
                $workJsonData = json_decode($work->jsonb, true);
                
                $witnessData['title'] = $workJsonData['pref_title'] ?? null;
                $witnessData['work'] = $this->getRelatedWorks('text_units', $id, '$.work_wit[*] ? (@.work.id == "' . $workJsonData['ark'] .'")')[0];
                
            } elseif (isset($witnessJsonData['work']['desc_title'])) {
                $witnessData['title'] = $witnessJsonData['work']['desc_title'];
            }
            
            if (isset($witnessJsonData['locus'])) {
                $witnessData['locus'] = $witnessJsonData['locus'];
            }
            
            if (isset($witnessJsonData['as_written'])) {
                $witnessData['as_written'] = $witnessJsonData['as_written'];
            }
            
            if (isset($witnessJsonData['excerpt'])) {
                $excerptOrder = [
                    'inc-mut',
                    'prologue',
                    'incipit',
                    'quote',
                    'explicit',
                    'des-mut'
                ];
                
                usort($witnessJsonData['excerpt'], function ($a, $b) use ($excerptOrder) {
                    $aIndex = array_search($a['type']['id'], $excerptOrder);
                    $bIndex = array_search($b['type']['id'], $excerptOrder);
                    
                    return $aIndex <=> $bIndex;
                });
                
                $witnessData['excerpt'] = $witnessJsonData['excerpt'];
            }
            
            if (isset($witnessJsonData['note'])) {
                $witnessData['note'] = $witnessJsonData['note'];
            }
            
            return $witnessData;
        })->filter()->values()->toArray();
    }
}
