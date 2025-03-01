<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="TaxProfile",
 *     type="object",
 *     required={"user_id", "tax_code", "address", "vat_number", "business_name"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="tax_code", type="string", example="RSSMRA80A01F205X"),
 *     @OA\Property(property="address", type="string", example="Via Roma 1, 00100, Milano"),
 *     @OA\Property(property="vat_number", type="string", example="11000500010"),
 *     @OA\Property(property="business_name", type="string", example="Mario Rossi Srl"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01 00:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-01 00:00:00")
 * )
 */
class TaxProfileSchema{}