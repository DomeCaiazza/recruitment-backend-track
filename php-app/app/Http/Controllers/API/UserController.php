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
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'required',
            'surname' => 'required',
            'password' => 'required',
          ]);
          $user = User::create($validatedData);
          return response()->json([
            'message' => 'User created successfully.',
            'data' => new UserResource($user)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new UserResource(User::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
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
        return response()->json([   
            'message' => 'User updated successfully.',
            'data' => new UserResource($user)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully.'
        ], 200);
    }
}
