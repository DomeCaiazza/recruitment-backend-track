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
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
            $invoice = $taxProfile->invoice()->create($validatedData);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json([
            'message' => 'Invoice created successfully.',
            'data' => new InvoiceResource($invoice->refresh())
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, TaxProfile $taxProfile, Invoice $invoice)
    {
        if ($invoice->tax_profile_id !== $taxProfile->id) {
            throw new NotFoundHttpException;
        }
        return new InvoiceResource($invoice);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
