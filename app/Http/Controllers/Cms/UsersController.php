<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Resources/Index', [
            'title' => 'Users',
            'resourceName' => 'users',
            'resources' => User::paginate(20),
            'columns' => [
                'name' => 'Name',
                'email' => 'E-mail'
            ],
            'createEndpoint' => route('users.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resources/Create', [
            'title' => 'Users > Add User',
            'schema' => json_decode(User::$createSchema),
            'uischema' => json_decode(User::$createUiSchema),
            'saveEndpoint' => route('users.store'),
            'redirectUrl' => route('users.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        // extract and validate metadata from the json field
        $data = $this->_validatedJsonMetadata($request);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user
            ? response()->json(['message' => 'User created successfully'])
            : response()->json(['error' => 'Error creating user']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('Resources/Edit', [
            'title' => 'Users > Edit User',
            'schema' => json_decode(User::$editSchema),
            'uischema' => json_decode(User::$editUiSchema),
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'saveEndpoint' => route('users.update', $user->id),
            'redirectUrl' => route('users.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        // extract and validate metadata from the json field
        $data = $this->_validatedJsonMetadata($request);

        $response = $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        return $response
            ? response()->json(['message' => 'User updated successfully'])
            : response()->json(['error' => 'Error updating user']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        $response = $user->delete();

        return $response
            ? response()->json(['message' => 'User deleted successfully'])
            : response()->json(['error' => 'Error deleting user']);
    }

    /**
     * Extract and validate metadata from the JSON data.
     *
     * @return array
     */
    private function _validatedJsonMetadata($request)
    {
        // extract metadata from json field
        $metadata = $this->_extractMetadataFromJsonData($request->json);

        // manually validate the metadata
        $validator = Validator::make($metadata, $request->myRules());

        if ($validator->fails()) {
            $request->failedValidation($validator);
        }
        return $validator->validated();
    }

    private function _extractMetadataFromJsonData($jsonData)
    {
        $metadata = [];
        if ($jsonData) {
            $metadata['name'] = isset($jsonData['name']) ? $jsonData['name'] : null;
            $metadata['email'] = isset($jsonData['email']) ? $jsonData['email'] : null;
            $metadata['password'] = isset($jsonData['password']) ? $jsonData['password'] : null;
        }
        return $metadata;
    }
}
