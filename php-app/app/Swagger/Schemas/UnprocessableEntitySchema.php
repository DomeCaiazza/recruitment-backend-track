<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="UnprocessableEntity",
 *     type="object",
 *     @OA\Property(
 *         property="message",
 *         type="object",
 *         @OA\Property(
 *             property="Field name",
 *             type="array",
 *             @OA\Items(type="string", example="Field error message")
 *         )
 *     )
 * )
 */
class UnprocessableEntitySchema{ }
