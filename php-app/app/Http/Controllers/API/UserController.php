<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

use App\Models\User;

class UserController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="List of users",
     *     description="Returns a paginated collection of users",
     *     tags={"Users"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
     *     @OA\Parameter(
     *         name="filter[email]",
     *         in="query",
     *         description="Filter by email",
     *         required=false,
     *         @OA\Schema(type="steing", format="email", example="mario@example.com")
     *     ),
     *     @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Filter by name",
     *         required=false,
     *         @OA\Schema(type="steing", example="Mario")
     *     ),
     *     @OA\Parameter(
     *         name="filter[surname]",
     *         in="query",
     *         description="Filter by surname",
     *         required=false,
     *         @OA\Schema(type="steing", example="Rossi")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/per_page"),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     * @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="data",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         ),
     *         @OA\Property(
     *             property="links",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Links")
     *         ),
     *         @OA\Property(
     *             property="meta",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Meta")
     *         )
     *     )
     * )
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $users = QueryBuilder::for(User::class)
        ->allowedFilters([
            AllowedFilter::partial('email'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('surname'),
        ])
        ->paginate($perPage);

        return UserResource::collection($users);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create a new user",
     *     description="Creates a new user after validating the input data and returns the created user's details.",
     *     tags={"Users"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
     *     @OA\RequestBody(
     *         required=true,
     *         description="The required data for creating a new user",
     *         @OA\JsonContent(
     *             required={"email", "name", "surname", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="surname", type="string", example="Doe"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(ref="#/components/schemas/UnprocessableEntity")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'required',
            'surname' => 'required',
            'password' => 'required',
          ]);
        

        try {
            $user = User::create($validatedData);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        }
          return response()->json([
            'message' => 'User created successfully.',
            'data' => new UserResource($user)
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get a single user",
     *     description="Returns a single user by their ID",
     *     tags={"Users"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User getted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found",
     *         @OA\JsonContent(ref="#/components/schemas/NotFound")
     *     )
     * )
     */
    public function show(string $id)
    {   
        return new UserResource(User::findOrFail($id));
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update an existing user",
     *     description="Updates an existing user's data. Only the provided fields will be updated.",
     *     tags={"Users"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to update",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for updating the user. Include only the fields you wish to change.",
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="surname", type="string", example="Doe"),
     *             @OA\Property(property="password", type="string", format="password", example="newSecret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No content, meaning no fields were changed"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found",
     *         @OA\JsonContent(ref="#/components/schemas/NotFound")
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $validatedData = $request->validate([
            'email' => 'sometimes|email|max:255',
            'name' => 'sometimes',
            'surname' => 'sometimes',
            'password' => 'sometimes',
          ]);
        $user->update($validatedData);

        if (!$user->wasChanged()) {
            return response()->noContent();
        }

        return response()->json([   
            'message' => 'User updated successfully.',
            'data' => new UserResource($user)
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete a user",
     *     description="Deletes a user by their ID and returns no content.",
     *     tags={"Users"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User deleted successfully, no content returned."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found",
     *         @OA\JsonContent(ref="#/components/schemas/NotFound")
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->noContent();
    }
}
