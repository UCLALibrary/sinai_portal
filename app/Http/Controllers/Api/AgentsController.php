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
            $fields = (new Agent())->getFillableFields($request->json, json_encode($request->json));

            // create the resource
            $agent = Agent::create($fields);

            return new AgentResource($agent);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AgentRequest $request, Agent $agent): AgentResource
    {
        return DB::transaction(function () use ($request, $agent) {
            $fields = $agent->getFillableFields($request->json, json_encode($request->json));

            // update the resource
            $agent->update($fields);

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
}
