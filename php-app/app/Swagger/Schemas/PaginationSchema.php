<?php


namespace App\Swagger\Schemas;

/**
 *     @OA\Parameter(
 *     parameter="per_page",
 *         name="per_page",
 *         in="query",
 *         description="Results per page",
 *         required=false,
 *         @OA\Schema(type="integer", default=10)
 *     ),
 *     @OA\Parameter(
 *         parameter="page",
 *         name="page",
 *         in="query",
 *         description="Results page",
 *         required=false,
 *         @OA\Schema(type="integer", default=1)
 *     ),
 */
class PaginationSchema {}