<?php

namespace App\Traits;

use App\Models\Agent;
use Illuminate\Support\Facades\DB;

trait RelatedAgents
{
    public function getRelatedAgents(string $table, string $id, string $jsonQuery): array
    {
        $agentsQuery = DB::table($table)
            ->selectRaw("jsonb_path_query(jsonb, ?) AS agent", [$jsonQuery])
            ->where('id', $id)
            ->get();
        
        $agents = $agentsQuery->map(function ($agent) {
            $agentData = json_decode($agent->agent, true);
            
            $resultData = [
                'id' => null,
                'ark' => null,
                'pref_name' => null,
                'value' => $agentData['value'] ?? null,
                'role' => $agentData['role'] ?? [],
                'as_written' => $agentData['as_written'] ?? null,
                'note' => $agentData['note'] ?? null,
            ];
            
            if (isset($agentData['id'])) {
                $agentObject = Agent::where('ark', $agentData['id'])->first();
                if ($agentObject) {
                    $agentObjectJson = json_decode($agentObject->jsonb, true);
                    
                    $resultData['id'] = $agentObject->id ?? null;
                    $resultData['ark'] = $agentObjectJson['ark'] ?? null;
                    $resultData['pref_name'] = $agentObjectJson['pref_name'] ?? null;
                    $resultData['agent_json'] = $agentObjectJson;
                }
            }
            
            return $resultData;
        })->filter()->values()->toArray();
        
        // Since there can be the same agent multiple times in the same JSON, we will filter unique by 'ark'.
        return collect($agents)->unique('ark')->values()->toArray();
    }
}
