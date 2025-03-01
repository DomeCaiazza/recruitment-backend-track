<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Meta",
 *     type="object",
 *             @OA\Property(property="current_page", type="integer", example=1),
 *             @OA\Property(property="from", type="integer", example=1),
 *             @OA\Property(property="last_page", type="integer", example=1),
 *             @OA\Property(
 *                 property="links",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="url", type="string", nullable=true, example=null),
 *                     @OA\Property(property="label", type="string", example="&laquo; Previous"),
 *                     @OA\Property(property="active", type="boolean", example=false)
 *                 )
 *             ),
 *             @OA\Property(property="path", type="string", example="http://[path]"),
 *             @OA\Property(property="per_page", type="integer", example=10),
 *             @OA\Property(property="to", type="integer", example=2),
 *             @OA\Property(property="total", type="integer", example=2)
 * )
 */
class MetaSchema{}