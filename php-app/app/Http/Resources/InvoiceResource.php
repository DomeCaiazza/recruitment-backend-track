<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tax_profile_id' => $this->tax_profile_id,
            'user_id' => $this->taxProfile->user_id,
            'invoice_number' => $this->invoice_number,
            'invoice_date' => $this->invoice_date, 
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'discount' => $this->discount,
            'currency' => $this->currency,
            'status' => $this->status,
            'paid_at' => $this->paid_at,
            'canceled_at' => $this->canceled_at,
            'notes' => $this->notes,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}