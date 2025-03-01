<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Links",
 *     type="object",
 *     @OA\Property(property="first", type="string", example="[url]?page=1"),
 *     @OA\Property(property="last", type="string", example="[url]?page=1"),
 *     @OA\Property(property="prev", type="string", nullable=true, example=null),
 *     @OA\Property(property="next", type="string", nullable=true, example=null),
 * )
 */
class LinksSchema{}