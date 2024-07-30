<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRequest;
use App\Http\Resources\AgentResource;
use App\Models\Agent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AgentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AgentResource::collection(Agent::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AgentRequest $request): AgentResource
    {
        return DB::transaction(function () use ($request) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // create the resource
            $agent = Agent::create($metadata);

            // insert the id into the json field
            $agent->json = json_encode(array_merge(json_decode($agent->json, true), ['id' => $agent->id]));

            $agent->save();

            return new AgentResource($agent);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AgentRequest $request, Agent $agent): AgentResource
    {
        return DB::transaction(function () use ($request, $agent) {
            // extract metadata from the json field to populate database columns for list view
            $metadata = $this->_extractMetadataFromJsonData($request->json);

            // update the resource
            $agent->update($metadata);

            return new AgentResource($agent);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agent $agent): JsonResponse
    {
        // TODO: do we want to allow deletion or just soft delete?

        $response = $agent->delete();
 
        return $response
            ? response()->json(['message' => 'Agent deleted successfully'])
            : response()->json(['error' => 'Error deleting agent']);
    }

    private function _extractMetadataFromJsonData($jsonData)
    {
        $metadata = [];
        if ($jsonData) {
            $metadata['json'] = json_encode($jsonData);
            $metadata['type'] = isset($jsonData['type']) ? $jsonData['type'] : null;
            $metadata['pref_name'] = isset($jsonData['pref_name']) ? $jsonData['pref_name'] : null;
        }
        return $metadata;
    }
}
