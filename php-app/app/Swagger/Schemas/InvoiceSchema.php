<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Invoice",
 *     type="object",
 *     required={"id", "user_id", "tax_profile_id", "subtotal", "tax_amount", "invoice_date"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="tax_profile_id", type="integer", example=1),
 *     @OA\Property(property="invoice_number", type="string", example="1740839386_NMEPW"),
 *     @OA\Property(property="invoice_date", type="string", format="date", example="2021-01-01"),
 *     @OA\Property(property="subtotal", type="string", example="100.00"),
 *     @OA\Property(property="tax_amount", type="string", example="22.00"), 
 *     @OA\Property(property="discount", type="string", example="00.00"), 
 *     @OA\Property(property="currency", type="string", example="EUR"),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="paid_at", type="string", format="date-time", example="null"), 
 *     @OA\Property(property="canceled_at", type="string", format="date-time", example="null"), 
 *     @OA\Property(property="notes", type="string", example="Comment"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01 00:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-01 00:00:00")
 * )
 */
class InvoiceSchema{}