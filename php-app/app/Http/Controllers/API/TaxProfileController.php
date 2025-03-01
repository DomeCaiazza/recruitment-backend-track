<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaxProfileResource;
use App\Models\TaxProfile;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class TaxProfileController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, User $user)
    {
        $perPage = $request->query('per_page', 10);

        $taxProfiles = QueryBuilder::for(TaxProfile::where('user_id', $user->id))
            ->allowedFilters([
                AllowedFilter::partial('tax_code'),
                AllowedFilter::partial('address'),
                AllowedFilter::partial('vat_number'),
                AllowedFilter::partial('business_name')
            ])
            ->paginate($perPage);
        
        return TaxProfileResource::collection($taxProfiles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'tax_code' => 'sometimes|max:128',
            'address' => 'sometimes|max:255',
            'vat_number' => 'sometimes|max:128',
            'business_name' => 'sometimes|max:128',
          ]);
        try {
            $taxProfile = $user->taxProfiles()->create($validatedData);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        }
        return response()->json([
            'message' => 'TaxProfile created successfully.',
            'data' => new TaxProfileResource($taxProfile)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, TaxProfile $taxProfile)
    {
        if ($taxProfile->user_id !== $user->id) {
            throw new NotFoundHttpException;
        }
        return new TaxProfileResource($taxProfile);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user, TaxProfile $taxProfile)
    {
        if ($taxProfile->user_id !== $user->id) {
            throw new NotFoundHttpException;
        }
        
        $validatedData = $request->validate([
            'tax_code' => 'sometimes|max:128',
            'address' => 'sometimes|max:255',
            'vat_number' => 'sometimes|max:128',
            'business_name' => 'sometimes|max:128',
          ]);

          $taxProfile->update($validatedData);
          if (!$taxProfile->wasChanged()) {
              return response()->noContent();
          }
  
          return response()->json([   
              'message' => 'TaxProfile updated successfully.',
              'data' => new TaxProfileResource($taxProfile)
          ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, TaxProfile $taxProfile)
    {
        if ($taxProfile->user_id !== $user->id) {
            throw new NotFoundHttpException;
        }
        $taxProfile->delete();
        return response()->noContent();
    }
}
