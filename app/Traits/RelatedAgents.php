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
        
        return $agentsQuery->map(function ($agent) {
            $agentData = json_decode($agent->agent, true);
            
            if (isset($agentData['id'])) {
                $agentObject = Agent::where('ark', $agentData['id'])->first();
                
                if ($agentObject) {
                    $agentJson = json_decode($agentObject->jsonb, true);
                    
                    return [
                        'id' => $agentObject->id ?? null,
                        'ark' => $agentJson['ark'] ?? null,
                        'pref_name' => $agentJson['pref_name'] ?? null,
                        'role' => $agentData['role'] ?? [],
                        'agent_json' => $agentJson
                    ];
                }
            }
            
            return null;
        })->filter()->values()->toArray();
    }
}
