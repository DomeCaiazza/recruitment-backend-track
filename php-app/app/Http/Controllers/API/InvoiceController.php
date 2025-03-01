<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\TaxProfile;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class InvoiceController extends Controller
{

    /**
    *     @OA\Get(
    *     path="/users/{userId}/tax-profiles/{taxProfileId}/invoices",
    *     summary="Retrieve a list of invoices",
    *     description="Returns a paginated list of invoices, with the ability to filter by invoice number, date, currency, status, and notes.",
    *     tags={"Invoices"},
    *     security={{"ApiKeyAuth":{}}},
    *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
    *     @OA\Parameter(
    *         name="userId",
    *         in="path",
    *         description="ID of the user",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="taxProfileId",
    *         in="path",
    *         description="ID of the tax profile",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="filter[invoice_number]",
    *         in="query",
    *         description="Filter by invoice number (partial match)",
    *         required=false,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="filter[invoice_date]",
    *         in="query",
    *         description="Filter by invoice date (partial match)",
    *         required=false,
    *         @OA\Schema(
    *             type="string",
    *             format="date"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="filter[invoice_date_before]",
    *         in="query",
    *         description="Filter invoices with a date less than or equal to the specified date",
    *         required=false,
    *         @OA\Schema(
    *             type="string",
    *             format="date"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="filter[invoice_date_after]",
    *         in="query",
    *         description="Filter invoices with a date greater than or equal to the specified date",
    *         required=false,
    *         @OA\Schema(
    *             type="string",
    *             format="date"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="filter[currency]",
    *         in="query",
    *         description="Filter by currency",
    *         required=false,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="filter[status]",
    *         in="query",
    *         description="Filter by status",
    *         required=false,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="filter[notes]",
    *         in="query",
    *         description="Filter by notes (partial match)",
    *         required=false,
    *         @OA\Schema(
    *             type="string"
    *         )
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
    *                 @OA\Items(ref="#/components/schemas/Invoice")
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
    *         response=401,
    *         description="Unauthorized"
    *     ),
    *     @OA\Response(
    *         response=404,
    *        description="Record not found",
    *       @OA\JsonContent(ref="#/components/schemas/NotFound")
    *     )
    * )
    */
    public function index(Request $request, User $user, TaxProfile $taxProfile)
    {
        $perPage = $request->query('per_page', 10);
    
        $invoices = QueryBuilder::for(Invoice::where('tax_profile_id', $taxProfile->id))
            ->allowedFilters([
                AllowedFilter::partial('invoice_number'),
                AllowedFilter::partial('invoice_date'),
                AllowedFilter::callback('invoice_date_before', function ($query, $value) {
                    return $query->where('invoice_date', '<=', $value);
                }),
                AllowedFilter::callback('invoice_date_after', function ($query, $value) {
                    return $query->where('invoice_date', '>=', $value);
                }),
                AllowedFilter::partial('currency'),
                AllowedFilter::partial('status'),
                AllowedFilter::partial('notes')
            ])
            ->paginate($perPage);
        
        return InvoiceResource::collection($invoices);
    }
    /**
     * @OA\Post(
     *     path="/users/{userId}/tax-profiles/{taxProfileId}/invoices",
     *     summary="Create a new invoice",
     *     description="Creates a new invoice for the specified tax profile.",
     *     operationId="createInvoice",
     *     tags={"Invoices"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="taxProfileId",
     *         in="path",
     *         description="ID of the tax profile",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"invoice_date", "subtotal", "tax_amount", "discount"},
     *             @OA\Property(
     *                 property="invoice_date",
     *                 type="string",
     *                 format="date",
     *                 example="2025-03-01"
     *             ),
     *             @OA\Property(
     *                 property="subtotal",
     *                 type="number",
     *                 format="float",
     *                 example=100.50
     *             ),
     *             @OA\Property(
     *                 property="tax_amount",
     *                 type="number",
     *                 format="float",
     *                 example=15.07
     *             ),
     *             @OA\Property(
     *                 property="discount",
     *                 type="number",
     *                 format="float",
     *                 example=5.00
     *             ),
     *             @OA\Property(
     *                 property="currency",
     *                 type="string",
     *                 example="EUR"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="pending"
     *             ),
     *             @OA\Property(
     *                 property="notes",
     *                 type="string",
     *                 example="Invoice for services rendered"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Invoice created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invoice created successfully."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Invoice"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request, User $user, TaxProfile $taxProfile)
    {
        $validatedData = $request->validate([
            'invoice_date' => 'required|date',
            'subtotal' => 'required|numeric',
            'tax_amount' => 'required|numeric',
            'discount' => 'required|numeric',
            'currency' => 'sometimes',
            'status' => 'sometimes',
            'notes' => 'sometimes'
          ]);
        try {
            $invoice = $taxProfile->invoices()->create($validatedData);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json([
            'message' => 'Invoice created successfully.',
            'data' => new InvoiceResource($invoice->refresh())
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/users/{userId}/tax-profiles/{taxProfileId}/invoices/{id}",
     *     summary="Retrieve a single invoice",
     *     description="Returns a single invoice resource, ensuring that the invoice belongs to the specified tax profile. If the invoice does not belong to the given tax profile, a 404 error is returned.",
     *     tags={"Invoices"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="taxProfileId",
     *         in="path",
     *         description="ID of the tax profile",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the invoice",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Invoice")
     *     ),
     *     @OA\Response(
     *         response=404,
     *        description="Record not found",
     *         @OA\JsonContent(ref="#/components/schemas/NotFound")
     *     )
     * )
     */

    public function show(User $user, TaxProfile $taxProfile, Invoice $invoice)
    {
        if ($invoice->tax_profile_id !== $taxProfile->id) {
            throw new NotFoundHttpException;
        }
        return new InvoiceResource($invoice);
    }

    /**
     * @OA\Put(
     *     path="/users/{userId}/tax-profiles/{taxProfileId}/invoices/{id}",
     *     summary="Update an invoice",
     *     description="Updates an existing invoice. The invoice is updated only if it belongs to the specified tax profile. If no changes occur, a 204 No Content is returned.",
     *     tags={"Invoices"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/xApiKeyHeader"),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="taxProfileId",
     *         in="path",
     *         description="ID of the tax profile",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the invoice",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="invoice_date",
     *                 type="string",
     *                 format="date",
     *                 example="2025-03-01"
     *             ),
     *             @OA\Property(
     *                 property="subtotal",
     *                 type="number",
     *                 format="float",
     *                 example=100.50
     *             ),
     *             @OA\Property(
     *                 property="tax_amount",
     *                 type="number",
     *                 format="float",
     *                 example=15.07
     *             ),
     *             @OA\Property(
     *                 property="discount",
     *                 type="number",
     *                 format="float",
     *                 example=5.00
     *             ),
     *             @OA\Property(
     *                 property="currency",
     *                 type="string",
     *                 example="USD"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="pending"
     *             ),
     *             @OA\Property(
     *                 property="notes",
     *                 type="string",
     *                 example="Updated invoice note"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invoice updated successfully."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Invoice"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No content returned (no changes made)"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Invalid arguments",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found",
     *         @OA\JsonContent(ref="#/components/schemas/NotFound")
     *    )     
     *     )
     * )
     */

    public function update(Request $request, User $user, TaxProfile $taxProfile, Invoice $invoice)
    {
        if ($invoice->tax_profile_id !== $taxProfile->id) {
            throw new NotFoundHttpException;
        }
        
        $validatedData = $request->validate([
            'invoice_date' => 'sometimes|date',
            'subtotal' => 'sometimes|decimal:2',
            'tax_amount' => 'sometimes|decimal:2',
            'discount' => 'sometimes|decimal:2',
            'currency' => 'sometimes',
            'status' => 'sometimes',
            'notes' => 'sometimes'
          ]);
          try{
            $invoice->update($validatedData);
            } catch (\InvalidArgumentException $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
          if (!$invoice->wasChanged()) {
              return response()->noContent();
          }
  
          return response()->json([   
              'message' => 'Invoice updated successfully.',
              'data' => new InvoiceResource($invoice->refresh())
          ], 200);
    }

    /**
     * @OA\Delete(
    *     path="/users/{userId}/tax-profiles/{taxProfileId}/invoices/{id}",
     *     summary="Delete invoice",
     *     description="Deletes an Invoice by their ID and returns no content.",
     *     tags={"Invoices"},
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
     *         name="taxProfileId",
     *         in="path",
     *         description="ID of the tax profile",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Invoice to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Invoice deleted successfully, no content returned."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found",
     *         @OA\JsonContent(ref="#/components/schemas/NotFound")
     *     )
     * )
     */
    public function destroy(User $user, TaxProfile $taxProfile, Invoice $invoice)
    {
        if ($invoice->tax_profile_id !== $taxProfile->id) {
            throw new NotFoundHttpException;
        }
        $invoice->delete();
        return response()->noContent();
    }
}
