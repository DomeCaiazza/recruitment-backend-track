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
     * @OA\Get(
     *     path="/users/{userId}/tax-profiles",
     *     summary="Retrieves user's tax profiles",
     *     description="Returns a paginated collection of tax profiles",
     *     tags={"TaxProfiles"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="filter[tax_code]",
     *         in="query",
     *         description="Filter for partial search of tax code",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter[address]",
     *         in="query",
     *         description="Filter for partial address search",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter[vat_number]",
     *         in="query",
     *         description="Filter for partial VAT number search",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter[business_name]",
     *         in="query",
     *         description="Filter for partial company name search",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/per_page"),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TaxProfile")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Links")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Meta")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *        description="Record not found",
     *       @OA\JsonContent(ref="#/components/schemas/NotFound")
     *     )
     * )
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
     * @OA\Post(
     *     path="/users/{userId}/tax-profiles",
     *     summary="Create a new user's tax profile",
     *     description="Creates a new tax profile associated with the specified user.",
     *     tags={"TaxProfiles"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a TaxProfile",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="tax_code",
     *                 type="string",
     *                 maxLength=128,
     *                 description="Tax code (optional, must be unique with vat_number)"
     *             ),
     *             @OA\Property(
     *                 property="address",
     *                 type="string",
     *                 maxLength=255,
     *                 description="Address (optional)"
     *             ),
     *             @OA\Property(
     *                 property="vat_number",
     *                 type="string",
     *                 maxLength=128,
     *                 description="VAT number (optional, must be unique with tax_code)"
     *             ),
     *             @OA\Property(
     *                 property="business_name",
     *                 type="string",
     *                 maxLength=128,
     *                 description="Business name (optional)"
     *             ),
     *             example={
     *                 "tax_code": "RSSMRA80A01F205X",
     *                 "address": "Via Roma 1, 00100, Milano",
     *                 "vat_number": "11000500010",
     *                 "business_name": "Mario Rossi Srl"
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="TaxCode created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="TaxProfile created successfully."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/TaxProfile"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(ref="#/components/schemas/UnprocessableEntity")
     *     )
     * )
     */
    public function store(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'tax_code' => 'sometimes|max:128',
            'address' => 'sometimes|max:255',
            'vat_number' => 'sometimes|max:128',
            'business_name' => 'sometimes|max:128',
          ]);
        $taxProfile = $user->taxProfiles()->create($validatedData);
        return response()->json([
            'message' => 'TaxProfile created successfully.',
            'data' => new TaxProfileResource($taxProfile)
        ], 201);
    }

   /**
     * @OA\Get(
     *     path="/api/users/{userId}/tax-profiles/{id}",
     *     summary="Get a single TaxProfile",
     *     description="Returns a single TaxProfile by their ID",
     *     tags={"TaxProfiles"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of user",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the TaxProfile to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User getted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TaxProfile")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found",
     *         @OA\JsonContent(ref="#/components/schemas/NotFound")
     *     )
     * )
     */
    public function show(User $user, TaxProfile $taxProfile)
    {
        if ($taxProfile->user_id !== $user->id) {
            throw new NotFoundHttpException;
        }
        return new TaxProfileResource($taxProfile);
    }

    /**
    * @OA\Put(
    *     path="/users/{userId}/tax-profiles/{id}",
    *     summary="Update an existing TaxProfile",
    *     description="Updates the details of a TaxProfile associated with the specified user. Returns a 404 error if the TaxProfile does not belong to the user.",
    *     tags={"TaxProfiles"},
    *     security={{"ApiKeyAuth":{}}},
    *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
    *     @OA\Parameter(
    *         name="userId",
    *         in="path",
    *         description="User ID",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="TaxProfile ID",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         description="Data for updating the TaxProfile",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="tax_code",
    *                 type="string",
    *                 maxLength=128,
    *                 description="Tax code (optional)"
    *             ),
    *             @OA\Property(
    *                 property="address",
    *                 type="string",
    *                 maxLength=255,
    *                 description="Address (optional)"
    *             ),
    *             @OA\Property(
    *                 property="vat_number",
    *                 type="string",
    *                 maxLength=128,
    *                 description="VAT number (optional)"
    *             ),
    *             @OA\Property(
    *                 property="business_name",
    *                 type="string",
    *                 maxLength=128,
    *                 description="Business name (optional)"
    *             ),
    *             example={
    *                 "tax_code": "RSSMRA80A01F205X",
    *                 "address": "Via Roma 2, 00100, Milano",
    *                 "vat_number": "11000500010",
    *                 "business_name": "New Mario Rossi Srl"
    *             }
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="TaxProfile updated successfully",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="message",
    *                 type="string",
    *                 example="TaxProfile updated successfully."
    *             ),
    *             @OA\Property(
    *                 property="data",
    *                 ref="#/components/schemas/TaxProfile"
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=204,
    *         description="No content returned (no changes made)"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Record not found",
    *         @OA\JsonContent(ref="#/components/schemas/NotFound")
    *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(ref="#/components/schemas/UnprocessableEntity")
     *     )
    * )
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
     * @OA\Delete(
    *     path="/users/{userId}/tax-profiles/{id}",
     *     summary="Delete a TaxProfile",
     *     description="Deletes a TaxProfile by their ID and returns no content.",
     *     tags={"TaxProfiles"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the TaxProfile to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="TaxProfile deleted successfully, no content returned."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found",
     *         @OA\JsonContent(ref="#/components/schemas/NotFound")
     *     )
     * )
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
