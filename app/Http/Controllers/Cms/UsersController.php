<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    use AuthorizesRequests;

    protected $routes = [
        'index' => 'users.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'edit' => 'users.edit',
        'update' => 'users.update',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        return Inertia::render('Resources/Index', [
            'title' => 'Users',
            'resources' => User::paginate(20),
            'columns' => [
                'name' => 'Name',
                'email' => 'E-mail',
                'role_names' => 'Role'
            ],
            'routes' => $this->routes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', User::class);

        return Inertia::render('Resources/Create', [
            'title' => 'Users > Add User',
            'schema' => json_decode(User::$createSchema),
            'uischema' => json_decode(User::$createUiSchema),
            'routes' => $this->routes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        // extract and validate metadata from the json field
        $data = $this->_validatedJsonMetadata($request);

        // create the resource
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
        $this->authorize('update', $user);

        return Inertia::render('Resources/Edit', [
            'title' => 'Users > Edit User',
            'schema' => json_decode(User::$editSchema),
            'uischema' => json_decode(User::$editUiSchema),
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles()->first()->id ?? null
            ],
            'resource' => $user,
            'routes' => $this->routes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {

        $this->authorize('update', $user);

        // extract and validate metadata from the json field
        $data = $this->_validatedJsonMetadata($request, $user);

        // update the resource
        $response = $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $role = Role::findById($data['role'], 'web');
        if($role) {
            $user->syncRoles($role);
        }

        return $response
            ? response()->json(['message' => 'User updated successfully'])
            : response()->json(['error' => 'Error updating user']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {

        $this->authorize('delete', User::class);

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
    private function _validatedJsonMetadata($request, $user = null)
    {
        // extract metadata from json field
        $metadata = $this->_extractMetadataFromJsonData($request->json);

        // manually validate the metadata
        $validator = Validator::make($metadata, $request->myRules($user));

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
            $metadata['role'] = isset($jsonData['role']) ? $jsonData['role'] : null;
        }
        return $metadata;
    }
}
