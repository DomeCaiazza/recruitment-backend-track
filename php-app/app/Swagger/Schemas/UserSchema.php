<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "name", "email"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Mario"),
 *     @OA\Property(property="surname", type="string", example="Rossi"),
 *     @OA\Property(property="email", type="string", example="mario@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01 00:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-01 00:00:00")
 * )
 */
class UserSchema{}